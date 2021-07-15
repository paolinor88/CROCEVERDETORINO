<?php
/**
 *
 * @author     Paolo Randone
 * @author     <mail@paolorandone.it>
 * @version    3.2
 * @note       Powered for Croce Verde Torino. All rights reserved
 *
 */
session_start();
//parametri DB
include "config/config.php";
include "config/include/destinatari.php";
//login
if (isset($_SESSION["ID"])){
    header("Location: index.php");
}
if (isset($_POST["LoginBTN"])){
    $id = $_POST["matricolaOP"];
    $password = $_POST["passwordOP"];

    $query = $db->query("SELECT * FROM utenti WHERE ID='$id' AND password='$password'");

    if ($query->num_rows>0){
        list($id, $cognome, $nome, $email, $cf, $password, $telefono, $ciclico, $livello, $stato, $sezione, $squadra)= $query->fetch_array();

        $_SESSION["ID"]=$id;
        $_SESSION["cognome"]=$cognome;
        $_SESSION["nome"]=$nome;
        $_SESSION["email"]=$email;
        $_SESSION["cf"]=$cf;
        $_SESSION["password"]=$password;
        $_SESSION["telefono"]=$telefono;
        $_SESSION["ciclico"]=$ciclico;
        $_SESSION["livello"]=$livello;
        $_SESSION["stato"]=$stato;
        $_SESSION["sezione"]=$sezione;
        $_SESSION["squadra"]=$squadra;
        header("Location: index.php");
    }
    else{
        echo "<script type='text/javascript'>alert('Accesso negato')</script>";
    }
}
//nicename accesso
$dictionaryLivello = array (
    1 => "Dipendente",
    2 => "Volontario",
    3 => "Altro",
    4 => "Logistica",
    5 => "Segreteria",
    6 => "ADMIN",
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
//nicename sezioni
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

//attiva utente
if(isset($_POST["activateBTN"])){
    $id = strtoupper($_POST["matricola"]);
    $email = $_POST["email"];
    $cf = strtoupper($_POST["cf"]);
    $password = $_POST["password"];
    $querycheck = $db->query("SELECT cognome, nome, stato FROM utenti WHERE ID='$id' AND cf='$cf'");
    $checkstato = $querycheck->fetch_array();
    if (($querycheck->num_rows>0)&&($checkstato['stato']==0)){
        $query = $db->query("UPDATE utenti SET email='$email', stato=1, password='$password' WHERE ID='$id'");
        $var = $db->query("SELECT cognome, nome, livello, sezione, squadra FROM utenti WHERE ID='$id'")->fetch_array();
        $cognome = $var['cognome'];
        $nome = $var['nome'];
        $livello =strtoupper($dictionaryLivello[$var['livello']]);
        $sezione = $dictionarySezione[$var['sezione']];
        $squadra = $dictionarySquadra[$var['squadra']];
        //TODO email
        $to= $email;
        $subject="Attivazione utenza";
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
            '{{livello}}',
            '{{sezione}}',
            '{{squadra}}',
        );
        $with = array(
            $id,
            $password,
            $cognome,
            $nome,
            $livello,
            $sezione,
            $squadra,
        );

        $corpo = file_get_contents('config/template/active.html');
        $corpo = str_replace ($replace, $with, $corpo);

        mail($to, $subject, $corpo, $headers);

        echo "<script type='text/javascript'>alert('Utente attivato con successo.\\nLe credenziali di accesso saranno inviate via mail entro 24 ore')</script>";

        if ($var){ // CC ADMIN
            $destinatario=$gestionale;
            $oggetto="Attivazione utenza $id $cognome $nome" ;
            $nome_mittente="Gestionale CVTO";
            $mail_mittente=$gestionale;
            $headers = "From: " .  $nome_mittente . " <" .  $mail_mittente . ">\r\n";
            //$headers .= "Bcc: ".$destinatario."\r\n";
            $headers .= "X-Mailer: PHP/" . phpversion();
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-type: text/html; charset=iso-8859-1";

            $replace = array(
                '{{id}}',
                '{{livello}}',
                '{{cognome}}',
                '{{nome}}',
            );
            $with = array(
                $id,
                $livello,
                $cognome,
                $nome,
            );

            $corpo = file_get_contents('config/template/ccactive.html');
            $corpo = str_replace ($replace, $with, $corpo);

            mail($destinatario, $oggetto, $corpo, $headers);
        } //end CC

    }else{
        echo "<script type='text/javascript'>alert('ERRORE. Se avevi già effettuato la registrazione e non ricordi più la password, scrivi a mail@paolorandone.it')</script>";
    }
}

?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <meta name="author" content="Paolo Randone">
    <link rel="apple-touch-icon" sizes="57x57" href="config/favicon/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="config/favicon/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="config/favicon/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="config/favicon/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="config/favicon/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="config/favicon/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="config/favicon/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="config/favicon/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="config/favicon/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192"  href="config/favicon/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="config/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="config/favicon/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="config/favicon/favicon-16x16.png">
    <link rel="manifest" href="config/favicon/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">
    <title>Gestionale CVTO - Login</title>

    <link rel="stylesheet" href="config/css/bootstrap.min.css">
    <link href='https://use.fontawesome.com/releases/v5.0.6/css/all.css' rel='stylesheet'>
    <link rel="stylesheet" href="config/css/custom.css">

    <script src="https://code.jquery.com/jquery-3.4.1.js" integrity="sha256-WpOohJOqMqqyKL9FccASB9O0KwACQJpFTUBLTYOVvVU=" crossorigin="anonymous"></script>
    <script src="config/js/bootstrap.bundle.min.js"></script>

    <script>
        $(document).ready(function(){
            $('[data-toggle="popover"]').popover();
        });
    </script>
</head>
<body>
<form action="login.php" method="post">
    <div class="container-fluid">
        <br>
        <div class="row text-center">
            <div class="col-md-3 col-md-offset-3"></div>
            <div class="text-center col-md-6">
                <div class="jumbotron">
                    <div align="center"><img class="img-fluid" src="config/images/logo.png" alt="logocvto"/></div>
                    <h4 class="text-center" style="color: #078f40">Accedi</h4>
                    <hr>
                    <div class="form-group">
                        <input type="text" id="matricolaOP" name="matricolaOP" class="form-control form-control-sm" placeholder="Matricola (es. V4512)" pattern="[D|V|C0-9]{5}">
                    </div> <!-- matricola -->
                    <div class="form-group">
                        <input type="password" id="passwordOP" name="passwordOP" class="form-control form-control-sm" placeholder="Password" >
                    </div> <!-- password -->
                    <div class="btn-group" role="group">
                        <button type="submit" class="btn btn-outline-primary btn-sm" id="LoginBTN" name="LoginBTN">Accedi</button>
                        <button type="button" data-toggle="modal" data-target="#modal1" class="btn btn-outline-success btn-sm" id="RegisterBTN" name="RegisterBTN">Registrati</button>
                    </div>
                    <br>
                    <div class="text-center">
                        <span style="font-size: 70%; "><a href="mailto: mail@paolorandone.it">Problemi di accesso?</a></span>
                    </div>
                    <hr>
                    <div class="text-center">
                        <a style="color: #078f40" href="https://croceverde.org/gestionale/istruzioni_gestionale_COMPLETE_v3.2.pdf" target="_blank">MANUALE UTENTE</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
</body>
<!-- MODAL Attivazione -->
<form action="login.php" method="post">
    <div class="modal" id="modal1" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal1Title">Attivazione utenza</h5>
                </div>
                <div class="modal-body">
                    <input hidden id="password" name="password" value="<?=$pwd?>">
                    <div class="form-group">
                        <label for="id">Matricola</label>
                        <input type="text" class="form-control form-control-sm" id="matricola" name="matricola" list="matricole" required>
                            <datalist id="matricole">
                            <?
                            $select = $db->query("SELECT ID FROM utenti WHERE stato!='1' ORDER BY ID");
                            while($ciclo = $select->fetch_array()){
                                echo "<option value=\"".$ciclo['ID']."\">".$ciclo['ID']."</option>";
                            }
                            ?>
                            </datalist>
                    </div> <!-- matricola -->
                    <div class="form-group">
                        <label for="cf">Codice fiscale</label>
                        <input id="cf" name="cf" class="form-control form-control-sm" required>
                    </div> <!-- cf -->
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control form-control-sm" id="email" name="email" aria-describedby="emailHelp" required>
                    </div> <!-- email -->
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="" id="defaultCheck1" required>
                        <label style="font-size: x-small" class="form-check-label" for="defaultCheck1">
                            Ho letto e accetto i <a href="https://github.com/paolinor88/gestionalecvto/blob/master/LICENSE.txt" target="_blank">termini di licenza</a>
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-dismiss="modal">Chiudi</button>
                    <button type="submit" class="btn btn-outline-success btn-sm" id="activateBTN" name="activateBTN">Attiva</button>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- FOOTER -->
<footer class="container-fluid">
    <div class="text-center">
        <font size="-4" style="color: lightgray; "><em>Powered for <a href="mailto:gestioneutenti@croceverde.org">Croce Verde Torino</a>. All rights reserved.<p>V 3.2</p></em></font>
    </div>
</footer>
</html>