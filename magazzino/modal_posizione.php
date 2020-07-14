<?php
include "../config/config.php";
if (isset($_GET["id"])) {
    $id = $_GET["id"];
    $modifica = $db->query("SELECT * FROM giacenza WHERE id='$id'")->fetch_array();
}
if(isset($_POST['update'])) {
    $id = $_POST['id'];
    $posizione = $_POST['posizioneF'];
    $update = $db->query("UPDATE giacenza SET posizione='$posizione' WHERE id='$id'");

    if ($update){
        echo '<script type="text/javascript">
        alert("Modifica eseguita con successo");
        location.href="magazzino.php";
        </script>';
    }else{
        echo '<script type="text/javascript">
        alert("ERRORE");
        location.href="magazzino.php";
        </script>';
    }
}
?>
<head xmlns="">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <meta name="author" content="Paolo Randone">
    <title>MODAL Posizione</title>
    <? require "../config/include/header.html";?>

</head>

<form action="modal_posizione.php" method="post">
    <div class="card text-center">
        <div class="card-body">
            <h6 class="card-title">Modifica posizione</h6>
            <input hidden name="id" value="<?=$id?>">
            <select id="posizioneF" class="form-control form-control-sm">
                <option value="Magazzino">Magazzino</option>
                <option value="Magazzion 2P">Magazzino 2P</option>
                <option value="Officina">Officina</option>
                <option value="Armadio scorte">Armadio scorte</option>
                <option value="Cantina">Cantina</option>
                <option value="Direzione">Direzione</option>
            </select>
        </div>
        <div class="card-footer">
            <button type="submit" name="update" class="btn btn-outline-success btn-sm" style="text-align: center">Aggiorna</button>
        </div>
    </div>
</form>


