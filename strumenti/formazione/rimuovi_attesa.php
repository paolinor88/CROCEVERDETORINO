<?php
session_start();
include "../config/config.php";
header('Content-Type: application/json');

if (!isset($_SESSION['Livello']) || $_SESSION['Livello'] != 28) {
    echo json_encode(["success" => false, "message" => "Accesso non autorizzato"]);
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo json_encode(["success" => false, "message" => "ID non valido"]);
    exit();
}

$id = intval($_GET['id']);

$stmt = $db->prepare("DELETE FROM lista_attesa WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => "Errore durante la rimozione"]);
}
