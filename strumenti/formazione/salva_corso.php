<?php
session_start();
include "../config/config.php";
global $db;

if (!isset($_SESSION['Livello']) || $_SESSION['Livello'] != 28) {
    echo json_encode(["success" => false, "error" => "Accesso negato"]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["success" => false, "error" => "Metodo non consentito"]);
    exit();
}

$id_corso = isset($_POST['id_corso']) ? intval($_POST['id_corso']) : 0;
$titolo = trim($_POST['titolo'] ?? '');
$descrizione = trim($_POST['descrizione'] ?? ''); // Nuovo campo
$note = trim($_POST['note'] ?? '');
$accesso_libero = isset($_POST['accesso_libero']) ? 1 : 0;

if (empty($titolo)) {
    echo json_encode(["success" => false, "error" => "Il titolo del corso è obbligatorio"]);
    exit();
}

if ($id_corso > 0) {

    $stmt = $db->prepare("UPDATE corsi SET titolo = ?, descrizione = ?, note = ?, accesso_libero = ? WHERE id_corso = ?");
    $stmt->bind_param("sssii", $titolo, $descrizione, $note, $accesso_libero, $id_corso);
} else {

    $stmt = $db->prepare("INSERT INTO corsi (titolo, descrizione, note, accesso_libero) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssi", $titolo, $descrizione, $note, $accesso_libero);
}

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "error" => "Errore nel salvataggio del corso"]);
}

$stmt->close();
?>
