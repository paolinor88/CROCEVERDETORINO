<?php

include "../config/pdo.php";

if (isset($_POST["title"])){
    $aggiungilavaggio = "INSERT INTO lavaggio_mezzi (title, user_id, start_event, stato) VALUES (:title, :user_id, :start_event, :stato)";

    $statement3 = $connect->prepare($aggiungilavaggio);
    $statement3->execute(
        array(
            ':title' => $_POST['title'],
            ':user_id' => $_POST['user_id'],
            ':start_event' => $_POST['start_event'],
            ':stato' => $_POST['stato'],
        )
    );
}

