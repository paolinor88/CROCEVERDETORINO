<?php
session_start();
include "../config/config.php";
global $db;

if (!isset($_GET['id_discente']) || !isset($_GET['id_edizione']) || !isset($_GET['id_corso'])) {
    echo json_encode(["success" => false, "message" => "Parametri mancanti"]);
    exit();
}

$id_discente = intval($_GET['id_discente']);
$id_edizione = intval($_GET['id_edizione']);
$id_corso = intval($_GET['id_corso']);

$stmt = $db->prepare("DELETE FROM autorizzazioni_corsi WHERE discente_id = ? AND id_edizione = ?");
$stmt->bind_param("ii", $id_discente, $id_edizione);

if (!$stmt->execute()) {
    echo json_encode(["success" => false, "message" => "Errore nella rimozione"]);
    exit();
}

$stmt = $db->prepare("UPDATE edizioni_corso SET posti_occupati = posti_occupati - 1 WHERE id_edizione = ?");
$stmt->bind_param("i", $id_edizione);
$stmt->execute();

echo json_encode(["success" => true]);
?>