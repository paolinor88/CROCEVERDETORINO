<?php

function addShift(&$shifts, $date, $squad) {
    // verifica se ci sono stati turni negli ultimi due giorni
    $twoDaysAgo = (new DateTime($date))->modify('-2 days')->format('Y-m-d');
    foreach($shifts as $shiftDate => $shiftData) {
        if($shiftDate > $twoDaysAgo && isset($shiftData[$squad])) {
            return false;
        }
    }
    if (!isset($shifts[$date])) {
        $shifts[$date] = [];
    }
    $shifts[$date][$squad] = true;
    return true;
}

// Creare l'array che memorizza i turni
$shifts = array();

// Creare i turni notturni
$day = new DateTime('2024-01-01');
$end = new DateTime('2025-01-01');
$squadIndex = 1;

while($day < $end) {
    if (addShift($shifts, $day->format('Y-m-d'), "Squadra $squadIndex Notturno")) {
        $squadIndex++;
        if($squadIndex > 9) {
            $squadIndex = 1;
        }
    }
    $day->modify('+1 day');
}

// Creare i turni festivi
$holidays = array(
    '2024-01-01', '2024-04-01', '2024-04-25', '2024-05-01', '2024-06-02', '2024-08-15',
    '2024-11-01', '2024-12-08', '2024-12-25', '2024-12-26', '2024-06-24'
);
$squadIndex = 1;

foreach ($holidays as $holiday) {
    $dayOfWeek = date('w', strtotime($holiday));

    if($dayOfWeek == 6 || ($holiday == '2024-06-24' && ($dayOfWeek == 3 || $dayOfWeek == 4))) {
        addShift($shifts, $holiday, "Squadra 10 Festivo");
    } else {
        if (addShift($shifts, $holiday, "Squadra $squadIndex Festivo")) {
            $squadIndex++;
            if($squadIndex > 9) {
                $squadIndex = 1;
            }
        }
    }
}

// Creare i turni domenicali
$day = new DateTime('2024-01-07');
$squadIndex = 1;

while($day < $end) {
    if (addShift($shifts, $day->format('Y-m-d'), "Squadra $squadIndex Domenicale")) {
        $squadIndex++;
        if($squadIndex > 9) {
            $squadIndex = 1;
        }
    }
    $day->modify('+7 day');
}

// Stampa la tabella
$weekdays = array('Domenica', 'Lunedì', 'Martedì', 'Mercoledì', 'Giovedì', 'Venerdì', 'Sabato');

echo "<table>";
echo "<tr><th>Data</th><th>Giorno della settimana</th><th>Turno notturno</th><th>Turno festivo</th><th>Turno domenicale</th></tr>";

ksort($shifts);
foreach ($shifts as $date => $shiftData) {
    echo "<tr>";
    echo "<td>$date</td>";
    echo "<td>" . $weekdays[date('w', strtotime($date))] . "</td>";
    echo "<td>" . (isset($shiftData['Squadra Notturno']) ? "Squadra " . $shiftData['Squadra Notturno'] : "") . "</td>";
    echo "<td>" . (isset($shiftData['Squadra Festivo']) ? "Squadra " . $shiftData['Squadra Festivo'] : "") . "</td>";
    echo "<td>" . (isset($shiftData['Squadra Domenicale']) ? "Squadra " . $shiftData['Squadra Domenicale'] : "") . "</td>";
    echo "</tr>";
}

echo "</table>";
?>
