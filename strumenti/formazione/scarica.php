<?php
session_start();
require_once "../config/config.php";
global $db;

if (!isset($_SESSION['discente_id'])) {
    exit("Accesso non autorizzato");
}

$IDCorso = isset($_GET['IDCorso']) ? intval($_GET['IDCorso']) : 0;
$discente_id = $_SESSION['discente_id'];

// Recupero il codice fiscale
$query = "SELECT codice_fiscale FROM discenti WHERE id = ?";
$stmt = $db->prepare($query);
$stmt->bind_param("i", $discente_id);
$stmt->execute();
$stmt->bind_result($codice_fiscale);
$stmt->fetch();
$stmt->close();

$codice_fiscale = strtoupper($codice_fiscale);
$filename = "{$codice_fiscale}_IDCORSO{$IDCorso}.pdf";
$filepath = __DIR__ . "/certificati/" . $filename;

if (file_exists($filepath)) {
    header("Content-Type: application/pdf");
    header("Content-Disposition: attachment; filename=\"$filename\"");
    readfile($filepath);
    exit();
} else {
    http_response_code(404);
    echo "Certificato non trovato.";
}
?>
