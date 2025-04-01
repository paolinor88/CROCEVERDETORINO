<?php
// Converte l'ora italiana in UTC per i controlli
$italianTimezone = new DateTimeZone("Europe/Rome");
$currentItalianTime = new DateTime("now", $italianTimezone);

// Imposta l'inizio del giorno valido alle 3:00 italiane
$validStart = new DateTime("today 03:00:00", $italianTimezone);
$validEnd = clone $validStart;
$validEnd->modify("+8 days");

// Converti i limiti in timestamp UNIX (UTC)
$validStartTimestamp = $validStart->getTimestamp();
$validEndTimestamp = $validEnd->getTimestamp();

// Ottieni i dati inviati
$startDate = strtotime($_POST['start']); // Data di inizio evento in UTC

// Controllo sui limiti temporali
if ($startDate < $validStartTimestamp || $startDate > $validEndTimestamp) {
    logNonConformi("Tentativo non conforme: " . json_encode($_POST));
    http_response_code(403);
    echo json_encode(['error' => 'Inserimento non valido']);
    exit;
}
