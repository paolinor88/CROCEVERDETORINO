<?php
$connect=new PDO('mysql:host=localhost;dbname=massi369_gestionale', 'urhqx7h4kxv84', 'Gestional€');

if (isset($_POST["id_utente"])){
    $inserisci = "INSERT INTO utenti_events (id_utente, id_evento) VALUES (:id_utente, :id_evento)";

    $statement = $connect->prepare($inserisci);
    $statement->execute(
        array(
            ':id_utente'  => $_POST['id_utente'],
            ':id_evento' => $_POST['id_evento']
        )
    );
}