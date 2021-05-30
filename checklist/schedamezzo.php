<?php
/**
 *
 * @author     Paolo Randone
 * @author     <mail@paolorandone.it>
 * @version    2.4
 * @note       Powered for Croce Verde Torino. All rights reserved
 *
 */
session_start();
//connessione DB
include "../config/config.php";
//controllo LOGIN
if (($_SESSION["livello"])<4){
    header("Location: ../error.php");
}
//recupera variabili mezzo
if (isset($_GET["ID"])){
    $id = $_GET["ID"];
    $readonly = "readonly";
    $modifica = $db->query("SELECT * FROM mezzi WHERE ID='$id'")->fetch_array();
}
//contatore check
if (isset($id)){
    $lastcheck = $db->query("SELECT DATACHECK FROM checklist WHERE IDMEZZO='$id' ORDER BY DATACHECK DESC LIMIT 1")->fetch_array();
}
//contatore lavaggi
if (isset($id)){
    $lastwash = $db->query("SELECT start_event FROM lavaggio_mezzi WHERE title='$id' ORDER BY start_event DESC LIMIT 1")->fetch_array();
}
//contatore scadenze
if (isset($id)){
    $lastscad = $db->query("SELECT DATACHECK FROM checklist WHERE IDMEZZO='$id' AND SCADENZE=1 ORDER BY DATACHECK DESC LIMIT 1")->fetch_array();
}
//nicename tipo
$dictionary = array (
    1 => "MSB",
    2 => "MSA",
    3 => "118",
);
//nicename stato
$dictionary1 = array (
    1 => "Attivo",
    2 => "Dismesso",
);
//update
if(isset($_POST["aggiornamezzo"])){
    $auto = $_POST["xauto"];
    $targa = $_POST["xtarga"];
    $tipo = $_POST["xtipo"];
    $stato = $_POST["xstato"];
    $note = $_POST["xnote"];

    $aggiornamezzo = $db->query("UPDATE mezzi SET targa='$targa', tipo='$tipo', stato='$stato', note='$note' WHERE ID='$auto'");
    echo '<script type="text/javascript">
        alert("Modifica effettuata con successo");
        location.href="mezzi.php";
        </script>';
}

?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <meta name="author" content="Paolo Randone">
    <title>Dettagli mezzo</title>

    <? require "../config/include/header.html";?>

    <script>
        $(document).ready(function(){
            $('[data-toggle="popover"]').popover();
        });
    </script>

</head>
<!-- NAVBAR -->
<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../index.php" style="color: #078f40">Home</a></li>
            <li class="breadcrumb-item"><a href="index.php" style="color: #078f40">Checklist elettronica</a></li>
            <li class="breadcrumb-item"><a href="mezzi.php" style="color: #078f40">Lista mezzi</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?=$id?></li>
        </ol>
    </nav>
</div>
<!-- CONTENT -->
<body>
<div class="container-fluid">
    <div class="row text-left">
        <div class="col-md-3 col-md-offset-3"></div>
        <div class="col-md-6">
            <div class="jumbotron">
                <form method="post" action="schedamezzo.php">
                    <input hidden id="xauto" name="xauto" value="<?=$id?>">
                    <h1  style="text-align: center">AUTO <?=$id?></h1>
                    <hr>
                    <div class="form-group">
                        <label for="xtarga">Targa</label>
                        <input id="xtarga" name="xtarga" type="text" class="form-control form-control-sm" value="<?=$modifica['targa']?>"
                            <?php
                            if (($_SESSION["livello"])!=6){
                                echo "disabled";
                            }

                            ?>>
                    </div>
                    <div class="form-group">
                        <label for="xtipo">Tipo</label>
                        <select class="form-control form-control-sm" id="xtipo" name="xtipo">
                            <?
                            for($a=1;$a<4;$a++){
                                ($a==$modifica['tipo'])? $sel="selected" : $sel="";
                                echo "<option $sel value='$a'>".$dictionary[$a]."</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="xstato">Stato</label>
                        <select class="form-control form-control-sm" id="xstato" name="xstato">
                            <?
                            for($a=1;$a<3;$a++){
                                ($a==$modifica['stato'])? $sel="selected" : $sel="";
                                echo "<option $sel value='$a'>".$dictionary1[$a]."</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="lastcheck">Ultima checklist</label>
                        <input id="lastcheck" type="text" class="form-control form-control-sm" <?=$readonly ?> value="<? $var=$lastcheck['DATACHECK']; $var1=date_create("$var"); echo date_format($var1, "d-m-Y")?>">
                    </div>
                    <div class="form-group">
                        <label for="lastwash">Ultimo lavaggio</label>
                        <input id="lastwash" type="text" class="form-control form-control-sm" <?=$readonly ?> value="<? $var=$lastwash['start_event']; $var1=date_create("$var"); echo date_format($var1, "d-m-Y")?>">
                    </div>
                    <div class="form-group">
                        <label for="lastscad">Ultimo controllo scadenze</label>
                        <input id="lastscad" type="text" class="form-control form-control-sm" <?=$readonly ?> value="<? $var=$lastscad['DATACHECK']; $var1=date_create("$var"); echo date_format($var1, "d-m-Y")?>">
                    </div>
                    <div class="form-group">
                        <label for="xnote">Note sul mezzo</label>
                        <textarea class="form-control" id="xnote" name="xnote" rows="10"><?=$modifica['note']?></textarea>
                    </div>
                    <hr>
                    <div style="text-align: center;">
                        <button type="submit" id="aggiornamezzo" name="aggiornamezzo" class="btn btn-success"><i class="fas fa-check"></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

</body>


<!-- FOOTER -->
<?php include('../config/include/footer.php'); ?>

</html>