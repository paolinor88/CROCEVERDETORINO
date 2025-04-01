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

if ($id_corso <= 0) {
    echo json_encode(["success" => false, "error" => "ID corso non valido"]);
    exit();
}

$stmt = $db->prepare("
    SELECT COUNT(ac.id) AS iscritti 
    FROM autorizzazioni_corsi ac
    JOIN edizioni_corso ec ON ac.id_edizione = ec.id_edizione
    WHERE ec.id_corso = ?
");
$stmt->bind_param("i", $id_corso);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$iscritti = $row['iscritti'];

if ($iscritti > 0) {
    echo json_encode(["success" => false, "error" => "Il corso ha iscritti attivi. Devi rimuoverli prima di eliminare."]);
    exit();
}

$stmt = $db->prepare("DELETE FROM edizioni_corso WHERE id_corso = ?");
$stmt->bind_param("i", $id_corso);
$stmt->execute();

$stmt = $db->prepare("DELETE FROM corsi WHERE id_corso = ?");
$stmt->bind_param("i", $id_corso);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "error" => "Errore durante l'eliminazione del corso"]);
}

$stmt->close();
?>
