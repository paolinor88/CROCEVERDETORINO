<?php

header('Access-Control-Allow-Origin: *');
session_start();

include "../config/config.php";
require_once('tcpdf/tcpdf.php');
class MyPDF extends TCPDF {

    public function Footer() {
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 8);
        $date = date('d/m/Y H:i');
        $customMessage = "Documento generato automaticamente su richiesta dell'utente";
        $footerText = "$customMessage il $date";
        $this->Cell(0, 10, $footerText, 0, 0, 'C');
    }
    public function AddBackgroundImage($imagePath) {
        $pageWidth = $this->getPageWidth();
        $pageHeight = $this->getPageHeight();

        list($imageWidth, $imageHeight) = getimagesize($imagePath);

        $widthRatio = $pageWidth / $imageWidth;
        $heightRatio = $pageHeight / $imageHeight;
        $scaleRatio = min($widthRatio, $heightRatio);

        $newWidth = $imageWidth * $scaleRatio;
        $newHeight = $imageHeight * $scaleRatio;

        $x = ($pageWidth - $newWidth) / 2;
        $y = ($pageHeight - $newHeight) / 2;

        $this->SetAlpha(0.1);

        $this->Image($imagePath, $x, $y, $newWidth, $newHeight, '', '', '', false, 300, '', false, false, 0);

        $this->SetAlpha(1);
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $NAut = trim($_POST['NAut'] ?? null);
    $RilasciataIl = trim($_POST['RilasciataIl'] ?? null);
    $RilasciataDa = trim($_POST['RilasciataDa'] ?? null);
    $ScadenzaAutUltimoRetraining = trim($_POST['ScadenzaAutUltimoRetraining'] ?? null);

    $cognome = preg_replace('/\s+/', ' ', trim($_POST['Cognome'] ?? null));
    $cognome = htmlspecialchars($cognome, ENT_QUOTES);

    $nome = preg_replace('/\s+/', ' ', trim($_POST['Nome'] ?? null));
    $nome = htmlspecialchars($nome, ENT_QUOTES);

    $ComuneNascita = preg_replace('/\s+/', ' ', trim($_POST['DescComune'] ?? null));
    $ComuneNascita = htmlspecialchars($ComuneNascita, ENT_QUOTES);

    $DataNascita = trim($_POST['DataNascita'] ?? null);
    $Mail = trim($_POST['Mail'] ?? null);
    $note = preg_replace('/\s+/', ' ', trim($_POST['note'] ?? null));
    $note = htmlspecialchars($note, ENT_QUOTES);

    $Codice = trim($_POST['Codice'] ?? null);

    $Checkbox1 = isset($_POST['Checkbox1']) ? 'SI' : 'NO';
    $Checkbox2 = isset($_POST['Checkbox2']) ? 'SI' : 'NO';

    $IDUtente = $_SESSION['IDUtente'] ?? null;

    if (
        empty($IDUtente) || empty($NAut) || empty($RilasciataIl) ||
        empty($RilasciataDa) || empty($ScadenzaAutUltimoRetraining) || empty($Mail)
    ) {
        echo "<script>alert('Tutti i campi sono obbligatori!');
    window.location.href = 'https://www.croceverde.org/autisti/form_A1.php';</script>";
        exit;
    }

    try {
        $stmt = $db->prepare("
            INSERT INTO AUTISTI_PATENTI_AUT (IDUtente, IDQualifica, NAut, DataInizio, RilasciataDa, ScadenzaAutUltimoRetraining)
            VALUES (?, 67, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE 
                NAut = VALUES(NAut), DataInizio = VALUES(DataInizio),
                RilasciataDa = VALUES(RilasciataDa), ScadenzaAutUltimoRetraining = VALUES(ScadenzaAutUltimoRetraining)
        ");
        $stmt->bind_param("issss", $IDUtente, $NAut, $RilasciataIl, $RilasciataDa, $ScadenzaAutUltimoRetraining);

        if (!$stmt->execute()) {
            throw new Exception("Errore SQL: " . $stmt->error);
        }
        $stmt->close();
        $dataCreazione = date('d-m-Y');

        $pdf = new MyPDF();
        //$pdf->setExtraMetadata('<</MarkInfo <</Marked true>>>>');

        $pdf->SetMargins(15, 20, 15);
        $pdf->SetHeaderMargin(10);
        $pdf->SetFooterMargin(10);

        $pdf->setHeaderData('', 0, 'Croce Verde Torino ODV | Via Tommaso Dorè, 4 - 10121 Torino (TO)', "\n");

        $pdf->SetFont('helvetica', '', 11);

        $pdf->AddPage();

        $backgroundImagePath = __DIR__ . '/config/images/Logo_CV_green.png'; // Percorso dell'immagine sfocata
        $pdf->AddBackgroundImage($backgroundImagePath);

        $html = "
<style>
    h2 { text-align: center; font-weight: bold; margin-bottom: 20px; }
    p { margin: 10px 0; line-height: 1.6; }
    ul { margin-left: 20px; }
    li { margin-bottom: 10px; }
   .bold { font-weight: bold; }
</style>
<br>
<h2>DICHIARAZIONE POSSESSO REQUISITI E TITOLI PATENTE DI GUIDA</h2>

<p>Io sottoscritto <strong>{$cognome} {$nome}</strong>, nato a {$ComuneNascita} il {$DataNascita}, Socio Volontario presso la Croce Verde Torino ODV, con riferimento alla patente di categoria B di seguito indicata</p>

<table cellpadding='6' border='1' cellspacing='0'>
    <tr>
        <td><strong>Numero:</strong></td>
        <td>{$NAut}</td>
    </tr>
    <tr>
        <td><strong>Rilasciata il:</strong></td>
        <td>{$RilasciataIl}</td>
    </tr>
    <tr>
        <td><strong>Rilasciata da:</strong></td>
        <td>{$RilasciataDa}</td>
    </tr>
    <tr>
        <td><strong>Scadenza:</strong></td>
        <td>{$ScadenzaAutUltimoRetraining}</td>
    </tr>
</table>
";

        $pdf->writeHTML($html, true, false, true, false, '');

        $pdf->Ln(10);
        $pdf->SetFont('helvetica', 'B', 14);
        $pdf->Cell(0, 10, 'DICHIARO DI:', 0, 1, 'C');

        $pdf->SetFont('helvetica', '', 11);
        $pdf->writeHTML("<ul>
            <li>Non aver avuto la decurtazione completa dei punti della patente: <strong>{$Checkbox1}</strong></li>
            <li>Non avere la patente di guida né sospesa né revocata: <strong>{$Checkbox2}</strong></li>
            <p>{$note}</p>
        </ul>", true, false, true, false, '');

        $pdf->writeHTML("
<style>
    p { margin: 10px 0; line-height: 1.6; }
</style>
<p>Inoltre mi impegno a comunicare prontamente alla Direzione dei Servizi, tramite il Gruppo Autisti, la perdita totale dei punti patente e ogni provvedimento ostativo alla guida di qualsiasi tipo (sospensione, sospensione breve, revoca).</p>
", true, false, true, false, '');

        function sanitizeFileName($string) {
            $string = preg_replace('/[\'"]/', '', $string);
            $string = preg_replace('/\s+/', '_', $string);
            $string = preg_replace('/[^A-Za-z0-9_\-]/', '', $string);
            return $string;
        }

        $cognomeSanitized = sanitizeFileName($cognome);
        $nomeSanitized = sanitizeFileName($nome);
        $CodiceSanitized = sanitizeFileName($Codice);

        $filePath = __DIR__ . "/pdf/AUTOCERTIFICAZIONE_{$cognomeSanitized}_{$nomeSanitized}_{$CodiceSanitized}#{$IDUtente}.pdf";
        //$filePath = __DIR__ . "/pdf/AUTOCERTIFICAZIONE_{$Codice}_{$cognome}_{$nome}_#{$IDUtente}.pdf";
        $pdf->Output($filePath, 'F');
        $fileData = chunk_split(base64_encode(file_get_contents($filePath)));
        $boundary = md5(time());
        $to = $Mail;
        $subject = "Autocertificazione possesso requisiti di guida";
        $message = "In allegato la tua autocertificazione compilata; una copia è stata salvata nella tua anagrafica e sarà conservata per gli scopi previsti.";

        $headers = "From: Gestionale CVTO <gestioneutenti@croceverde.org>\r\n";
        $headers .= "Bcc: noreply@croceverde.org, autisti@croceverde.org\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: multipart/mixed; boundary=\"{$boundary}\"\r\n";

        $emailBody = "--{$boundary}\r\n";
        $emailBody .= "Content-Type: text/plain; charset=UTF-8\r\n\r\n";
        $emailBody .= $message . "\r\n";
        $emailBody .= "--{$boundary}\r\n";
        $emailBody .= "Content-Type: application/pdf; name=\"AUTOCERTIFICAZIONE_{$cognomeSanitized}_{$nomeSanitized}_{$CodiceSanitized}#{$IDUtente}.pdf\"\r\n";
        $emailBody .= "Content-Transfer-Encoding: base64\r\n";
        $emailBody .= "Content-Disposition: attachment; filename=\"AUTOCERTIFICAZIONE_{$cognomeSanitized}_{$nomeSanitized}_{$CodiceSanitized}#{$IDUtente}.pdf\"\r\n\r\n";
        $emailBody .= $fileData . "\r\n";
        $emailBody .= "--{$boundary}--";

        if (!mail($to, $subject, $emailBody, $headers)) {
            throw new Exception("Errore durante l'invio dell'email.");
        }

        unlink($filePath);

        echo "<script>
    alert('Autocertificazione salvata e inviata con successo!');
    window.location.href = 'https://www.croceverde.org';
</script>";
        exit;


    } catch (Exception $e) {
        echo "<script>alert('Errore: " . addslashes($e->getMessage()) . "'); window.history.back();</script>";
    }
} else {
    echo "<script>alert('Metodo non consentito.'); window.location.href='form_A1.php';</script>";
}
?>
