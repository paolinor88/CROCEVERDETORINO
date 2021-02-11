<?php
/**
 *
 * @author     Paolo Randone
 * @author     <mail@paolorandone.it>
 * @version    1.6
 * @note       Powered for Croce Verde Torino. All rights reserved
 *
 */
session_start();
//connessione DB
include "../config/config.php";
//login
if (!isset($_SESSION["ID"])){
    header("Location: login.php");
}
//nicename tipo
$dictionaryTipo = array (
    1 => "MSB",
    2 => "MSA",
    3 => "FLOTTA 118",
);
//controllo login
if (isset($_POST['IDMEZZO'])){
    $idoperatore = $_SESSION['ID'];
    $idmezzo = $_POST['IDMEZZO'];
    $cognome = $_SESSION['cognome'];
    $nome = $_SESSION['nome'];
    $select = $db->query("SELECT * FROM mezzi WHERE ID='$idmezzo' AND stato='1'")->fetch_array();
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <meta name="author" content="Paolo Randone">
    <title>Inserisci checklist</title>

    <!-- CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link href='https://use.fontawesome.com/releases/v5.0.6/css/all.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/4.2.0/bootstrap/main.css" integrity="sha256-x0YxbDRtAIaUla1aUzriI3mpIDGtu8IhYeY3QV+BkNU=" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/4.2.0/core/main.css" integrity="sha256-nJK+Jim06EmZazdCbGddx5ixnqfXA13Wlw3JizKK1GU=" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/4.2.0/daygrid/main.css" integrity="sha256-QG5qcyovbK2zsUkGMWTVn0PZM1P7RVx0Z05QwB9dCeg=" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/4.2.0/list/main.css" integrity="sha256-gpHlSx15cEHBt1GPo8ga6S1vK7UmzF+T340tdS1/Y58=" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/4.2.0/timegrid/main.css" integrity="sha256-UpqyFskjj8q6ioNCrwGzObqiE56OxEYuSBXUHGqDBII=" crossorigin="anonymous" />
    <link rel="stylesheet" href="../config/css/custom.css">

    <!-- JS Libraries -->
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.4.1.js" integrity="sha256-WpOohJOqMqqyKL9FccASB9O0KwACQJpFTUBLTYOVvVU=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <script src="../config/js/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/4.2.0/bootstrap/main.js" integrity="sha256-YLvGa/6UrzsYa6pgPIwxuiXtsS854c/pImjL3kfK+sY=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/4.2.0/core/locales/it.js" integrity="sha256-HTUuCsuY6mAPyqtUnnQVzvefPfqmi39vIaasHuVekGc=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/4.2.0/core/main.js" integrity="sha256-F4ovzqUMsKm41TQVQO+dWHQA+sshyOUdmnDcTPMIHkM=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/4.2.0/daygrid/main.js" integrity="sha256-I1bdnmA3OtkQwlbwNbJQ2y+kH2fIXfnIjhAfYhxJqiY=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/4.2.0/interaction/main.js" integrity="sha256-MJ15XCTL71Z+hio+iodzZBMJFmxsuOCnozIN43XBJ5k=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/4.2.0/list/main.js" integrity="sha256-2KsOec1MfjERCcgKf5TJrQo/Zu88GspTdI9ZeX8XRPg=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/4.2.0/moment/main.js" integrity="sha256-rjGqYkUy/H0HOInAH/bCkT7AbIEQ2oRath9cz7k7pL4=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/4.2.0/google-calendar/main.js" integrity="sha256-brhVVWkR/rWC0bW6rGoC+1WhKOzOJ4ZcgB0rwG6yv88=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/4.2.0/timegrid/main.js" integrity="sha256-Q7vy6GHSfPnAFHnyM58AAI+jLJRBr3o7VHGlu+6mUlY=" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>

    <script>
        $(document).ready(function(){
            $('[data-toggle="popover"]').popover();
        });
    </script>
    <!-- STRUTTURA CHECKLIST -->
    <script>
        $(document).ready(function() {
            $('#inviacheck').on('click', function(){
                var IDMEZZO = $("#IDMEZZO").val();
                var IDOPERATORE = $("#IDOPERATORE").val();
                var tipo = $("#tipo").val();
                var DATACHECK = moment().format('YYYY-MM-DD HH:mm:ss');
                var LAVAGGIO = $("#lavaggioesterno:checked").val();
                var SCADENZE = $("#scadenze:checked").val();
                var prova = $("#prova option:selected").val();
                //AMBULANZA
                var spinale = $("#spinale").prop("checked") ? 'OK' : 'MANCANTE';
                var scoop = $("#scoop").prop("checked") ? 'OK' : 'MANCANTE';
                var collari = $("#collari").prop("checked") ? 'OK' : 'MANCANTE';
                var elettrodi = $("#elettrodi").prop("checked") ? 'OK' : 'MANCANTE';
                var gel = $("#gel").prop("checked") ? 'OK' : 'MANCANTE';
                var ecg = $("#ecg").prop("checked") ? 'OK' : 'MANCANTE';
                var sixlead = $("#6lead").prop("checked") ? 'OK' : 'MANCANTE';
                var fourlead = $("#4lead").prop("checked") ? 'OK' : 'MANCANTE';
                var saturimetro = $("#saturimetro").prop("checked") ? 'OK' : 'MANCANTE';
                var pacing = $("#pacing").prop("checked") ? 'OK' : 'MANCANTE';
                var circuitoventilatore = $("#circuitoventilatore").prop("checked") ? 'OK' : 'MANCANTE';
                var maschere = $("#maschere").prop("checked") ? 'OK' : 'MANCANTE';
                var piastre = $("#piastre").prop("checked") ? 'OK' : 'MANCANTE';
                var LP = $("#LP").prop("checked") ? 'OK' : 'MANCANTE';
                var cavoLP = $("#cavoLP").prop("checked") ? 'OK' : 'MANCANTE';
                var batterieLP = $("#batterieLP").prop("checked") ? 'OK' : 'MANCANTE';
                var aspiratore = $("#aspiratore").prop("checked") ? 'OK' : 'MANCANTE';
                var ventilatore = $("#ventilatore").prop("checked") ? 'OK' : 'MANCANTE';
                var cavovent12 = $("#cavovent12").prop("checked") ? 'OK' : 'MANCANTE';
                var cavovent220 = $("#cavovent220").prop("checked") ? 'OK' : 'MANCANTE';
                var pompa = $("#pompa").prop("checked") ? 'OK' : 'MANCANTE';
                var cavopompa12 = $("#cavopompa12").prop("checked") ? 'OK' : 'MANCANTE';
                var cavopompa220 = $("#cavopompa220").prop("checked") ? 'OK' : 'MANCANTE';
                var bombolefisse = $("#bombolefisse").prop("checked") ? 'OK' : 'MANCANTE';
                var taglienti = $("#taglienti").prop("checked") ? 'OK' : 'MANCANTE';
                var DAE = $("#DAE").prop("checked") ? 'OK' : 'MANCANTE';
                var lenzuola = $("#lenzuola").prop("checked") ? 'OK' : 'MANCANTE';
                var cpap = $("#cpap").prop("checked") ? 'OK' : 'MANCANTE';
                var pedimate = $("#pedimate").prop("checked") ? 'OK' : 'MANCANTE';
                var guanti = $("#guanti").prop("checked") ? 'OK' : 'MANCANTE';
                var sedia = $("#sedia").prop("checked") ? 'OK' : 'MANCANTE';
                var KED = $("#KED").prop("checked") ? 'OK' : 'MANCANTE';
                var steccobende = $("#steccobende").prop("checked") ? 'OK' : 'MANCANTE';
                var bomboleport = $("#bomboleport").prop("checked") ? 'OK' : 'MANCANTE';
                var caschi = $("#caschi").prop("checked") ? 'OK' : 'MANCANTE';
                var padella = $("#padella").prop("checked") ? 'OK' : 'MANCANTE';
                var carta = $("#carta").prop("checked") ? 'OK' : 'MANCANTE';
                var amputazioni = $("#amputazioni").prop("checked") ? 'OK' : 'MANCANTE';
                var ragno = $("#ragno").prop("checked") ? 'OK' : 'MANCANTE';
                var trauma = $("#trauma").prop("checked") ? 'OK' : 'MANCANTE';
                var cinghie = $("#cinghie").prop("checked") ? 'OK' : 'MANCANTE';
                var estintorepost = $("#estintorepost").prop("checked") ? 'OK' : 'MANCANTE';
                var coltrino = $("#coltrino").prop("checked") ? 'OK' : 'MANCANTE';
                var coperta = $("#coperta").prop("checked") ? 'OK' : 'MANCANTE';
                var traslatore = $("#traslatore").prop("checked") ? 'OK' : 'MANCANTE';
                var estintoreant = $("#estintoreant").prop("checked") ? 'OK' : 'MANCANTE';
                var faro = $("#faro").prop("checked") ? 'OK' : 'MANCANTE';
                var scasso = $("#scasso").prop("checked") ? 'OK' : 'MANCANTE';
                var bloccocv = $("#bloccocv").prop("checked") ? 'OK' : 'MANCANTE';
                var schede118 = $("#schede118").prop("checked") ? 'OK' : 'MANCANTE';
                var fuoriservizio = $("#fuoriservizio").prop("checked") ? 'OK' : 'MANCANTE';
                var antifiamma = $("#antifiamma").prop("checked") ? 'OK' : 'MANCANTE';
                var panseptil = $("#panseptil").prop("checked") ? 'OK' : 'MANCANTE';
                var luci = $("#luci").prop("checked") ? 'OK' : 'NON FUNZIONANTE (vedi note)';
                var blu = $("#blu").prop("checked") ? 'OK' : 'NON FUNZIONANTE (vedi note)';
                var sirene = $("#sirene").prop("checked") ? 'OK' : 'NON FUNZIONANTE (vedi note)';
                var gasolio = $("#gasolio").prop("checked") ? 'OK' : 'MANCANTE';
                var telepass = $("#telepass").prop("checked") ? 'OK' : 'MANCANTE';
                var doc = $("#doc").prop("checked") ? 'OK' : 'MANCANTE';
                var cartaagip = $("#cartaagip").prop("checked") ? 'OK' : 'MANCANTE';
                var lavaggioesterno = $("#lavaggioesterno").prop("checked") ? 'EFFETTUATO' : 'NON EFFETTUATO';
                var lavaggiointerno = $("#lavaggiointerno").prop("checked") ? 'EFFETTUATO' : 'NON EFFETTUATO';
                var disinfezione = $("#disinfezione").prop("checked") ? 'EFFETTUATO' : 'NON EFFETTUATO';
                var battesedia = $("#battesedia").prop("checked") ? 'OK' : 'MANCANTE';
                //
                var scadenzeborsa = $("#scadenze").prop("checked") ? 'EFFETTUATO' : 'NON EFFETTUATO';
                var ambuped = $("#ambuped").prop("checked") ? 'OK' : 'MANCANTE';
                var reservoirped = $("#reservoirped").prop("checked") ? 'OK' : 'MANCANTE';
                var filtroped = $("#filtroped").prop("checked") ? 'OK' : 'MANCANTE';
                var maschereped = $("#maschereped").prop("checked") ? 'OK' : 'MANCANTE';
                var guedelped = $("#guedelped").prop("checked") ? 'OK' : 'MANCANTE';
                var ossped = $("#ossped").prop("checked") ? 'OK' : 'MANCANTE';
                var ambuadulti = $("#ambuadulti").prop("checked") ? 'OK' : 'MANCANTE';
                var reservoiradulti = $("#reservoiradulti").prop("checked") ? 'OK' : 'MANCANTE';
                var filtroadulti = $("#filtroadulti").prop("checked") ? 'OK' : 'MANCANTE';
                var maschereadulti = $("#maschereadulti").prop("checked") ? 'OK' : 'MANCANTE';
                var guedeladulti = $("#guedeladulti").prop("checked") ? 'OK' : 'MANCANTE';
                var ossadulti = $("#ossadulti").prop("checked") ? 'OK' : 'MANCANTE';
                var fisio = $("#fisio").prop("checked") ? 'OK' : 'MANCANTE';
                var h2o2 = $("#h2o2").prop("checked") ? 'OK' : 'MANCANTE';
                var betadine = $("#betadine").prop("checked") ? 'OK' : 'MANCANTE';
                var cerotti = $("#cerotti").prop("checked") ? 'OK' : 'MANCANTE';
                var benda = $("#benda").prop("checked") ? 'OK' : 'MANCANTE';
                var garze = $("#garze").prop("checked") ? 'OK' : 'MANCANTE';
                var ghiaccio = $("#ghiaccio").prop("checked") ? 'OK' : 'MANCANTE';
                var arterioso = $("#arterioso").prop("checked") ? 'OK' : 'MANCANTE';
                var venoso = $("#venoso").prop("checked") ? 'OK' : 'MANCANTE';
                var rasoio = $("#rasoio").prop("checked") ? 'OK' : 'MANCANTE';
                var sfigmo = $("#sfigmo").prop("checked") ? 'OK' : 'MANCANTE';
                var fonendo = $("#fonendo").prop("checked") ? 'OK' : 'MANCANTE';
                var saturimetrob = $("#saturimetrob").prop("checked") ? 'OK' : 'MANCANTE';
                var termometro = $("#termometro").prop("checked") ? 'OK' : 'MANCANTE';
                var sondini = $("#sondini").prop("checked") ? 'OK' : 'MANCANTE';
                var maschereborsa = $("#maschereborsa").prop("checked") ? 'OK' : 'MANCANTE';
                var robin = $("#robin").prop("checked") ? 'OK' : 'MANCANTE';
                var guantisterili = $("#guantisterili").prop("checked") ? 'OK' : 'MANCANTE';
                var telini = $("#telini").prop("checked") ? 'OK' : 'MANCANTE';
                var metalline = $("#metalline").prop("checked") ? 'OK' : 'MANCANTE';
                var spazzatura = $("#spazzatura").prop("checked") ? 'OK' : 'MANCANTE';
                var pappagallo = $("#pappagallo").prop("checked") ? 'OK' : 'MANCANTE';
                var dpi = $("#dpi").prop("checked") ? 'OK' : 'MANCANTE';
                var chirurgiche = $("#chirurgiche").prop("checked") ? 'OK' : 'MANCANTE';
                var monossido = $("#monossido").prop("checked") ? 'OK' : 'MANCANTE';
                //
                var note = $("#note").val();
                swal({
                    text: "Confermare invio?",
                    icon: "warning",
                    buttons:{
                        cancel:{
                            text: "Annulla",
                            value: null,
                            visible: true,
                            closeModal: true,
                        },
                        confirm:{
                            text: "Conferma",
                            value: true,
                            visible: true,
                            closeModal: true,
                        },
                    },
                })
                    .then((confirm) => {
                        if(confirm){
                            //alert(LAVAGGIO)
                            $.ajax({
                                url:"send.php",
                                type:"POST",
                                data:{prova:prova, IDMEZZO:IDMEZZO, IDOPERATORE:IDOPERATORE, tipo:tipo, DATACHECK:DATACHECK, LAVAGGIO:LAVAGGIO, SCADENZE:SCADENZE, note:note,
                                    spinale:spinale, scoop:scoop, collari:collari, elettrodi:elettrodi, gel:gel, ecg:ecg, sixlead:sixlead,
                                    fourlead:fourlead, saturimetro:saturimetro, pacing:pacing, circuitoventilatore:circuitoventilatore,
                                    maschere:maschere, piastre:piastre, LP:LP, cavoLP:cavoLP, batterieLP:batterieLP, aspiratore:aspiratore,
                                    ventilatore:ventilatore, cavovent12:cavovent12, cavovent220:cavovent220, pompa:pompa, cavopompa12:cavopompa12,
                                    cavopompa220:cavopompa220, bombolefisse:bombolefisse, taglienti:taglienti, DAE:DAE, lenzuola:lenzuola,
                                    cpap:cpap, pedimate:pedimate, guanti:guanti, sedia:sedia, KED:KED, steccobende:steccobende,
                                    bomboleport:bomboleport, caschi:caschi, padella:padella, carta:carta, amputazioni:amputazioni,
                                    ragno:ragno, trauma:trauma, cinghie:cinghie, estintorepost:estintorepost, coltrino:coltrino,
                                    coperta:coperta, traslatore:traslatore, estintoreant:estintoreant, faro:faro, scasso:scasso,
                                    bloccocv:bloccocv, schede118:schede118, fuoriservizio:fuoriservizio, antifiamma:antifiamma,
                                    panseptil:panseptil, luci:luci, blu:blu, sirene:sirene, gasolio:gasolio, telepass:telepass,
                                    doc:doc, cartaagip:cartaagip, lavaggioesterno:lavaggioesterno, lavaggiointerno:lavaggiointerno,
                                    disinfezione:disinfezione, battesedia:battesedia, scadenzeborsa:scadenzeborsa, ambuped:ambuped, reservoirped:reservoirped, filtroped:filtroped,
                                    maschereped:maschereped, guedelped:guedelped, ossped:ossped, ambuadulti:ambuadulti, reservoiradulti:reservoiradulti,
                                    filtroadulti:filtroadulti, maschereadulti:maschereadulti, guedeladulti:guedeladulti, ossadulti:ossadulti, fisio:fisio,
                                    h2o2:h2o2, betadine:betadine, cerotti:cerotti, benda:benda, garze:garze, ghiaccio:ghiaccio, arterioso:arterioso,
                                    venoso:venoso, rasoio:rasoio, sfigmo:sfigmo, fonendo:fonendo, saturimetrob:saturimetrob, termometro:termometro, sondini:sondini,
                                    maschereborsa:maschereborsa, robin:robin, guantisterili:guantisterili, telini:telini, metalline:metalline, spazzatura:spazzatura,
                                    pappagallo:pappagallo, dpi:dpi, chirurgiche:chirurgiche, monossido:monossido},
                                success:function(){
                                    swal({text:"Checklist inviata con successo", icon: "success", timer: 1000, button:false, closeOnClickOutside: false});
                                    setTimeout(function () {
                                            location.href='index.php';
                                        },1001
                                    )
                                }
                            });
                        } else {
                            swal({text:"Invio annullato come richiesto!", timer: 1000, button:false, closeOnClickOutside: false});
                        }
                    })
            })
        });
    </script>

</head>
<!-- NAVBAR -->
<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../index.php" style="color: #078f40">Home</a></li>
            <li class="breadcrumb-item"><a href="index.php" style="color: #078f40">Checklist elettronica</a></li>
            <li class="breadcrumb-item active" aria-current="page">Nuova checklist</li>
        </ol>
    </nav>
</div>
<!-- CONTENT -->
<body>
<div class="container-fluid">
    <div class="jumbotron">
        <form name="check">
            <center>
                <b><?=$idoperatore?> <?=$cognome?> <?=$nome?></b> / <b>AUTO <?=$idmezzo?></b> / <b>CHECKLIST <?=$dictionaryTipo[$select['tipo']]?></b>
            </center>
            <hr>
            <?php
            $notealert = $db->query("SELECT DATACHECK, NOTE FROM checklist WHERE IDMEZZO='$idmezzo' AND NOTE!='' ORDER BY DATACHECK DESC");
            if ($notealert->num_rows > 0) {
                echo "<div class=\"alert alert-danger\" role=\"alert\">
                        <h5 class=\"alert-heading\" STYLE='text-align: center'>Segnalazioni attive!</h5>";
                while($ciclo = $notealert->fetch_array()){
                    echo "<p style='font-size: small'>".$ciclo['DATACHECK']." -> ".$ciclo['NOTE']."</p>";
                }
                echo "</div><hr>";
            }
            ?>

            <input hidden id="IDMEZZO" value="<?=$idmezzo?>">
            <input hidden id="IDOPERATORE" value="<?=$idoperatore?>">
            <input hidden id="tipo" value="<?=$select['tipo']?>">
            <div class="alert alert-success" style="text-align: center" role="alert">
                <b>VANO SANITARIO</b>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="spinale">
                <label class="form-check-label" for="spinale">Asse spinale</label>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="scoop">
                <label class="form-check-label" for="scoop">Barella scoop</label>
            </div>
            <hr>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="collari">
                <label class="form-check-label" for="collari">1x Collari (Pediatric - NoNeck - Regular)</label>
            </div>
            <hr>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="elettrodi"
                    <?php
                    if (($select['tipo'])!=2){
                        echo "disabled";
                    }

                    ?>>
                <label class="form-check-label" for="elettrodi">Elettrodi</label>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="gel"
                    <?php
                    if (($select['tipo'])!=2){
                        echo "disabled";
                    }

                    ?>>
                <label class="form-check-label" for="gel">Gel</label>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="ecg"
                    <?php
                    if (($select['tipo'])!=2){
                        echo "disabled";
                    }

                    ?>>
                <label class="form-check-label" for="ecg">Carta ECG</label>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="6lead"
                    <?php
                    if (($select['tipo'])!=2){
                        echo "disabled";
                    }

                    ?>>
                <label class="form-check-label" for="6lead">Cavo 6 derivazioni</label>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="4lead"
                    <?php
                    if (($select['tipo'])!=2){
                        echo "disabled";
                    }

                    ?>>
                <label class="form-check-label" for="4lead">Cavo 4 derivazioni</label>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="saturimetro"
                    <?php
                    if (($select['tipo'])!=2){
                        echo "disabled";
                    }

                    ?>>
                <label class="form-check-label" for="saturimetro">Cavo saturimetro</label>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="pacing"
                    <?php
                    if (($select['tipo'])!=2){
                        echo "disabled";
                    }

                    ?>>
                <label class="form-check-label" for="pacing">Cavo "mani libere" / Pacing</label>
            </div>
            <!--<div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="quickcombo">
                <label class="form-check-label" for="quickcombo">1x Elettrodi Quick Combo ADULTI e PEDIATRICI</label>
            </div>-->
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="circuitoventilatore"
                    <?php
                    if (($select['tipo'])!=2){
                        echo "disabled";
                    }

                    ?>>
                <label class="form-check-label" for="circuitoventilatore">Corrugato e valvola ventilatore</label>
            </div>
            <hr>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="maschere">
                <label class="form-check-label" for="maschere">3x Maschere ossigeno ADULTI e PEDIATRICHE</label>
            </div>
            <hr>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="piastre"
                    <?php
                    if (($select['tipo'])!=2){
                        echo "disabled";
                    }

                    ?>>
                <label class="form-check-label" for="piastre">1x Piastre LP 12/15 ADULTI e PEDIATRICHE</label>
            </div>
            <hr>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="LP"
                    <?php
                    if (($select['tipo'])!=2){
                        echo "disabled";
                    }

                    ?>>
                <label class="form-check-label" for="LP">Defibrillatore LP 12/15</label>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="cavoLP"
                    <?php
                    if (($select['tipo'])!=2){
                        echo "disabled";
                    }

                    ?>>
                <label class="form-check-label" for="cavoLP">Alimentatore 12v con cavo</label>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="batterieLP"
                    <?php
                    if (($select['tipo'])!=2){
                        echo "disabled";
                    }

                    ?>>
                <label class="form-check-label" for="batterieLP">Batterie</label>
            </div>
            <hr>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="aspiratore">
                <label class="form-check-label" for="aspiratore">Aspiratore con sondino</label>
            </div>
            <hr>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="ventilatore"
                    <?php
                    if (($select['tipo'])!=2){
                        echo "disabled";
                    }

                    ?>>
                <label class="form-check-label" for="ventilatore">Ventilatore polmonare Drager</label>
            </div>
            <div class="form-check form-group">
                <input type="checkbox" class="form-check-input" id="cavovent12"
                    <?php
                    if (($select['tipo'])!=2){
                        echo "disabled";
                    }

                    ?>>
                <label class="form-check-label" for="cavovent12">Cavo ventilatore 12v</label>
            </div>
            <div class="form-check form-group">
                <input type="checkbox" class="form-check-input" id="cavovent220"
                    <?php
                    if (($select['tipo'])!=2){
                        echo "disabled";
                    }

                    ?>>
                <label class="form-check-label" for="cavovent220">Cavo ventilatore 220v</label>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="pompa"
                    <?php
                    if (($select['tipo'])!=2){
                        echo "disabled";
                    }

                    ?>>
                <label class="form-check-label" for="pompa">Pompa infusionale Ivac</label>
            </div>
            <div class="form-check form-group">
                <input type="checkbox" class="form-check-input" id="cavopompa12"
                    <?php
                    if (($select['tipo'])!=2){
                        echo "disabled";
                    }

                    ?>>
                <label class="form-check-label" for="cavopompa12">Cavo pompa 12v</label>
            </div>
            <div class="form-check form-group">
                <input type="checkbox" class="form-check-input" id="cavopompa220"
                    <?php
                    if (($select['tipo'])!=2){
                        echo "disabled";
                    }

                    ?>>
                <label class="form-check-label" for="cavopompa220">Cavo pompa 220v</label>
            </div>
            <hr>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="bombolefisse">
                <label class="form-check-label" for="bombolefisse">2x Bombole ossigeno fisse 7lt.</label>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="taglienti">
                <label class="form-check-label" for="taglienti">Porta taglienti</label>
            </div>
            <hr>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="DAE">
                <label class="form-check-label" for="DAE">DAE + piastre ADULTI e PEDIATRICHE</label>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="lenzuola">
                <label class="form-check-label" for="lenzuola">10x Lenzuola monouso</label>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="cpap"
                    <?php
                    if (($select['tipo'])!=2){
                        echo "disabled";
                    }

                    ?>>
                <label class="form-check-label" for="cpap">1x Maschere CPAP (S-M-L)</label>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="pedimate"
                    <?php
                    if (($select['tipo'])!=3){
                        echo "disabled";
                    }

                    ?>>
                <label class="form-check-label" for="pedimate">Pedi-Mate</label>
            </div>
            <hr>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="guanti">
                <label class="form-check-label" for="guanti">1x Guanti (S-M-L-XL)</label>
            </div>
            <hr>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="sedia">
                <label class="form-check-label" for="sedia">Sedia portantina</label>
            </div>
            <hr>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="KED">
                <label class="form-check-label" for="KED">Kit estricazione</label>
            </div>
            <hr>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="steccobende">
                <label class="form-check-label" for="steccobende">Set steccobende con pompa</label>
            </div>
            <hr>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="bomboleport">
                <label class="form-check-label" for="bomboleport">2x Bombole portatili 3lt.</label>
            </div>
            <hr>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="caschi">
                <label class="form-check-label" for="caschi">4x Elmetti</label>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="padella">
                <label class="form-check-label" for="padella">Padella</label>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="carta">
                <label class="form-check-label" for="carta">Rotolo carta</label>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="amputazioni"
                    <?php
                    if (($select['tipo'])!=2){
                        echo "disabled";
                    }

                    ?>>
                <label class="form-check-label" for="amputazioni">Sacca porta arti</label>
            </div>
            <hr>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="ragno">
                <label class="form-check-label" for="ragno">Ragno</label>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="trauma">
                <label class="form-check-label" for="trauma">Base + fermacapo + mentoniere</label>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="cinghie">
                <label class="form-check-label" for="cinghie">3x Cinture scoop</label>
            </div>
            <hr>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="estintorepost">
                <label class="form-check-label" for="estintorepost">Estintore posteriore</label>
                <small class="text-muted">
                    (Lancetta sul verde)
                </small>
            </div>
            <hr>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="coltrino">
                <label class="form-check-label" for="coltrino">Coltrino</label>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="coperta">
                <label class="form-check-label" for="coperta">Coperta</label>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="traslatore"
                    <?php
                    if (($select['tipo'])==3){
                        echo "disabled";
                    }

                    ?>>
                <label class="form-check-label" for="traslatore">Traslatore</label>
            </div>
            <hr>
            <div class="alert alert-success" style="text-align: center" role="alert">
                <b>CABINA GUIDA</b>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="estintoreant">
                <label class="form-check-label" for="estintoreant">Estintore anteriore</label>
                <small class="text-muted">
                    (Lancetta sul verde)
                </small>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="faro">
                <label class="form-check-label" for="faro">Faro di ricerca</label>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="scasso">
                <label class="form-check-label" for="scasso">Set da scasso</label>
                <small class="text-muted">
                    (Mazzetta, cesoia, leverino, guanti da lavoro, torcia a vento)
                </small>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="bloccocv">
                <label class="form-check-label" for="bloccocv">Blocco auto CV</label>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="schede118">
                <label class="form-check-label" for="schede118">5x Schede MSB 118</label>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="fuoriservizio">
                <label class="form-check-label" for="fuoriservizio">Cartello FUORI SERVIZIO</label>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="antifiamma">
                <label class="form-check-label" for="antifiamma">Coperta anti-fiamma</label>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="panseptil">
                <label class="form-check-label" for="panseptil">Disinfettante</label>
            </div>
            <hr>
            <div class="alert alert-success" style="text-align: center" role="alert">
                <b>CONTROLLI MEZZO</b>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="luci">
                <label class="form-check-label" for="luci">Luci</label>
                <small class="text-muted">
                    (Specificare nelle note)
                </small>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="blu">
                <label class="form-check-label" for="blu">Lampeggianti</label>
                <small class="text-muted">
                    (Specificare nelle note)
                </small>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="sirene">
                <label class="form-check-label" for="sirene">Sirene</label>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="gasolio">
                <label class="form-check-label" for="gasolio">Carburante</label>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="telepass">
                <label class="form-check-label" for="telepass">Telepass</label>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="doc">
                <label class="form-check-label" for="doc">Documenti</label>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="cartaagip">
                <label class="form-check-label" for="cartaagip">Carta carburante</label>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="lavaggioesterno" value="1">
                <label class="form-check-label" for="lavaggioesterno">Lavaggio esterno</label>
                <small class="text-muted">
                    (Segnare solo se effettuato)
                </small>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="lavaggiointerno">
                <label class="form-check-label" for="lavaggiointerno">Lavaggio interno</label>
                <small class="text-muted">
                    (Segnare solo se effettuato)
                </small>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="disinfezione">
                <label class="form-check-label" for="disinfezione">Disinfezione</label>
                <small class="text-muted">
                    (Segnare solo se effettuato)
                </small>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="battesedia"
                    <?php
                    if (($select['tipo'])==3){
                        echo "disabled";
                    }

                    ?>>
                <label class="form-check-label" for="battesedia">Batteria + caricabatteria sedia</label>
            </div>
            <hr>
            <div class="alert alert-success" style="text-align: center" role="alert">
                <b>CONTROLLO BORSA</b>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="scadenze" value="1">
                <label class="form-check-label" for="scadenze">Controllo scadenze
                    <?
                    $controlloscandenza=$db->query("SELECT DATACHECK, SCADENZE from checklist WHERE IDMEZZO='$idmezzo' AND SCADENZE=1 ORDER BY DATACHECK DESC LIMIT 1");
                    if ($controlloscandenza->num_rows>0){
                        list($ultimascadenza)= $controlloscandenza->fetch_array();
                        echo "(ultimo controllo in data $ultimascadenza)";
                    }
                    ?>
                </label>
            </div>
            <hr>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="ambuped">
                <label class="form-check-label" for="ambuped">Pallone autoespandibile pediatrico</label>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="reservoirped">
                <label class="form-check-label" for="reservoirped">Reservoire +  valvola</label>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="filtroped">
                <label class="form-check-label" for="filtroped">Filtri (2x)</label>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="maschereped">
                <label class="form-check-label" for="maschereped">Mascherine (3 misure)</label>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="guedelped">
                <label class="form-check-label" for="guedelped">Guedel (3 misure)</label>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="ossped">
                <label class="form-check-label" for="ossped">Raccordo ossigeno</label>
            </div>
            <hr>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="ambuadulti">
                <label class="form-check-label" for="ambuadulti">Pallone autoespandibile adulti</label>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="reservoiradulti">
                <label class="form-check-label" for="reservoiradulti">Reservoire +  valvola</label>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="filtroadulti">
                <label class="form-check-label" for="filtroadulti">Filtri (2x)</label>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="maschereadulti">
                <label class="form-check-label" for="maschereadulti">Mascherine (5 misure)</label>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="guedeladulti">
                <label class="form-check-label" for="guedeladulti">Guedel (5 misure)</label>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="ossadulti">
                <label class="form-check-label" for="ossadulti">Raccordo ossigeno</label>
            </div>
            <hr>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="fisio">
                <label class="form-check-label" for="fisio">Fisiologiche (2x)</label>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="h2o2">
                <label class="form-check-label" for="h2o2">Acqua ossigenata (1x)</label>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="betadine">
                <label class="form-check-label" for="betadine">Betadine (1x)</label>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="cerotti">
                <label class="form-check-label" for="cerotti">Cerotti (2x)</label>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="benda">
                <label class="form-check-label" for="benda">Peha haft (4x)</label>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="garze">
                <label class="form-check-label" for="garze">Garze sterili (6x)</label>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="ghiaccio">
                <label class="form-check-label" for="ghiaccio">Ghiaccio (3x)</label>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="arterioso">
                <label class="form-check-label" for="arterioso">Laccio arterioso</label>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="venoso">
                <label class="form-check-label" for="venoso">Laccio venoso</label>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="rasoio">
                <label class="form-check-label" for="rasoio">Rasoio</label>
            </div>
            <hr>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="sfigmo">
                <label class="form-check-label" for="sfigmo">Sfigmomanometro</label>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="fonendo">
                <label class="form-check-label" for="fonendo">Fonendoscopio</label>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="saturimetrob"
                    <?php
                    if (($select['tipo'])!=3){
                        echo "disabled";
                    }

                    ?>>
                <label class="form-check-label" for="saturimetrob">Saturimetro</label>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="termometro"
                    <?php
                    if (($select['tipo'])!=3){
                        echo "disabled";
                    }

                    ?>>
                <label class="form-check-label" for="termometro">Termometro</label>
            </div>
            <hr>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="sondini">
                <label class="form-check-label" for="sondini">Sondini aspiratore (5x)</label>
            </div>
            <hr>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="maschereborsa">
                <label class="form-check-label" for="maschereborsa">Mascherine reservoir (2 adulti + 2 pediatriche)</label>
            </div>
            <hr>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="robin">
                <label class="form-check-label" for="robin">Robin</label>
            </div>
            <hr>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="guantisterili">
                <label class="form-check-label" for="guantisterili">Guanti sterili (4x)</label>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="telini">
                <label class="form-check-label" for="telini">Telini sterili (2x)</label>
            </div>
            <hr>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="metalline">
                <label class="form-check-label" for="metalline">Metalline (4x)</label>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="spazzatura">
                <label class="form-check-label" for="spazzatura">Sacchetti rifiuti</label>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="pappagallo">
                <label class="form-check-label" for="pappagallo">Pappagallo monouso (2x)</label>
            </div>
            <hr>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="dpi">
                <label class="form-check-label" for="dpi">Kit infettivi (3x)</label>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="chirurgiche">
                <label class="form-check-label" for="chirurgiche">Mascherine chirurgiche</label>
            </div>
            <hr>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="monossido"
                    <?php
                    if (($select['tipo'])!=3){
                        echo "disabled";
                    }

                    ?>>
                <label class="form-check-label" for="monossido">Rilevatore gas tossici</label>
            </div>

            <hr>
            <div class="form-group">
                <label for="note">Segnalazioni</label>
                <textarea class="form-control" name="note" id="note" rows="10" maxlength="250"></textarea>
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
            <center>
                <button type="button" id="inviacheck" name="inviacheck" class="btn btn-success"><i class="fas fa-check"></i></button>
            </center>
        </form>
    </div>
</div>
</body>

<!-- FOOTER -->
<footer class="container-fluid">
    <div class="text-center">
        <font size="-4" style="color: lightgray; "><em>Powered for <a href="mailto:info@croceverde.org">Croce Verde Torino</a>. All rights reserved.<p>V 1.0</p></em></font>
    </div>
</footer>
</html>