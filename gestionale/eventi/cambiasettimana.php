<?php
/**
 *
 * @author     Paolo Randone
 * @author     <paolo.randone@croceverde.org>
* @version    8.1
 * @note       Powered for Croce Verde Torino. All rights reserved
 *
 */
session_start();
include "../config/config.php";
include "../config/include/destinatari.php";

if (!isset($_SESSION["ID"])){
    header("Location: ../login.php");
}else{
    $matricola = $_SESSION["ID"];
    $estrai = $db->query("SELECT * FROM utenti WHERE ID='$matricola'")->fetch_array();
    $cognomenomerichiedente= $estrai['cognome'].' '.$estrai['nome'];

    $cognomerichiedente= $estrai['cognome'];
    $emailrichiedente= $estrai['email'];
}
$emailaccettante = '';
if(isset($_POST["invia"])){
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    $selectdip= $_POST["selectdip"];
    $nuovo1= $_POST["turnonuovo1"];
    $nuovo2= $_POST["turnonuovo2"];
    $cognomeaccettante = '';
    $selected_option_parts = explode(' ', $selectdip);
    if (count($selected_option_parts) >= 2) {
        // Unisci tutte le parti tranne l'ultima per formare il cognome composto
        array_pop($selected_option_parts); // Rimuove l'ultima parte (se non fa parte del cognome)
        $cognomeaccettante = implode(' ', $selected_option_parts);

        // Usa parametri preparati per evitare injection
        $stmt = $db->prepare("SELECT email FROM utenti WHERE cognome = ?");
        $stmt->bind_param("s", $cognomeaccettante);

        $stmt->execute();
        $result = $stmt->get_result();
        $selectacc = $result->fetch_assoc();

        $emailaccettante = $selectacc['email'];

        $stmt->close();
    }

    $selectsett= $_POST["selectsett"];
    //TODO modificare destinatario
    //$to = 'paolo.randone@croceverde.org';
    $to= $autoparco;
    $subject="Cambio turno settimana n.".' '.$selectsett.'_'.$cognomerichiedente .'-'.$cognomeaccettante;
    $nome_mittente="Gestionale CVTO";
    $mail_mittente=$gestionale;
    $headers = "From: " .  $nome_mittente . " <" .  $mail_mittente . ">\r\n";
    $headers .= "Cc: ".$emailrichiedente.", ".$emailaccettante."\r\n";
    //$headers .= "Bcc: ".$emailrichiedente."\r\n";
    //$headers .= "Bcc: ".$emailaccettante."\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=iso-8859-1";

    $replace = array(
        '{{richiedente}}',
        '{{accettante}}',
        '{{settimana}}',
        '{{richiedentenuovo}}',
        '{{accettantenuovo}}',
    );
    $with = array(
        $cognomenomerichiedente,
        $selectdip,
        $selectsett,
        $nuovo1,
        $nuovo2,
    );

    $corpo = file_get_contents('../config/template/settimana.html');
    $corpo = str_replace ($replace, $with, $corpo);

    mail($to, $subject, $corpo, $headers);


    echo '<script type="text/javascript">
        alert("Richiesta inviata con successo");
        //location.href="index.php";
        </script>';
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <meta name="author" content="Paolo Randone">
    <title>Modulo cambio settimana</title>

    <? require "../config/include/header.html";?>

    <script>
        $(document).ready(function () {
            // Function to update the value of nomenuovo2 based on selectdip
            function updateNomenuovo2() {
                var selectdipValue = $('#selectdip').val();
                $('#nomenuovo2').val(selectdipValue);
            }

            // Call the function when the page loads
            updateNomenuovo2();

            // Call the function whenever selectdip changes
            $('#selectdip').on('change', updateNomenuovo2);

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
            <li class="breadcrumb-item active" aria-current="page">Cambio settimana</li>
        </ol>
    </nav>
</div>
<br>


<div class="container-fluid">
    <form action="cambiasettimana.php" method="post">
        <div class="jumbotron">
            <h3 style="text-align: center">RICHIESTA CAMBIO SETTIMANALE</h3>
            <br>
            <div class="col-md-12">
                <label for="selectsett">Il sottoscritto <?=$cognomenomerichiedente?> chiede di cambiare il turno della <b>settimana</b> numero </label>
                <select class="form-control form-control-sm" id="selectsett" name="selectsett">
                    <option value="">Scegli...</option>
                    <?php
                    for ($a = 1; $a <= 39; $a++) {
                        echo "<option value='$a'>$a</option>";
                    }
                    ?>
                </select>
            </div>
            <br>
            <div class="col-md-12">
                <label for="selectdip">Con il collega</label>
                <select id="selectdip" name="selectdip" class="form-control form-control-sm" required>
                    <option value="">Scegli...</option>
                    <?
                    $select = $db->query("SELECT cognome, nome, email  FROM utenti WHERE stato=1 and  ID LIKE 'D%' and utenti.livello<5 order by cognome");
                    while($ciclo = $select->fetch_array()){
                        $emailaccettante= $ciclo['email'];
                        echo "<option value=\"".$ciclo['cognome'].' '.$ciclo['nome']."\">".$ciclo['cognome'].' '.$ciclo['nome']."</option>";
                    }
                    ?>
                </select>
            </div>
            <br>

<hr>
            <div class="col-md-12">
                <p>Il turno sarà quindi così svolto:</p>
                <input type="text" name="turnonuovo1" disabled value="<?=$cognomenomerichiedente?>">
                <select class="form-control form-control-sm" id="turnonuovo1" name="turnonuovo1">
                    <option value="">Scegli...</option>
                    <option value="6:00/14:00">6:00/14:00</option>
                    <option value="7:00/15:00">7:00/15:00</option>
                    <option value="7:30/15:30">7:30/15:30</option>
                    <option value="08:00/16:00">08:00/16:00</option>
                    <option value="09:00/17:00">09:00/17:00</option>
                    <option value="10:00/18:00">10:00/18:00</option>
                    <option value="003 mattino">003 mattino</option>
                    <option value="003 pomeriggio">003 pomeriggio</option>
                    <option value="007 mattino">007 mattino</option>
                    <option value="007 pomeriggio">007 pomeriggio</option>
                    <option value="011 mattino">011 mattino</option>
                    <option value="011 pomeriggio">011 pomeriggio</option>
                    <option value="023">023</option>
                    <option value="039 mattino">039 mattino</option>
                    <option value="039 pomeriggio">039 pomeriggio</option>
                    <option value="055 mattino">055 mattino</option>
                    <option value="055 pomeriggio">055 pomeriggio</option>
                    <option value="270 mattino">270 mattino</option>
                    <option value="270 pomeriggio">270 pomeriggio</option>
                    <option value="400 mattino">400 mattino</option>
                    <option value="400 pomeriggio">400 pomeriggio</option>
                    <option value="410 mattino">410 mattino</option>
                    <option value="410 notte">410 notte</option>
                    <option value="430 mattino">430 mattino</option>
                    <option value="430 pomeriggio">430 pomeriggio</option>
                    <option value="610 mattino">610 mattino</option>
                    <option value="610 pomeriggio">610 pomeriggio</option>
                    <option value="680">680</option>
                    <option value="Notti aggiuntive">Notti aggiuntive</option>
                </select>
            </div>
            <br>
            <div class="col-md-12">
                <input type="text" name="nomenuovo2" id="nomenuovo2" disabled value="">
                <select class="form-control form-control-sm" id="turnonuovo2" name="turnonuovo2">
                    <option>Scegli...</option>
                    <option value="6:00/14:00">6:00/14:00</option>
                    <option value="7:00/15:00">7:00/15:00</option>
                    <option value="7:30/15:30">7:30/15:30</option>
                    <option value="08:00/16:00">08:00/16:00</option>
                    <option value="09:00/17:00">09:00/17:00</option>
                    <option value="10:00/18:00">10:00/18:00</option>
                    <option value="003 mattino">003 mattino</option>
                    <option value="003 pomeriggio">003 pomeriggio</option>
                    <option value="007 mattino">007 mattino</option>
                    <option value="007 pomeriggio">007 pomeriggio</option>
                    <option value="011 mattino">011 mattino</option>
                    <option value="011 pomeriggio">011 pomeriggio</option>
                    <option value="023">023</option>
                    <option value="039 mattino">039 mattino</option>
                    <option value="039 pomeriggio">039 pomeriggio</option>
                    <option value="055 mattino">055 mattino</option>
                    <option value="055 pomeriggio">055 pomeriggio</option>
                    <option value="270 mattino">270 mattino</option>
                    <option value="270 pomeriggio">270 pomeriggio</option>
                    <option value="400 mattino">400 mattino</option>
                    <option value="400 pomeriggio">400 pomeriggio</option>
                    <option value="410 mattino">410 mattino</option>
                    <option value="410 notte">410 notte</option>
                    <option value="430 mattino">430 mattino</option>
                    <option value="430 pomeriggio">430 pomeriggio</option>
                    <option value="610 mattino">610 mattino</option>
                    <option value="610 pomeriggio">610 pomeriggio</option>
                    <option value="680">680</option>
                    <option value="Notti aggiuntive">Notti aggiuntive</option>
                </select>
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