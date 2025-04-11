<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require '../vendor/autoload.php';
include "../config/config.php";
include "config/include/dictionary.php";

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

function getStatoServizioLabel($val) {
    switch ($val) {
        case 1: return 'Richiesto';
        case 2: return 'Accettato';
        case 3: return 'Confermato';
        case 4: return 'Rifiutato';
        case 5: return 'Annullato';
        case 6: return 'Chiuso';
        default: return 'Sconosciuto';
    }
}

$tipoServizioMap = [
    1 => 'A/R',
    2 => 'Solo andata',
];

$mezzoRichiestoMap = [
    1 => "Disabili",
    2 => "Autovettura",
];

function boolToLabel($val) {
    return $val == 1 ? 'Sì' : 'No';
}

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

$headers = [
    'IDServizio', 'StatoServizio', 'DataOraServizio', 'Richiedente', 'Contatto',
    'Partenza', 'Destinazione', 'TipoServizio', 'MezzoRichiesto', 'MezzoAssegnato',
    'Carrozzina', 'SediaMotore', 'InfoPaziente', 'InfoServizio',
    'Tariffa', 'Equipaggio', 'StatoTel'
];

$colIndex = 'A';
foreach ($headers as $header) {
    $sheet->setCellValue($colIndex . '1', $header);
    $sheet->getStyle($colIndex . '1')->getFont()->setBold(true);
    $sheet->getStyle($colIndex . '1')->getFill()->setFillType(Fill::FILL_SOLID)
        ->getStartColor()->setRGB('D9EAD3');
    $sheet->getColumnDimension($colIndex)->setAutoSize(true);
    $sheet->getStyle($colIndex)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
    $colIndex++;
}

$lastColIndex = count($headers);
$lastCol = Coordinate::stringFromColumnIndex($lastColIndex);
$sheet->setAutoFilter("A1:{$lastCol}1");
$sheet->freezePane('A2');

$where = [];

if (isset($_GET['dataFiltro']) && $_GET['dataFiltro'] === 'futuri') {
    $where[] = "DataOraServizio >= NOW()";
}

if (isset($_GET['statoFiltro']) && $_GET['statoFiltro'] !== '') {
    $statoFiltro = intval($_GET['statoFiltro']);
    $where[] = "StatoServizio = $statoFiltro";
}

$query = "SELECT * FROM mobilita";
if (!empty($where)) {
    $query .= " WHERE " . implode(" AND ", $where);
}
$query .= " ORDER BY DataOraServizio";

$result = $db->query($query);

$rowNumber = 2;

while ($row = $result->fetch_assoc()) {
    $colIndex = 'A';
    foreach ($headers as $field) {
        $val = $row[$field];

        switch ($field) {
            case 'StatoServizio':
                $val = getStatoServizioLabel($val);
                break;
            case 'StatoTel':
                $val = $dictionaryStatoTel[$val] ?? $val;
                break;
            case 'DataOraServizio':
                $val = (!empty($val) && $val != '0000-00-00 00:00:00') ? date("d/m/Y H:i", strtotime($val)) : '';
                break;
            case 'TipoServizio':
                $val = $tipoServizioMap[$val] ?? $val;
                break;
            case 'MezzoRichiesto':
                $val = $mezzoRichiestoMap[$val] ?? $val;
                break;
            case 'Carrozzina':
            case 'SediaMotore':
                $val = boolToLabel($val);
                break;
        }

        $sheet->setCellValue($colIndex . $rowNumber, $val);
        $colIndex++;
    }

    $rowNumber++;
}

$sheet->getStyle("A1:{$lastCol}" . ($rowNumber - 1))->applyFromArray([
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color' => ['rgb' => 'AAAAAA']
        ],
    ],
]);

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="servizi_mobilita_' . date("Ymd_His") . '.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>
