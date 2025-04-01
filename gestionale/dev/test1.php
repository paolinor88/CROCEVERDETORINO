<?php

include "../config/pdo.php";

// Recuperare l'elenco delle guardie dalla tabella
$query = $connect->query("SELECT DataGuardia, Squadra FROM guardie");
$existingGuardies = $query->fetchAll(PDO::FETCH_ASSOC);

$lastGuardies = [];
foreach ($existingGuardies as $guardia) {
    $lastGuardies[$guardia['Squadra']] = $guardia['DataGuardia'];
}

// Creare un elenco delle domeniche e dei giorni festivi dell'anno
if(isset($_POST["submit"])){
    $year = $_POST["anno"];
}
$dates = [];
for ($i = 1; $i <= 12; $i++) {
    $numDays = cal_days_in_month(CAL_GREGORIAN, $i, $year);
    for ($j = 1; $j <= $numDays; $j++) {
        $currentDate = "$year-$i-$j";
        if (date('N', strtotime($currentDate)) == 7) { // domeniche
            $dates[] = $currentDate;
        }
    }
}
// Qui aggiungi i tuoi giorni festivi. Ho aggiunto solo il 1 gennaio come esempio.
$dates[] = "$year-01-01";

sort($dates);
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
// Assegnare una guardia a ogni domenica e giorno festivo
$assignedGuardies = [];
foreach ($dates as $date) {
    for ($squadra = 1; $squadra <= 9; $squadra++) {
        $daysSinceLastGuardia = isset($lastGuardies[$squadra]) ? (strtotime($date) - strtotime($lastGuardies[$squadra])) / (60*60*24) : PHP_INT_MAX;
        if ($daysSinceLastGuardia > 2 && !in_array($squadra, $assignedGuardies)) {
            $lastGuardies[$squadra] = $date;
            $assignedGuardies[$date] = $squadra;
            break;
        }
    }
}

// Inserire questi nuovi turni nella tabella
foreach ($assignedGuardies as $date => $squadra) {
    $pdo->prepare("INSERT INTO guardie (DataGuardia, Squadra) VALUES (?, ?)")->execute([$date, $squadra]);
}

echo "Guardie assegnate con successo!";
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