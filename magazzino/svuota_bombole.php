<?php
header('Content-Type: application/json');
include "../config/pdo.php";
session_start();

if ($_SESSION["livello"] < 4) {
    echo json_encode(["success" => false, "message" => "Permessi insufficienti."]);
    exit;
}

try {
    // Inizio transazione per assicurare che tutte le operazioni avvengano insieme
    $connect->beginTransaction();

    // Aggiorna StatoBombola a 2 per tutte le bombole con Destinazione='VUOTO' in magazzino
    $stmt = $connect->prepare("
        UPDATE o_inventario AS i
        JOIN ossigeno AS o ON i.IDBombola = o.IDBombola
        SET i.StatoBombola = 2
        WHERE o.Destinazione = 'VUOTO' AND o.StatoMovimento=1
    ");
    $stmt->execute();

    // Ottieni gli IDBombola aggiornati
    $idBomboleStmt = $connect->prepare("
        SELECT i.IDBombola FROM o_inventario AS i
        JOIN ossigeno AS o ON i.IDBombola = o.IDBombola
        WHERE o.Destinazione = 'VUOTO'
    ");
    $idBomboleStmt->execute();
    $idBombole = $idBomboleStmt->fetchAll(PDO::FETCH_COLUMN);

    // Per ogni IDBombola, aggiungi un nuovo record in ossigeno e aggiorna i record precedenti
    foreach ($idBombole as $idBombola) {
        // Inserisci un nuovo record in ossigeno
        $insertStmt = $connect->prepare("
            INSERT INTO ossigeno (IDBombola, TipoMovimento, Destinazione, StatoMovimento)
            VALUES (:IDBombola, 'RITIRO', 'SOL', 1)
        ");
        $insertStmt->bindParam(':IDBombola', $idBombola);
        $insertStmt->execute();

        // Aggiorna StatoMovimento a 2 per i record esistenti con lo stesso IDBombola
        $updateStmt = $connect->prepare("
            UPDATE ossigeno
            SET StatoMovimento = 2
            WHERE IDBombola = :IDBombola AND StatoMovimento = 1
        ");
        $updateStmt->bindParam(':IDBombola', $idBombola);
        $updateStmt->execute();
    }

    // Conferma la transazione
    $connect->commit();

    echo json_encode(["success" => true]);
} catch (PDOException $e) {
    // Annulla la transazione in caso di errore
    $connect->rollBack();
    echo json_encode(["success" => false, "message" => "Errore nel database: " . $e->getMessage()]);
}
?>
