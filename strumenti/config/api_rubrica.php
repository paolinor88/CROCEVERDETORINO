<?php
header('Content-Type: application/json');

$API_KEY = 'ZEauU4Kh';

if (!isset($_GET['token']) || $_GET['token'] !== $API_KEY) {
    http_response_code(401);
    echo json_encode(['error' => 'Accesso non autorizzato']);
    exit;
}

include "pdo.php";

try {
    $connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Errore di connessione DB']);
    exit;
}

$sql = "SELECT * FROM rubrica";
$stmt = $connect->prepare($sql);
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($data, JSON_UNESCAPED_UNICODE);
