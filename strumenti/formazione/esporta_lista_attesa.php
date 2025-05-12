<?php
session_start();
include "../config/config.php";

if (!isset($_SESSION['Livello']) || $_SESSION['Livello'] != 28) {
    header("Location: index.php");
    exit();
}

$id_corso = isset($_GET['id_corso']) ? intval($_GET['id_corso']) : null;

if (!$id_corso) {
    die("Corso non specificato.");
}

$stmt = $db->prepare("
    SELECT la.id, la.id_utente, r.Nome, r.Cognome, r.CodFiscale, r.Mail, r.IDFiliale, r.IDSquadra
    FROM lista_attesa la
    JOIN rubrica r ON la.id_utente = r.IDUtente
    WHERE la.id_corso = ?
    ORDER BY la.data_iscrizione ASC
");
$stmt->bind_param("i", $id_corso);
$stmt->execute();
$result = $stmt->get_result();
$lista_attesa = $result->fetch_all(MYSQLI_ASSOC);

// Output CSV
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=lista_attesa_corso.csv');

$output = fopen('php://output', 'w');

// Intestazione colonne
fputcsv($output, ['Nome', 'Cognome', 'Codice Fiscale', 'Email', 'Sezione', 'Squadra']);

foreach ($lista_attesa as $utente) {
    fputcsv($output, [
        $utente['Nome'],
        $utente['Cognome'],
        $utente['CodFiscale'],
        $utente['Mail'],
        $utente['IDFiliale'],
        $utente['IDSquadra'],
    ]);
}

exit();
?>
