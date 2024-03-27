<?php
/**
 *
 * @author     Paolo Randone
 * @author     <paolo.randone@croceverde.org>
* @version    7.3
 * @note       Powered for Croce Verde Torino. All rights reserved
 *
 */
if(isset($_POST["submit"])){
    $anno = $_POST["anno"];
    $squadrauno = $_POST["squadrauno"];
    $squadra_1gennaio = $_POST["squadra_1gennaio"];
}

// Definisci le squadre
$squadre = array(1, 2, 3, 4, 5, 6, 7, 8, 9);

// Crea un array di tutti i giorni dell'anno
$giorni_anno = range(strtotime($anno.'-01-01'), strtotime($anno.'-12-31'), 86400);

// Inizializza gli array dei turni notturni e festivi
$turni_notturni = array();
$turni_diurni = array();
$turni_fest = array();

// ciclo turni notturni
$numero_squadra = $squadrauno;
foreach ($giorni_anno as $giorno) {
    $turni_notturni[date('d-m-Y', $giorno)] = $numero_squadra;
    $numero_squadra++;
    if ($numero_squadra > 9) {
        $numero_squadra = 1;
    }
}

// Assegna festivi
foreach ($giorni_anno as $giorno) {
    $data = date('d-m-Y', $giorno);
    $data_weekday = date('N', $giorno);
    $patrono = strtotime('24-06-'.$anno);
    $patronox =date('d-m-Y', $patrono);
    $patrono_weekday = date('N', $patrono);

    if ($data == $anno . '-01-01') {
        // Assegna la squadra del primo gennaio diurno
        $turni_diurni[$data] = $squadra_1gennaio;
    } elseif ($data_weekday == 7 || in_array($data, festivita_anno())) {
        // Verifica quale squadra ha il minor numero di turni diurni e almeno due giorni di riposo
        $squadra_min_turni = null;
        $min_turni = PHP_INT_MAX;

        foreach ($squadre as $squadra) {
            $num_turni = count_turni_diurni($turni_diurni, $squadra);

            if ($num_turni < $min_turni && verifica_riposo_diurno($turni_notturni, $turni_diurni, $squadra, $data)) {
                $squadra_min_turni = $squadra;
                $min_turni = $num_turni;
            }
        }

        // Assegna festivo alla prima squadra utile che soddisfa i requisiti
        $turni_diurni[$data] = $squadra_min_turni;
    }

    if (date('N', $giorno) == 7 || in_array($data, festivita_anno()) ) {
        // Verifica quale squadra ha il minor numero di turni diurni e almeno due giorni di riposo
        $squadra_min_turni = null;
        $min_turni = PHP_INT_MAX;
        foreach ($squadre as $squadra) {
            $num_turni = count_turni_diurni($turni_diurni, $squadra);
            if ($num_turni < $min_turni && verifica_riposo_diurno($turni_notturni, $turni_diurni, $squadra, $data)) {
                $squadra_min_turni = $squadra;
                $min_turni = $num_turni;
            }
        }
        // Assegna festivo alla prima sq utile che soddisfa i requisiti
        $turni_diurni[$data] = $squadra_min_turni;
    }
    //se patrono è martedì assegna a sq sabato
    if (date('N', $patrono) == 2 && in_array($data, festivita_anno()) ){
        $turni_diurni[$patronox] = "SAB";
    }
    //se patrono è mercoledì assegna a sabato
    if (date('N', $patrono) == 3 && in_array($data, festivita_anno()) ){
        $turni_diurni[$patronox] = "SAB";
    }
    //se un festivo è di sabato assegna a sabato
    if (date('N', $giorno) == 6 && in_array($data, festivita_anno()) ){
        $turni_diurni[$data] = "SAB";
    }
}

// Funzione che restituisce un array di tutte le festività delll'anno
function festivita_anno() {
    //dataentry anno
    $anno = $_POST["anno"];

    //Definisci pasquetta
    $pasqua = easter_date($anno);
    $pasquetta = strtotime("+1 day", $pasqua);

    //definisci array festivi
    $festivita = array(
        '01-01-'.$anno, // Capodanno
        '06-01-'.$anno, // Epifania
        date('d-m-Y', $pasquetta), // Pasquetta
        '25-04-'.$anno, // Liberazione
        '01-05-'.$anno, // Lavoratori
        '02-06-'.$anno, // Repubblica
        '24-06-'.$anno, // Patrono
        '15-08-'.$anno, // Ferragosto
        '01-11-'.$anno, // Santi
        '08-12-'.$anno, // Immacolata
        '25-12-'.$anno, // Natale
        '26-12-'.$anno, // Santo Stefano
    );

    //ritorna array per assegnazione
    return array_values($festivita);
}

//conteggia festivi assegnati
function count_turni_diurni($turni_diurni, $squadra) {
    $count = 0;
    foreach ($turni_diurni as $data => $num_squadra) {
        if ($num_squadra == $squadra) {
            $count++;
        }
    }
    return $count;
}

// verifica due turni di riposo
function verifica_riposo_diurno($turni_notturni, $turni_diurni, $squadra, $data_diurno) {
    $riposo_minimo = strtotime('-2 days', strtotime($data_diurno));
    $riposo_massimo = strtotime('+2 days', strtotime($data_diurno));
    foreach ($turni_notturni as $data => $num_squadra) {
        if ($num_squadra == $squadra && strtotime($data) >= $riposo_minimo && strtotime($data) <= $riposo_massimo) {
            return false;
        }
    }
    foreach ($turni_diurni as $data => $num_squadra) {
        if ($num_squadra == $squadra && strtotime($data) >= $riposo_minimo && strtotime($data) <= $riposo_massimo && $data != $data_diurno) {
            return false;
        }
    }
    return true;
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
<form action="p2.php" method="post">
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
                SQ. NOTTURNA INIZIALE
            </th>
            <th>
                <input type="text" name="squadrauno" required>
            </th>
        </tr>
        <tr>
            <th>
                SQ. CAPODANNO DIURNO
            </th>
            <th>
                <input type="text" name="squadra_1gennaio" required>
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