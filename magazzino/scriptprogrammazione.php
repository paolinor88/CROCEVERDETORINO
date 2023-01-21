<?php
session_start();

include "../config/pdo.php";

if (isset($_POST["title"])){
    $assegnamezzo = "UPDATE programmazione SET title=(:title) WHERE id=(:id)";

    $statement = $connect->prepare($assegnamezzo);
    $statement->execute(
        array(
            ':title' => $_POST['title'],
            ':id' => $_POST['id'],
        )
    );
}