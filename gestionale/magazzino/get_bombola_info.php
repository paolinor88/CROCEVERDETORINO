<?php
header('Content-Type: application/json');
include "../config/pdo.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['IDBombola'])) {
    $IDBombola = trim($_POST['IDBombola']);

    try {
        $stmt = $connect->prepare("SELECT o.IDBombola, o.TipoMovimento, o.Destinazione, o.StatoMovimento, 
                                          i.TipoBombola, i.StatoBombola
                                   FROM ossigeno o
                                   LEFT JOIN o_inventario i ON o.IDBombola = i.IDBombola
                                   WHERE o.IDBombola = :IDBombola
                                   ORDER BY o.IDMovimento DESC LIMIT 1");
        $stmt->bindParam(':IDBombola', $IDBombola);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'data' => $result]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Nessun record trovato.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Errore del database: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Richiesta non valida.']);
}
