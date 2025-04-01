<?php
include "../config/config.php";

if(isset($_GET['IDEvento'])) {
    $IDEvento = $_GET['IDEvento'];
} else {
    $IDEvento = 1;
}
$dictionaryPatologia = array (
    1 => "MEDICO",
    2 => "TRAUMA",
);

$query = "SELECT * FROM interventi WHERE IDEvento = $IDEvento ORDER BY ID_INTERVENTO";
$result = $db->query($query);

$output = '';
while ($ciclo = $result->fetch_array()) {
    $buttonClass = 'btn-outline-dark';
    if ($ciclo['CODICEGRAVITA'] == 1) {
        $buttonClass = 'btn-success';
    } elseif ($ciclo['CODICEGRAVITA'] == 2) {
        $buttonClass = 'btn-warning';
    } elseif ($ciclo['CODICEGRAVITA'] == 3) {
        $buttonClass = 'btn-danger';
    }
    $output .= '<tr>';
    $output .= '<td class="align-middle"><a class="btn btn-sm ' . $buttonClass . '" href="schedaintervento.php?ID_INTERVENTO=' . $ciclo['ID_INTERVENTO'] . '">' . $ciclo['ID_INTERVENTO'] . '</a></td>';
    $output .= '<td class="align-middle">' . date("H:i", strtotime($ciclo['ORAINIZIO'])) . '</td>';
    $output .= '<td class="align-middle">' . $ciclo['COGNOME'] . ' ' . $ciclo['NOME'] . '</td>';
    $output .= '<td class="align-middle">' . $ciclo['POSTAZIONE'] . '</td>';
    $output .= '<td class="align-middle">' . $dictionaryPatologia[$ciclo['CODICEPATOLOGIA']] . '</td>';
    $output .= '<td class="align-middle">' . $ciclo['ESITO'] . '</td>';
    $output .= '<td class="align-middle">' . $ciclo['NOTE'] . '</td>';
    $output .= '<td class="align-middle">';
    switch ($ciclo['STATO']) {
        case "1":
            $output .= '<i class="fas fa-stethoscope"></i>';
            break;
        case "2":
            $output .= '<i class="fas fa-stethoscope"></i> <i class="fas fa-ambulance"></i>';
            break;
        case "3":
            $output .= '<i class="fas fa-stethoscope"></i> <i class="fas fa-times"></i>';
            break;
        case "4":
            $output .= '<i class="fas fa-stethoscope"></i> <i class="fas fa-sign-out-alt"></i>';
            break;
    }
    $output .= '</td>';
    $oraFine = $ciclo['ORAFINE'] ? date("H:i", strtotime($ciclo['ORAFINE'])) : '';
    $output .= '<td class="align-middle">' . $oraFine . '</td>';
    $output .= '</tr>';
}

echo $output;
?>
