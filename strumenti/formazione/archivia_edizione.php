<?php
session_start();
include "../config/config.php";

// **Controllo accesso livello 28**
if (!isset($_SESSION['Livello']) || $_SESSION['Livello'] != 28) {
    echo json_encode(["success" => false, "error" => "Accesso negato"]);
    exit();
}

$id_edizione = $_POST['id_edizione'] ?? null;
if (!$id_edizione) {
    echo json_encode(["success" => false, "error" => "ID edizione mancante"]);
    exit();
}

$stmt = $db->prepare("SELECT archiviata FROM edizioni_corso WHERE id_edizione = ?");
$stmt->bind_param("i", $id_edizione);
$stmt->execute();
$result = $stmt->get_result();
$edizione = $result->fetch_assoc();

if (!$edizione) {
    echo json_encode(["success" => false, "error" => "Edizione non trovata"]);
    exit();
}

$nuovo_stato = ($edizione['archiviata'] == 0) ? 1 : 0;

$stmt = $db->prepare("UPDATE edizioni_corso SET archiviata = ?, posti_occupati=0 WHERE id_edizione = ?");
$stmt->bind_param("ii", $nuovo_stato, $id_edizione);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    if ($nuovo_stato == 1) {
        // **Se l'edizione viene archiviata, revoca le autorizzazioni**
        $stmt = $db->prepare("DELETE FROM autorizzazioni_corsi WHERE id_edizione = ?");
        $stmt->bind_param("i", $id_edizione);
        $stmt->execute();
    }

    echo json_encode(["success" => true, "archiviata" => $nuovo_stato]);
} else {
    echo json_encode(["success" => false, "error" => "Errore nell'aggiornamento"]);
}
?>
