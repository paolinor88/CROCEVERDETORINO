<?php
/**
 *
 * @author     Paolo Randone
 * @author     <paolo.randone@croceverde.org>
* @version    8.0
 * @note       Powered for Croce Verde Torino. All rights reserved
 *
 */
session_start();
include "../config/config.php";
include "../config/include/destinatari.php";
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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

    $numerogiorni = $_POST["numerogiorni"];
    $datainizio = $_POST["datainizio"];
    $datafine = $_POST["datafine"];
    $tipoassenza = $_POST["tipoassenza"];
    $note = $_POST["note"];

    $to = $autoparco ?? '';
    //$to = 'paolo.randone@croceverde.org' ?? '';
    $nome_mittente = "Gestionale CVTO";
    $mail_mittente = $gestionale;

    $headers = "From: " . $nome_mittente . " <" . $mail_mittente . ">\r\n";
    $headers .= "Cc: " . $emailrichiedente . "\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=iso-8859-1";
    
if ($numerogiorni==1){
    $subject = "Richiesta " .$datainizio.'_'. $tipoassenza . '_' . $cognomerichiedente;
    $templatePath = '../config/template/ferie1.html';
    if (file_exists($templatePath)) {
        $replace = array(
            '{{richiedente}}',
            '{{numerogiorni}}',
            '{{tipoassenza}}',
            '{{datainizio}}',
            '{{note}}',
        );
        $with = array(
            $cognomenomerichiedente,
            $numerogiorni,
            $tipoassenza,
            $datainizio,
            $note,
        );

        $corpo = file_get_contents($templatePath);
        $corpo = str_replace($replace, $with, $corpo);

        // Verifica
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
}else{
    $templatePath = '../config/template/ferie.html';
    $subject = "Richiesta " .$datainizio.'_al_'. $datafine . '_' . $tipoassenza . '_' . $cognomerichiedente;
    if (file_exists($templatePath)) {
        $replace = array(
            '{{richiedente}}',
            '{{numerogiorni}}',
            '{{tipoassenza}}',
            '{{datainizio}}',
            '{{datafine}}',
            '{{note}}',
        );
        $with = array(
            $cognomenomerichiedente,
            $numerogiorni,
            $tipoassenza,
            $datainizio,
            $datafine,
            $note,
        );

        $corpo = file_get_contents($templatePath);
        $corpo = str_replace($replace, $with, $corpo);

        // Verifica
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

}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <meta name="author" content="Paolo Randone">
    <title>Modulo richiesta ferie/RFA</title>

    <? require "../config/include/header.html";?>
    <script>
        $(document).ready(function () {
            $('#indietro').on('click', function(){
                location.href='index.php';
            });
        });
    </script>
    <script>

        function gestisciCampoNote() {
            var tipoAssenza = document.querySelector('input[name="tipoassenza"]:checked').value;
            var noteInput = document.getElementById('notefield');
            if (tipoAssenza === 'PERMESSO') {
                noteInput.style.display = 'block';
            } else {
                noteInput.style.display = 'none';
                noteInput.value = '';
            }
        }
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('numerogiorni').addEventListener('change', function() {
                var numerogiorni = parseInt(this.value);
                var dataFineContainer = document.getElementById('dataFineContainer');
                if (numerogiorni > 1) {
                    dataFineContainer.style.display = 'block';
                } else {
                    dataFineContainer.style.display = 'none';
                    document.getElementById('datafine').value = ''; // Resetta il valore di datafine se viene nascosto
                }
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('numerogiorni').addEventListener('change', function() {
                var numerogiorni = parseInt(this.value);
                var datainizioLabel = document.getElementById('datainizio_label');
                if (numerogiorni === 1) {
                    datainizioLabel.textContent = 'Il giorno';
                } else {
                    datainizioLabel.textContent = 'Dal giorno';
                }
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
            <li class="breadcrumb-item active" aria-current="page">Richiesta ferie</li>
        </ol>
    </nav>
</div>
<br>
<div class="container-fluid">
    <form action="ferie.php" method="post">
        <div class="jumbotron">
            <h3 style="text-align: center">RICHIESTA FERIE/RFA/PERMESSO</h3>
            <br>
            <div class="row mb-3">
                <label for="numerogiorni" class="col-sm-2 col-form-label">Numero giorni</label>
                <div class="col-sm-1">
                    <input type="number" class="form-control form-control-sm" id="numerogiorni" name="numerogiorni">
                </div>
            </div>
            <div class="row mb-3">
                <label for="datainizio" id="datainizio_label" class="col-sm-2 col-form-label">Il giorno</label>
                <div class="col-sm-2">
                    <input type="date" class="form-control form-control-sm" id="datainizio" name="datainizio">
                </div>
            </div>
            <div class="row mb-3" id="dataFineContainer" style="display: none;">
                <label for="datafine" class="col-sm-2 col-form-label">Al giorno</label>
                <div class="col-sm-2">
                    <input type="date" class="form-control form-control-sm" id="datafine" name="datafine">
                </div>
            </div>
            <fieldset class="row mb-3">
                <legend class="col-form-label col-sm-2 pt-0">Tipo assenza</legend>
                <div class="col-sm-10">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="tipoassenza" id="FERIE" value="FERIE" onclick="gestisciCampoNote()">
                        <label class="form-check-label" for="FERIE">
                            FERIE
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="tipoassenza" id="RFA" value="RFA" onclick="gestisciCampoNote()">
                        <label class="form-check-label" for="tipoassenza">
                            RFA
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="tipoassenza" id="PERMESSO" value="PERMESSO" onclick="gestisciCampoNote()">
                        <label class="form-check-label" for="gridRadios3">
                            PERMESSO
                        </label>
                    </div>
                </div>
            </fieldset>
            <div class="row mb-3" id="notefield" style="display: none;">
                <label for="note" class="col-sm-2 col-form-label">Note</label>
                <div class="col-sm-12">
                    <input type="text" class="form-control form-control-sm" id="note" name="note" placeholder="Motiva la richiesta di permesso">
                </div>
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