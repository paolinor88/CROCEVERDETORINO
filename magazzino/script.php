<?php
include "../config/pdo.php";

if (isset($_POST["nome"])){
    if($_POST['scadenza']!="-01"){
        $inserisci = "INSERT INTO giacenza (nome, tipo, quantita, scadenza, dettagli, posizione, categoria, fornitore, prezzo) VALUES (:nome, :tipo, :quantita, :scadenza, :dettagli, :posizione, :categoria, :fornitore, :prezzo)";

        $statement = $connect->prepare($inserisci);
        $statement->execute(
            array(
                ':nome'  => $_POST['nome'],
                ':tipo' => $_POST['tipo'],
                ':quantita' => $_POST['quantita'],
                ':scadenza' => $_POST['scadenza'],

                ':dettagli' => $_POST['dettagli'],
                ':posizione' => $_POST['posizione'],
                ':categoria' => $_POST['categoria'],
                ':fornitore' => $_POST['fornitore'],
                ':prezzo' => $_POST['prezzo'],
            )
        );
    }else{
        $insert = "INSERT INTO giacenza (nome, tipo, quantita, scadenza, dettagli, posizione, categoria, fornitore, prezzo) VALUES (:nome, :tipo, :quantita, :scadenza, :dettagli, :posizione, :categoria, :fornitore, :prezzo)";

        $statement1 = $connect->prepare($insert);
        $statement1->execute(
            array(
                ':nome'  => $_POST['nome'],
                ':tipo' => $_POST['tipo'],
                ':quantita' => $_POST['quantita'],
                ':scadenza' => NULL,

                ':dettagli' => $_POST['dettagli'],
                ':posizione' => $_POST['posizione'],
                ':categoria' => $_POST['categoria'],
                ':fornitore' => $_POST['fornitore'],
                ':prezzo' => $_POST['prezzo'],
            )
        );
    }

}


if (isset($_POST["id"])){
    $update = "UPDATE giacenza SET quantita=:quantita WHERE id=:id";

    $statement2 = $connect->prepare($update);
    $statement2->execute(
        array(
            ':quantita' => $_POST['quantitaF'],
            ':id' => $_POST['id'],
        )
    );
}
