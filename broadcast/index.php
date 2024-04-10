<?php
/**
 *
 * @author     Paolo Randone
 * @author     <paolo.randone@croceverde.org>
* @version    7.4
 * @note       Powered for Croce Verde Torino. All rights reserved
 *
 */
session_start();
//parametri DB
include "../config/config.php";
/*
if (!isset($_SESSION["ID"])){
    header("Location: login.php");
}
*/
require_once('ultramsg.class.php');

//print_r($api)
if (isset($_POST["inviacomunicazione"])){
    $token="wjbgtj9przecszj2";
    $instance_id="instance24120";
    $client = new UltraMsg\WhatsAppApi($token,$instance_id);

    $to=$_POST["to"]; //DESTINATARIO SINGOLO
    //$to="120363029985497987@g.us"; //GRUPPO TEST
    $body=$_POST["body"];
    $api=$client->sendChatMessage($to,$body);

    if ($to){
        echo '<script type="text/javascript">
        alert("COMUNICAZIONE INVIATA CON SUCCESSO");
        location.href="index.php";
        </script>';
    }

    else {
        echo '<script type="text/javascript">
        alert("ERRORE");
        </script>';
    }
}

?>
<!DOCTYPE html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <meta name="author" content="Paolo Randone">
    <title>Comunicazioni</title>

    <? require "../config/include/header.html";?>

</head>
<!-- NAVBAR -->
<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../index.php" style="color: #078f40">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Comunicazioni</li>
        </ol>
    </nav>
</div>
<body>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-3 col-md-offset-3"></div>
        <div class="col-md-6">
            <div class="jumbotron">
                <form method="post" action="index.php">
                    <div class="form-group">
                        <label for="testo">Numero</label>
                        <input type="text" id="to" name="to" class="form-control form-control-sm"  autofocus required placeholder="+39">
                    </div>
                    <div class="form-group">
                        <label for="testo">Testo</label>
                        <textarea type="text" id="body" name="body" class="form-control form-control-sm" rows="5" required></textarea>
                    </div>
                    <br>
                    <div>
                        <button type="button" class="btn btn-sm btn-outline-success" data-toggle="modal" data-target="#modalconferma"><i class="fas fa-check"></i> Invia</button>
                    </div>
                    <div class="modal" id="modalconferma" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
                            <div class="modal-content">
                                <div class="modal-body">
                                    <p>Confermi l'invio del messaggio?</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-outline-secondary btn-sm" data-dismiss="modal">Annulla</button>
                                    <button type="submit" class="btn btn-success btn-sm" name="inviacomunicazione">Conferma</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

</body>




<?php include('../config/include/footer.php'); ?>
