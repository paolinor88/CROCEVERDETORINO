<?php
require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Style\Color;

session_start();
include "../config/config.php";

// Recupera i parametri dei filtri
$filiale = isset($_GET['filiale']) ? $_GET['filiale'] : '';
$squadra = isset($_GET['squadra']) ? $_GET['squadra'] : '';

$dictionaryFiliale = array(
    1 => "Torino",
    2 => "Alpignano",
    3 => "Borgaro/Caselle",
    4 => "Ciriè",
    5 => "San Mauro",
    6 => "Venaria",
);

$dictionarySquadra = array(
    1 => "1",
    2 => "2",
    3 => "3",
    4 => "4",
    5 => "5",
    6 => "6",
    7 => "7",
    8 => "8",
    9 => "9",
    10 => "Sabato",
    11 => "Montagna",
    18 => "Diurno",
    19 => "Giovani",
    20 => "Serv. Generali",
    22 => "Serv. Cittadino",
    23 => "Dipendenti",
);

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

$sheet->setCellValue('A1', 'CODICE');
$sheet->setCellValue('B1', 'COGNOME');
$sheet->setCellValue('C1', 'NOME');
$sheet->setCellValue('D1', 'CELLULARE');
$sheet->setCellValue('E1', 'EMAIL');
$sheet->setCellValue('F1', 'DATA DI NASCITA');
$sheet->setCellValue('G1', 'SEZIONE');
$sheet->setCellValue('H1', 'SQUADRA');
$sheet->setCellValue('I1', 'RIENTRI');
$sheet->setCellValue('J1', 'NORMALI');
$sheet->setCellValue('K1', 'URGENZE');
$sheet->setCellValue('L1', 'SCADENZA URGENZE');
$sheet->setCellValue('M1', 'OVER');
$sheet->setCellValue('N1', 'SCADENZA OVER');
$sheet->setCellValue('O1', 'MAX RINNOVO OVER 65');
$sheet->setCellValue('P1', '1° MODULO TEORICO');
$sheet->setCellValue('Q1', '2° MODULO TEORICO');
$sheet->setCellValue('R1', 'PRATICO BASE');
$sheet->setCellValue('S1', 'PRATICO PLUS');
$sheet->setCellValue('T1', 'NUMERO PATENTE');
$sheet->setCellValue('U1', 'DATA RILASCIO');
$sheet->setCellValue('V1', 'SCADENZA PATENTE');
$sheet->setCellValue('W1', 'ENTE DI RILASCIO');

$sheet->setAutoFilter('A1:W1');

$headerStyleArray = [
    'font' => [
        'bold' => true,
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => [
            'argb' => '29ABE2',
        ],
    ],
];
$sheet->getStyle('A1:W1')->applyFromArray($headerStyleArray);

$query = "SELECT * FROM rubrica WHERE (R=3 OR MA=4 OR MAU=5)";
if ($filiale) {
    $query .= " AND IDFiliale = '{$filiale}'";
}
if ($squadra) {
    $query .= " AND IDSquadra = '{$squadra}'";
}
$query .= " ORDER BY Cognome";
$result = $db->query($query);

function mostraDatiAutistiExcel($db, $codice, $tabella, $tipo)
{
    $query = $db->query("SELECT * FROM $tabella WHERE Codice='$codice'");
    $dati = [];

    if ($query && $query->num_rows > 0) {
        while ($row = $query->fetch_array()) {
            if (!empty($row['DataInizio']) && strtotime($row['DataInizio']) !== false) {
                $dati[$tipo] = date('Y-m-d', strtotime($row['DataInizio']));
            }
            if ($tipo === 'Urgenze' && !empty($row['SCADENZAURGENZE'])) {
                $dati['Scadenza Urgenze'] = date('Y-m-d', strtotime($row['SCADENZAURGENZE']));
            } elseif ($tipo === 'Over') {
                if (!empty($row['SCADENZAOVER'])) {
                    $dati['Scadenza Over'] = date('Y-m-d', strtotime($row['SCADENZAOVER']));
                }
                if (!empty($row['LIMITEOVER'])) {
                    $dati['Max Rinnovo Over'] = date('Y-m-d', strtotime($row['LIMITEOVER']));
                }
            }
        }
    }
    return $dati;
}

function mostraDataInizioFormazione($db, $codice, $idQualifica)
{
    $query = $db->query("SELECT DataInizio FROM AUTISTI_FORMAZIONE WHERE Codice='$codice' AND IDQualifica='$idQualifica'");
    $data = '';

    if ($query && $query->num_rows > 0) {
        $row = $query->fetch_array();
        if (!empty($row['DataInizio']) && strtotime($row['DataInizio']) !== false) {
            $data = date('Y-m-d', strtotime($row['DataInizio']));
        }
    }
    return $data ?: null;
}

function verificaAutistaOver($db, $codice)
{
    $query = $db->query("SELECT * FROM AUTISTI_OVER WHERE Codice='$codice' AND IDQualifica='59'");
    return $query && $query->num_rows > 0 ? 'VERO' : '';
}

function mostraDatiPatente($db, $codice)
{
    $query = $db->query("SELECT Naut, DataInizio, ScadenzaAutUltimoRetraining, RilasciataDa FROM AUTISTI_PATENTI WHERE Codice='$codice'");
    $dati = [];

    if ($query && $query->num_rows > 0) {
        $row = $query->fetch_array();
        $dati['Naut'] = $row['Naut'];
        if (!empty($row['DataInizio']) && strtotime($row['DataInizio']) !== false) {
            $dati['DataInizio'] = date('Y-m-d', strtotime($row['DataInizio']));
        }
        if (!empty($row['ScadenzaAutUltimoRetraining']) && strtotime($row['ScadenzaAutUltimoRetraining']) !== false) {
            $dati['Scadenza'] = date('Y-m-d', strtotime($row['ScadenzaAutUltimoRetraining']));
        }
        $dati['RilasciataDa'] = $row['RilasciataDa'];
    }

    return $dati;
}

$row = 2;
while ($ciclo = $result->fetch_assoc()) {
    $sheet->setCellValue("A{$row}", $ciclo['Codice']);
    $sheet->setCellValue("B{$row}", $ciclo['Cognome']);
    $sheet->setCellValue("C{$row}", $ciclo['Nome']);
    $sheet->setCellValue("D{$row}", $ciclo['Cellulare']);
    $sheet->setCellValue("E{$row}", $ciclo['Mail']);
    if(!empty($ciclo['DataNascita'])) {
        $sheet->setCellValue("F{$row}", \PhpOffice\PhpSpreadsheet\Shared\Date::stringToExcel($ciclo['DataNascita']));
    }
    $sheet->setCellValue("G{$row}", $dictionaryFiliale[$ciclo['IDFiliale']] ?? '');
    $sheet->setCellValue("H{$row}", $dictionarySquadra[$ciclo['IDSquadra']] ?? '');

    $datiAutistiRientri = mostraDatiAutistiExcel($db, $ciclo['Codice'], 'AUTISTI_RIENTRI', 'Rientri');
    if (!empty($datiAutistiRientri['Rientri'])) {
        $sheet->setCellValue("I{$row}", \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(strtotime($datiAutistiRientri['Rientri'])));
    }

    $datiAutistiNormali = mostraDatiAutistiExcel($db, $ciclo['Codice'], 'AUTISTI_NORMALI', 'Normali');
    if (!empty($datiAutistiNormali['Normali'])) {
        $sheet->setCellValue("J{$row}", \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(strtotime($datiAutistiNormali['Normali'])));
    }

    $datiAutistiUrgenze = mostraDatiAutistiExcel($db, $ciclo['Codice'], 'AUTISTI_URGENZE', 'Urgenze');
    if (!empty($datiAutistiUrgenze['Urgenze'])) {
        $sheet->setCellValue("K{$row}", \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(strtotime($datiAutistiUrgenze['Urgenze'])));
        if (!empty($datiAutistiUrgenze['Scadenza Urgenze'])) {
            $sheet->setCellValue("L{$row}", \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(strtotime($datiAutistiUrgenze['Scadenza Urgenze'])));
        }
    }

    $overVerifica = verificaAutistaOver($db, $ciclo['Codice']);
    $sheet->setCellValue("M{$row}", $overVerifica);

    $datiOver = mostraDatiAutistiExcel($db, $ciclo['Codice'], 'AUTISTI_OVER', 'Over');
    if (!empty($datiOver['Scadenza Over'])) {
        $sheet->setCellValue("N{$row}", \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(strtotime($datiOver['Scadenza Over'])));
    }
    if (!empty($datiOver['Max Rinnovo Over'])) {
        $sheet->setCellValue("O{$row}", \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(strtotime($datiOver['Max Rinnovo Over'])));
    }

    $modulo1Teorico = mostraDataInizioFormazione($db, $ciclo['Codice'], 39);
    if ($modulo1Teorico) {
        $sheet->setCellValue("P{$row}", \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(strtotime($modulo1Teorico)));
    }

    $modulo2Teorico = mostraDataInizioFormazione($db, $ciclo['Codice'], 40);
    if ($modulo2Teorico) {
        $sheet->setCellValue("Q{$row}", \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(strtotime($modulo2Teorico)));
    }

    $praticoBase = mostraDataInizioFormazione($db, $ciclo['Codice'], 50);
    if ($praticoBase) {
        $sheet->setCellValue("R{$row}", \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(strtotime($praticoBase)));
    }

    $praticoPlus = mostraDataInizioFormazione($db, $ciclo['Codice'], 62);
    if ($praticoPlus) {
        $sheet->setCellValue("S{$row}", \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(strtotime($praticoPlus)));
    }

    $datiPatente = mostraDatiPatente($db, $ciclo['Codice']);
    $sheet->setCellValue("T{$row}", $datiPatente['Naut'] ?? '');
    if (!empty($datiPatente['DataInizio'])) {
        $sheet->setCellValue("U{$row}", \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(strtotime($datiPatente['DataInizio'])));
    }
    if (!empty($datiPatente['Scadenza'])) {
        $sheet->setCellValue("V{$row}", \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(strtotime($datiPatente['Scadenza'])));
    }
    $sheet->setCellValue("W{$row}", $datiPatente['RilasciataDa'] ?? '');

    $row++;
}

$dateColumns = ['F', 'I', 'J', 'K', 'L', 'N', 'O', 'P', 'Q', 'R', 'S', 'U', 'V'];
foreach ($dateColumns as $columnID) {
    $sheet->getStyle("{$columnID}2:{$columnID}{$row}")
        ->getNumberFormat()
        ->setFormatCode(NumberFormat::FORMAT_DATE_DDMMYYYY);
}

$today = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(time());

$conditionalStyles = $sheet->getStyle('L2:L' . $row)->getConditionalStyles();
$condition = new Conditional();
$condition->setConditionType(Conditional::CONDITION_CELLIS)
    ->setOperatorType(Conditional::OPERATOR_LESSTHAN)
    ->addCondition($today);
$condition->getStyle()->getFont()->getColor()->setARGB(Color::COLOR_RED);
$conditionalStyles[] = $condition;
$sheet->getStyle('L2:L' . $row)->setConditionalStyles($conditionalStyles);

$conditionalStyles = $sheet->getStyle('N2:N' . $row)->getConditionalStyles();
$condition = new Conditional();
$condition->setConditionType(Conditional::CONDITION_CELLIS)
    ->setOperatorType(Conditional::OPERATOR_LESSTHAN)
    ->addCondition($today);
$condition->getStyle()->getFont()->getColor()->setARGB(Color::COLOR_RED);
$conditionalStyles[] = $condition;
$sheet->getStyle('N2:N' . $row)->setConditionalStyles($conditionalStyles);

$conditionalStyles = $sheet->getStyle('O2:O' . $row)->getConditionalStyles();
$condition = new Conditional();
$condition->setConditionType(Conditional::CONDITION_CELLIS)
    ->setOperatorType(Conditional::OPERATOR_LESSTHAN)
    ->addCondition($today);
$condition->getStyle()->getFont()->getColor()->setARGB(Color::COLOR_RED);
$conditionalStyles[] = $condition;
$sheet->getStyle('O2:O' . $row)->setConditionalStyles($conditionalStyles);

$conditionalStyles = $sheet->getStyle('V2:V' . $row)->getConditionalStyles();
$condition = new Conditional();
$condition->setConditionType(Conditional::CONDITION_CELLIS)
    ->setOperatorType(Conditional::OPERATOR_LESSTHAN)
    ->addCondition($today);
$condition->getStyle()->getFont()->getColor()->setARGB(Color::COLOR_RED);
$conditionalStyles[] = $condition;
$sheet->getStyle('V2:V' . $row)->setConditionalStyles($conditionalStyles);

foreach (range('A', 'W') as $columnID) {
    $sheet->getColumnDimension($columnID)->setAutoSize(true);
}

$writer = new Xlsx($spreadsheet);
$filename = 'Autisti_'.$dictionaryFiliale[$filiale].'-'.$dictionarySquadra[$squadra].'.xlsx';

header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');

$writer->save('php://output');
exit;
?>
