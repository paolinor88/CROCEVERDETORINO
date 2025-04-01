<?php
include "../config/config.php";

if (isset($_POST['candidato_id'])) {
    $candidato_id = $_POST['candidato_id'];

    $stmt = $db->prepare("SELECT DataNascita FROM rubrica WHERE IDUtente = ?");
    $stmt->bind_param("i", $candidato_id);
    $stmt->execute();
    $stmt->bind_result($dataNascita);
    $stmt->fetch();
    $stmt->close();

    echo $dataNascita;
}
?>
