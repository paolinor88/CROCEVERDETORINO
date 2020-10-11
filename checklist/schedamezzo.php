<?php
/**
 *
 * @author     Paolo Randone
 * @author     <mail@paolorandone.it>
 * @version    1.4
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
    $lastcheck = $db->query("SELECT DATACHECK FROM checklist WHERE IDMEZZO=$id ORDER BY DATACHECK DESC LIMIT 1")->fetch_array();
}
//contatore lavaggi
if (isset($id)){
    $lastwash = $db->query("SELECT DATACHECK FROM checklist WHERE IDMEZZO=$id AND LAVAGGIO IS TRUE ORDER BY DATACHECK DESC LIMIT 1")->fetch_array();
}
//contatore scadenze
if (isset($id)){
    $lastscad = $db->query("SELECT DATACHECK FROM checklist WHERE IDMEZZO=$id AND SCADENZE=1 ORDER BY DATACHECK DESC LIMIT 1")->fetch_array();
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
//mail lavaggi
if(isset($_POST["stampalavaggi"])){
    $numeroauto = $_POST["numeroauto"];

    $array= ($_POST["allwash"]);
    $nwash= count($array);
    for ($i=0;$i<$nwash;$i++){
        $elenco .= ($array[$i])."<br>";
    };
    //PARAMETRI MAIL ->
    $now = date("Y-m-d");
    $to= $_SESSION['email'];
    $subject="Elenco lavaggi auto $numeroauto";
    $nome_mittente="Gestionale CVTO";
    $mail_mittente="gestioneutenti@croceverde.org";
    $headers = "From: " .  $nome_mittente . " <" .  $mail_mittente . ">\r\n";
    $headers .= "Bcc: ".$mail_mittente."\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=iso-8859-1";

    $replace = array(
        '{{numeroauto}}',
        '{{now}}',
        '{{elenco}}',
    );
    $with = array(
        $numeroauto,
        $now,
        $elenco,
    );

    $corpo = file_get_contents('../config/template/wash.html');
    $corpo = str_replace ($replace, $with, $corpo);

    mail($to, $subject, $corpo, $headers);

    echo '<script type="text/javascript">
            alert("Riepilogo inviato con successo");
            location.href="mezzi.php";
        </script>';
}
//mail checklist
if(isset($_POST["stampacheck"])){
    $numeroauto = $_POST["numeroauto"];

    $array= ($_POST["allcheck"]);
    $ncheck= count($array);
    for ($i=0;$i<$ncheck;$i++){
        $elenco .= ($array[$i])."<br>";
    };

    $now = date("Y-m-d");
    $to= $_SESSION['email'];
    $subject="Elenco checklist auto $numeroauto";
    $nome_mittente="Gestionale CVTO";
    $mail_mittente="gestioneutenti@croceverde.org";
    $headers = "From: " .  $nome_mittente . " <" .  $mail_mittente . ">\r\n";
    $headers .= "Bcc: ".$mail_mittente."\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=iso-8859-1";

    $replace = array(
        '{{numeroauto}}',
        '{{now}}',
        '{{elenco}}',
    );
    $with = array(
        $numeroauto,
        $now,
        $elenco,
    );

    $corpo = file_get_contents('../config/template/check.html');
    $corpo = str_replace ($replace, $with, $corpo);

    mail($to, $subject, $corpo, $headers);

    echo '<script type="text/javascript">
        alert("Riepilogo inviato con successo");
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
                        <label for="lastcheck">Ultima checklist <button type="button" id="storicocheck" name="storicocheck" data-toggle="modal" data-target="#modal2" class="btn btn-sm btn-outline-secondary"><i class="fas fa-database"></i></button></label>
                        <input id="lastcheck" type="text" class="form-control form-control-sm" <?=$readonly ?> value="<?=$lastcheck['DATACHECK']?>">
                    </div>
                    <div class="form-group">
                        <label for="lastwash">Ultimo lavaggio <button type="button" id="storicolavaggi" name="storicolavaggi" data-toggle="modal" data-target="#modal1" class="btn btn-sm btn-outline-secondary"><i class="fas fa-database"></i></button></label>
                        <input id="lastwash" type="text" class="form-control form-control-sm" <?=$readonly ?> value="<?=$lastwash['DATACHECK']?>">
                    </div>
                    <div class="form-group">
                        <label for="lastscad">Controllo scadenze</label>
                        <input id="lastscad" type="text" class="form-control form-control-sm" <?=$readonly ?> value="<?=$lastscad['DATACHECK']?>">
                    </div>
                    <div class="form-group">
                        <label for="xnote">Note sul mezzo</label>
                        <textarea class="form-control" id="xnote" name="xnote" rows="10"><?=$modifica['note']?></textarea>
                    </div>
                    <hr>
                    <center>
                        <button type="submit" id="aggiornamezzo" name="aggiornamezzo" class="btn btn-success"><i class="fas fa-check"></i></button>
                    </center>
                </form>
            </div>
        </div>
    </div>
</div>

</body>

<!-- MODAL LAVAGGI -->
<div class="modal" id="modal1" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content">
            <form method="post" action="schedamezzo.php">
                <input hidden id="numeroauto" name="numeroauto" value="<?=$id?>">
                <div class="modal-header">
                    <h6 class="modal-title" id="modal1Title">Selezionare i lavaggi da stampare</h6>
                </div>
                <div class="modal-body">
                    <select multiple class="form-control form-control-sm" id="allwash" name="allwash[]">
                        <?php
                        $allwash = $db->query("SELECT * FROM checklist WHERE IDMEZZO=$id AND LAVAGGIO IS TRUE ORDER BY DATACHECK DESC");
                        while ($ciclo = $allwash->fetch_array()){
                            echo "<option value=\"".$ciclo['DATACHECK']."\">".$ciclo['DATACHECK']."</option>";
                        };?>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-dismiss="modal">Chiudi</button>
                    <button type="submit" class="btn btn-outline-success btn-sm" id="stampalavaggi" name="stampalavaggi">Stampa</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL CHECK -->
<div class="modal" id="modal2" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content">
            <form method="post" action="schedamezzo.php">
                <input hidden id="numeroauto" name="numeroauto" value="<?=$id?>">
                <div class="modal-header">
                    <h6 class="modal-title" id="modal1Title">Selezionare le check da stampare</h6>
                </div>
                <div class="modal-body">
                    <select multiple class="form-control form-control-sm" id="allcheck" name="allcheck[]">
                        <?php
                        $allwash = $db->query("SELECT * FROM checklist WHERE IDMEZZO=$id ORDER BY DATACHECK DESC");
                        while ($ciclo = $allwash->fetch_array()){
                            echo "<option value=\"".$ciclo['DATACHECK']."\">".$ciclo['DATACHECK']."</option>";
                        };?>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-dismiss="modal">Chiudi</button>
                    <button type="submit" class="btn btn-outline-success btn-sm" id="stampacheck" name="stampacheck">Stampa</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- FOOTER -->
<?php include('../config/include/footer.php'); ?>

</html>