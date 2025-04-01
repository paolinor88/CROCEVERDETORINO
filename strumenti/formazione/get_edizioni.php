<?php
include "../config/config.php";

if (!isset($_GET['id_corso']) || !is_numeric($_GET['id_corso'])) {
    echo json_encode([]);
    exit();
}

$id_corso = intval($_GET['id_corso']);

$query = "SELECT id_edizione, DATE_FORMAT(data_inizio, '%d/%m/%Y') AS data_inizio FROM edizioni_corso WHERE id_corso = ?";
$stmt = $db->prepare($query);
$stmt->bind_param("i", $id_corso);
$stmt->execute();
$result = $stmt->get_result();

$edizioni = [];
while ($row = $result->fetch_assoc()) {
    $edizioni[] = $row;
}

echo json_encode($edizioni);
?>