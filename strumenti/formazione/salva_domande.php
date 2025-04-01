<?php
session_start();
include "../config/config.php";

$id_corso = $_POST['id_corso'];
$id_lezione = $_POST['id_lezione'];
$id_domanda = $_POST['id'] ?? null;
$domanda = $_POST['domanda'];
$risposta1 = $_POST['risposta1'];
$risposta2 = $_POST['risposta2'];
$risposta3 = $_POST['risposta3'];
$risposta4 = $_POST['risposta4'];
$risposta_corretta = $_POST['risposta_corretta'];

if ($id_domanda) {
    $stmt = $db->prepare("UPDATE test_domande SET domanda=?, risposta1=?, risposta2=?, risposta3=?, risposta4=?, risposta_corretta=? WHERE id=?");
    $stmt->bind_param("ssssssi", $domanda, $risposta1, $risposta2, $risposta3, $risposta4, $risposta_corretta, $id_domanda);
} else {
    $stmt = $db->prepare("INSERT INTO test_domande (id_corso, id_lezione, domanda, risposta1, risposta2, risposta3, risposta4, risposta_corretta) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iisssssi", $id_corso, $id_lezione, $domanda, $risposta1, $risposta2, $risposta3, $risposta4, $risposta_corretta);
}

$stmt->execute();
header("Location: gestione_domande.php?id_corso=$id_corso&id_lezione=$id_lezione&msg=Domanda salvata con successo&icon=success");
exit();
