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

try {
    $startDate = new DateTime($_POST['start'], new DateTimeZone("Europe/Rome"));
    $startHour = (int) $startDate->format('H'); // Ora (0-23)
    $startMinute = (int) $startDate->format('i'); // Minuti (0-59)
} catch (Exception $e) {
    logNonConformi("Errore nella creazione di StartDate: " . $e->getMessage() . " | Dati ricevuti: " . json_encode($_POST));
    http_response_code(400);
    echo json_encode(['error' => 'Formato data non valido o problema interno.']);
    exit;
}

// Debug per verificare StartDate
file_put_contents(
    'debug_log.txt',
    "StartDate interpretato: " . ($startDate ? $startDate->format('Y-m-d H:i:s') : 'NULL') .
    " | StartHour: " . ($startDate ? $startHour : 'NULL') .
    " | StartMinute: " . ($startDate ? $startMinute : 'NULL') . "\n",
    FILE_APPEND
);


// Ora corrente del server
$currentHour = (int) date('H'); // Ora attuale (0-23)
$currentMinute = (int) date('i'); // Minuti attuali (0-59)

// Blocco tra le 23:59 e le 03:00 italiane
if (($currentHour === 23 && $currentMinute >= 59) || ($currentHour >= 0 && $currentHour < 3)) {
    logNonConformi("Tentativo di inserimento effettuato in fascia bloccata (23:59-03:00): " . json_encode($_POST));
    http_response_code(403);
    echo json_encode(['error' => 'Inserimenti non consentiti tra le 23:59 e le 03:00 italiane.']);
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
    "Fuso orario del server: " . date_default_timezone_get() . "\n" .
    "Dati ricevuti dal client: " . json_encode($_POST) . "\n" .
    "StartDate interpretato: {$startDate->format('Y-m-d H:i:s')} (Europe/Rome) | StartHour: $startHour | StartMinute: $startMinute\n",
    FILE_APPEND
);

