<?php
session_start();
include "../config/config.php";
require_once('tcpdf/tcpdf.php');

if (!isset($_SESSION['discente_id']) || !isset($_GET['id_corso'])) {
    die("Accesso negato.");
}

$discente_id = intval($_SESSION['discente_id']);
$id_corso = intval($_GET['id_corso']);

$query = "
    SELECT COUNT(l.id_lezione) AS total_lezioni,
           SUM(COALESCE(p.completata, 0)) AS lezioni_completate,
           SUM(COALESCE(p.superato_test, 0)) AS test_superati,
           c.titolo
    FROM lezioni l
    JOIN corsi c ON l.id_corso = c.id_corso
    LEFT JOIN progresso_lezioni p 
        ON l.id_lezione = p.id_lezione 
        AND p.discente_id = ?
        AND p.id_corso = ?
    WHERE l.id_corso = ?
";

$stmt = $db->prepare($query);
$stmt->bind_param("iii", $discente_id, $id_corso, $id_corso);
$stmt->execute();
$stmt->bind_result($total_lezioni, $lezioni_completate, $test_superati, $titolo_corso);
$stmt->fetch();
$stmt->close();

if ($total_lezioni == 0 || $lezioni_completate < $total_lezioni || $test_superati < $total_lezioni) {
    die("Corso non completato.");
}

$query_discente = "SELECT d.nome, d.cognome, d.email, r.Codice 
                   FROM discenti d
                   JOIN rubrica r ON d.id = r.IDUtente
                   WHERE d.id = ?";
$stmt = $db->prepare($query_discente);
$stmt->bind_param("i", $discente_id);
$stmt->execute();
$stmt->bind_result($nome, $cognome, $email, $codice_rubrica);
$stmt->fetch();
$stmt->close();

function sanitizeFilename($string) {
    return preg_replace('/[^A-Za-z0-9_\-]/', '_', $string);
}

$titolo_corso_sanitizzato = sanitizeFilename($titolo_corso);
$cognome_sanitizzato = sanitizeFilename($cognome);
$nome_sanitizzato = sanitizeFilename($nome);
$codice_rubrica_sanitizzato = sanitizeFilename($codice_rubrica);

$pdf = new TCPDF();
$pdf->SetCreator('Croce Verde Torino');
$pdf->SetAuthor('Croce Verde Torino');
$pdf->SetTitle('Certificato di Completamento');
$pdf->SetMargins(15, 20, 15);
$pdf->SetHeaderMargin(10);

$pdf->setHeaderData('', 0, 'Croce Verde Torino ODV | Via Tommaso Dorè, 4 - 10121 Torino (TO)', "\n");
$pdf->AddPage();

ob_end_clean();

$logoSinistra = __DIR__ . "/config/images/Logo_CV_green.png";  // Logo Croce Verde Torino
$logoDestra = __DIR__ . "/config/images/logo_partner.png"; // Logo del partner

if (file_exists($logoSinistra)) {
    $pdf->Image($logoSinistra, 17, 25, 30); // X = 15, Y = 10, Larghezza = 40mm
}
if (file_exists($logoDestra)) {
    $pdf->Image($logoDestra, 164, 25, 30); // X = 150, Y = 10, Larghezza = 40mm
}

$html = '
<style>
    h1 { font-size: 24px; text-align: center; font-weight: bold; color: #00A25E; }
    h2 { font-size: 18px; text-align: center; font-weight: bold; }
    p { text-align: center; font-size: 14px; margin-top: 10px; }
    .signature { margin-top: 50px; text-align: left; font-size: 12px; }
    .container { border: 3px solid #00A25E; padding: 20px; border-radius: 10px; text-align: center; }
</style>
<div class="container">
    <h2>Società Italiana Medicina</h2>
    <h2>Emergenza Urgenza Pediatrica</h2>
    <h2>Si certifica che</h2>
    <h1>' . strtoupper($nome . ' ' . $cognome) . '</h1>
    <p>Ha completato con successo il corso:</p>
    <h2>' . htmlspecialchars($titolo_corso) . '</h2>
    <p>Data di completamento: <strong>' . date("d/m/Y") . '</strong></p>
</div>
';

$pdf->writeHTML($html, true, false, true, false, '');

$certificatiPath = __DIR__ . "/certificati/";
if (!is_dir($certificatiPath)) {
    mkdir($certificatiPath, 0775, true);
}

$filename = "Certificato_{$titolo_corso_sanitizzato}#{$cognome_sanitizzato}_{$nome_sanitizzato}_{$codice_rubrica_sanitizzato}.pdf";
$filePath = $certificatiPath . $filename;
$pdf->Output($filePath, 'F');

if (!file_exists($filePath) || filesize($filePath) == 0) {
    die("Errore: Il certificato non è stato generato correttamente.");
}

inviaEmailCertificato($email, $nome, $cognome, $titolo_corso, $filename, $filePath,
    $titolo_corso_sanitizzato, $cognome_sanitizzato, $nome_sanitizzato, $codice_rubrica_sanitizzato);

echo json_encode(["success" => true, "message" => "Certificato generato e inviato via email."]);

function inviaEmailCertificato($email, $nome, $cognome, $titolo_corso, $filename, $filePath,
                               $titolo_corso_sanitizzato, $cognome_sanitizzato, $nome_sanitizzato, $codice_rubrica_sanitizzato) {
    $to = $email;
    $cc = "noreply@croceverde.org";

    $subject = "Certificato di completamento - " . strtoupper($titolo_corso_sanitizzato) . "#" . strtoupper($cognome_sanitizzato) . "_" . strtoupper($nome_sanitizzato) . "_" . $codice_rubrica_sanitizzato;

    $boundary = md5(time());

    $headers = "From: Gestionale CVTO <gestioneutenti@croceverde.org>\r\n";
    $headers .= "Bcc: $cc\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: multipart/mixed; boundary=\"$boundary\"\r\n";

    $emailBody = "--$boundary\r\n";
    $emailBody .= "Content-Type: text/html; charset=UTF-8\r\n";
    $emailBody .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
    $emailBody .= "<html><body>";
    $emailBody .= "<p><strong>Gentile $nome $cognome,</strong></p>";
    $emailBody .= "<p>Congratulazioni! Hai completato con successo il corso <strong>$titolo_corso</strong>.</p>";
    $emailBody .= "<p>In allegato trovi il tuo certificato di completamento.</p>";
    $emailBody .= "<p>Grazie,<br><strong>Il gruppo formazione Croce Verde Torino</strong></p>";
    $emailBody .= "</body></html>\r\n";

    $emailBody .= "--$boundary\r\n";
    $emailBody .= "Content-Type: application/pdf; name=\"$filename\"\r\n";
    $emailBody .= "Content-Transfer-Encoding: base64\r\n";
    $emailBody .= "Content-Disposition: attachment; filename=\"$filename\"\r\n\r\n";
    $emailBody .= chunk_split(base64_encode(file_get_contents($filePath)), 76, "\r\n");
    $emailBody .= "\r\n--$boundary--";

    if (!mail($to, $subject, $emailBody, $headers)) {
        error_log("Errore nell'invio della email a: $to", 0);
        die("Errore nell'invio della email.");
    }
}
?>