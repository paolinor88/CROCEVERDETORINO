<?php

// Avvia la sessione
session_start();

// Includi il file di configurazione per la connessione PDO
include "../config/pdo.php";

// Controlla se la connessione PDO è definita
if (!isset($connect)) {
    die("Errore: connessione al database non trovata. Verifica il file di configurazione.");
}

// Funzione per registrare tentativi non conformi
function logNonConformi($message) {
    $logFile = __DIR__ . '/log_non_conformi.txt'; // Percorso del file di log
    $date = date('Y-m-d H:i:s'); // Data e ora correnti
    $ip = $_SERVER['REMOTE_ADDR']; // Indirizzo IP dell'utente
    $logMessage = "[$date] IP: $ip - $message\n"; // Messaggio di log
    file_put_contents($logFile, $logMessage, FILE_APPEND); // Scrive nel file di log
}

// Imposta il fuso orario del server
date_default_timezone_set("Europe/Rome");

// Ora corrente del server
$currentDateTime = new DateTime("now");

// Imposta i limiti orari (dalle 3:00 italiane di oggi a 7 giorni dopo)
$validStart = new DateTime("today 03:00:00"); // Ora minima valida (3:00 italiane di oggi)

$validEnd = (clone $validStart)->modify("+6 days 23:59:59");

// Ottieni la data inviata
try {
    $startDate = new DateTime($_POST['start'], $italianTimezone);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['error' => 'Formato data non valido.']);
    exit;
}

// Controllo sui limiti temporali
if ($startDate < $validStart || $startDate > $validEnd) {
    logNonConformi("Tentativo non conforme: " . json_encode($_POST));
    http_response_code(403);
    echo json_encode(['error' => 'Inserimento non consentito']);
    exit;
}

// Inserisci i dati nel database
try {
    $query = "INSERT INTO agenda (title, start_event, end_event, user_id) VALUES (:title, :start_event, :end_event, :user_id)";
    $statement = $connect->prepare($query);
    $statement->execute([
        ':title' => $_POST['title'],
        ':start_event' => $_POST['start'], // Deve essere nel formato corretto
        ':end_event' => $_POST['end'],     // Deve essere nel formato corretto
        ':user_id' => $_POST['user_id']
    ]);

    echo json_encode(['success' => 'Inserimento riuscito']);
} catch (PDOException $e) {
    logNonConformi("Errore database: " . $e->getMessage());
    file_put_contents('debug_log.txt', "Query SQL Error: " . $e->getMessage() . "\n", FILE_APPEND);
    http_response_code(500);
    echo json_encode(['error' => 'Errore interno. Riprova più tardi.']);
    exit;
}
file_put_contents(
    'debug_log.txt',
    "Ora corrente del server: " . date('Y-m-d H:i:s') . "\n" .
    "Dati ricevuti dal client: " . json_encode($_POST) . "\n" .
    "StartDate interpretato: {$startDate->format('Y-m-d H:i:s')} | ValidStart: {$validStart->format('Y-m-d H:i:s')} | ValidEnd: {$validEnd->format('Y-m-d H:i:s')}\n" .
    "Risultato controllo: StartDate < ValidStart = " . ($startDate < $validStart ? 'true' : 'false') . " | StartDate > ValidEnd = " . ($startDate > $validEnd ? 'true' : 'false') . "\n",
    FILE_APPEND
);

