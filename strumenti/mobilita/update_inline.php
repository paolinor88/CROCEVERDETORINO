<?php
session_start();
header('Content-Type: application/json');
include "../config/config.php";
global $db;

// Ricevi JSON puro dal body
$input = file_get_contents("php://input");
$data = json_decode($input, true);

if (!isset($data['servizi']) || !is_array($data['servizi'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Dati non validi']);
    exit;
}

$aggiornati = 0;

foreach ($data['servizi'] as $riga) {
    if (!isset($riga['id']) || !is_numeric($riga['id'])) continue;

    $id = (int)$riga['id'];
    $richiedente = trim($riga['richiedente'] ?? '');
    $partenza = trim($riga['partenza'] ?? '');
    $destinazione = trim($riga['destinazione'] ?? '');
    $mezzo = trim($riga['mezzo'] ?? '');
    $equipaggio = trim($riga['equipaggio'] ?? '');

    $sql = "UPDATE mobilita SET Richiedente=?, Partenza=?, Destinazione=?, MezzoAssegnato=?, Equipaggio=? WHERE IDServizio=?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("sssssi", $richiedente, $partenza, $destinazione, $mezzo, $equipaggio, $id);

    if ($stmt->execute()) {
        $aggiornati++;
    }

    $stmt->close();
}

echo json_encode([
    'success' => true,
    'righe_aggiornate' => $aggiornati
]);
