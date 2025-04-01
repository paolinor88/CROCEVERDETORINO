<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include "../config/config.php";

require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

function debug_log($message) {
    $log_file = 'php_errorlog'; // Assicurati che questo percorso sia scrivibile
    file_put_contents($log_file, date('Y-m-d H:i:s') . " - " . $message . "\n", FILE_APPEND);
}

$IDFiliale = isset($_GET['IDFiliale']) ? $_GET['IDFiliale'] : '';
$IDSquadra = isset($_GET['IDSquadra']) ? $_GET['IDSquadra'] : '';

debug_log("IDFiliale: $IDFiliale");
debug_log("IDSquadra: $IDSquadra");

$query = "SELECT Codice, Cognome, Nome, IDFiliale, IDSquadra FROM rubrica WHERE (R=3 OR MA=4 OR MAU=5)";

if ($IDFiliale !== '') {
    $query .= " AND IDFiliale = '$IDFiliale'";
}

if ($IDSquadra !== '') {
    $query .= " AND IDSquadra = '$IDSquadra'";
}

debug_log("Query: $query");

$result = $db->query($query);

if ($result === false) {
    die("Errore nella query: " . $db->error);
}

$num_rows = $result->num_rows;
debug_log("Numero di record trovati: $num_rows");

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

$sheet->setCellValue('A1', 'Codice');
$sheet->setCellValue('B1', 'Cognome');
$sheet->setCellValue('C1', 'Nome');
$sheet->setCellValue('D1', 'Sezione');
$sheet->setCellValue('E1', 'Squadra');

$rowNumber = 2;
while ($row = $result->fetch_assoc()) {
    $sheet->setCellValue('A' . $rowNumber, $row['Codice']);
    $sheet->setCellValue('B' . $rowNumber, $row['Cognome']);
    $sheet->setCellValue('C' . $rowNumber, $row['Nome']);
    $sheet->setCellValue('D' . $rowNumber, $row['IDFiliale']);
    $sheet->setCellValue('E' . $rowNumber, $row['IDSquadra']);
    $rowNumber++;
}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="autisti_export.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>
