<?php
/**
 *
 * @author     Paolo Randone
 * @author     <paolo.randone@croceverde.org>
 * @version    8.2
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
    $nuovo1Weeks= $_POST["turnonuovo1Weeks"];
    $nuovo2Weeks= $_POST["turnonuovo2Weeks"];
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
    $to= $autoparco;
    //$to= $randone;
    $subject="Cambio FERIE".' '.$cognomerichiedente .'-'.$cognomeaccettante;
    $nome_mittente="Gestionale CVTO";
    $mail_mittente=$gestionale;
    $headers = "From: " .  $nome_mittente . " <" .  $mail_mittente . ">\r\n";
    $headers .= "Bcc: ".$emailrichiedente."\r\n";
    $headers .= "Bcc: ".$emailaccettante."\r\n";
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
        $nuovo1Weeks,
        $nuovo2Weeks,
    );

    $corpo = file_get_contents('../config/template/cambiaferie.html');
    $corpo = str_replace ($replace, $with, $corpo);

    mail($to, $subject, $corpo, $headers);

    echo '<script type="text/javascript">
        alert("Richiesta inviata con successo");
        location.href="index.php";
        </script>';
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <meta name="author" content="Paolo Randone">
    <title>Modulo cambio FERIE</title>

    <? require "../config/include/header.html";?>

    <script>
        $(document).ready(function () {
            // Function to collect the weeks selected for giving
            function getSelectedWeeksToGive() {
                var selectedWeeks = [];
                $('input[name="selectsett"]:checked').each(function() {
                    selectedWeeks.push($(this).val());
                });
                return selectedWeeks.join(', ');
            }

            // Function to collect the weeks selected for receiving
            function getSelectedWeeksToReceive() {
                var selectedWeeks = [];
                $('input[name="selectsett2"]:checked').each(function() {
                    selectedWeeks.push($(this).val());
                });
                return selectedWeeks.join(', ');
            }

            // Update the fields 'turnonuovo1' and 'turnonuovo2'
            function updateFields() {
                var selectDipValue = $('#selectdip').val();
                var weeksToGive = getSelectedWeeksToGive();
                var weeksToReceive = getSelectedWeeksToReceive();

                // Update 'turnonuovo1' with the name of the requester and the weeks they receive
                $('#turnonuovo1Name').val('<?=$cognomenomerichiedente?>');
                $('#turnonuovo1Weeks').val(weeksToReceive);

                // Update 'turnonuovo2' with the name of the colleague and the weeks they give
                $('#turnonuovo2Name').val(selectDipValue);
                $('#turnonuovo2Weeks').val(weeksToGive);
            }

            // Events to update the fields when selections change
            $('#selectdip').change(updateFields);
            $('input[name="selectsett"], input[name="selectsett2"]').change(updateFields);

            // Initialize the fields when the page loads
            updateFields();
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
            <li class="breadcrumb-item active" aria-current="page">Cambio FERIE</li>
        </ol>
    </nav>
</div>
<br>


<div class="container-fluid">
    <form action="cambiaferie.php" method="post">
        <div class="jumbotron">
            <h3 style="text-align: center">RICHIESTA CAMBIO FERIE</h3>
            <br>
            <div class="col-md-12">
                <label for="selectsett">Il sottoscritto <?=$cognomenomerichiedente?> chiede di <u>cedere il turno di FERIE</u> delle <b>settimane</b> numero </label>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="selectsett" id="selectsett" value="2" >
                    <label class="form-check-label" for="selectsett">
                        2 (dal 23/06 al 27/06)
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="selectsett" id="selectsett" value="3" >
                    <label class="form-check-label" for="selectsett">
                        3 (dal 30/06 al 05/07)
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="selectsett" id="selectsett" value="4" >
                    <label class="form-check-label" for="selectsett">
                        4 (dal 07/07 al 11/07)
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="selectsett" id="selectsett" value="5" >
                    <label class="form-check-label" for="selectsett">
                        5 (dal 14/07 al 18/07)
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="selectsett" id="selectsett" value="6" >
                    <label class="form-check-label" for="selectsett">
                        6 (dal 21/07 al 25/07)
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="selectsett" id="selectsett" value="7" >
                    <label class="form-check-label" for="selectsett">
                        7 (dal 28/07 al 01/08)
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="selectsett" id="selectsett" value="8" >
                    <label class="form-check-label" for="selectsett">
                        8 (dal 04/08 al 08/08)
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="selectsett" id="selectsett" value="9" >
                    <label class="form-check-label" for="selectsett">
                        9 (dal 11/08 al 15/08)
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="selectsett" id="selectsett" value="10" >
                    <label class="form-check-label" for="selectsett">
                        10 (dal 18/08 al 22/08)
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="selectsett" id="selectsett" value="11" >
                    <label class="form-check-label" for="selectsett">
                        11 (dal 25/08 al 29/08)
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="selectsett" id="selectsett" value="12" >
                    <label class="form-check-label" for="selectsett">
                        12 (dal 02/09 al 05/09)
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="selectsett" id="selectsett" value="13" >
                    <label class="form-check-label" for="selectsett">
                        13 (dal 08/09 al 12/09)
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="selectsett" id="selectsett" value="14" >
                    <label class="form-check-label" for="selectsett">
                        14 (dal 15/09 al 19/09)
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="selectsett" id="selectsett" value="15" >
                    <label class="form-check-label" for="selectsett">
                        15 dal 22/09 al 26/09)
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="selectsett" id="selectsett" value="16" >
                    <label class="form-check-label" for="selectsett">
                        16 (dal 29/09 al 3/10)
                    </label>
                </div>
            </div>
            <br>
            <div class="col-md-12">
                <label for="selectdip">Al collega</label>
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
                <br>
                <label for="selectsett2"><u>In cambio</u> delle <b>settimane</b> numero</label>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="selectsett2" id="selectsett2" value="2" >
                    <label class="form-check-label" for="selectsett2">
                        2 (dal 23/06 al 27/06)
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="selectsett2" id="selectsett2" value="3" >
                    <label class="form-check-label" for="selectsett2">
                        3 (dal 30/06 al 05/07)
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="selectsett2" id="selectsett2" value="4" >
                    <label class="form-check-label" for="selectsett2">
                        4 (dal 07/07 al 11/07)
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="selectsett2" id="selectsett2" value="5" >
                    <label class="form-check-label" for="selectsett2">
                        5 (dal 14/07 al 18/07)
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="selectsett2" id="selectsett2" value="6" >
                    <label class="form-check-label" for="selectsett2">
                        6 (dal 21/07 al 25/07)
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="selectsett2" id="selectsett2" value="7" >
                    <label class="form-check-label" for="selectsett2">
                        7 (dal 28/07 al 01/08)
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="selectsett2" id="selectsett2" value="8" >
                    <label class="form-check-label" for="selectsett2">
                        8 (dal 04/08 al 08/08)
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="selectsett2" id="selectsett2" value="9" >
                    <label class="form-check-label" for="selectsett2">
                        9 (dal 11/08 al 15/08)
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="selectsett2" id="selectsett2" value="10" >
                    <label class="form-check-label" for="selectsett2">
                        10 (dal 18/08 al 22/08)
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="selectsett2" id="selectsett2" value="11" >
                    <label class="form-check-label" for="selectsett2">
                        11 (dal 25/08 al 29/08)
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="selectsett2" id="selectsett2" value="12" >
                    <label class="form-check-label" for="selectsett2">
                        12 (dal 02/09 al 05/09)
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="selectsett2" id="selectsett2" value="13" >
                    <label class="form-check-label" for="selectsett2">
                        13 (dal 08/09 al 12/09)
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="selectsett2" id="selectsett2" value="14" >
                    <label class="form-check-label" for="selectsett2">
                        14 (dal 15/09 al 19/09)
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="selectsett2" id="selectsett2" value="15" >
                    <label class="form-check-label" for="selectsett2">
                        15 dal 22/09 al 26/09)
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="selectsett2" id="selectsett2" value="16" >
                    <label class="form-check-label" for="selectsett2">
                        16 (dal 29/09 al 3/10)
                    </label>
                </div>
            </div>
        </div>
        <hr>
        <div class="col-md-12">
            <p>Le ferie saranno quindi svolte nelle settimane:</p>
            <input type="text" class="form-control" id="turnonuovo1Name" name="turnonuovo1Name" readonly>
            <input type="text" class="form-control" id="turnonuovo1Weeks" name="turnonuovo1Weeks" readonly>
        </div>
        <br>
        <div class="col-md-12">
            <input type="text" class="form-control" id="turnonuovo2Name" name="turnonuovo2Name" readonly>
            <input type="text" class="form-control" id="turnonuovo2Weeks" name="turnonuovo2Weeks" readonly>
        </div>
        <br>
        <div style="text-align: center">
            <div class="btn-group" role="group">
                <button type="submit" class="btn btn-sm btn-success" id="invia" name="invia"><i class="fas fa-check"></i> INVIA</button>
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