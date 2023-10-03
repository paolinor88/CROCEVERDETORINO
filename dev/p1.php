<?php

function addShift(&$squad, $date) {
    // verifica se ci sono stati turni negli ultimi due giorni
    $lastShiftDate = end($squad);
    if ($lastShiftDate) {
        $diff = date_diff(date_create($lastShiftDate), date_create($date))->d;
        if ($diff <= 2) {
            return false;
        }
    }
    $squad[] = $date;
    return true;
}

// Creare un array di nove squadre
$squads = array();
for ($i = 1; $i <= 10; $i++) {
    $squads[$i] = array(
        'night_shifts' => array(),
        'holiday_shifts' => array(),
        'sunday_shifts' => array()
    );
}

// Creare i turni notturni
$day = new DateTime('2024-01-01');
$end = new DateTime('2025-01-01');
$squadIndex = 1;

while($day < $end) {
    if (addShift($squads[$squadIndex]['night_shifts'], $day->format('Y-m-d'))) {
        $squadIndex++;
        if($squadIndex > 9) {
            $squadIndex = 1;
        }
    }
    $day->modify('+1 day');
}

// Creare i turni festivi
// Elenchiamo i giorni festivi in Italia
$holidays = array(
    '2024-01-01', '2024-04-01', '2024-04-25', '2024-05-01', '2024-06-02', '2024-08-15',
    '2024-11-01', '2024-12-08', '2024-12-25', '2024-12-26', '2024-06-24'
);
$squadIndex = 1;

foreach ($holidays as $holiday) {
    $dayOfWeek = date('w', strtotime($holiday));

    if($dayOfWeek == 6 || ($holiday == '2024-06-24' && ($dayOfWeek == 3 || $dayOfWeek == 4))) {
        addShift($squads[10]['holiday_shifts'], $holiday);
    } else {
        if (addShift($squads[$squadIndex]['holiday_shifts'], $holiday)) {
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
    if (addShift($squads[$squadIndex]['sunday_shifts'], $day->format('Y-m-d'))) {
        $squadIndex++;
        if($squadIndex > 9) {
            $squadIndex = 1;
        }
    }
    $day->modify('+7 day');
}

// Stampa la tabella
echo "<table>";
echo "<tr><th>Squadra</th><th>Turni notturni</th><th>Turni festivi</th><th>Turni domenicali</th></tr>";

foreach ($squads as $squad => $shifts) {
    echo "<tr>";
    echo "<td>Squadra $squad</td>";
    echo "<td>" . implode(', ', $shifts['night_shifts']) . "</td>";
    echo "<td>" . implode(', ', $shifts['holiday_shifts']) . "</td>";
    echo "<td>" . implode(', ', $shifts['sunday_shifts']) . "</td>";
    echo "</tr>";
}
echo "</table>";
?>
