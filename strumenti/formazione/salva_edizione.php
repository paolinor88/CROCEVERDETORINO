<?php
session_start();
include "../config/config.php";
global $db;

if (!isset($_SESSION['Livello']) || $_SESSION['Livello'] != 28) {
    echo json_encode(["success" => false, "error" => "Accesso negato"]);
    exit();
}

$id_edizione = $_POST['id_edizione'] ?? 0;
$id_corso = $_POST['id_corso'] ?? 0;
$data_inizio = $_POST['data_inizio'] ?? '';
$orario_inizio = $_POST['orario_inizio'] ?? '';
$posti_disponibili = $_POST['posti_disponibili'] ?? 0;

if (empty($id_corso) || empty($data_inizio) || empty($orario_inizio) || $posti_disponibili <= 0) {
    echo json_encode(["success" => false, "error" => "Tutti i campi sono obbligatori"]);
    exit();
}

$data_inizio = date('Y-m-d', strtotime($data_inizio));

$orario_inizio = date('H:i', strtotime($orario_inizio));

if ($id_edizione > 0) {
    $stmt = $db->prepare("UPDATE edizioni_corso SET data_inizio = ?, orario_inizio = ?, posti_disponibili = ? WHERE id_edizione = ?");
    $stmt->bind_param("ssii", $data_inizio, $orario_inizio, $posti_disponibili, $id_edizione);
} else {
    $stmt = $db->prepare("INSERT INTO edizioni_corso (id_corso, data_inizio, orario_inizio, posti_disponibili) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("issi", $id_corso, $data_inizio, $orario_inizio, $posti_disponibili);
}

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "error" => "Errore durante il salvataggio"]);
}

$stmt->close();
?>
