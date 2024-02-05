<?php

function getSquadraPerData($data) {
    // Calcola il numero totale di giorni trascorsi dalla data di inizio
    $dataInizio = new DateTime('2024-01-01');
    $dataInput = new DateTime($data);

    $intervallo = $dataInizio->diff($dataInput);
    $giorniTrascorsi = $intervallo->days;

    // Determina quale squadra (da 1 a 9) è associata a quella data
    $squadra = ($giorniTrascorsi % 9) + 1;

    return $squadra;
}

// Esempio d'uso
$inizio = '2024-01-01';
$fine = '2034-01-01';

$dataCorrente = new DateTime($inizio);
$dataFine = new DateTime($fine);

// Inizio tabella
echo "<table border='1'>";
echo "<tr><th>DataGuardia</th><th>Squadra</th></tr>";

while ($dataCorrente <= $dataFine) {
    $squadra = getSquadraPerData($dataCorrente->format('Y-m-d'));

    // Stampa riga tabella
    echo "<tr>";
    echo "<td>" . $dataCorrente->format('Y-m-d') . "</td>"; // Formato DATE di MySQL
    echo "<td>" . $squadra . "</td>";
    echo "</tr>";

    $dataCorrente->modify('+1 day');
}

// Fine tabella
echo "</table>";

?>
