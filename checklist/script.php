<?php
include "../config/pdo.php";
if (isset($_POST["stato"])){
    $update = "UPDATE checklist SET STATO=:STATO WHERE IDCHECK=:id";

    $statement2 = $connect->prepare($update);
    $statement2->execute(
        array(
            ':STATO' => $_POST['stato'],
            ':id' => $_POST['id'],
        )
    );
}