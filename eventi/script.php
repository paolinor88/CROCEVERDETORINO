<?php
include "../config/pdo.php";

if (isset($_POST["id_utente"])){
    $inserisci = "INSERT INTO utenti_events (id_utente, id_evento) VALUES (:id_utente, :id_evento)";

    $statement = $connect->prepare($inserisci);
    $statement->execute(
        array(
            ':id_utente'  => $_POST['id_utente'],
            ':id_evento' => $_POST['id_evento']
        )
    );
};

if(isset($_POST["id"]))
{
    $query = "DELETE FROM agenda WHERE id=:id";

    $statement = $connect->prepare($query);
    $statement->execute(
        array(
            ':id'  => $_POST['id'],
        )
    );
};
