<?php
session_start();
include "../config/config.php";
include "config/include/destinatari.php";
include "config/include/dictionary.php";

require_once('tcpdf/tcpdf.php');

class MyPDF extends TCPDF {
    public function Footer() {
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 8);
        $date = date('d/m/Y H:i');
        $footerText = "Documento generato automaticamente il $date";
        $this->Cell(0, 10, $footerText, 0, 0, 'C');
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $id_richiesta = intval($_POST['id_richiesta']);
    $candidato_id = intval($_POST['id_utente']);
    $candidato = $_POST['candidato'] ?? 'N/A';
    $esaminatore = $_POST['esaminatore'] ?? 'N/A';

    $datarientri = !empty($_POST['datarientri']) ? $_POST['datarientri'] : 'N/A';
    $datanormali = !empty($_POST['datanormali']) ? $_POST['datanormali'] : 'N/A';

    $tipoprova_label = $_POST['tipoprovalabel'] ?? 'N/A';
    $attenzione = $_POST['attenzione'] ?? 'N/A';
    $rallenta = $_POST['rallenta'] ?? 'N/A';
    $ferma = $_POST['ferma'] ?? 'N/A';
    $destra = $_POST['destra'] ?? 'N/A';
    $agita = $_POST['agita'] ?? 'N/A';
    $toponomastica = $_POST['toponomastica'] ?? 'N/A';
    $percorso = $_POST['percorso'] ?? 'N/A';
    $disagio = $_POST['disagio'] ?? 'N/A';
    $esitoX = $_POST['esito'] ?? 'N/A';
    $esito = $dictionaryEsito[$esitoX];
    $candidato_mail = $_POST['candidato_mail'] ?? '';
    $candidato_stringa = $_POST['candidatostringa'] ?? '';
    $commenti_esame = htmlspecialchars(strip_tags(trim($_POST['commentiesame'] ?? '')));
    $data_prova = date("d/m/Y");

    $stmt = $db->prepare("UPDATE AUTISTI_RICHIESTE SET EsitoProva = ?, NoteEsame = ?, StatoRichiesta=3 WHERE IDRichiesta = ?");
    if (!$stmt) {
        die("Errore nella preparazione della query: " . $db->error);
    }
    $stmt->bind_param("ssi", $esitoX, $commenti_esame, $id_richiesta);
    if (!$stmt->execute()) {
        die("Errore durante l'aggiornamento dell'EsitoProva: " . $stmt->error);
    }
    $stmt->close();

    $stmt = $db->prepare("UPDATE AUTISTI_RICHIESTE SET DataProva = CURDATE() WHERE IDRichiesta = ? AND DataProva IS NULL");
    if (!$stmt) {
        die("Errore nella preparazione della query per aggiornare la DataProva: " . $db->error);
    }
    $stmt->bind_param("i", $id_richiesta);
    if (!$stmt->execute()) {
        die("Errore durante l'aggiornamento della DataProva: " . $stmt->error);
    }
    $stmt->close();

    $stmt = $db->prepare("SELECT IDFiliale, IDSquadra FROM rubrica WHERE IDUtente = ?");
    $stmt->bind_param("i", $candidato_id);
    $stmt->execute();
    $stmt->bind_result($IDFiliale, $IDSquadra);
    $stmt->fetch();
    $stmt->close();

    $cc = ($IDFiliale == 1) ? ($emailSquadre[$IDSquadra] ?? '') : ($emailFiliali[$IDFiliale] ?? '');

    $pdf = new MyPDF();
    $pdf->SetMargins(15, 20, 15);
    $pdf->SetHeaderMargin(10);
    $pdf->SetFooterMargin(10);
    $pdf->setHeaderData('', 0, 'Croce Verde Torino ODV | Via Tommaso Dorè, 4 - 10121 Torino (TO)', "\n");
    $pdf->AddPage();
    $pdf->SetFont('helvetica', '', 12);

    $html = "
    <style>
        h2 { text-align: center; color: #00A25E; font-size: 18px; }
        p { margin: 8px 0; line-height: 1.6; }
        .bold { font-weight: bold; }
    </style>
    <h2>VALUTAZIONE PROVA DI GUIDA URGENZE</h2>
    <table cellpadding='6' border='1' cellspacing='0'>
        <tr><td><strong>Candidato:</strong></td><td>{$candidato}</td></tr>
        <tr><td><strong>Esaminatore:</strong></td><td>{$esaminatore}</td></tr>
        <tr><td>Data prova rientri:</td><td>{$datarientri}</td></tr>
        <tr><td>Data prova normali:</td><td>{$datanormali}</td></tr>
        <tr><td><strong>Data prova urgenze:</strong></td><td>{$data_prova}</td></tr>
    </table>
    <br>
    <h2>Punteggio</h2>
    <table cellpadding='6' border='1' cellspacing='0'>
        <tr><td><strong>Attenzione agli incroci</strong></td><td>{$attenzione}</td></tr>
        <tr><td><strong>Si ferma agli incroci con semaforo verde</strong></td><td>{$rallenta}</td></tr>
        <tr><td><strong>Si ferma agli incroci con semaforo rosso</strong></td><td>{$ferma}</td></tr>
        <tr><td><strong>Ha chiesto se la destra è libera</strong></td><td>{$destra}</td></tr>
        <tr><td><strong>Si è agitato nella guida in urgenza</strong></td><td>{$agita}</td></tr>
        <tr><td><strong>Conosce la toponomastica della città</strong></td><td>{$toponomastica}</td></tr>
        <tr><td><strong>Ha utilizzato il percorso più breve in servizio</strong></td><td>{$percorso}</td></tr>
        <tr><td><strong>Ha prestato attenzione a provocare il minor disagio al paziente</strong></td><td>{$disagio}</td></tr>
    </table>
        <h2>Commenti</h2>
    <table cellpadding='6' border='1' cellspacing='0'>
        <tr><td>{$commenti_esame}</td></tr>
    </table>
    <br>
    <h2>Esito Finale: <span class='bold'>{$esito}</span></h2>
    ";

    $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

    $FileNameSanitized = preg_replace('/[^A-Za-z0-9_\-]/', '_', $candidato_stringa);
    $filename = "Valutazione_{$tipoprova_label}_{$FileNameSanitized}_ID{$id_richiesta}.pdf";
    $filePath = __DIR__ . "/pdf/" . $filename;
    $pdf->Output($filePath, 'F');

    if (!file_exists($filePath)) {
        die("Errore: Il file PDF non è stato generato correttamente.");
    }

    $to = "$autisti";
    $subject = "Esito prova {$tipoprova_label} - {$FileNameSanitized}";
    $boundary = md5(time());
    $headers = "From: Gestionale CVTO <gestioneutenti@croceverde.org>\r\n";
    if (!empty($cc)) {
        $headers .= "Cc: $cc\r\n";
    }
    $headers .= "Bcc: $noreply\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: multipart/mixed; boundary=\"$boundary\"\r\n";

    $emailBody = "--$boundary\r\n";
    $emailBody .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $emailBody .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
    $emailBody .= "Gentile Responsabile,\r\n\r\n";
    $emailBody .= "la prova di guida {$tipoprova_label} di {$candidato} è stata valutata con il seguente esito: {$esito}.\r\n";
    $emailBody .= "Troverai il documento dettagliato in allegato.\r\n\r\n";
    $emailBody .= "Grazie,\r\n";
    $emailBody .= "Gruppo Autisti Croce Verde Torino\r\n\r\n";
    $emailBody .= "--$boundary\r\n";
    $emailBody .= "Content-Type: application/pdf; name=\"$filename\"\r\n";
    $emailBody .= "Content-Transfer-Encoding: base64\r\n";
    $emailBody .= "Content-Disposition: attachment; filename=\"$filename\"\r\n\r\n";
    $emailBody .= chunk_split(base64_encode(file_get_contents($filePath)), 76, "\r\n");
    $emailBody .= "\r\n--$boundary--";

    if (!mail($to, $subject, $emailBody, $headers)) {
        error_log("Errore nell'invio della email a: $to, CC: $cc", 0);
        die("Errore nell'invio della email. Contatta l'amministratore.");
    }

    unlink($filePath);
    echo "<script>alert('Report esame generato e inviato con successo!'); window.location.href='index.php';</script>";
}
?>
