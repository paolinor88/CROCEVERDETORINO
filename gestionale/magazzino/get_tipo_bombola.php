<?php
include "../config/pdo.php";
header('Content-Type: application/json');

$response = ['tipo' => null];

if (isset($_POST['IDBombola'])) {
    $IDBombola = $_POST['IDBombola'];

    $stmt = $connect->prepare("SELECT TipoBombola FROM o_inventario WHERE IDBombola = :IDBombola");
    $stmt->bindParam(':IDBombola', $IDBombola);
    $stmt->execute();

    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($result) {
        $response['tipo'] = $result['TipoBombola'];
    }
}

echo json_encode($response);
