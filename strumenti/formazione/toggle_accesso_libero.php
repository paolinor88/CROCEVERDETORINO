<?php
session_start();
include "../config/config.php";

if (!isset($_SESSION['Livello']) || $_SESSION['Livello'] != 28) {
    echo json_encode(["success" => false, "error" => "Accesso negato"]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["success" => false, "error" => "Metodo non consentito"]);
    exit();
}

$id_corso = $_POST['id_corso'] ?? null;
$accesso_libero = isset($_POST['accesso_libero']) ? intval($_POST['accesso_libero']) : null;

if (!$id_corso || $accesso_libero === null) {
    echo json_encode(["success" => false, "error" => "Parametri mancanti"]);
    exit();
}

$stmt = $db->prepare("UPDATE corsi SET accesso_libero = ? WHERE id_corso = ?");
$stmt->bind_param("ii", $accesso_libero, $id_corso);
$success = $stmt->execute();

if ($success) {
    if ($stmt->affected_rows > 0) {
        echo json_encode(["success" => true, "accesso_libero" => $accesso_libero]);
    } else {
        echo json_encode(["success" => true, "message" => "Lo stato era già impostato così."]);
    }
} else {
    echo json_encode(["success" => false, "error" => "Errore durante l'aggiornamento"]);
}
?>
