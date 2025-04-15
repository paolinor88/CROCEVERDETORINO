<?php
session_start();
include "../config/config.php";

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["success" => false, "error" => "Metodo non consentito"]);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);
$ordine = $data['ordine'] ?? [];

if (!is_array($ordine)) {
    echo json_encode(["success" => false, "error" => "Formato dati non valido"]);
    exit();
}

foreach ($ordine as $index => $id_domanda) {
    $stmt = $db->prepare("UPDATE test_domande SET ordine = ? WHERE id = ?");
    $ordine_val = $index + 1;
    $stmt->bind_param("ii", $ordine_val, $id_domanda);
    $stmt->execute();
}

echo json_encode(["success" => true]);
