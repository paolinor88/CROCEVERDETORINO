<?php
session_start();
include "../config/config.php";

if (!isset($_GET['id_edizione'])) {
    echo json_encode(["success" => false, "error" => "ID edizione mancante"]);
    exit();
}

$id_edizione = intval($_GET['id_edizione']);

$stmt = $db->prepare("SELECT COUNT(*) AS num_discenti FROM autorizzazioni_corsi WHERE id_edizione = ?");
$stmt->bind_param("i", $id_edizione);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

echo json_encode(["success" => true, "num_discenti" => $row['num_discenti']]);
?>
