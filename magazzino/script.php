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

if (isset($_POST["title"])){
    $delete = "DELETE FROM lavaggio_mezzi WHERE id=:id";

    $statement3 = $connect->prepare($delete);
    $statement3->execute(
        array(
            ':id' => $_POST['id']
        )
    );
}

if (isset($_POST["statoF"])){
    $updatestato = "UPDATE richiesta_giacenza SET STATO=:STATO WHERE ID_RICHIESTA=:ID_RICHIESTA";

    $statement4 = $connect->prepare($updatestato);
    $statement4->execute(
        array(
            ':STATO' => $_POST['statoF'],
            ':ID_RICHIESTA' => $_POST['id_richiesta'],
        )
    );

    if(($_POST["statoF"])!='1'){
        include "../config/config.php";

        $id_richiesta= $_POST['id_richiesta'];
        $var = $db->query("SELECT QUANTITA, ID_ITEM FROM richiesta_giacenza WHERE ID_RICHIESTA='$id_richiesta'")->fetch_array();
        $delta = $var['QUANTITA'];
        $id_item = $var['ID_ITEM'];

        $updatequantita = $db->query("UPDATE giacenza SET quantita=(quantita-'$delta') WHERE id='$id_item'");

    }
}
