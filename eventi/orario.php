<?php
/**
 *
 * @author     Paolo Randone
 * @author     <paolo.randone@croceverde.org>
* @version    8.0
 * @note       Powered for Croce Verde Torino. All rights reserved
 *
 */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include "../config/config.php";
include "../config/include/destinatari.php";

if (!isset($_SESSION["ID"])) {
    header("Location: ../login.php");
} else {
    $matricola = $_SESSION["ID"];
    $estrai = $db->query("SELECT * FROM utenti WHERE ID='$matricola'")->fetch_array();
    $cognomenomerichiedente = $estrai['cognome'] . ' ' . $estrai['nome'];
    $cognomerichiedente = $estrai['cognome'];
    $emailrichiedente = $estrai['email'];
}

if (isset($_POST["invia"])) {
    $datainizio = $_POST["datainizio"];
    //$to= $autoparco;
    $to = 'autoparco@croceverde.org';

    $nome_mittente = "Gestionale CVTO";
    $mail_mittente = $gestionale;
    $subject = "Richiesta " .$datainizio. "_IN_ORARIO_" . $cognomerichiedente;
    $headers = "From: " . $nome_mittente . " <" . $mail_mittente . ">\r\n";
    $headers .= "Cc: " . $emailrichiedente . "\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=iso-8859-1";

    $templatePath = '../config/template/orario.html';
    if (file_exists($templatePath)) {
        $replace = array(
            '{{richiedente}}',
            '{{datainizio}}',
        );
        $with = array(
            $cognomenomerichiedente,
            $datainizio,
        );

        $corpo = file_get_contents($templatePath);
        $corpo = str_replace($replace, $with, $corpo);
        /*
        var_dump($to);
        var_dump($subject);
        var_dump($corpo);
        var_dump($headers);
*/
        if (mail($to, $subject, $corpo, $headers)) {
            echo '<script type="text/javascript">
                alert("Richiesta inviata con successo");
                location.href="index.php";
                </script>';
        } else {
            echo '<script type="text/javascript">
                alert("Errore nell\'invio della mail. Contatta l\'amministratore.");
                location.href="index.php"; 
            </script>';
        }
    } else {
        echo '<script type="text/javascript">
                alert("Il template HTML della mail non esiste. Contatta l\'amministratore.");
            location.href="index.php"; 
            </script>';
    }
}

?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <meta name="author" content="Paolo Randone">
    <title>Modulo richiesta smonto in orario</title>

    <? require "../config/include/header.html";?>
    <script>
        $(document).ready(function () {
            $('#indietro').on('click', function(){
                location.href='index.php';
            });
        });
    </script>
</head>
<body>


<!-- NAVBAR -->
<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../index.php" style="color: #078f40">Home</a></li>
            <li class="breadcrumb-item"><a href="index.php" style="color: #078f40">Calendario</a></li>
            <li class="breadcrumb-item active" aria-current="page">Smonto in orario</li>
        </ol>
    </nav>
</div>
<br>
<div class="container-fluid">
    <form action="orario.php" method="post">
        <div class="jumbotron">
            <h3 style="text-align: center">RICHIESTA SMONTO IN ORARIO</h3>
            <br>
            <div class="col md-12">
            <p>Il sottoscritto <?=$cognomenomerichiedente?> chiede di poter smontare in orario il <b>giorno </b><input type="date" class="form-control form-control-sm" id="datainizio" name="datainizio"></p>
            </div>
            <br>
            <div style="text-align: center">
                <div class="btn-group" role="group">
                    <button type="submit" class="btn btn-sm btn-outline-success" id="invia" name="invia"><i class="fas fa-check"></i> INVIA</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="indietro" name="indietro"><i class="fas fa-undo"></i> ANNULLA</button>
                </div>
            </div>
        </div>
    </form>
</div>
</body>
<? if (!isset($calendario)){
    include('../config/include/footer.php');
}
?>


</html>