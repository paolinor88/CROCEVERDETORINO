<?php
session_start();
include "../config/config.php";
global $db;

if (!isset($_SESSION['Livello']) || $_SESSION['Livello'] != 28) {
    echo json_encode(["success" => false, "error" => "Accesso negato"]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["success" => false, "error" => "Metodo non consentito: " . $_SERVER['REQUEST_METHOD']]);
    exit();
}

error_log("POST ricevuto: " . json_encode($_POST));

$id_edizione = isset($_POST['id_edizione']) ? intval($_POST['id_edizione']) : 0;

if ($id_edizione <= 0) {
    echo json_encode(["success" => false, "error" => "ID edizione non valido"]);
    exit();
}

$stmt = $db->prepare("SELECT COUNT(*) AS iscritti FROM autorizzazioni_corsi WHERE id_edizione = ?");
$stmt->bind_param("i", $id_edizione);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($row['iscritti'] > 0) {
    echo json_encode(["success" => false, "error" => "Impossibile eliminare: ci sono iscritti a questa edizione"]);
    exit();
}

$stmt = $db->prepare("DELETE FROM edizioni_corso WHERE id_edizione = ?");
$stmt->bind_param("i", $id_edizione);
if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "error" => "Errore durante l'eliminazione"]);
}

$stmt->close();
?>
