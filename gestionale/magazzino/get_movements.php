<?php
header('Access-Control-Allow-Origin: *');
include "../config/pdo.php";

if (isset($_GET['IDBombola'])) {
    $IDBombola = $_GET['IDBombola'];

    $stmt = $connect->prepare("SELECT TipoMovimento, Destinazione, Timestamp FROM ossigeno WHERE IDBombola = :IDBombola ORDER BY IDMovimento DESC");
    $stmt->bindParam(':IDBombola', $IDBombola);
    $stmt->execute();

    $movements = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($movements) {
        echo "<table class='table table-sm'>";
        echo "<thead><tr><th>Tipo</th><th>Destinazione</th><th>Tempo</th></tr></thead><tbody>";
        foreach ($movements as $movement) {
            echo "<tr><td>" . $movement['TipoMovimento'] . "</td><td>" . $movement['Destinazione'] . "</td><td>" . $movement['Timestamp'] . "</td></tr>";
        }
        echo "</tbody></table>";
    } else {
        echo "<p>Nessun movimento trovato per questa bombola.</p>";
    }
} else {
    echo "<p>Errore: IDBombola non fornito.</p>";
}
?>
