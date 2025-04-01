<?php
session_start();
include "../config/config.php";
global $db;

if (!isset($_SESSION['discente_id'])) {
    echo json_encode(["success" => false, "error" => "Utente non autenticato"]);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);
$discente_id = $_SESSION['discente_id'];
$id_lezione = $data['id_lezione'];
$id_corso = $data['id_corso'];
$tempo_visionato = $data['tempo_visionato'];

$stmt = $db->prepare("SELECT tempo_visionato, completata FROM progresso_lezioni WHERE discente_id = ? AND id_lezione = ? AND id_corso = ?");
$stmt->bind_param("iii", $discente_id, $id_lezione, $id_corso);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($tempo_salvato, $completata);
$stmt->fetch();

if ($stmt->num_rows > 0) {
    if ($tempo_visionato > $tempo_salvato) {
        $stmt = $db->prepare("UPDATE progresso_lezioni 
            SET tempo_visionato = ?, completata = IF(? >= (SELECT durata FROM lezioni WHERE id_lezione = ?), 1, completata) 
            WHERE discente_id = ? AND id_lezione = ? AND id_corso = ?");
        $stmt->bind_param("iiiiii", $tempo_visionato, $tempo_visionato, $id_lezione, $discente_id, $id_lezione, $id_corso);
        $stmt->execute();
    }
} else {
    $stmt = $db->prepare("INSERT INTO progresso_lezioni (discente_id, id_lezione, id_corso, tempo_visionato, completata) 
                          VALUES (?, ?, ?, ?, IF(? >= (SELECT durata FROM lezioni WHERE id_lezione = ?), 1, 0))");
    $stmt->bind_param("iiiiii", $discente_id, $id_lezione, $id_corso, $tempo_visionato, $tempo_visionato, $id_lezione);
    $stmt->execute();
}

echo json_encode(["success" => true]);
?>
