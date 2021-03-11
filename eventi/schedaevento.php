<?php
header('Access-Control-Allow-Origin: *');

session_start();
/**
 *
 * @author     Paolo Randone
 * @author     <mail@paolorandone.it>
 * @version    2.0
 * @note       Powered for Croce Verde Torino. All rights reserved
 *
 */

//parametri DB
include "../config/config.php";
if(isset($_GET["provenienza"])){
    $calendario = $_GET["provenienza"];
}

if (($_SESSION["livello"])<4){
    header("Location: ../error.php");
}

$dictionaryStato = array (
    1 => "Programmato",
    2 => "Pubblicato",
    3 => "Chiuso",
    4 => "Archiviato",
);
$dictionarySezione = array (
    1 => "Torino",
    2 => "Alpignano",
    3 => "Borgaro/Caselle",
    4 => "Ciriè",
    5 => "San Mauro",
    6 => "Venaria",
    7 => "",
);
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
if (isset($_GET["id"])){
    $id = $_GET["id"];
    $readonly = "readonly";
    $modifica = $db->query("SELECT * FROM events WHERE id='$id'")->fetch_array();

}
//aggiorna
if(isset($_POST["update"])){
    $id = $_POST["id"];
    $title = $_POST["title"];
    $start_event = $_POST["start_event"];
    $format= date_create($_POST['start_event']);
    $start= date_format($format, 'Y-m-d');
    $luogo = $_POST["luogo"];
    $note = $_POST["note"];
    $msa = $_POST["msa"];
    $msb = $_POST["msb"];
    $pma = $_POST["pma"];
    $squadre = $_POST["squadre"];
    $provenienza = $_POST["provenienza"];

    $update = $db->query("UPDATE events SET title='$title', start_event='$start', luogo='$luogo', note='$note', msa='$msa', msb='$msb', pma='$pma', squadre='$squadre' WHERE ID='$id'");
    if ($provenienza=='calendario'){
        echo '<script type="text/javascript">
        alert("Evento aggiornato con successo");
        location.href="calendar.php";
        </script>';
    }else{
        echo '<script type="text/javascript">
        alert("Evento aggiornato con successo");
        location.href="event.php";
        </script>';
    }
}

//pubblica evento
if (isset($_POST["aprievento"])){
    $id = $_POST["id"];
    $provenienza = $_POST["provenienza"];

    $aprievento = $db->query("UPDATE events SET stato=2 WHERE id='$id'");
    if ($provenienza=='calendario'){
        echo '<script type="text/javascript">
        alert("Evento aggiornato con successo");
        location.href="calendar.php";
        </script>';
    }else{
        echo '<script type="text/javascript">
        alert("Evento aggiornato con successo");
        location.href="event.php";
        </script>';
    }
}
//chiudi evento
if (isset($_POST["chiudievento"])){
    $id = $_POST["id"];
    $provenienza = $_POST["provenienza"];

    $chiudievento = $db->query("UPDATE events SET stato=3 WHERE id='$id'");
    if ($provenienza=='calendario'){
        echo '<script type="text/javascript">
        alert("Evento aggiornato con successo");
        location.href="calendar.php";
        </script>';
    }else{
        echo '<script type="text/javascript">
        alert("Evento aggiornato con successo");
        location.href="event.php";
        </script>';
    }
}
//archivia evento
if (isset($_POST["archivia"])){
    $id = $_POST["id"];
    $provenienza = $_POST["provenienza"];

    $archivia = $db->query("UPDATE events SET stato=4 WHERE ID='$id'");
    if ($provenienza=='calendario'){
        echo '<script type="text/javascript">
        alert("Evento aggiornato con successo");
        location.href="calendar.php";
        </script>';
    }else{
        echo '<script type="text/javascript">
        alert("Evento aggiornato con successo");
        location.href="event.php";
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
    <title>Gestione evento</title>
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

    <script>
        $( function() {
            $( "#start_event" ).datepicker({ dateFormat: 'dd-mm-yy' });
        } );
    </script>
</head>
<!-- NAVBAR -->
<?
if (isset($calendario)){
    echo "<div class=\"modal-header\">
                <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\">
                    <span aria-hidden=\"true\">&times;</span>
                </button>
            </div>";
}else{
    echo"<div class=\"container-fluid\">
    <nav aria-label=\"breadcrumb\">
        <ol class=\"breadcrumb\">
            <li class=\"breadcrumb-item\"><a href=\"../index.php\" style=\"color: #078f40\">Home</a></li>
            <li class=\"breadcrumb-item\"><a href=\"index.php\" style=\"color: #078f40\">Eventi e calendario</a></li>
            <li class=\"breadcrumb-item\"><a href=\"event.php\" style=\"color: #078f40\">Eventi</a></li>
            <li class=\"breadcrumb-item active\" aria-current=\"page\">".$modifica['title']."</li>
        </ol>
    </nav>
</div>";
}
?>

<body>
<div class="container-fluid">

    <div class="jumbotron">
        <form method="post" action="schedaevento.php">
            <input hidden id="id" name="id" value="<?=$id?>">
            <input hidden id="provenienza" name="provenienza" value="<?=$_GET["provenienza"]?>">
            <h1  style="text-align: center"><?=$modifica['title']?></h1>
            <h5  style="text-align: center">Stato evento: <?=$dictionaryStato[$modifica['stato']]?></h5>
            <hr>
            <form>
                <div class="form-row">
                    <div class="form-group col-md-5">
                        <label for="nome_evento">Nome</label>
                        <input type="text" class="form-control form-control-sm" id="title" name="title" value="<?=$modifica['title']?>">
                    </div>
                    <div class="form-group col-md-2">
                        <label for="data_evento">Data</label>
                        <input type="text" class="form-control form-control-sm" id="start_event" name="start_event" value="<?$start=date_create($modifica['start_event']); echo date_format($start, 'd-m-Y')?>">
                    </div>
                    <div class="form-group col-md-5">
                        <label for="luogo_evento">Luogo </label>
                        <input type="text" class="form-control form-control-sm" id="luogo" name="luogo" value="<?=$modifica['luogo']?>">
                    </div>
                </div>
                <div class="form-group">
                    <label for="note_evento">Note</label>
                    <textarea rows="4" maxlength="250" class="form-control form-control-sm" id="note" name="note"><?=$modifica['note']?></textarea>
                    <span id="conteggio" style="font-size: small; color: grey"></span>
                    <script type="text/javascript">
                        // avvio il controllo all'evento keyup
                        $('textarea#note').keyup(function() {
                            // definisco il limite massimo di caratteri
                            var limite = 250;
                            var quanti = $(this).val().length;
                            // mostro il conteggio in real-time
                            $('span#conteggio').html(quanti + ' / ' + limite);
                            // quando raggiungo il limite
                            if(quanti >= limite) {
                                // mostro un avviso
                                $('span#conteggio').html('<strong>Non puoi inserire più di ' + limite + ' caratteri!</strong>');
                                // taglio il contenuto per il numero massimo di caratteri ammessi
                                var $contenuto = $(this).val().substr(0,limite);
                                $('textarea#note').val($contenuto);
                            }
                        });
                    </script>
                </div>
                <div class="form-row" id="necessità">
                    <div class="form-group col-md-2">
                        <label for="msa">MSA</label>
                        <input type="text" class="form-control form-control-sm" id="msa" name="msa" value="<?=$modifica['msa']?>">
                    </div>
                    <div class="form-group col-md-2">
                        <label for="msb">MSB</label>
                        <input type="text" class="form-control form-control-sm" id="msb" name="msb" value="<?=$modifica['msb']?>">
                    </div>
                    <div class="form-group col-md-2">
                        <label for="pma">PMA</label>
                        <input type="text" class="form-control form-control-sm" id="pma" name="pma" value="<?=$modifica['pma']?>">
                    </div>
                    <div class="form-group col-md-2">
                        <label for="squadre">Squadre a piedi</label>
                        <input type="text" class="form-control form-control-sm" id="squadre" name="squadre" value="<?=$modifica['squadre']?>">
                    </div>
                    <div class="form-group col-md-2">
                        <div id="risorse"></div>
                        <script type="text/javascript">
                            // avvio il controllo all'evento keyup
                            $('#necessità').keyup(function() {
                                // definisco il limite massimo di caratteri
                                var MSA = ($('#msa').val()*2);
                                var MSB = ($('#msb').val()*2);
                                var PIEDI = ($('#squadre').val()*2);
                                // mostro il conteggio in real-time
                                $('div#risorse').html("Totale volontari: "+(MSA+MSB+PIEDI)+"<br>Totale autisti: "+((MSA+MSB)/2)+"<br>Totale militi: "+(PIEDI+((MSA+MSB)/2)));

                            });
                        </script>
                    </div>
                </div>
                <p>
                    <button <?if(($modifica["stato"])==2)echo "disabled"?> class="btn btn-primary" type="button" id="aprievento" name="aprievento" data-toggle="modal" data-target="#modalapri">Pubblica</button>
                    <button class="btn btn-primary" type="button" data-toggle="collapse" data-target=".multi-collapse" aria-expanded="false" aria-controls="disponibili confermati">DISPONIBILI / CONFERMATI</button>
                    <button class="btn btn-primary" type="button" >Invia conferma</button>
                    <button <?if(($modifica["stato"])==3)echo "disabled"?> class="btn btn-primary" type="button" id="chiudievento" name="chiudievento" data-toggle="modal" data-target="#modalchiudi">Chiudi</button>
                </p>
                <div class="row">
                    <div class="col">
                        <div class="collapse multi-collapse" id="disponibili">
                            <div class="card card-body">
                                <H5>Disponibilità</H5>
                                <?
                                if (($modifica["stato"])==1){
                                    echo "Attenzione, la raccolta delle disponibilità non è ancora attiva per questo evento";
                                }else{
                                    $idevento = $modifica["id"];
                                    $selectdisp = $db->query("SELECT id_utente FROM utenti_events WHERE id_evento='$idevento' order by id_utente");

                                    while($ciclodisp = $selectdisp->fetch_array()){
                                        $userdetails = $ciclodisp["id_utente"];
                                        $selectutente = $db->query("SELECT cognome, nome, sezione, squadra FROM utenti WHERE ID='$userdetails'")->fetch_array();
                                        echo $ciclodisp["id_utente"]." ".$selectutente["cognome"]." ".$selectutente["nome"]." / Sez. ".$dictionarySezione[$selectutente["sezione"]]." - Sq. ".$dictionarySquadra[$selectutente["squadra"]]."<br>";
                                    }
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="collapse multi-collapse" id="confermati">
                            <div class="card card-body">
                                <H5>Confermati</H5>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <center>
                    <div class="btn-group" role="group">
                        <button type="submit" class="btn btn-sm btn-outline-success" id="update" name="update"><i class="fas fa-check"></i></button>
                        <button type="button" class="btn btn-sm btn-outline-danger" data-toggle="modal" data-target="#modalarchivia"><i class="far fa-trash-alt"></i></button>
                    </div>
                    <br>
                    <font size="-1"><em><i class="fas fa-check" style="color: #1a712c"></i> Salva le modifiche e ritorna alla pagina di riepilogo<br>
                            <i class="far fa-trash-alt" style="color: #CC0000"></i> Archivia evento</em></font>
                </center>

                <!-- pubblica -->
                <div class="modal fade" id="modalapri" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
                        <div class="modal-content">
                            <div class="modal-body">
                                <p>Se confermi, l'evento diventerà pubblico e sarà visibile per l'inserimento della disponibilità.</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-secondary btn-sm" data-dismiss="modal">Annulla</button>
                                <button type="submit" class="btn btn-outline-success btn-sm" name="aprievento">Conferma</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- chiudi -->
                <div class="modal fade" id="modalchiudi" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
                        <div class="modal-content">
                            <div class="modal-body">
                                <p>Se confermi, l'evento verrà chiuso e non sarà più possibile inserire disponibilità.</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-secondary btn-sm" data-dismiss="modal">Annulla</button>
                                <button type="submit" class="btn btn-outline-success btn-sm" name="chiudievento">Conferma</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- archivia -->
                <div class="modal fade" id="modalarchivia" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
                        <div class="modal-content">
                            <div class="modal-body">
                                <p>Se confermi, l'evento verrà archiviato e non sarà più visibile.</p><p>Questa azione non potrà essere annullata.</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-secondary btn-sm" data-dismiss="modal">Annulla</button>
                                <button type="submit" class="btn btn-outline-danger btn-sm" name="archiviaevento">Conferma</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <div class="modal" id="modaldelete" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalCenterTitle">Confermi l'eliminazione?</h5>
                        </div>
                        <div class="modal-body">
                            <p>Premendo conferma, l'evento e tutti i contenuti caricati andranno persi</p>
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

</body>
<!-- FOOTER -->
<?php include('../config/include/footer.php'); ?>

</html>
