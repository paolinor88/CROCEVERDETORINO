<?php
session_start();
include "../config/config.php";
global $db;

if (!isset($_SESSION['discente_id'])) {
    echo json_encode([
        "tempo_visionato" => 0,
        "completata" => 0
    ]);
    exit();
}

$discente_id = $_SESSION['discente_id'];
$id_lezione = $_GET['id_lezione'];

$stmt = $db->prepare("SELECT tempo_visionato, completata FROM progresso_lezioni WHERE discente_id = ? AND id_lezione = ?");
$stmt->bind_param("ii", $discente_id, $id_lezione);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($tempo_visionato, $completata);
$stmt->fetch();

echo json_encode([
    "tempo_visionato" => $tempo_visionato ?? 0,
    "completata" => $completata ?? 0
]);
?>
