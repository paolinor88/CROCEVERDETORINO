<?php
include "../config/pdo.php";
if (isset($_POST["visto"])){
    $update = "UPDATE checklist SET VISTO=:visto WHERE IDCHECK=:id";

    $statement2 = $connect->prepare($update);
    $statement2->execute(
        array(
            ':visto' => $_POST['visto'],
            ':id' => $_POST['id'],
        )
    );
}
if (isset($_POST["chiuso"])){
    $update = "UPDATE checklist SET VISTO=:visto, CHIUSO=:chiuso WHERE IDCHECK=:id";

    $statement2 = $connect->prepare($update);
    $statement2->execute(
        array(
            ':visto' => "2",
            ':id' => $_POST['id'],
            ':chiuso' => $_POST['chiuso'],
        )
    );
}
if (isset($_POST["apri"])){
    $update = "UPDATE checklist SET CHIUSO=:chiuso WHERE IDCHECK=:id";

    $statement2 = $connect->prepare($update);
    $statement2->execute(
        array(
            ':id' => $_POST['id'],
            ':chiuso' => $_POST['chiuso'],
        )
    );
}