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
//connessione DB
include "../config/config.php";
//login
if (!isset($_SESSION["ID"])){
    header("Location: ../login.php");
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
//echo date_format()
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <meta name="author" content="Paolo Randone">
    <title>Inserisci checklist</title>

    <? require "../config/include/header.html";?>

    <script>
        $(document).ready(function(){
            $('[data-toggle="popover"]').popover();
        });
    </script>

    <script>
        // Example starter JavaScript for disabling form submissions if there are invalid fields
        (function() {
            'use strict';
            window.addEventListener('load', function() {
                // Fetch all the forms we want to apply custom Bootstrap validation styles to
                var forms = document.getElementsByClassName('needs-validation');
                // Loop over them and prevent submission
                var validation = Array.prototype.filter.call(forms, function(form) {
                    form.addEventListener('submit', function(event) {
                        if (form.checkValidity() === false) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
            }, false);
        })(); 
    </script>

    <!-- STRUTTURA CHECKLIST -->
    <script>
        $(document).ready(function() {

            $('#inviacheck').on('click', function(){

                var IDMEZZO = $("#IDMEZZO").val();
                var IDOPERATORE = $("#IDOPERATORE").val();
                var tipo = $("#tipo").val();
                var DATACHECK = moment().format('YYYY-MM-DD HH:mm:ss');
                var ESTERNO = $("#lavaggioesterno option:selected").val();
                var INTERNO = $("#lavaggiointerno option:selected").val();
                var SANIFICAZIONE = $("#disinfezione option:selected").val();
                var SCADENZE = $("#scadenze:checked").val();
                var OLIO = $("#olio:checked").val();

                //CONTROLLI AMBULANZA
                var spinale = $("#spinale option:selected").val();
                var scoop = $("#scoop option:selected").val();
                var collari = $("#collari option:selected").val();
                var elettrodi = $("#elettrodi option:selected").val();
                var gel = $("#gel option:selected").val();
                var ecg = $("#ecg option:selected").val();
                var sixlead = $("#6lead option:selected").val();
                var fourlead = $("#4lead option:selected").val();
                var saturimetro = $("#saturimetro option:selected").val();
                var pacing = $("#pacing option:selected").val();
                var circuitoventilatore = $("#circuitoventilatore option:selected").val();
                var maschere = $("#maschere option:selected").val();
                var piastre = $("#piastre option:selected").val();
                var LP = $("#LP option:selected").val();
                var cavoLP = $("#cavoLP option:selected").val();
                var batterieLP = $("#batterieLP option:selected").val();
                var aspiratore = $("#aspiratore option:selected").val();
                var ventilatore = $("#ventilatore option:selected").val();
                var cavovent12 = $("#cavovent12 option:selected").val();
                var cavovent220 = $("#cavovent220 option:selected").val();
                var pompa = $("#pompa option:selected").val();
                var cavopompa12 = $("#cavopompa12 option:selected").val();
                var cavopompa220 = $("#cavopompa220 option:selected").val();
                var bombolefisse = $("#bombolefisse option:selected").val();
                var taglienti = $("#taglienti option:selected").val();
                var DAE = $("#DAE option:selected").val();
                var lenzuola = $("#lenzuola option:selected").val();
                var cpap = $("#cpap option:selected").val();
                var pedimate = $("#pedimate option:selected").val();
                var guanti = $("#guanti option:selected").val();
                var sedia = $("#sedia option:selected").val();
                var KED = $("#KED option:selected").val();
                var steccobende = $("#steccobende option:selected").val();
                var bomboleport = $("#bomboleport option:selected").val();
                var caschi = $("#caschi option:selected").val();
                var padella = $("#padella option:selected").val();
                var carta = $("#carta option:selected").val();
                var amputazioni = $("#amputazioni option:selected").val();
                var ragno = $("#ragno option:selected").val();
                var trauma = $("#trauma option:selected").val();
                var cinghie = $("#cinghie option:selected").val();
                var estintorepost = $("#estintorepost option:selected").val();
                var coltrino = $("#coltrino option:selected").val();
                var coperta = $("#coperta option:selected").val();
                var traslatore = $("#traslatore option:selected").val();
                var estintoreant = $("#estintoreant option:selected").val();
                var faro = $("#faro option:selected").val();
                var scasso = $("#scasso option:selected").val();
                var bloccocv = $("#bloccocv option:selected").val();
                var schede118 = $("#schede118 option:selected").val();
                var fuoriservizio = $("#fuoriservizio option:selected").val();
                var antifiamma = $("#antifiamma option:selected").val();
                var panseptil = $("#panseptil option:selected").val();
                var luci = $("#luci option:selected").val();
                var blu = $("#blu option:selected").val();
                var sirene = $("#sirene option:selected").val();
                var gasolio = $("#gasolio option:selected").val();
                var telepass = $("#telepass option:selected").val();
                var doc = $("#doc option:selected").val();
                var cartaagip = $("#cartaagip option:selected").val();
                var lavaggioesternotext = $("#lavaggioesterno option:selected").text();
                var lavaggiointernotext = $("#lavaggiointerno option:selected").text();
                var disinfezionetext = $("#disinfezione option:selected").text();
                var battesedia = $("#battesedia option:selected").val();
                var oliocheck = $("#olio").prop("checked") ? 'EFFETTUATO' : 'NON EFFETTUATO';
                var rabbocco = $("#rabbocco option:selected").val();
                var tablet = $("#tablet option:selected").val();

                //CONTROLLI BORSA
                var scadenzeborsa = $("#scadenze").prop("checked") ? 'EFFETTUATO' : 'NON EFFETTUATO';
                var ambuped = $("#ambuped option:selected").val();
                var reservoirped = $("#reservoirped option:selected").val();
                var filtroped = $("#filtroped option:selected").val();
                var maschereped = $("#maschereped option:selected").val();
                var guedelped = $("#guedelped option:selected").val();
                var ossped = $("#ossped option:selected").val();
                var ambuadulti = $("#ambuadulti option:selected").val();
                var reservoiradulti = $("#reservoiradulti option:selected").val();
                var filtroadulti = $("#filtroadulti option:selected").val();
                var maschereadulti = $("#maschereadulti option:selected").val();
                var guedeladulti = $("#guedeladulti option:selected").val();
                var ossadulti = $("#ossadulti option:selected").val();
                var fisio = $("#fisio option:selected").val();
                var h2o2 = $("#h2o2 option:selected").val();
                var betadine = $("#betadine option:selected").val();
                var cerotti = $("#cerotti option:selected").val();
                var benda = $("#benda option:selected").val();
                var garze = $("#garze option:selected").val();
                var ghiaccio = $("#ghiaccio option:selected").val();
                var arterioso = $("#arterioso option:selected").val();
                var venoso = $("#venoso option:selected").val();
                var rasoio = $("#rasoio option:selected").val();
                var sfigmo = $("#sfigmo option:selected").val();
                var fonendo = $("#fonendo option:selected").val();
                var saturimetrob = $("#saturimetrob option:selected").val();
                var termometro = $("#termometro option:selected").val();
                var sondini = $("#sondini option:selected").val();
                var maschereborsa = $("#maschereborsa option:selected").val();
                var robin = $("#robin option:selected").val();
                var guantisterili = $("#guantisterili option:selected").val();
                var telini = $("#telini option:selected").val();
                var metalline = $("#metalline option:selected").val();
                var spazzatura = $("#spazzatura option:selected").val();
                var pappagallo = $("#pappagallo option:selected").val();
                var dpi = $("#dpi option:selected").val();
                var chirurgiche = $("#chirurgiche option:selected").val();
                var monossido = $("#monossido option:selected").val();
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
                                data:{IDMEZZO:IDMEZZO, IDOPERATORE:IDOPERATORE, tipo:tipo, DATACHECK:DATACHECK, ESTERNO:ESTERNO, INTERNO:INTERNO, SANIFICAZIONE:SANIFICAZIONE, SCADENZE:SCADENZE, OLIO:OLIO, note:note,
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
                                    doc:doc, cartaagip:cartaagip, lavaggioesternotext:lavaggioesternotext, lavaggiointernotext:lavaggiointernotext,
                                    disinfezionetext:disinfezionetext, battesedia:battesedia, scadenzeborsa:scadenzeborsa, ambuped:ambuped, reservoirped:reservoirped, filtroped:filtroped,
                                    maschereped:maschereped, guedelped:guedelped, ossped:ossped, ambuadulti:ambuadulti, reservoiradulti:reservoiradulti,
                                    filtroadulti:filtroadulti, maschereadulti:maschereadulti, guedeladulti:guedeladulti, ossadulti:ossadulti, fisio:fisio,
                                    h2o2:h2o2, betadine:betadine, cerotti:cerotti, benda:benda, garze:garze, ghiaccio:ghiaccio, arterioso:arterioso,
                                    venoso:venoso, rasoio:rasoio, sfigmo:sfigmo, fonendo:fonendo, saturimetrob:saturimetrob, termometro:termometro, sondini:sondini,
                                    maschereborsa:maschereborsa, robin:robin, guantisterili:guantisterili, telini:telini, metalline:metalline, spazzatura:spazzatura,
                                    pappagallo:pappagallo, dpi:dpi, chirurgiche:chirurgiche, monossido:monossido, oliocheck:oliocheck, rabbocco:rabbocco, tablet:tablet},
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
            <li class="breadcrumb-item"><a href="index.php" style="color: #078f40">Checklist</a></li>
            <li class="breadcrumb-item active" aria-current="page">Nuova checklist</li>
        </ol>
    </nav>
</div>

<body>
<div class="container-fluid">
    <div class="jumbotron">
        <form name="check" class="needs-validation" novalidate>
            <div style="text-align: center;">
                <b><?=$idoperatore?> <?=$nome?> <?=$cognome?></b> / <b>AUTO <?=$idmezzo?></b> / <b>CHECKLIST <?=$dictionaryTipo[$select['tipo']]?></b>
            </div>
            <hr>
            <?php
            $notealert = $db->query("SELECT DATACHECK, NOTE FROM checklist WHERE IDMEZZO='$idmezzo' AND NOTE!='' AND STATO!='3' AND STATO!='4 'ORDER BY DATACHECK DESC");
            if ($notealert->num_rows > 0) {
                echo "<div class=\"alert alert-danger\" role=\"alert\">
                        <h5 class=\"alert-heading\" STYLE='text-align: center'>Segnalazioni attive!</h5>";
                while($ciclo = $notealert->fetch_array()){
                    $var=$ciclo['DATACHECK'];$var1=date_create("$var");
                    echo "<p style='font-size: small'>".date_format($var1, "d-m-Y H:i")." -> ".$ciclo['NOTE']."</p>";
                }
                echo "</div><hr>";
            }
            ?>

            <input hidden id="IDMEZZO" value="<?=$idmezzo?>">
            <input hidden id="IDOPERATORE" value="<?=$idoperatore?>">
            <input hidden id="tipo" value="<?=$select['tipo']?>">
            <div class="form-group row">
                    <select class="form-control form-control-sm" id="blank" required hidden>
                        <option disabled selected value="">blank</option>
                    </select>
            </div>
            <div class="alert alert-success" style="text-align: center" role="alert">
                <b>VANO SANITARIO</b>
            </div>
            <div class="form-group row">
                <label class="col-sm-4 col-form-label" for="spinale">Asse spinale</label>
                <div class="col col-sm-2">
                    <select class="form-control form-control-sm" id="spinale" required>
                        <option disabled selected value="">Scegli...</option>
                        <option value="OK">OK</option>
                        <option value="MANCANTE">Mancante</option>
                        <option value="Ripristinato">Ripristinato</option>
                        <option value="Vedi note">Vedi note</option>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-4 col-form-label" for="scoop">Barella scoop</label>
                <div class="col col-sm-2">
                    <select class="form-control form-control-sm" id="scoop" required>
                        <option disabled selected value="">Scegli...</option>
                        <option value="OK">OK</option>
                        <option value="MANCANTE">Mancante</option>
                        <option value="Ripristinato">Ripristinato</option>
                        <option value="Vedi note">Vedi note</option>
                    </select>
                </div>
            </div>
            <hr>
            <div class="form-group row">
                <label class="col-sm-4 col-form-label" for="collari">1x Collari (Pediatric - NoNeck - Regular)</label>
                <div class="col col-sm-2">
                    <select class="form-control form-control-sm" id="collari" required>
                        <option disabled selected value="">Scegli...</option>
                        <option value="OK">OK</option>
                        <option value="MANCANTE">Mancante</option>
                        <option value="Ripristinato">Ripristinato</option>
                        <option value="Parziale">Quantità inferiore</option>
                        <option value="Vedi note">Vedi note</option>
                    </select>
                </div>
            </div>
            <?php
            if (($select['tipo'])==2): ?>
                <hr>
                <div class="form-group row">
                    <label class="col-sm-4 col-form-label" for="elettrodi">Elettrodi</label>
                    <div class="col col-sm-2">
                        <select class="form-control form-control-sm" id="elettrodi" required>
                            <option disabled selected value="">Scegli...</option>
                            <option value="OK">OK</option>
                            <option value="MANCANTE">Mancante</option>
                            <option value="Ripristinato">Ripristinato</option>
                            <option value="Parziale">Quantità inferiore</option>
                            <option value="Vedi note">Vedi note</option>
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-4 col-form-label" for="gel">Gel</label>
                    <div class="col col-sm-2">
                        <select class="form-control form-control-sm" id="gel" required>
                            <option disabled selected value="">Scegli...</option>
                            <option value="OK">OK</option>
                            <option value="MANCANTE">Mancante</option>
                            <option value="Ripristinato">Ripristinato</option>
                            <option value="Parziale">Quantità inferiore</option>
                            <option value="Vedi note">Vedi note</option>
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-4 col-form-label" for="ecg">Carta ECG</label>
                    <div class="col col-sm-2">
                        <select class="form-control form-control-sm" id="ecg" required>
                            <option disabled selected value="">Scegli...</option>
                            <option value="OK">OK</option>
                            <option value="MANCANTE">Mancante</option>
                            <option value="Ripristinato">Ripristinato</option>
                            <option value="Parziale">Quantità inferiore</option>
                            <option value="Vedi note">Vedi note</option>
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-4 col-form-label" for="6lead">Cavo 6 derivazioni</label>
                    <div class="col col-sm-2">
                        <select class="form-control form-control-sm" id="6lead" required>
                            <option disabled selected value="">Scegli...</option>
                            <option value="OK">OK</option>
                            <option value="MANCANTE">Mancante</option>
                            <option value="Guasto">Guasto</option>
                            <option value="Ripristinato">Ripristinato</option>
                            <option value="Parziale">Quantità inferiore</option>
                            <option value="Vedi note">Vedi note</option>
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-4 col-form-label" for="4lead">Cavo 4 derivazioni</label>
                    <div class="col col-sm-2">
                        <select class="form-control form-control-sm" id="4lead" required>
                            <option disabled selected value="">Scegli...</option>
                            <option value="OK">OK</option>
                            <option value="MANCANTE">Mancante</option>
                            <option value="Guasto">Guasto</option>
                            <option value="Ripristinato">Ripristinato</option>
                            <option value="Parziale">Quantità inferiore</option>
                            <option value="Vedi note">Vedi note</option>
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-4 col-form-label" for="saturimetro">Cavo saturimetro</label>
                    <div class="col col-sm-2">
                        <select class="form-control form-control-sm" id="saturimetro" required>
                            <option disabled selected value="">Scegli...</option>
                            <option value="OK">OK</option>
                            <option value="MANCANTE">Mancante</option>
                            <option value="Guasto">Guasto</option>
                            <option value="Ripristinato">Ripristinato</option>
                            <option value="Vedi note">Vedi note</option>
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-4 col-form-label" for="pacing">Cavo mani libere / pacing</label>
                    <div class="col col-sm-2">
                        <select class="form-control form-control-sm" id="pacing" required>
                            <option disabled selected value="">Scegli...</option>
                            <option value="OK">OK</option>
                            <option value="MANCANTE">Mancante</option>
                            <option value="Guasto">Guasto</option>
                            <option value="Ripristinato">Ripristinato</option>
                            <option value="Vedi note">Vedi note</option>
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-4 col-form-label" for="circuitoventilatore">Corrugato e valvola ventilatore</label>
                    <div class="col col-sm-2">
                        <select class="form-control form-control-sm" id="circuitoventilatore" required>
                            <option disabled selected value="">Scegli...</option>
                            <option value="OK">OK</option>
                            <option value="MANCANTE">Mancante</option>
                            <option value="Guasto">Guasto</option>
                            <option value="Ripristinato">Ripristinato</option>
                            <option value="Vedi note">Vedi note</option>
                        </select>
                    </div>
                </div>
            <? endif; ?>
            <hr>
            <div class="form-group row">
                <label class="col-sm-4 col-form-label" for="maschere">3x Maschere ossigeno ADULTI e PEDIATRICHE</label>
                <div class="col col-sm-2">
                    <select class="form-control form-control-sm" id="maschere" required>
                        <option disabled selected value="">Scegli...</option>
                        <option value="OK">OK</option>
                        <option value="MANCANTE">Mancante</option>
                        <option value="Ripristinato">Ripristinato</option>
                        <option value="Parziale">Quantità inferiore</option>
                        <option value="Vedi note">Vedi note</option>
                    </select>
                </div>
            </div>
            <?php
            if (($select['tipo'])==2): ?>
                <hr>
                <div class="form-group row">
                    <label class="col-sm-4 col-form-label" for="piastre">1x Piastre LP 12/15 ADULTI e PEDIATRICHE</label>
                    <div class="col col-sm-2">
                        <select class="form-control form-control-sm" id="piastre" required>
                            <option disabled selected value="">Scegli...</option>
                            <option value="OK">OK</option>
                            <option value="MANCANTE">Mancante</option>
                            <option value="Ripristinato">Ripristinato</option>
                            <option value="Vedi note">Vedi note</option>
                        </select>
                    </div>
                </div>
            <? endif; ?>

            <?php
            if (($select['tipo'])==2): ?>
                <hr>
                <div class="form-group row">
                    <label class="col-sm-4 col-form-label" for="LP">Defibrillatore LP 12/15</label>
                    <div class="col col-sm-2">
                        <select class="form-control form-control-sm" id="LP" required>
                            <option disabled selected value="">Scegli...</option>
                            <option value="OK">OK</option>
                            <option value="MANCANTE">Mancante</option>
                            <option value="Guasto">Guasto</option>
                            <option value="Ripristinato">Ripristinato</option>
                            <option value="Vedi note">Vedi note</option>
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-4 col-form-label" for="cavoLP">Alimentatore 12v con cavo</label>
                    <div class="col col-sm-2">
                        <select class="form-control form-control-sm" id="cavoLP" required>
                            <option disabled selected value="">Scegli...</option>
                            <option value="OK">OK</option>
                            <option value="MANCANTE">Mancante</option>
                            <option value="Guasto">Guasto</option>
                            <option value="Ripristinato">Ripristinato</option>
                            <option value="Vedi note">Vedi note</option>
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-4 col-form-label" for="batterieLP">Batterie</label>
                    <div class="col col-sm-2">
                        <select class="form-control form-control-sm" id="batterieLP" required>
                            <option disabled selected value="">Scegli...</option>
                            <option value="OK">OK</option>
                            <option value="MANCANTE">Mancante</option>
                            <option value="Guasto">Guasto</option>
                            <option value="Vedi note">Vedi note</option>
                        </select>
                    </div>
                </div>
            <? endif; ?>
            <hr>
            <div class="form-group row">
                <label class="col-sm-4 col-form-label" for="aspiratore">Aspiratore con sondino</label>
                <div class="col col-sm-2">
                    <select class="form-control form-control-sm" id="aspiratore" required>
                        <option disabled selected value="">Scegli...</option>
                        <option value="OK">OK</option>
                        <option value="MANCANTE">Mancante</option>
                        <option value="Guasto">Guasto</option>
                        <option value="Ripristinato">Ripristinato</option>
                        <option value="Vedi note">Vedi note</option>
                    </select>
                </div>
            </div>
            <?php
            if (($select['tipo'])==2): ?>
                <hr>
                <div class="form-group row">
                    <label class="col-sm-4 col-form-label" for="ventilatore">Ventilatore polmonare Drager</label>
                    <div class="col col-sm-2">
                        <select class="form-control form-control-sm" id="ventilatore" required>
                            <option disabled selected value="">Scegli...</option>
                            <option value="OK">OK</option>
                            <option value="MANCANTE">Mancante</option>
                            <option value="Guasto">Guasto</option>
                            <option value="Ripristinato">Ripristinato</option>
                            <option value="Vedi note">Vedi note</option>
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-4 col-form-label" for="cavovent12">Cavo ventilatore 12v</label>
                    <div class="col col-sm-2">
                        <select class="form-control form-control-sm" id="cavovent12" required>
                            <option disabled selected value="">Scegli...</option>
                            <option value="OK">OK</option>
                            <option value="MANCANTE">Mancante</option>
                            <option value="Guasto">Guasto</option>
                            <option value="Ripristinato">Ripristinato</option>
                            <option value="Vedi note">Vedi note</option>
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-4 col-form-label" for="cavovent220">Cavo ventilatore 220v</label>
                    <div class="col col-sm-2">
                        <select class="form-control form-control-sm" id="cavovent220" required>
                            <option disabled selected value="">Scegli...</option>
                            <option value="OK">OK</option>
                            <option value="MANCANTE">Mancante</option>
                            <option value="Guasto">Guasto</option>
                            <option value="Ripristinato">Ripristinato</option>
                            <option value="Vedi note">Vedi note</option>
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-4 col-form-label" for="pompa">Pompa infusionale</label>
                    <div class="col col-sm-2">
                        <select class="form-control form-control-sm" id="pompa" required>
                            <option disabled selected value="">Scegli...</option>
                            <option value="OK">OK</option>
                            <option value="MANCANTE">Mancante</option>
                            <option value="Guasto">Guasto</option>
                            <option value="Ripristinato">Ripristinato</option>
                            <option value="Vedi note">Vedi note</option>
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-4 col-form-label" for="cavopompa12">Cavo pompa 12v</label>
                    <div class="col col-sm-2">
                        <select class="form-control form-control-sm" id="cavopompa12" required>
                            <option disabled selected value="">Scegli...</option>
                            <option value="OK">OK</option>
                            <option value="MANCANTE">Mancante</option>
                            <option value="Guasto">Guasto</option>
                            <option value="Ripristinato">Ripristinato</option>
                            <option value="Vedi note">Vedi note</option>
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-4 col-form-label" for="cavopompa220">Cavo pompa 220v</label>
                    <div class="col col-sm-2">
                        <select class="form-control form-control-sm" id="cavopompa220" required>
                            <option disabled selected value="">Scegli...</option>
                            <option value="OK">OK</option>
                            <option value="MANCANTE">Mancante</option>
                            <option value="Guasto">Guasto</option>
                            <option value="Ripristinato">Ripristinato</option>
                            <option value="Vedi note">Vedi note</option>
                        </select>
                    </div>
                </div>
            <? endif; ?>
            <hr>
            <div class="form-group row">
                <label class="col-sm-4 col-form-label" for="bombolefisse">2x Bombole ossigeno fisse 7lt.</label>
                <div class="col col-sm-2">
                    <select class="form-control form-control-sm" id="bombolefisse" required>
                        <option disabled selected value="">Scegli...</option>
                        <option value="OK">OK</option>
                        <option value="MANCANTE">Mancante</option>
                        <option value="Ripristinato">Ripristinato</option>
                        <option value="Parziale">Quantità inferiore</option>
                        <option value="Vedi note">Vedi note</option>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-4 col-form-label" for="taglienti">Porta taglienti</label>
                <div class="col col-sm-2">
                    <select class="form-control form-control-sm" id="taglienti" required>
                        <option disabled selected value="">Scegli...</option>
                        <option value="OK">OK</option>
                        <option value="MANCANTE">Mancante</option>
                        <option value="Ripristinato">Ripristinato</option>
                        <option value="Parziale">Quantità inferiore</option>
                        <option value="Vedi note">Vedi note</option>
                    </select>
                </div>
            </div>
            <hr>
            <div class="form-group row">
                <label class="col-sm-4 col-form-label" for="DAE">DAE + piastre ADULTI e PEDIATRICHE <small class="text-muted">(Dove previsto)</small></label>
                <div class="col col-sm-2">
                    <select class="form-control form-control-sm" id="DAE" required>
                        <option disabled selected value="">Scegli...</option>
                        <option value="OK">OK</option>
                        <option value="MANCANTE">Mancante</option>
                        <option value="Guasto">Guasto</option>
                        <option value="Ripristinato">Ripristinato</option>
                        <option value="Vedi note">Vedi note</option>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-4 col-form-label" for="lenzuola">10x Lenzuola monouso</label>
                <div class="col col-sm-2">
                    <select class="form-control form-control-sm" id="lenzuola" required>
                        <option disabled selected value="">Scegli...</option>
                        <option value="OK">OK</option>
                        <option value="MANCANTE">Mancante</option>
                        <option value="Ripristinato">Ripristinato</option>
                        <option value="Parziale">Quantità inferiore</option>
                        <option value="Vedi note">Vedi note</option>
                    </select>
                </div>
            </div>
            <?php
            if (($select['tipo'])==2): ?>
                <div class="form-group row">
                    <label class="col-sm-4 col-form-label" for="cpap">1x Maschere CPAP (S-M-L)</label>
                    <div class="col col-sm-2">
                        <select class="form-control form-control-sm" id="cpap" required>
                            <option disabled selected value="">Scegli...</option>
                            <option value="OK">OK</option>
                            <option value="MANCANTE">Mancante</option>
                            <option value="Ripristinato">Ripristinato</option>
                            <option value="Parziale">Quantità inferiore</option>
                            <option value="Vedi note">Vedi note</option>
                        </select>
                    </div>
                </div>
            <? endif; ?>
            <?php
            if (($select['tipo'])==3): ?>
                <div class="form-group row">
                    <label class="col-sm-4 col-form-label" for="pedimate">Pedi-Mate</label>
                    <div class="col col-sm-2">
                        <select class="form-control form-control-sm" id="pedimate" required>
                            <option disabled selected value="">Scegli...</option>
                            <option value="OK">OK</option>
                            <option value="MANCANTE">Mancante</option>
                            <option value="Guasto">Guasto</option>
                            <option value="Ripristinato">Ripristinato</option>
                            <option value="Vedi note">Vedi note</option>
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-4 col-form-label" for="tablet">Tablet + stampante <small class="text-muted">(SOLO PER MSB)</small></label>
                    <div class="col col-sm-2">
                        <select class="form-control form-control-sm" id="tablet" required>
                            <option disabled selected value="">Scegli...</option>
                            <option value="OK">OK</option>
                            <option value="MANCANTE">Mancante</option>
                            <option value="Guasto">Guasto</option>
                            <option value="Vedi note">Vedi note</option>
                        </select>
                    </div>
                </div>
            <? endif; ?>
            <hr>
            <div class="form-group row">
                <label class="col-sm-4 col-form-label" for="guanti">1x Guanti (S-M-L-XL)</label>
                <div class="col col-sm-2">
                    <select class="form-control form-control-sm" id="guanti" required>
                        <option disabled selected value="">Scegli...</option>
                        <option value="OK">OK</option>
                        <option value="MANCANTE">Mancante</option>
                        <option value="Ripristinato">Ripristinato</option>
                        <option value="Parziale">Quantità inferiore</option>
                        <option value="Vedi note">Vedi note</option>
                    </select>
                </div>
            </div>
            <hr>
            <div class="form-group row">
                <label class="col-sm-4 col-form-label" for="sedia">Sedia portantina</label>
                <div class="col col-sm-2">
                    <select class="form-control form-control-sm" id="sedia" required>
                        <option disabled selected value="">Scegli...</option>
                        <option value="OK">OK</option>
                        <option value="MANCANTE">Mancante</option>
                        <option value="Guasto">Guasto</option>
                        <option value="Ripristinato">Ripristinato</option>
                        <option value="Vedi note">Vedi note</option>
                    </select>
                </div>
            </div>
            <hr>
            <div class="form-group row">
                <label class="col-sm-4 col-form-label" for="KED">Kit estricazione</label>
                <div class="col col-sm-2">
                    <select class="form-control form-control-sm" id="KED" required>
                        <option disabled selected value="">Scegli...</option>
                        <option value="OK">OK</option>
                        <option value="MANCANTE">Mancante</option>
                        <option value="Guasto">Guasto</option>
                        <option value="Ripristinato">Ripristinato</option>
                        <option value="Vedi note">Vedi note</option>
                    </select>
                </div>
            </div>
            <hr>
            <div class="form-group row">
                <label class="col-sm-4 col-form-label" for="steccobende">Set steccobende con pompa</label>
                <div class="col col-sm-2">
                    <select class="form-control form-control-sm" id="steccobende" required>
                        <option disabled selected value="">Scegli...</option>
                        <option value="OK">OK</option>
                        <option value="MANCANTE">Mancante</option>
                        <option value="Guasto">Guasto</option>
                        <option value="Ripristinato">Ripristinato</option>
                        <option value="Parziale">Quantità inferiore</option>
                        <option value="Vedi note">Vedi note</option>
                    </select>
                </div>
            </div>
            <hr>
            <div class="form-group row">
                <label class="col-sm-4 col-form-label" for="bomboleport">2x Bombole portatili 3lt.</label>
                <div class="col col-sm-2">
                    <select class="form-control form-control-sm" id="bomboleport" required>
                        <option disabled selected value="">Scegli...</option>
                        <option value="OK">OK</option>
                        <option value="MANCANTE">Mancante</option>
                        <option value="Ripristinato">Ripristinato</option>
                        <option value="Parziale">Quantità inferiore</option>
                        <option value="Vedi note">Vedi note</option>
                    </select>
                </div>
            </div>
            <hr>
            <div class="form-group row">
                <label class="col-sm-4 col-form-label" for="caschi">4x Elmetti</label>
                <div class="col col-sm-2">
                    <select class="form-control form-control-sm" id="caschi" required>
                        <option disabled selected value="">Scegli...</option>
                        <option value="OK">OK</option>
                        <option value="MANCANTE">Mancante</option>
                        <option value="Guasto">Guasto</option>
                        <option value="Ripristinato">Ripristinato</option>
                        <option value="Parziale">Quantità inferiore</option>
                        <option value="Vedi note">Vedi note</option>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-4 col-form-label" for="padella">Padella</label>
                <div class="col col-sm-2">
                    <select class="form-control form-control-sm" id="padella" required>
                        <option disabled selected value="">Scegli...</option>
                        <option value="OK">OK</option>
                        <option value="MANCANTE">Mancante</option>
                        <option value="Ripristinato">Ripristinato</option>
                        <option value="Parziale">Quantità inferiore</option>
                        <option value="Vedi note">Vedi note</option>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-4 col-form-label" for="carta">Rotolo carta</label>
                <div class="col col-sm-2">
                    <select class="form-control form-control-sm" id="carta" required>
                        <option disabled selected value="">Scegli...</option>
                        <option value="OK">OK</option>
                        <option value="MANCANTE">Mancante</option>
                        <option value="Ripristinato">Ripristinato</option>
                        <option value="Vedi note">Vedi note</option>
                    </select>
                </div>
            </div>
            <?php
            if (($select['tipo'])!=2): ?>
                <div class="form-group row">
                    <label class="col-sm-4 col-form-label" for="amputazioni">Sacca porta arti</label>
                    <div class="col col-sm-2">
                        <select class="form-control form-control-sm" id="amputazioni" required>
                            <option disabled selected value="">Scegli...</option>
                            <option value="OK">OK</option>
                            <option value="MANCANTE">Mancante</option>
                            <option value="Guasto">Guasto</option>
                            <option value="Ripristinato">Ripristinato</option>
                            <option value="Vedi note">Vedi note</option>
                        </select>
                    </div>
                </div>
            <? endif; ?>
            <hr>
            <div class="form-group row">
                <label class="col-sm-4 col-form-label" for="ragno">Ragno</label>
                <div class="col col-sm-2">
                    <select class="form-control form-control-sm" id="ragno" required>
                        <option disabled selected value="">Scegli...</option>
                        <option value="OK">OK</option>
                        <option value="MANCANTE">Mancante</option>
                        <option value="Guasto">Guasto</option>
                        <option value="Ripristinato">Ripristinato</option>
                        <option value="Vedi note">Vedi note</option>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-4 col-form-label" for="trauma">Base + fermacapo + mentoniere</label>
                <div class="col col-sm-2">
                    <select class="form-control form-control-sm" id="trauma" required>
                        <option disabled selected value="">Scegli...</option>
                        <option value="OK">OK</option>
                        <option value="MANCANTE">Mancante</option>
                        <option value="Guasto">Guasto</option>
                        <option value="Ripristinato">Ripristinato</option>
                        <option value="Vedi note">Vedi note</option>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-4 col-form-label" for="cinghie">3x Cinture scoop</label>
                <div class="col col-sm-2">
                    <select class="form-control form-control-sm" id="cinghie" required>
                        <option disabled selected value="">Scegli...</option>
                        <option value="OK">OK</option>
                        <option value="MANCANTE">Mancante</option>
                        <option value="Guasto">Guasto</option>
                        <option value="Ripristinato">Ripristinato</option>
                        <option value="Parziale">Quantità inferiore</option>
                        <option value="Vedi note">Vedi note</option>
                    </select>
                </div>
            </div>
            <hr>
            <div class="form-group row">
                <label class="col-sm-4 col-form-label" for="estintorepost">Estintore posteriore <small class="text-muted">(Lancetta sul verde)</small></label>
                <div class="col col-sm-2">
                    <select class="form-control form-control-sm" id="estintorepost" required>
                        <option disabled selected value="">Scegli...</option>
                        <option value="OK">OK</option>
                        <option value="MANCANTE">Mancante</option>
                        <option value="Esaurito">Esaurito</option>
                        <option value="Scaduto">Scaduto</option>
                    </select>
                </div>
            </div>
            <hr>
            <div class="form-group row">
                <label class="col-sm-4 col-form-label" for="coltrino">Coltrino</label>
                <div class="col col-sm-2">
                    <select class="form-control form-control-sm" id="coltrino" required>
                        <option disabled selected value="">Scegli...</option>
                        <option value="OK">OK</option>
                        <option value="MANCANTE">Mancante</option>
                        <option value="Ripristinato">Ripristinato</option>
                        <option value="Vedi note">Vedi note</option>
                    </select>
                </div>
            </div>
            <?php
            if (($select['tipo'])!=3): ?>
                <div class="form-group row">
                    <label class="col-sm-4 col-form-label" for="traslatore">Traslatore</label>
                    <div class="col col-sm-2">
                        <select class="form-control form-control-sm" id="traslatore" required>
                            <option disabled selected value="">Scegli...</option>
                            <option value="OK">OK</option>
                            <option value="MANCANTE">Mancante</option>
                            <option value="Guasto">Guasto</option>
                            <option value="Ripristinato">Ripristinato</option>
                            <option value="Vedi note">Vedi note</option>
                        </select>
                    </div>
                </div>
            <? endif; ?>
            <hr>
            <div class="alert alert-success" style="text-align: center" role="alert">
                <b>CABINA GUIDA</b>
            </div>
            <div class="form-group row">
                <label class="col-sm-4 col-form-label" for="estintoreant">Estintore anteriore <small class="text-muted">(Lancetta sul verde)</small></label>
                <div class="col col-sm-2">
                    <select class="form-control form-control-sm" id="estintoreant" required>
                        <option disabled selected value="">Scegli...</option>
                        <option value="OK">OK</option>
                        <option value="MANCANTE">Mancante</option>
                        <option value="Esaurito">Esaurito</option>
                        <option value="Scaduto">Scaduto</option>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-4 col-form-label" for="faro">Faro di ricerca</label>
                <div class="col col-sm-2">
                    <select class="form-control form-control-sm" id="faro" required>
                        <option disabled selected value="">Scegli...</option>
                        <option value="OK">OK</option>
                        <option value="MANCANTE">Mancante</option>
                        <option value="Guasto">Guasto</option>
                        <option value="Ripristinato">Ripristinato</option>
                        <option value="Vedi note">Vedi note</option>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-4 col-form-label" for="scasso">Set da scasso <small class="text-muted">(Mazzetta, cesoia, leverino, guanti da lavoro, torcia a vento)</small></label>
                <div class="col col-sm-2">
                    <select class="form-control form-control-sm" id="scasso" required>
                        <option disabled selected value="">Scegli...</option>
                        <option value="OK">OK</option>
                        <option value="MANCANTE">Mancante</option>
                        <option value="Guasto">Guasto</option>
                        <option value="Ripristinato">Ripristinato</option>
                        <option value="Parziale">Quantità inferiore</option>
                        <option value="Vedi note">Vedi note</option>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-4 col-form-label" for="bloccocv">Blocco auto CV</label>
                <div class="col col-sm-2">
                    <select class="form-control form-control-sm" id="bloccocv" required>
                        <option disabled selected value="">Scegli...</option>
                        <option value="OK">OK</option>
                        <option value="MANCANTE">Mancante</option>
                        <option value="Ripristinato">Ripristinato</option>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-4 col-form-label" for="schede118">5x Schede MSB/MSA</label>
                <div class="col col-sm-2">
                    <select class="form-control form-control-sm" id="schede118" required>
                        <option disabled selected value="">Scegli...</option>
                        <option value="OK">OK</option>
                        <option value="MANCANTE">Mancante</option>
                        <option value="Parziale">Quantità inferiore</option>
                        <option value="Vedi note">Vedi note</option>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-4 col-form-label" for="fuoriservizio">Cartello FUORI SERVIZIO</label>
                <div class="col col-sm-2">
                    <select class="form-control form-control-sm" id="fuoriservizio" required>
                        <option disabled selected value="">Scegli...</option>
                        <option value="OK">OK</option>
                        <option value="MANCANTE">Mancante</option>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-4 col-form-label" for="antifiamma">Coperta anti-fiamma</label>
                <div class="col col-sm-2">
                    <select class="form-control form-control-sm" id="antifiamma" required>
                        <option disabled selected value="">Scegli...</option>
                        <option value="OK">OK</option>
                        <option value="MANCANTE">Mancante</option>
                        <option value="Ripristinato">Ripristinato</option>
                        <option value="Vedi note">Vedi note</option>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-4 col-form-label" for="panseptil">Disinfettante</label>
                <div class="col col-sm-2">
                    <select class="form-control form-control-sm" id="panseptil" required>
                        <option disabled selected value="">Scegli...</option>
                        <option value="OK">OK</option>
                        <option value="MANCANTE">Mancante</option>
                        <option value="Guasto">Guasto</option>
                        <option value="Ripristinato">Ripristinato</option>
                        <option value="Vedi note">Vedi note</option>
                    </select>
                </div>
            </div>
            <hr>
            <div class="alert alert-info" style="text-align: center" role="alert">
                <b>CONTROLLI MEZZO</b>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="olio" value="1">
                <label class="form-check-label" for="olio">Controllo olio motore
                    <small class="text-muted">
                    <?
                    $controlloolio=$db->query("SELECT DATACHECK, OLIO from checklist WHERE IDMEZZO='$idmezzo' AND OLIO=1 ORDER BY DATACHECK DESC LIMIT 1");
                    if ($controlloolio->num_rows>0){
                        list($ultimolio)= $controlloolio->fetch_array();
                        $var2=date_create("$ultimolio");
                        echo "(ultimo controllo in data ".date_format($var2, "d-m-Y").")";
                    }
                    ?>
                    </small>
                </label>
            </div>
            <div class="form-group row">
                <label class="col-sm-4 col-form-label" for="rabbocco">Rabbocco olio motore <small class="text-muted"><?if ($idmezzo<=256):?>Tipo 5W-30<?endif;?><?if ($idmezzo>=258):?> Tipo 0W-30<?endif;?></small></label>
                <div class="col col-sm-2">
                    <select class="form-control form-control-sm" id="rabbocco">
                        <option value="0" selected="selected">NON EFFETTUATO</option>
                        <option value="0.5">0.5 Kg</option>
                        <option value="1.0">1 Kg</option>
                        <option value="1.5">1.5 Kg</option>
                        <option value="2.0">2 Kg</option>
                    </select>
                </div>
            </div>
            <hr>
            <div class="form-group row">
                <label class="col-sm-4 col-form-label" for="luci">Luci</label>
                <div class="col col-sm-2">
                    <select class="form-control form-control-sm" id="luci" required>
                        <option disabled selected value="">Scegli...</option>
                        <option value="OK">OK</option>
                        <option value="Guasto">Guasto</option>
                        <option value="Ripristinato">Ripristinato</option>
                        <option value="Vedi note">Vedi note</option>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-4 col-form-label" for="blu">Lampeggianti</label>
                <div class="col col-sm-2">
                    <select class="form-control form-control-sm" id="blu" required>
                        <option disabled selected value="">Scegli...</option>
                        <option value="OK">OK</option>
                        <option value="Guasto">Guasto</option>
                        <option value="Ripristinato">Ripristinato</option>
                        <option value="Vedi note">Vedi note</option>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-4 col-form-label" for="sirene">Sirene</label>
                <div class="col col-sm-2">
                    <select class="form-control form-control-sm" id="sirene" required>
                        <option disabled selected value="">Scegli...</option>
                        <option value="OK">OK</option>
                        <option value="Guasto">Guasto</option>
                        <option value="Ripristinato">Ripristinato</option>
                        <option value="Vedi note">Vedi note</option>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-4 col-form-label" for="gasolio">Carburante</label>
                <div class="col col-sm-2">
                    <select class="form-control form-control-sm" id="gasolio" required>
                        <option disabled selected value="">Scegli...</option>
                        <option value="OK">OK</option>
                        <option value="MANCANTE">Mancante</option>
                        <option value="Ripristinato">Ripristinato</option>
                        <option value="Vedi note">Vedi note</option>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-4 col-form-label" for="telepass">Telepass</label>
                <div class="col col-sm-2">
                    <select class="form-control form-control-sm" id="telepass" required>
                        <option disabled selected value="">Scegli...</option>
                        <option value="OK">OK</option>
                        <option value="MANCANTE">Mancante</option>
                        <option value="Guasto">Guasto</option>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-4 col-form-label" for="doc">Documenti</label>
                <div class="col col-sm-2">
                    <select class="form-control form-control-sm" id="doc" required>
                        <option disabled selected value="">Scegli...</option>
                        <option value="OK">OK</option>
                        <option value="MANCANTE">Mancante</option>
                        <option value="Vedi note">Vedi note</option>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-4 col-form-label" for="cartaagip">Carta carburante</label>
                <div class="col col-sm-2">
                    <select class="form-control form-control-sm" id="cartaagip" required>
                        <option disabled selected value="">Scegli...</option>
                        <option value="OK">OK</option>
                        <option value="MANCANTE">Mancante</option>
                        <option value="Guasto">Guasto</option>
                        <option value="Ripristinato">Ripristinato</option>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-4 col-form-label" for="lavaggioesterno">Lavaggio esterno
                    <small class="text-muted">
                        <?
                        $controlloesterno=$db->query("SELECT start_event, title, esterno from lavaggio_mezzi WHERE title='$idmezzo' AND esterno=1 ORDER BY start_event DESC LIMIT 1");
                        if ($controlloesterno->num_rows>0){
                            list($ultimoesterno)= $controlloesterno->fetch_array();
                            $var4=date_create("$ultimoesterno");

                            echo "(ultimo lavaggio in data ".date_format($var4, "d-m-Y").")";
                        }
                        ?>
                    </small>
                </label>
                <div class="col col-sm-2">
                    <select class="form-control form-control-sm" id="lavaggioesterno">
                        <option value="1">EFFETTUATO</option>
                        <option value="0" selected="selected">NON EFFETTUATO</option>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-4 col-form-label" for="lavaggiointerno">Lavaggio interno
                    <small class="text-muted">
                        <?
                        $controllointerno=$db->query("SELECT start_event, title, interno from lavaggio_mezzi WHERE title='$idmezzo' AND interno=1 ORDER BY start_event DESC LIMIT 1");
                        if ($controllointerno->num_rows>0){
                            list($ultimointerno)= $controllointerno->fetch_array();
                            $var3=date_create("$ultimointerno");

                            echo "(ultimo lavaggio in data ".date_format($var3, "d-m-Y").")";
                        }
                        ?>
                    </small>
                </label>
                <div class="col col-sm-2">
                    <select class="form-control form-control-sm" id="lavaggiointerno">
                        <option value="1">EFFETTUATO</option>
                        <option value="0" selected="selected">NON EFFETTUATO</option>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-4 col-form-label" for="disinfezione">Sanificazione
                    <small class="text-muted">
                        <?
                        $controllosanificazione=$db->query("SELECT start_event, title, neb from lavaggio_mezzi WHERE title='$idmezzo' AND neb=1 ORDER BY start_event DESC LIMIT 1");
                        if ($controllosanificazione->num_rows>0){
                            list($ultimasanificazione)= $controllosanificazione->fetch_array();
                            $var5=date_create("$ultimasanificazione");

                            echo "(ultima sanificazione in data ".date_format($var5, "d-m-Y").")";
                        }
                        ?>
                    </small>
                </label>
                <div class="col col-sm-2">
                    <select class="form-control form-control-sm" id="disinfezione">
                        <option value="1">EFFETTUATA</option>
                        <option value="0" selected="selected">NON EFFETTUATA</option>
                    </select>
                </div>
            </div>
            <?php
            if (($select['tipo'])!=3): ?>
                <div class="form-group row">
                    <label class="col-sm-4 col-form-label" for="battesedia">Batteria + caricabatteria sedia <small class="text-muted">(Dove previsto)</small></label>
                    <div class="col col-sm-2">
                        <select class="form-control form-control-sm" id="battesedia" required>
                            <option disabled selected value="">Scegli...</option>
                            <option value="OK">OK</option>
                            <option value="MANCANTE">Mancante</option>
                            <option value="Guasto">Guasto</option>
                            <option value="Vedi note">Vedi note</option>
                        </select>
                    </div>
                </div>
            <? endif; ?>
            <hr>
            <div class="alert alert-warning" style="text-align: center" role="alert">
                <b>CONTROLLO BORSA</b>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="scadenze" value="1">
                <label class="form-check-label" for="scadenze">Controllo scadenze
                    <small class="text-muted">
                        <?
                        $controlloscandenza=$db->query("SELECT DATACHECK, SCADENZE from checklist WHERE IDMEZZO='$idmezzo' AND SCADENZE=1 ORDER BY DATACHECK DESC LIMIT 1");
                        if ($controlloscandenza->num_rows>0){
                            list($ultimascadenza)= $controlloscandenza->fetch_array();
                            $var6=date_create("$ultimascadenza");

                            echo "(ultimo controllo in data ".date_format($var6, "d-m-Y").")";
                        }
                        ?>
                    </small>
                </label>
            </div>
            <hr>
            <div class="form-group row">
                <label class="col-sm-4 col-form-label" for="ambuped">Pallone autoespandibile pediatrico</label>
                <div class="col col-sm-2">
                    <select class="form-control form-control-sm" id="ambuped" required>
                        <option disabled selected value="">Scegli...</option>
                        <option value="OK">OK</option>
                        <option value="MANCANTE">Mancante</option>
                        <option value="Guasto">Guasto</option>
                        <option value="Ripristinato">Ripristinato</option>
                        <option value="Vedi note">Vedi note</option>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-4 col-form-label" for="reservoirped">Reservoire +  valvola (PED)</label>
                <div class="col col-sm-2">
                    <select class="form-control form-control-sm" id="reservoirped" required>
                        <option disabled selected value="">Scegli...</option>
                        <option value="OK">OK</option>
                        <option value="MANCANTE">Mancante</option>
                        <option value="Guasto">Guasto</option>
                        <option value="Ripristinato">Ripristinato</option>
                        <option value="Vedi note">Vedi note</option>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-4 col-form-label" for="filtroped">Filtri (2x)</label>
                <div class="col col-sm-2">
                    <select class="form-control form-control-sm" id="filtroped" required>
                        <option disabled selected value="">Scegli...</option>
                        <option value="OK">OK</option>
                        <option value="MANCANTE">Mancante</option>
                        <option value="Ripristinato">Ripristinato</option>
                        <option value="Parziale">Quantità inferiore</option>
                        <option value="Vedi note">Vedi note</option>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-4 col-form-label" for="maschereped">Mascherine (3 misure PED)</label>
                <div class="col col-sm-2">
                    <select class="form-control form-control-sm" id="maschereped" required>
                        <option disabled selected value="">Scegli...</option>
                        <option value="OK">OK</option>
                        <option value="MANCANTE">Mancante</option>
                        <option value="Ripristinato">Ripristinato</option>
                        <option value="Parziale">Quantità inferiore</option>
                        <option value="Vedi note">Vedi note</option>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-4 col-form-label" for="guedelped">Guedel (3 misure PED)</label>
                <div class="col col-sm-2">
                    <select class="form-control form-control-sm" id="guedelped" required>
                        <option disabled selected value="">Scegli...</option>
                        <option value="OK">OK</option>
                        <option value="MANCANTE">Mancante</option>
                        <option value="Ripristinato">Ripristinato</option>
                        <option value="Parziale">Quantità inferiore</option>
                        <option value="Vedi note">Vedi note</option>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-4 col-form-label" for="ossped">Raccordo ossigeno</label>
                <div class="col col-sm-2">
                    <select class="form-control form-control-sm" id="ossped" required>
                        <option disabled selected value="">Scegli...</option>
                        <option value="OK">OK</option>
                        <option value="MANCANTE">Mancante</option>
                        <option value="Ripristinato">Ripristinato</option>
                        <option value="Parziale">Quantità inferiore</option>
                        <option value="Vedi note">Vedi note</option>
                    </select>
                </div>
            </div>
            <hr>
            <div class="form-group row">
                <label class="col-sm-4 col-form-label" for="ambuadulti">Pallone autoespandibile adulti</label>
                <div class="col col-sm-2">
                    <select class="form-control form-control-sm" id="ambuadulti" required>
                        <option disabled selected value="">Scegli...</option>
                        <option value="OK">OK</option>
                        <option value="MANCANTE">Mancante</option>
                        <option value="Guasto">Guasto</option>
                        <option value="Ripristinato">Ripristinato</option>
                        <option value="Vedi note">Vedi note</option>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-4 col-form-label" for="reservoiradulti">Reservoire +  valvola ADU</label>
                <div class="col col-sm-2">
                    <select class="form-control form-control-sm" id="reservoiradulti" required>
                        <option disabled selected value="">Scegli...</option>
                        <option value="OK">OK</option>
                        <option value="MANCANTE">Mancante</option>
                        <option value="Guasto">Guasto</option>
                        <option value="Ripristinato">Ripristinato</option>
                        <option value="Vedi note">Vedi note</option>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-4 col-form-label" for="filtroadulti">Filtri (2x)</label>
                <div class="col col-sm-2">
                    <select class="form-control form-control-sm" id="filtroadulti" required>
                        <option disabled selected value="">Scegli...</option>
                        <option value="OK">OK</option>
                        <option value="MANCANTE">Mancante</option>
                        <option value="Ripristinato">Ripristinato</option>
                        <option value="Parziale">Quantità inferiore</option>
                        <option value="Vedi note">Vedi note</option>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-4 col-form-label" for="maschereadulti">Mascherine (5 misure ADU)</label>
                <div class="col col-sm-2">
                    <select class="form-control form-control-sm" id="maschereadulti" required>
                        <option disabled selected value="">Scegli...</option>
                        <option value="OK">OK</option>
                        <option value="MANCANTE">Mancante</option>
                        <option value="Ripristinato">Ripristinato</option>
                        <option value="Parziale">Quantità inferiore</option>
                        <option value="Vedi note">Vedi note</option>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-4 col-form-label" for="guedeladulti">Guedel (5 misure ADU)</label>
                <div class="col col-sm-2">
                    <select class="form-control form-control-sm" id="guedeladulti" required>
                        <option disabled selected value="">Scegli...</option>
                        <option value="OK">OK</option>
                        <option value="MANCANTE">Mancante</option>
                        <option value="Ripristinato">Ripristinato</option>
                        <option value="Parziale">Quantità inferiore</option>
                        <option value="Vedi note">Vedi note</option>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-4 col-form-label" for="ossadulti">Raccordo ossigeno</label>
                <div class="col col-sm-2">
                    <select class="form-control form-control-sm" id="ossadulti" required>
                        <option disabled selected value="">Scegli...</option>
                        <option value="OK">OK</option>
                        <option value="MANCANTE">Mancante</option>
                        <option value="Ripristinato">Ripristinato</option>
                        <option value="Vedi note">Vedi note</option>
                    </select>
                </div>
            </div>
            <hr>
            <div class="form-group row">
                <label class="col-sm-4 col-form-label" for="fisio">Fisiologiche (2x)</label>
                <div class="col col-sm-2">
                    <select class="form-control form-control-sm" id="fisio" required>
                        <option disabled selected value="">Scegli...</option>
                        <option value="OK">OK</option>
                        <option value="MANCANTE">Mancante</option>
                        <option value="Ripristinato">Ripristinato</option>
                        <option value="Parziale">Quantità inferiore</option>
                        <option value="Vedi note">Vedi note</option>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-4 col-form-label" for="h2o2">Acqua ossigenata (1x)</label>
                <div class="col col-sm-2">
                    <select class="form-control form-control-sm" id="h2o2" required>
                        <option disabled selected value="">Scegli...</option>
                        <option value="OK">OK</option>
                        <option value="MANCANTE">Mancante</option>
                        <option value="Ripristinato">Ripristinato</option>
                        <option value="Vedi note">Vedi note</option>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-4 col-form-label" for="betadine">Betadine (1x)</label>
                <div class="col col-sm-2">
                    <select class="form-control form-control-sm" id="betadine" required>
                        <option disabled selected value="">Scegli...</option>
                        <option value="OK">OK</option>
                        <option value="MANCANTE">Mancante</option>
                        <option value="Ripristinato">Ripristinato</option>
                        <option value="Vedi note">Vedi note</option>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-4 col-form-label" for="cerotti">Cerotti (2x)</label>
                <div class="col col-sm-2">
                    <select class="form-control form-control-sm" id="cerotti" required>
                        <option disabled selected value="">Scegli...</option>
                        <option value="OK">OK</option>
                        <option value="MANCANTE">Mancante</option>
                        <option value="Ripristinato">Ripristinato</option>
                        <option value="Parziale">Quantità inferiore</option>
                        <option value="Vedi note">Vedi note</option>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-4 col-form-label" for="benda">Peha haft (4x)</label>
                <div class="col col-sm-2">
                    <select class="form-control form-control-sm" id="benda" required>
                        <option disabled selected value="">Scegli...</option>
                        <option value="OK">OK</option>
                        <option value="MANCANTE">Mancante</option>
                        <option value="Ripristinato">Ripristinato</option>
                        <option value="Parziale">Quantità inferiore</option>
                        <option value="Vedi note">Vedi note</option>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-4 col-form-label" for="garze">Garze sterili (6x)</label>
                <div class="col col-sm-2">
                    <select class="form-control form-control-sm" id="garze" required>
                        <option disabled selected value="">Scegli...</option>
                        <option value="OK">OK</option>
                        <option value="MANCANTE">Mancante</option>
                        <option value="Ripristinato">Ripristinato</option>
                        <option value="Parziale">Quantità inferiore</option>
                        <option value="Vedi note">Vedi note</option>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-4 col-form-label" for="ghiaccio">Ghiaccio (3x)</label>
                <div class="col col-sm-2">
                    <select class="form-control form-control-sm" id="ghiaccio" required>
                        <option disabled selected value="">Scegli...</option>
                        <option value="OK">OK</option>
                        <option value="MANCANTE">Mancante</option>
                        <option value="Ripristinato">Ripristinato</option>
                        <option value="Parziale">Quantità inferiore</option>
                        <option value="Vedi note">Vedi note</option>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-4 col-form-label" for="arterioso">Laccio arterioso</label>
                <div class="col col-sm-2">
                    <select class="form-control form-control-sm" id="arterioso" required>
                        <option disabled selected value="">Scegli...</option>
                        <option value="OK">OK</option>
                        <option value="MANCANTE">Mancante</option>
                        <option value="Ripristinato">Ripristinato</option>
                        <option value="Vedi note">Vedi note</option>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-4 col-form-label" for="venoso">Laccio venoso</label>
                <div class="col col-sm-2">
                    <select class="form-control form-control-sm" id="venoso" required>
                        <option disabled selected value="">Scegli...</option>
                        <option value="OK">OK</option>
                        <option value="MANCANTE">Mancante</option>
                        <option value="Ripristinato">Ripristinato</option>
                        <option value="Vedi note">Vedi note</option>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-4 col-form-label" for="rasoio">Rasoio</label>
                <div class="col col-sm-2">
                    <select class="form-control form-control-sm" id="rasoio" required>
                        <option disabled selected value="">Scegli...</option>
                        <option value="OK">OK</option>
                        <option value="MANCANTE">Mancante</option>
                        <option value="Ripristinato">Ripristinato</option>
                        <option value="Vedi note">Vedi note</option>
                    </select>
                </div>
            </div>
            <hr>
            <div class="form-group row">
                <label class="col-sm-4 col-form-label" for="sfigmo">Sfigmomanometro</label>
                <div class="col col-sm-2">
                    <select class="form-control form-control-sm" id="sfigmo" required>
                        <option disabled selected value="">Scegli...</option>
                        <option value="OK">OK</option>
                        <option value="MANCANTE">Mancante</option>
                        <option value="Guasto">Guasto</option>
                        <option value="Ripristinato">Ripristinato</option>
                        <option value="Vedi note">Vedi note</option>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-4 col-form-label" for="fonendo">Fonendoscopio</label>
                <div class="col col-sm-2">
                    <select class="form-control form-control-sm" id="fonendo" required>
                        <option disabled selected value="">Scegli...</option>
                        <option value="OK">OK</option>
                        <option value="MANCANTE">Mancante</option>
                        <option value="Guasto">Guasto</option>
                        <option value="Ripristinato">Ripristinato</option>
                        <option value="Vedi note">Vedi note</option>
                    </select>
                </div>
            </div>
            <?php
            if (($select['tipo'])==3): ?>
                <div class="form-group row">
                    <label class="col-sm-4 col-form-label" for="saturimetrob">Saturimetro</label>
                    <div class="col col-sm-2">
                        <select class="form-control form-control-sm" id="saturimetrob" required>
                            <option disabled selected value="">Scegli...</option>
                            <option value="OK">OK</option>
                            <option value="MANCANTE">Mancante</option>
                            <option value="Guasto">Guasto</option>
                            <option value="Ripristinato">Ripristinato</option>
                            <option value="Vedi note">Vedi note</option>
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-4 col-form-label" for="termometro">Termometro</label>
                    <div class="col col-sm-2">
                        <select class="form-control form-control-sm" id="termometro" required>
                            <option disabled selected value="">Scegli...</option>
                            <option value="OK">OK</option>
                            <option value="MANCANTE">Mancante</option>
                            <option value="Guasto">Guasto</option>
                            <option value="Ripristinato">Ripristinato</option>
                            <option value="Vedi note">Vedi note</option>
                        </select>
                    </div>
                </div>
            <? endif; ?>
            <hr>
            <div class="form-group row">
                <label class="col-sm-4 col-form-label" for="sondini">Sondini aspiratore (5x)</label>
                <div class="col col-sm-2">
                    <select class="form-control form-control-sm" id="sondini" required>
                        <option disabled selected value="">Scegli...</option>
                        <option value="OK">OK</option>
                        <option value="MANCANTE">Mancante</option>
                        <option value="Ripristinato">Ripristinato</option>
                        <option value="Parziale">Quantità inferiore</option>
                        <option value="Vedi note">Vedi note</option>
                    </select>
                </div>
            </div>
            <hr>
            <div class="form-group row">
                <label class="col-sm-4 col-form-label" for="maschereborsa">Mascherine reservoir (2 adulti + 2 pediatriche)</label>
                <div class="col col-sm-2">
                    <select class="form-control form-control-sm" id="maschereborsa" required>
                        <option disabled selected value="">Scegli...</option>
                        <option value="OK">OK</option>
                        <option value="MANCANTE">Mancante</option>
                        <option value="Ripristinato">Ripristinato</option>
                        <option value="Parziale">Quantità inferiore</option>
                        <option value="Vedi note">Vedi note</option>
                    </select>
                </div>
            </div>
            <hr>
            <div class="form-group row">
                <label class="col-sm-4 col-form-label" for="robin">Robin</label>
                <div class="col col-sm-2">
                    <select class="form-control form-control-sm" id="robin" required>
                        <option disabled selected value="">Scegli...</option>
                        <option value="OK">OK</option>
                        <option value="MANCANTE">Mancante</option>
                        <option value="Guasto">Guasto</option>
                        <option value="Ripristinato">Ripristinato</option>
                        <option value="Vedi note">Vedi note</option>
                    </select>
                </div>
            </div>
            <hr>
            <div class="form-group row">
                <label class="col-sm-4 col-form-label" for="guantisterili">Guanti sterili (4x)</label>
                <div class="col col-sm-2">
                    <select class="form-control form-control-sm" id="guantisterili" required>
                        <option disabled selected value="">Scegli...</option>
                        <option value="OK">OK</option>
                        <option value="MANCANTE">Mancante</option>
                        <option value="Ripristinato">Ripristinato</option>
                        <option value="Parziale">Quantità inferiore</option>
                        <option value="Vedi note">Vedi note</option>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-4 col-form-label" for="telini">Telini sterili (2x)</label>
                <div class="col col-sm-2">
                    <select class="form-control form-control-sm" id="telini" required>
                        <option disabled selected value="">Scegli...</option>
                        <option value="OK">OK</option>
                        <option value="MANCANTE">Mancante</option>
                        <option value="Ripristinato">Ripristinato</option>
                        <option value="Parziale">Quantità inferiore</option>
                        <option value="Vedi note">Vedi note</option>
                    </select>
                </div>
            </div>
            <hr>
            <div class="form-group row">
                <label class="col-sm-4 col-form-label" for="metalline">Metalline (4x)</label>
                <div class="col col-sm-2">
                    <select class="form-control form-control-sm" id="metalline" required>
                        <option disabled selected value="">Scegli...</option>
                        <option value="OK">OK</option>
                        <option value="MANCANTE">Mancante</option>
                        <option value="Ripristinato">Ripristinato</option>
                        <option value="Parziale">Quantità inferiore</option>
                        <option value="Vedi note">Vedi note</option>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-4 col-form-label" for="spazzatura">Sacchetti rifiuti</label>
                <div class="col col-sm-2">
                    <select class="form-control form-control-sm" id="spazzatura" required>
                        <option disabled selected value="">Scegli...</option>
                        <option value="OK">OK</option>
                        <option value="MANCANTE">Mancante</option>
                        <option value="Ripristinato">Ripristinato</option>
                        <option value="Parziale">Quantità inferiore</option>
                        <option value="Vedi note">Vedi note</option>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-4 col-form-label" for="pappagallo">Pappagallo monouso (2x)</label>
                <div class="col col-sm-2">
                    <select class="form-control form-control-sm" id="pappagallo" required>
                        <option disabled selected value="">Scegli...</option>
                        <option value="OK">OK</option>
                        <option value="MANCANTE">Mancante</option>
                        <option value="Ripristinato">Ripristinato</option>
                        <option value="Parziale">Quantità inferiore</option>
                        <option value="Vedi note">Vedi note</option>
                    </select>
                </div>
            </div>
            <hr>
            <div class="form-group row">
                <label class="col-sm-4 col-form-label" for="dpi">Kit infettivi (3x)</label>
                <div class="col col-sm-2">
                    <select class="form-control form-control-sm" id="dpi" required>
                        <option disabled selected value="">Scegli...</option>
                        <option value="OK">OK</option>
                        <option value="MANCANTE">Mancante</option>
                        <option value="Ripristinato">Ripristinato</option>
                        <option value="Parziale">Quantità inferiore</option>
                        <option value="Vedi note">Vedi note</option>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-4 col-form-label" for="chirurgiche">Mascherine chirurgiche</label>
                <div class="col col-sm-2">
                    <select class="form-control form-control-sm" id="chirurgiche" required>
                        <option disabled selected value="">Scegli...</option>
                        <option value="OK">OK</option>
                        <option value="MANCANTE">Mancante</option>
                        <option value="Ripristinato">Ripristinato</option>
                        <option value="Parziale">Quantità inferiore</option>
                        <option value="Vedi note">Vedi note</option>
                    </select>
                </div>
            </div>
            <?php
            if (($select['tipo'])==3): ?>
                <hr>
                <div class="form-group row">
                    <label class="col-sm-4 col-form-label" for="monossido">Rilevatore gas tossici</label>
                    <div class="col col-sm-2">
                        <select class="form-control form-control-sm" id="monossido" required>
                            <option disabled selected value="">Scegli...</option>
                            <option value="OK">OK</option>
                            <option value="MANCANTE">Mancante</option>
                            <option value="Guasto">Guasto</option>
                            <option value="Ripristinato">Ripristinato</option>
                            <option value="Vedi note">Vedi note</option>
                        </select>
                    </div>
                </div>
            <? endif; ?>
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
            <div style="text-align: center;">
                <button type="submit" id="inviacheck" name="inviacheck" class="btn btn-success"><i class="fas fa-check"></i></button>
            </div>
        </form>
    </div>
</div>


</body>

<!-- FOOTER -->
<?php include('../config/include/footer.php'); ?>

</html>