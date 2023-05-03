<?php
if(isset($_POST["submit"])){
    $anno = $_POST["anno"];
    $squadrauno = $_POST["squadrauno"];
}

$squadre = array(1, 2, 3, 4, 5, 6, 7, 8, 9);

$giorni_anno = range(strtotime($anno.'-01-01'), strtotime($anno.'-12-31'), 86400);

$turni_notturni = array();
$turni_diurni = array();
$turni_fest = array();

$numero_squadra = $squadrauno;
foreach ($giorni_anno as $giorno) {
    $turni_notturni[date('d-m-Y', $giorno)] = $numero_squadra;
    $numero_squadra++;
    if ($numero_squadra > 9) {
        $numero_squadra = 1;
    }
}

foreach ($giorni_anno as $giorno) {
    $data = date('d-m-Y', $giorno);
    $patrono = strtotime('24-06-'.$anno);
    $patronox =date('d-m-Y', $patrono);
    $patrono_weekday = date('N', $patrono);
    if (in_array($data, festivita_anno())) {
        if(date('N', $giorno) == 7){
            continue;
        }
        $squadra_min_turni = null;
        $min_turni = PHP_INT_MAX;
        foreach ($squadre as $squadra) {
            $num_turni = count_turni_diurni($turni_diurni, $squadra);
            if ($num_turni < $min_turni && verifica_riposo_diurno($turni_notturni, $turni_diurni, $squadra, $data)) {
                $squadra_min_turni = $squadra;
                $min_turni = $num_turni;
            }
        }
        $turni_diurni[$data] = $squadra_min_turni;
    }
    if (date('N', $giorno) == 7) {
        $squadra_min_turni = null;
        $min_turni = PHP_INT_MAX;
        foreach ($squadre as $squadra) {
            $num_turni = count_turni_diurni($turni_diurni, $squadra);
            if ($num_turni < $min_turni && verifica_riposo_diurno($turni_notturni, $turni_diurni, $squadra, $data)) {
                $squadra_min_turni = $squadra;
                $min_turni = $num_turni;
            }
        }
        $turni_diurni[$data] = $squadra_min_turni;
    }
    if (date('N', $patrono) == 2 && in_array($data, festivita_anno()) ){
        $turni_diurni[$patronox] = "SAB";
    }
    if (date('N', $patrono) ==6 && in_array($data, festivita_anno()) ){
        $turni_diurni[$patronox] = "SAB";
    }
}

function festivita_anno() {
    global $anno;
    return array(
        '01-01-'.$anno,
        '06-01-'.$anno,
        '25-04-'.$anno,
        '01-05-'.$anno,
        '02-06-'.$anno,
        '15-08-'.$anno,
        '01-11-'.$anno,
        '08-12-'.$anno,
        '25-12-'.$anno,
        '26-12-'.$anno
    );
}

function verifica_riposo_diurno($turni_notturni, $turni_diurni, $squadra, $data) {
    $giorno_prima = date('d-m-Y', strtotime($data.' -1 day'));
    return $turni_notturni[$giorno_prima] != $squadra && (empty($turni_diurni[$giorno_prima]) || $turni_diurni[$giorno_prima] != $squadra);
}

function count_turni_diurni($turni_diurni, $squadra) {
    $num_turni = 0;
    foreach ($turni_diurni as $turno) {
        if ($turno == $squadra) {
            $num_turni++;
        }
    }
    return $num_turni;
}

?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <meta name="author" content="Paolo Randone">
    <title>Guardie</title>

    <style>
        table, th, td {
            border: 1px solid black;
        }
        table.center {
            margin-left: auto;
            margin-right: auto;
        }
        td.festivi {
            background-color: #ffc107;
        }
    </style>

</head>
<body style="font-family: Arial,serif">
<form action="creaguardie.php" method="post">
    <table class="center">
        <tr>
            <th>
                ANNO
            </th>
            <th>
                <input type="text" name="anno" required>
            </th>
        </tr>
        <tr>
            <th>
                SQ. INIZIALE
            </th>
            <th>
                <input type="text" name="squadrauno" required>
            </th>
        </tr>
    </table>
    <br>
    <div style="text-align: center">
        <input type="submit" name="submit">
    </div>
</form>
<hr>
<?
if(isset($anno)){
    setlocale(LC_TIME, 'it_IT.utf8');
    // Stampa il totale dei turni diurni per ciascuna squadra
    echo '<h3>ANNO '.$anno.'</h3>';
    echo '<p><b>Totale turni festivi per squadra:</b></p>';
    echo '<ul>';
    foreach ($squadre as $squadra) {
        echo '<li>SQ. ' . $squadra . ': ' . count_turni_diurni($turni_diurni, $squadra) . '</li>';
    }
    $patronolettere = strtotime($patronox);
    $etichettasg = strftime("%A", $patronolettere);
    echo '</ul>';
    echo '<p><b>San Giovanni Patrono: '. $etichettasg .' '.$patronox.' (SQ. '.$turni_diurni[$patronox].')</b></p>';
    // Stampa il calendario dei turni in una tabella HTML
    echo '<table>';
    echo '<thead>';
    echo '<tr>';
    echo '<th>Data</th>';
    echo '<th>Notturno</th>';
    echo '<th>Festivo</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';
    foreach ($giorni_anno as $giorno) {
        $data = date('d-m-Y', $giorno);
        $datalettere = strtotime($data);
        $giornolettere = strftime("%a", $datalettere);
        echo '<tr>';
        echo '<td>' . $data . ' - ' .$giornolettere .'</td>';
        echo '<td style="text-align: center">SQ. ' . $turni_notturni[$data] . '</td>';
        if (date('N', $giorno) == 7 || in_array($data, festivita_anno())) {
            echo '<td style="text-align: center" class="festivi">SQ. ' . $turni_diurni[$data] . '</td>';
        } else {
            echo '<td></td>';
        }
        echo '</tr>';
    }
    echo '</tbody>';
    echo '</table>';


}
?>
</body>
</html>