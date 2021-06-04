<?php
/**
 *
 * @author     Paolo Randone
 * @author     <mail@paolorandone.it>
 * @version    3.0
 * @note       Powered for Croce Verde Torino. All rights reserved
 *
 */
session_start();
//parametri DB
include "../config/config.php";
include "../config/include/destinatari.php";
//controllo accesso
if (($_SESSION["livello"])<4){
    header("Location: ../error.php");
}
//nicename livelli di accesso
$dictionaryLivello = array (
    1 => "Dipendente",
    2 => "Volontario",
    3 => "Altro",
    4 => "- Logistica",
    5 => "-- Segreteria",
    6 => "--- ADMIN",
);
//nicename sezioni
$dictionarySezione = array (
    1 => "Torino",
    2 => "Alpignano",
    3 => "Borgaro/Caselle",
    4 => "Ciriè",
    5 => "San Mauro",
    6 => "Venaria",
    7 => "",
);
//nicename squadre
$dictionarySquadra = array (
    1 => "Prima",
    2 => "Seconda",
    3 => "Terza",
    4 => "Quarta",
    5 => "Quinta",
    6 => "Sesta",
    7 => "Settima",
    8 => "Ottava",
    9 => "Nona",
    10 => "Sabato",
    11 => "Montagna",
    12 => "Direzione",
    13 => "Lunedì",
    14 => "Martedì",
    15 => "Mercoledì",
    16 => "Giovedì",
    17 => "Venerdì",
    18 => "Diurno",
    19 => "Giovani",
    20 => "Servizi Generali",
    21 => "Altro",
    22 => "",
);
//recupera variabili
if (isset($_GET["ID"])){
    $id = $_GET["ID"];
    $readonly = "readonly";
    $modifica = $db->query("SELECT * FROM utenti WHERE ID='$id'")->fetch_array();
}
//aggiorna
if(isset($_POST["update"])){
    $id = $_POST["xmatricola"];
    $cognomeL = $_POST["xcognome"];
    $cognome = strtoupper($cognomeL);
    $nomeL = $_POST["xnome"];
    $nome = strtoupper($nomeL);
    $email = $_POST["xemail"];
    $telefono = $_POST["xtelefono"];
    $livello = $_POST["xlivello"];
    $sezione = $_POST["xsezione"];
    $squadra = $_POST["xsquadra"];

    $update = $db->query("UPDATE utenti SET cognome='$cognome', nome='$nome', email='$email', telefono='$telefono', livello='$livello', sezione='$sezione', squadra='$squadra' WHERE ID='$id'");
    echo '<script type="text/javascript">
        alert("Aggiornamento effettuato con successo");
        location.href="index.php";
        </script>';

    if(isset($_POST["update"])){
        $email = $_POST['xemail'];
        $cognomeL = $_POST["xcognome"];
        $cognome = strtoupper($cognomeL);
        $nomeL = $_POST["xnome"];
        $nome = strtoupper($nomeL);
        $password = $_POST['xpassword'];
        $livellod = $dictionaryLivello[$_POST['xlivello']];
        $sezioned = $dictionarySezione[$_POST['xsezione']];
        $squadrad = $dictionarySquadra[$_POST['xsquadra']];
        //TODO modificare destinatario
        $to= $email;
        $subject="Riepilogo informazioni";
        $nome_mittente="Gestionale CVTO";
        $mail_mittente=$gestionale;
        $headers = "From: " .  $nome_mittente . " <" .  $mail_mittente . ">\r\n";
        $headers .= "Bcc: ".$randone."\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion();
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=iso-8859-1";

        $replace = array(
            '{{id}}',
            '{{password}}',
            '{{cognome}}',
            '{{nome}}',
            '{{livellod}}',
            '{{sezioned}}',
            '{{squadrad}}',
        );
        $with = array(
            $id,
            $password,
            $cognome,
            $nome,
            $livellod,
            $sezioned,
            $squadrad,
        );

        $corpo = file_get_contents('../config/template/reminder.html');
        $corpo = str_replace ($replace, $with, $corpo);

        mail($to, $subject, $corpo, $headers);

    }
}
//set inactive
if (isset($_POST["delete"])){
    $id = $_POST["xmatricola"];
    $elimina = $db->query("UPDATE utenti SET stato=0, email='', password='' WHERE ID='$id'");

    if ($elimina){
        echo '<script type="text/javascript">
        alert("UTENTE INATTIVATO CON SUCCESSO");
        location.href="index.php";
        </script>';
    }

    else {
        echo '<script type="text/javascript">
        alert("ERRORE");
        </script>';
    }

}
//generatore password
function generatePassword ( $length = 8 ): string
{
    $password = '';
    $possibleChars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $i = 0;
    while ($i < $length) {
        $char = substr($possibleChars, mt_rand(0, strlen($possibleChars)-1), 1);
        if (!strstr($password, $char)) {
            $password .= $char;
            $i++;
        }
    }
    return $password;
}

$pwd = generatePassword(8);

//resetpwd
if(isset($_POST["resetpwd"])){
    $id = $_POST["xmatricola"];
    $cognomeL = $_POST["xcognome"];
    $cognome = strtoupper($cognomeL);
    $nomeL = $_POST["xnome"];
    $nome = strtoupper($nomeL);
    $email = $_POST["xemail"];
    $livello = $_POST["xlivello"];

    $reset = $db->query("UPDATE utenti SET password='$pwd' WHERE ID='$id'");
    echo '<script type="text/javascript">
        alert("Password reimpostata con successo");
        location.href="index.php";
        </script>';

    if(isset($_POST["resetpwd"])){
        $email = $_POST['xemail'];
        $cognomeL = $_POST["xcognome"];
        $cognome = strtoupper($cognomeL);
        $nomeL = $_POST["xnome"];
        $nome = strtoupper($nomeL);
        $password = $pwd;
        //TODO modificare destinatario
        $to= $email;
        $nome_mittente="Gestionale CVTO";
        $mail_mittente=$gestionale;
        $headers = "From: " .  $nome_mittente . " <" .  $mail_mittente . ">\r\n";
        $headers .= "Bcc: ".$randone."\r\n";
        //$headers .= "Reply-To: " .  $mail_mittente . "\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion();
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=iso-8859-1";

        $subject="Reset password";

        $replace = array(
            '{{nome}}',
            '{{pwd}}',
        );
        $with = array(
            $nome,
            $pwd,
        );

        $corpo = file_get_contents('../config/template/reset.html');
        $corpo = str_replace ($replace, $with, $corpo);

        mail($to, $subject, $corpo, $headers);

    }
}

?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <meta name="author" content="Paolo Randone">
    <title>Gestione utenze</title>

    <? require "../config/include/header.html";?>

    <script>
        $(document).ready(function(){
            $('[data-toggle="popover"]').popover();
        });
    </script>

    <script>
        $(document).ready(function () {
            $('#indietro').on('click', function(){
                location.href='index.php';
            })
        })
    </script>
</head>
<!-- NAVBAR -->
<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../index.php" style="color: #078f40">Home</a></li>
            <li class="breadcrumb-item"><a href="index.php" style="color: #078f40">Utenze</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?=$id?></li>
        </ol>
    </nav>
</div>
<body>
<div class="container-fluid">
    <div class="row text-left">
        <div class="col-md-3 col-md-offset-3"></div>
        <div class="col-md-6">
            <div class="jumbotron">
                <form method="post" action="schedaoperatore.php">
                    <input hidden id="matricola" name="matricola" value="<?=$id?>">
                    <input hidden id="xpassword" name="xpassword" value="<?=$modifica['password']?>">
                    <h1  style="text-align: center"><?=$modifica['cognome']?> <?=$modifica['nome']?></h1>
                    <hr>
                    <div class="form-group">
                        <label for="xmatricola">Matricola</label>
                        <input id="xmatricola" name="xmatricola" type="text" class="form-control form-control-sm" <?=$readonly ?> value="<?=$id?>">
                    </div>
                    <div class="form-group">
                        <label for="xcognome">Cognome</label>
                        <input id="xcognome" name="xcognome" type="text" class="form-control form-control-sm" value="<?=$modifica['cognome']?>">
                    </div>
                    <div class="form-group">
                        <label for="xnome">Nome</label>
                        <input id="xnome" name="xnome" type="text" class="form-control form-control-sm" value="<?=$modifica['nome']?>">
                    </div>
                    <div class="form-group">
                        <label for="xemail">Email</label>
                        <input id="xemail" name="xemail" type="email" class="form-control form-control-sm" value="<?=$modifica['email']?>">
                    </div>
                    <div class="form-group">
                        <label for="xtelefono">Telefono</label>
                        <input id="xtelefono" name="xtelefono" type="text" class="form-control form-control-sm" value="<?=$modifica['telefono']?>">
                    </div>
                    <div class="form-group">
                        <label for="xsezione">Sezione</label>
                        <select class="form-control form-control-sm" id="xsezione" name="xsezione">
                            <?
                            if (($_SESSION["livello"])!=6){
                                for($a=1;$a<8;$a++){
                                    ($a==$modifica['sezione'])? $sel="selected" : $sel="";
                                    echo "<option disabled $sel value='$a'>".$dictionarySezione[$a]."</option>";
                                }
                            }else{
                                for($a=1;$a<8;$a++){
                                    ($a==$modifica['sezione'])? $sel="selected" : $sel="";
                                    echo "<option $sel value='$a'>".$dictionarySezione[$a]."</option>";
                                }
                            }

                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="xsquadra">Squadra</label>
                        <select class="form-control form-control-sm" id="xsquadra" name="xsquadra">
                            <?
                            if (($_SESSION["livello"])!=6){
                                for($a=1;$a<23;$a++){
                                    ($a==$modifica['squadra'])? $sel="selected" : $sel="";
                                    echo "<option disabled $sel value='$a'>".$dictionarySquadra[$a]."</option>";
                                }
                            }else{
                                for($a=1;$a<23;$a++){
                                    ($a==$modifica['squadra'])? $sel="selected" : $sel="";
                                    echo "<option $sel value='$a'>".$dictionarySquadra[$a]."</option>";
                                }
                            }

                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="xlivello">Livello</label>
                        <select class="form-control form-control-sm" id="xlivello" name="xlivello">
                            <?
                            if (($_SESSION["livello"])!=6){
                                for($a=1;$a<6;$a++){
                                    ($a==$modifica['livello'])? $sel="selected" : $sel="";
                                    echo "<option disabled $sel value='$a'>".$dictionaryLivello[$a]."</option>";
                                }
                            }else{
                                for($a=1;$a<7;$a++){
                                    ($a==$modifica['livello'])? $sel="selected" : $sel="";
                                    echo "<option $sel value='$a'>".$dictionaryLivello[$a]."</option>";
                                }
                            }

                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="xpassword">Password</label>
                        <input id="xpassword" name="xpassword" type="<?if(($_SESSION['ID'])!='D9999'){echo "password";}else{echo "text";} ?>" class="form-control form-control-sm" <?=$readonly ?> value="<?=$modifica['password']?>">
                    </div>
                    <button type="submit" id="resetpwd" name="resetpwd" class="btn btn-warning btn-sm"><i class="fas fa-sync-alt"></i> Reset password</button>
                    <hr>
                    <div style="text-align: center;">
                        <div class="btn-group" role="group">
                            <button type="submit" class="btn btn-sm btn-outline-success" id="update" name="update"><i class="fas fa-check"></i></button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="indietro" name="indietro"><i class="fas fa-undo"></i></button>
                            <button type="button" class="btn btn-sm btn-outline-danger" data-toggle="modal" data-target="#modaldelete" <?php if (($_SESSION["livello"])!=6){ echo "disabled";} ?>><i class="far fa-trash-alt"></i></button>
                        </div>
                        <br>
                        <font size="-1"><em>Confermando <i class="fas fa-check" style="color: #1a712c"></i>, sarà inviata una mail all'utente con il riepilogo delle informazioni<br>Per ritornare alla lista degli operatori senza apporatare modifiche, utilizzare il pulsante grigio <i class="fas fa-undo" style="color: #595959"></i><br>Per rendere inattivo l'utente, premere il pulsante rosso <i class="far fa-trash-alt" style="color: #CC0000"></i></em></font>
                    </div>
                    <!-- delete -->
                    <div class="modal" id="modaldelete" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalCenterTitle">Confermi l'eliminazione?</h5>
                                </div>
                                <div class="modal-body">
                                    <p>Premendo conferma, l'utente verrà disabilitato e non potrà più accedere al portale.</p>
                                    <p>Le checklist compilate e i dati archiviati saranno salvati e rimarranno consultabili.</p>
                                    <p>Questa azione non potrà essere annullata.</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-outline-secondary btn-sm" data-dismiss="modal">Annulla</button>
                                    <button type="submit" class="btn btn-danger btn-sm" name="delete">Conferma</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
<!-- FOOTER -->
<?php include('../config/include/footer.php'); ?>

</html>
