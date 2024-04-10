<?php
include "../config/pdo.php";

if (isset($_POST["id"])){
    $aggiornasegnalazione = "UPDATE SegnalazioniGuastiMezzi SET SegnalazioniGuastiMezzi.DataVerificato=NOW(), SegnalazioniGuastiMezzi.NoteVerificato=:note WHERE ID=:ID";

    $statement7 = $connect->prepare($aggiornasegnalazione);
    $statement7->execute(
        array(
            ':ID' => $_POST['id'],
            ':note' => $_POST['note']
        )
    );
}