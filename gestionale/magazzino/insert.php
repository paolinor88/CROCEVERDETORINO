<?php
session_start();

include "../config/pdo.php";

if (isset($_POST["title"])){
    $aggiungilavaggio = "INSERT INTO lavaggio_mezzi (title, user_id, start_event, stato, esterno, interno, neb) VALUES (:title, :user_id, :start_event, :stato, :esterno, :interno, :neb)";

    $statement3 = $connect->prepare($aggiungilavaggio);
    $statement3->execute(
        array(
            ':title' => $_POST['title'],
            ':user_id' => $_POST['user_id'],
            ':start_event' => $_POST['start_event'],
            ':stato' => $_POST['stato'],
            ':esterno' => '1',
            ':interno' => '1',
            ':neb' => '1',
        )
    );
}

