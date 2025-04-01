<?php
session_start();
include "../config/config.php";

if (!isset($_SESSION['Livello']) || $_SESSION['Livello'] != 28) {
    header("Location: index.php");
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id']) || !isset($_GET['id_corso']) || !isset($_GET['id_lezione'])) {
    header("Location: gestione_domande.php?msg=Errore nei parametri&id_corso={$_GET['id_corso']}&id_lezione={$_GET['id_lezione']}&icon=error");
    exit();
}

$id_domanda = intval($_GET['id']);
$id_corso = intval($_GET['id_corso']);
$id_lezione = intval($_GET['id_lezione']);

$stmt = $db->prepare("DELETE FROM test_domande WHERE id = ?");
$stmt->bind_param("i", $id_domanda);

if ($stmt->execute()) {
    header("Location: gestione_domande.php?msg=Domanda eliminata con successo&id_corso=$id_corso&id_lezione=$id_lezione&icon=success");
} else {
    header("Location: gestione_domande.php?msg=Errore durante l'eliminazione&id_corso=$id_corso&id_lezione=$id_lezione&icon=error");
}

$stmt->close();
exit();
