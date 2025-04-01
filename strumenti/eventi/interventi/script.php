<?php
include "../config/pdo.php";

if (isset($_POST["cognome"])) {
    $inserisci = "INSERT INTO interventi (COGNOME, NOME, NASCITA, INDIRIZZO, TELEFONO, SQUADRA, POSTAZIONE, ORAINIZIO, CODICEPATOLOGIA, CODICEGRAVITA, ESITO, STATO, NOTE, ORAFINE, IDEvento) VALUES (:cognome, :nome, :nascita, :indirizzo, :telefono, :squadra, :posizione, :inizio, :patologia, :gravita, :esito, :stato, :note, :fine, :IDEvento)";

    $statement = $connect->prepare($inserisci);
    $statement->bindValue(':cognome', $_POST['cognome']);
    $statement->bindValue(':nome', $_POST['nome']);
    $statement->bindValue(':nascita', $_POST['nascita']);
    $statement->bindValue(':indirizzo', $_POST['indirizzo']);
    $statement->bindValue(':telefono', $_POST['telefono']);
    $statement->bindValue(':squadra', $_POST['squadra']);
    $statement->bindValue(':posizione', $_POST['posizione']);
    $statement->bindValue(':inizio', $_POST['inizio']);
    $statement->bindValue(':patologia', $_POST['patologia']);
    $statement->bindValue(':gravita', $_POST['gravita']);
    $statement->bindValue(':esito', $_POST['esito']);
    $statement->bindValue(':stato', $_POST['stato']);
    $statement->bindValue(':note', $_POST['note']);
    $statement->bindValue(':fine', $_POST['fine']);
    $statement->bindValue(':IDEvento', $_POST['IDEvento']);
    $statement->execute();
}


