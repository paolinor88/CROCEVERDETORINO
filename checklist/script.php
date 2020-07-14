<?php
/*
 * File di script
 * Non modificare i parametri
 */
session_start();
//connessione DB
$connect=new PDO('mysql:host=localhost;dbname=massi369_gestionale', 'massi369', '@Croceto99');
// aggiungi mezzo
if(isset($_POST["targa"])){


        $query = "INSERT INTO mezzi (ID, targa, tipo, note) VALUES (:ID, :targa, :tipo, :note)";

        $statement = $connect->prepare($query);
        $statement->execute(
            array(
                ':ID'  => $_POST['ID'],
                ':targa'  => $_POST['targa'],
                ':tipo' => $_POST['tipo'],
                ':note' => $_POST['note'],
            )
        );
    };
