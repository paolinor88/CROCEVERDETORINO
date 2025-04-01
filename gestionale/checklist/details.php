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
//parametri DB
include "../config/config.php";
include "../config/include/destinatari.php";

//controllo LOGIN / accesso consentito a logistica, segreteria e ADMIN
if (($_SESSION["livello"])<4){
    header("Location: ../error.php");
}
//GET SEGNALAZIONE
if (isset($_GET["ID"])){
    $id = $_GET["ID"];
    $modifica = $db->query("SELECT * FROM checklist WHERE IDCHECK='$id'")->fetch_array();
    $idcompilatore = $modifica['IDOPERATORE'];
}
//compilatore
$select = $db->query("SELECT ID, cognome, nome, squadra, sezione, email FROM utenti WHERE ID='$idcompilatore'")->fetch_array();

//variabile sessione
$idmittente= $_SESSION['ID'];
$cognomemittente = $_SESSION['cognome'];
$nomemittente = $_SESSION['nome'];
$emailmittente = $_SESSION['email'];
$squadramittente = $_SESSION['squadra'];
$sezionemittente = $_SESSION['sezione'];

//nicename livelli
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
    1 => "TO",
    2 => "AL",
    3 => "BC",
    4 => "CI",
    5 => "SM",
    6 => "VE",
    7 => "DIP",
    8 => "SCN",
);
//nicename sezioni
$dictionarySquadra = array (
    1 => "1",
    2 => "2",
    3 => "3",
    4 => "4",
    5 => "5",
    6 => "6",
    7 => "7",
    8 => "8",
    9 => "9",
    10 => "SAB",
    11 => "MON",
    12 => "DDS",
    13 => "Lunedì",
    14 => "Martedì",
    15 => "Mercoledì",
    16 => "Giovedì",
    17 => "Venerdì",
    18 => "DIU",
    19 => "Giovani",
    20 => "Servizi Generali",
    21 => "Altro",
    22 => "TO",
    23 => "TO",
);

if(isset($_POST["reply"])) {
    $idmezzo = $_POST['idmezzo'];
    $idcompilatore = $_POST['idcompilatore'];
    $nomecompilatore = $_POST['nomecompilatore'];
    $cognomecompilatore = $_POST['cognomecompilatore'];
    $squadracompilatore = $_POST['squadracompilatore'];
    $sezionecompilatore = $_POST['sezionecompilatore'];
    $emailcompilatore = $_POST['emailcompilatore'];
    $note = $_POST['note'];
    $risposta = $_POST["risposta"];

    //TODO modificare destinatario

    $to= $emailcompilatore;//.', '.$bechis;
    $nome_mittente="Checklist CVTO";
    $mail_mittente=$checklist;
    $headers = "From: " .  $nome_mittente . " <" .  $mail_mittente . ">\r\n";
    $headers .= "Bcc: ".$comunicazioni."\r\n";
    //$headers .= "Reply-To: " .  $mail_mittente . "\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=iso-8859-1";
    $subject = "Re: Segnalazione auto ".$idmezzo."";
    $corpo = "
        <html lang='it'>
            <body>
                <p>[".$idmittente."] ".$nomemittente." ".$cognomemittente." ha risposto:</p>
                <p>**</p>
                <p>".$risposta."</p>
                <p>**</p>
                <br>
                <p>Al messaggio inviato da [".$idcompilatore."] ".$nomecompilatore." ".$cognomecompilatore." (".$dictionarySquadra[$squadracompilatore]." ".$dictionarySezione[$sezionecompilatore]."):</p>
                <p>**</p>
                <p>".$note."</p>
                <p>**</p>
                

            </body>
        </html>";

    mail($to, $subject, $corpo, $headers);

        echo '<script type="text/javascript">
        alert("Risposta inviata con successo");
        location.href="archivio.php";
        </script>';


}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <meta name="author" content="Paolo Randone">
    <title>Gestisci segnalazione</title>

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
            <li class="breadcrumb-item"><a href="index.php" style="color: #078f40">Checklist</a></li>
            <li class="breadcrumb-item"><a href="archivio.php" style="color: #078f40">Archivio</a></li>
            <li class="breadcrumb-item active" aria-current="page">Segnalazione</li>
        </ol>
    </nav>
</div>
<!--content-->
<body>
<div class="container-fluid">
    <div class="row text-left">
    <div class="col-md-3 col-md-offset-3"></div>
    <div class="col-md-6">
        <div class="jumbotron">
            <form method="post" action="details.php">
                <input hidden id="xcheck" name="xcheck" value="<?=$id?>">
                <p>AUTO: <b><?=$modifica['IDMEZZO']?></b></p>
                <p>DATA: <?$var=$modifica["DATACHECK"];$var1=date_create("$var");echo $datatesto=date_format($var1, "d/m/Y");?> ORE: <?$var=$modifica["DATACHECK"];$var1=date_create("$var");echo $oratesto=date_format($var1, "H:i");?> </p>
                <p>SEZIONE: <?=$dictionarySezione[$select['sezione']]?></p>
                <p>SQUADRA: <?=$dictionarySquadra[$select['squadra']]?></p>
                <p>COMPILATORE: <b><?=$select['cognome']?> <?=$select['nome']?></b></p>
                <p>MATRICOLA: <?=$modifica['IDOPERATORE']?></p>
                <hr>
                <div class="form-group">
                    <textarea class="form-control" id="xnote" name="xnote" readonly rows="15"><?=$modifica['NOTE']?></textarea>
                </div>
                <div style="text-align: center;">
                    <div class="btn-group" role="group">
                        <button type="button" id="rispondi" name="rispondi" class="btn btn-sm btn-outline-info" aria-label="Rispondi" data-toggle="modal" data-target="#modalrisposta"><i class="fas fa-reply"></i></button>

                        <a href="archivio.php" class="btn btn-sm btn-outline-secondary" id="indietro"><i class="fas fa-undo"></i></a>

                    </div>
                    <br>
                   <span style="font-size: smaller; "><em>Premi il pulsante <i class="fas fa-reply" style="color: steelblue"></i> per rispondere, oppure  <i class="fas fa-undo" style="color: grey"></i> per tornare alla pagina precedente</em></span>
                </div>
            </form>
        </div>
    </div>
</div>


</body>
<!-- Modal -->
<form action="details.php" method="post">
    <div class="modal" id="modalrisposta" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="exampleModalLabel">Rispondi a <?= $modifica['IDOPERATORE'] ?></h6>
                    <input hidden id="idmezzo" name="idmezzo" value="<?=$modifica['IDMEZZO']?>">
                    <input hidden id="idcompilatore" name="idcompilatore" value="<?=$select['ID']?>">
                    <input hidden id="cognomecompilatore" name="cognomecompilatore" value="<?=$select['cognome']?>">
                    <input hidden id="nomecompilatore" name="nomecompilatore" value="<?=$select['nome']?>">
                    <input hidden id="sezionecompilatore" name="sezionecompilatore" value="<?=$select['sezione']?>">
                    <input hidden id="squadracompilatore" name="squadracompilatore" value="<?=$select['squadra']?>">
                    <input hidden id="emailcompilatore" name="emailcompilatore" value="<?=$select['email']?>">
                    <input hidden id="note" name="note" value="<?=$modifica['NOTE']?>">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <textarea class="form-control" name="risposta" rows="15" placeholder="Digita qui la risposta" autofocus></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="reply" class="btn btn-info btn-sm" >Invia</button>
                </div>
            </div>
        </div>
    </div>
</form>


<!-- FOOTER -->
<?php include('../config/include/footer.php'); ?>

</html>