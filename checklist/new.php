<?php
/**
 *
 * @author     Paolo Randone
 * @author     <mail@paolorandone.it>
 * @version    5.0
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



?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <meta name="author" content="Paolo Randone">
    <title>Inserisci checklist</title>

    <? require "../config/include/header.html";?>
    <script rel="stylesheet" src="https://cdn.jsdelivr.net/npm/bs-custom-file-input/dist/bs-custom-file-input.js"></script>

    <script>
        $(document).ready(function(){
            $('[data-toggle="popover"]').popover();
        });
    </script>

    <script>
        $(document).ready(function () {
            bsCustomFileInput.init()
        })
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
                var ESTERNO = $("input[name='lavaggioesterno']:checked").val();
                var INTERNO = $("input[name='lavaggiointerno']:checked").val();
                var SANIFICAZIONE = $("input[name='disinfezione']:checked").val();
                var SCADENZE = $("#scadenze:checked").val();
                var OLIO = $("#olio:checked").val();

                //CONTROLLI AMBULANZA
                ////VANO SANITARIO
                //from check_msb
                var spinale = $("input[name='spinale']:checked").val();
                var ragno = $("input[name='ragno']:checked").val();
                var trauma = $("input[name='trauma']:checked").val();
                var cinghie = $("input[name='cinghie']:checked").val();
                var scoop = $("input[name='scoop']:checked").val();
                var collari = $("input[name='collari']:checked").val();
                var KED = $("input[name='KED']:checked").val();
                var steccobende = $("input[name='steccobende']:checked").val();
                var maschere = $("input[name='maschere']:checked").val();
                var bombolefisse = $("input[name='bombolefisse']:checked").val();
                var bomboleport = $("input[name='bomboleport']:checked").val();
                var aspiratore = $("input[name='aspiratore']:checked").val();
                var DAE = $("input[name='DAE']:checked").val();
                var guanti = $("input[name='guanti']:checked").val();
                var taglienti = $("input[name='taglienti']:checked").val();
                var lenzuola = $("input[name='lenzuola']:checked").val();
                var caschi = $("input[name='caschi']:checked").val();
                var padella = $("input[name='padella']:checked").val();
                var carta = $("input[name='carta']:checked").val();
                var coltrino = $("input[name='coltrino']:checked").val();
                var sedia = $("input[name='sedia']:checked").val();
                var estintorepost = $("input[name='estintorepost']:checked").val();
                //end check_msb
                var battesedia = $("input[name='battesedia']:checked").val();

                //CABINA
                //from check_cabina
                var estintoreant = $("input[name='estintoreant']:checked").val();
                var faro = $("input[name='faro']:checked").val();
                var scasso = $("input[name='scasso']:checked").val();
                var bloccocv = $("input[name='bloccocv']:checked").val();
                var schede118 = $("input[name='schede118']:checked").val();
                var fuoriservizio = $("input[name='fuoriservizio']:checked").val();
                var antifiamma = $("input[name='antifiamma']:checked").val();
                var panseptil = $("input[name='panseptil']:checked").val();
                //end check_cabina

                //from check_msa
                var elettrodi = $("input[name='elettrodi']:checked").val();
                var gel = $("input[name='gel']:checked").val();
                var ecg = $("input[name='ecg']:checked").val();
                var sixlead = $("input[name='sixlead']:checked").val();
                var fourlead = $("input[name='fourlead']:checked").val();
                var saturimetro = $("input[name='saturimetro']:checked").val();
                var pacing = $("input[name='pacing']:checked").val();
                var circuitoventilatore = $("input[name='circuitoventilatore']:checked").val();
                var piastre = $("input[name='piastre']:checked").val();
                var LP = $("input[name='LP']:checked").val();
                var cavoLP = $("input[name='cavoLP']:checked").val();
                var batterieLP = $("input[name='batterieLP']:checked").val();
                var ventilatore = $("input[name='ventilatore']:checked").val();
                var cavovent12 = $("input[name='cavovent12']:checked").val();
                var cavovent220 = $("input[name='cavovent220']:checked").val();
                var pompa = $("input[name='pompa']:checked").val();
                var cavopompa12 = $("input[name='cavopompa12']:checked").val();
                var cavopompa220 = $("input[name='cavopompa220']:checked").val();
                var cpap = $("input[name='cpap']:checked").val();
                //end check_msa

                //CONTROLLI MEZZO
                var oliocheck = $("#olio").prop("checked") ? 'EFFETTUATO' : 'NON EFFETTUATO';
                var rabbocco = $("#rabbocco option:selected").val();
                var kilometriolio = $("#kilometriolio").val();

                var lavaggioesternotext = $("input[name='lavaggioesterno']:checked").next('label').text();
                var lavaggiointernotext = $("input[name='lavaggiointerno']:checked").next('label').text();
                var disinfezionetext = $("input[name='disinfezione']:checked").next('label').text();

                //from check_mezzo
                var luci = $("input[name='luci']:checked").val();
                var blu = $("input[name='blu']:checked").val();
                var sirene = $("input[name='sirene']:checked").val();
                var gasolio = $("input[name='gasolio']:checked").val();
                var telepass = $("input[name='telepass']:checked").val();
                var doc = $("input[name='doc']:checked").val();
                var cartaagip = $("input[name='cartaagip']:checked").val();
                //end check_mezzo

                //SOLO 118
                var tablet = $("input[name='tablet']:checked").val();
                var pedimate = $("input[name='pedimate']:checked").val();
                //SOLO SEDE
                var traslatore = $("input[name='traslatore']:checked").val();


                //CONTROLLI BORSA
                var scadenzeborsa = $("#scadenze").prop("checked") ? 'EFFETTUATO' : 'NON EFFETTUATO';
                //from check_borsa
                var ambuped = $("input[name='ambuped']:checked").val();
                var reservoirped = $("input[name='reservoirped']:checked").val();
                var filtroped = $("input[name='filtroped']:checked").val();
                var maschereped = $("input[name='maschereped']:checked").val();
                var guedelped = $("input[name='guedelped']:checked").val();
                var ossped = $("input[name='ossped']:checked").val();
                var ambuadulti = $("input[name='ambuadulti']:checked").val();
                var reservoiradulti = $("input[name='reservoiradulti']:checked").val();
                var filtroadulti = $("input[name='filtroadulti']:checked").val();
                var maschereadulti = $("input[name='maschereadulti']:checked").val();
                var guedeladulti = $("input[name='guedeladulti']:checked").val();
                var ossadulti = $("input[name='ossadulti']:checked").val();
                var fisio = $("input[name='fisio']:checked").val();
                var h2o2 = $("input[name='h2o2']:checked").val();
                var betadine = $("input[name='betadine']:checked").val();
                var cerotti = $("input[name='cerotti']:checked").val();
                var benda = $("input[name='benda']:checked").val();
                var garze = $("input[name='garze']:checked").val();
                var ghiaccio = $("input[name='ghiaccio']:checked").val();
                var arterioso = $("input[name='arterioso']:checked").val();
                var venoso = $("input[name='venoso']:checked").val();
                var rasoio = $("input[name='rasoio']:checked").val();
                var sfigmo = $("input[name='sfigmo']:checked").val();
                var fonendo = $("input[name='fonendo']:checked").val();
                var sondini = $("input[name='sondini']:checked").val();
                var maschereborsa = $("input[name='maschereborsa']:checked").val();
                var robin = $("input[name='robin']:checked").val();
                var guantisterili = $("input[name='guantisterili']:checked").val();
                var telini = $("input[name='telini']:checked").val();
                var metalline = $("input[name='metalline']:checked").val();
                var spazzatura = $("input[name='spazzatura']:checked").val();
                var pappagallo = $("input[name='pappagallo']:checked").val();
                var dpi = $("input[name='dpi']:checked").val();
                var chirurgiche = $("input[name='chirurgiche']:checked").val();
                //if 118
                var monossido = $("input[name='monossido']:checked").val();
                var saturimetrob = $("input[name='saturimetrob']:checked").val();
                var termometro = $("input[name='termometro']:checked").val();

                var note = $("#note").val();
                var file = $("#file").val();

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
                                data:{IDMEZZO:IDMEZZO, IDOPERATORE:IDOPERATORE, tipo:tipo, DATACHECK:DATACHECK, ESTERNO:ESTERNO, INTERNO:INTERNO, SANIFICAZIONE:SANIFICAZIONE, SCADENZE:SCADENZE, OLIO:OLIO, note:note, file:file, kilometriolio:kilometriolio,
                                    spinale:spinale, scoop:scoop, collari:collari, elettrodi:elettrodi, gel:gel, ecg:ecg, sixlead:sixlead,
                                    fourlead:fourlead, saturimetro:saturimetro, pacing:pacing, circuitoventilatore:circuitoventilatore,
                                    maschere:maschere, piastre:piastre, LP:LP, cavoLP:cavoLP, batterieLP:batterieLP, aspiratore:aspiratore,
                                    ventilatore:ventilatore, cavovent12:cavovent12, cavovent220:cavovent220, pompa:pompa, cavopompa12:cavopompa12,
                                    cavopompa220:cavopompa220, bombolefisse:bombolefisse, taglienti:taglienti, DAE:DAE, lenzuola:lenzuola,
                                    cpap:cpap, pedimate:pedimate, guanti:guanti, sedia:sedia, KED:KED, steccobende:steccobende,
                                    bomboleport:bomboleport, caschi:caschi, padella:padella, carta:carta,
                                    ragno:ragno, trauma:trauma, cinghie:cinghie, estintorepost:estintorepost, coltrino:coltrino,
                                    traslatore:traslatore, estintoreant:estintoreant, faro:faro, scasso:scasso,
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
            if ($notealert->num_rows > 0) { ?>

            <div class="alert alert-danger" role="alert">
                <h5 class="alert-heading" STYLE='text-align: center'>Segnalazioni attive!</h5>
                <?
                while($ciclo = $notealert->fetch_array()){
                    $var=$ciclo['DATACHECK'];$var1=date_create("$var");
                    echo "<p style='font-size: small'>".date_format($var1, "d-m-Y H:i")." -> ".$ciclo['NOTE']."</p>";
                }
                echo "</div><hr>";
                }
                ?>
                <?php
                $query = $db->query("SELECT * FROM images WHERE id_mezzo='$idmezzo' AND status!=3 AND status !=4 ORDER BY id DESC");

                if($query->num_rows > 0){ ?>
                    <div class="accordion" id="accordionExample">
                        <div class="card">
                            <h2 class="mb-0">
                                <div class="alert alert-danger" role="alert">
                                    <button class="btn btn-block text-left collapsed alert-heading" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                        <h5 class="alert-heading" STYLE='text-align: center'><i class="fas fa-camera"></i> Vedi foto</h5>
                                    </button>
                                </div>
                            </h2>
                            <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordionExample">
                                <div class="card-body">
                                    <?
                                    while($row = $query->fetch_assoc()){
                                        $imageURL = 'uploads/'.$row["file_name"];
                                        ?>
                                        <img src="<?php echo $imageURL; ?>" alt="" width="200" class="img-thumbnail" />
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br>
                    <?
                }?>

                <input hidden id="IDMEZZO" value="<?=$idmezzo?>">
                <input hidden id="IDOPERATORE" value="<?=$idoperatore?>">
                <input hidden id="tipo" value="<?=$select['tipo']?>">
                <div class="form-group row">
                    <select class="form-control form-control-sm" id="blank" required hidden>
                        <option disabled selected value="">blank</option>
                    </select>
                </div>

                <? include "../config/template/check_msb.html"; ?>

                <?php
                if (($select['tipo'])!=3): ?>
                    <!-- TRASLATORE -->
                    <hr>
                    <div class="form-group row" id="traslatore">
                        <label class="col-sm-4 col-form-label" for="traslatore"><b>Traslatore</b></label>
                        <div class="col col-sm-2">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="traslatore" id="traslatore1" value="OK" required>
                                <label class="form-check-label" for="traslatore1">OK</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="traslatore" id="traslatore2" value="MANCANTE" required>
                                <label class="form-check-label" for="traslatore2">Mancante</label>
                            </div>
                        </div>
                        <div class="col col-sm-2">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="traslatore" id="traslatore3" value="Ripristinato" required>
                                <label class="form-check-label" for="traslatore3">Ripristinato</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="traslatore" id="traslatore4" value="Vedi note" required>
                                <label class="form-check-label" for="traslatore4">Vedi note</label>
                            </div>
                        </div>
                    </div>
                <? endif; ?>
                <?php
                if (($select['tipo'])==2){
                    include "../config/template/check_msa.html";
                }
                ?>
                <?php
                if (($select['tipo'])==3): ?>
                    <!-- PEDIMATE -->
                    <hr>
                    <div class="form-group row" id="pedimate">
                        <label class="col-sm-4 col-form-label" for="pedimate"><b>Pedi-Mate</b></label>
                        <div class="col col-sm-2">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="pedimate" id="pedimate1" value="OK" required>
                                <label class="form-check-label" for="pedimate1">OK</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="pedimate" id="pedimate2" value="MANCANTE" required>
                                <label class="form-check-label" for="pedimate2">Mancante</label>
                            </div>
                        </div>
                        <div class="col col-sm-2">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="pedimate" id="pedimate3" value="Ripristinato" required>
                                <label class="form-check-label" for="pedimate3">Ripristinato</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="pedimate" id="pedimate4" value="Vedi note" required>
                                <label class="form-check-label" for="pedimate4">Vedi note</label>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <!-- TABLET -->
                    <div class="form-group row" id="tablet">
                        <label class="col-sm-4 col-form-label" for="tablet"><b>Tablet + stampante</b> <small class="text-muted">(SOLO PER MSB)</small></label>
                        <div class="col col-sm-2">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="tablet" id="tablet1" value="OK" required>
                                <label class="form-check-label" for="tablet1">OK</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="tablet" id="tablet2" value="MANCANTE" required>
                                <label class="form-check-label" for="tablet2">Mancante</label>
                            </div>
                        </div>
                        <div class="col col-sm-2">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="tablet" id="tablet3" value="Ripristinato" required>
                                <label class="form-check-label" for="tablet3">Ripristinato</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="tablet" id="tablet4" value="Vedi note" required>
                                <label class="form-check-label" for="tablet4">Vedi note</label>
                            </div>
                        </div>
                    </div>
                <? endif; ?>
                <hr>
                <? include "../config/template/check_cabina.html"; ?>
                <div class="alert alert-info" style="text-align: center" role="alert">
                    <b>CONTROLLI MEZZO</b>
                </div>
                <div class="form-group form-check">
                    <input type="checkbox" class="form-check-input" id="olio" value="1">
                    <label class="form-check-label" for="olio"><b>Controllo olio motore</b>
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
                    <label class="col-sm-4 col-form-label" for="rabbocco"><b>Rabbocco olio motore</b> <small class="text-muted"><?if ($idmezzo<=256):?>Tipo 5W-30<?endif;?><?if ($idmezzo>=258):?> Tipo 0W-30<?endif;?></small></label>
                    <div class="col col-sm-2">
                        <select class="form-control form-control-sm" id="rabbocco">
                            <option value="0" selected="selected">NON EFFETTUATO</option>
                            <option value="0.5">0.5 Kg</option>
                            <option value="1.0">1 Kg</option>
                            <option value="1.5">1.5 Kg</option>
                            <option value="2.0">2 Kg</option>
                        </select>
                    </div>
                    <label class="col-sm-4 col-form-label" for="rabbocco"><b>Kilometri:</b></label>
                    <div class="col col-sm-2">
                        <input type="text" class="form-control form-control-sm" id="kilometriolio">
                    </div>
                </div>
                <hr>
                <div class="form-group row" id="lavaggioesterno">
                    <label class="col-sm-4 col-form-label" for="lavaggioesterno"><b>Lavaggio esterno</b>
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
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="lavaggioesterno" id="lavaggioesterno1" value="1" required>
                            <label class="form-check-label" for="lavaggioesterno1">EFFETTUATO</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="lavaggioesterno" id="lavaggioesterno2" value="0" CHECKED required>
                            <label class="form-check-label" for="lavaggioesterno2">NON EFFETTUATO</label>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="form-group row" id="lavaggiointerno">
                    <label class="col-sm-4 col-form-label" for="lavaggiointerno"><b>Lavaggio interno</b>
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
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="lavaggiointerno" id="lavaggiointerno1" value="1" required>
                            <label class="form-check-label" for="lavaggiointerno1">EFFETTUATO</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="lavaggiointerno" id="lavaggiointerno2" value="0" CHECKED required>
                            <label class="form-check-label" for="lavaggiointerno2">NON EFFETTUATO</label>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="form-group row" id="disinfezione">
                    <label class="col-sm-4 col-form-label" for="disinfezione"><b>Sanificazione</b>
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
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="disinfezione" id="disinfezione1" value="1" required>
                            <label class="form-check-label" for="disinfezione1">EFFETTUATA</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="disinfezione" id="disinfezione2" value="0" CHECKED required>
                            <label class="form-check-label" for="disinfezione2">NON EFFETTUATA</label>
                        </div>
                    </div>
                </div>
                <?php
                if (($select['tipo'])!=3): ?>
                    <hr>
                    <div class="form-group row" id="battesedia">
                        <label class="col-sm-4 col-form-label" for="battesedia"><b>Batteria + caricabatteria sedia</b><small class="text-muted"> (Dove previsto)</small></label>
                        <div class="col col-sm-2">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="battesedia" id="battesedia1" value="OK" required>
                                <label class="form-check-label" for="battesedia1">OK</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="battesedia" id="battesedia2" value="MANCANTE" required>
                                <label class="form-check-label" for="battesedia2">Mancante</label>
                            </div>
                        </div>
                        <div class="col col-sm-2">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="battesedia" id="battesedia3" value="Guasto" required>
                                <label class="form-check-label" for="battesedia3">Guasto</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="battesedia" id="battesedia4" value="Vedi note" required>
                                <label class="form-check-label" for="battesedia4">Vedi note</label>
                            </div>
                        </div>
                    </div>
                <? endif; ?>
                <hr>
                <? include "../config/template/check_mezzo.html"; ?>
                <div class="alert alert-warning" style="text-align: center" role="alert">
                    <b>CONTROLLO BORSA</b>
                </div>
                <div class="form-group form-check">
                    <input type="checkbox" class="form-check-input" id="scadenze" value="1">
                    <label class="form-check-label" for="scadenze"><b>Controllo scadenze</b>
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
                <? include "../config/template/check_borsa.html"; ?>

                <?php
                if (($select['tipo'])==3): ?>
                    <div class="form-group row" id="saturimetrob">
                        <label class="col-sm-4 col-form-label" for="saturimetrob"><b>Saturimetro</b></label>
                        <div class="col col-sm-2">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="saturimetrob" id="saturimetrob1" value="OK" required>
                                <label class="form-check-label" for="saturimetrob1">OK</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="saturimetrob" id="saturimetrob2" value="GUASTO" required>
                                <label class="form-check-label" for="saturimetrob2">Guasto</label>
                            </div>
                        </div>
                        <div class="col col-sm-2">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="saturimetrob" id="saturimetrob3" value="MANCANTE" required>
                                <label class="form-check-label" for="saturimetrob3">Mancante</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="saturimetrob" id="saturimetrob4" value="Vedi note" required>
                                <label class="form-check-label" for="saturimetrob4">Vedi note</label>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="form-group row" id="termometro">
                        <label class="col-sm-4 col-form-label" for="termometro"><b>Termometro</b></label>
                        <div class="col col-sm-2">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="termometro" id="termometro1" value="OK" required>
                                <label class="form-check-label" for="termometro1">OK</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="termometro" id="termometro2" value="GUASTO" required>
                                <label class="form-check-label" for="termometro2">Guasto</label>
                            </div>
                        </div>
                        <div class="col col-sm-2">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="termometro" id="termometro3" value="MANCANTE" required>
                                <label class="form-check-label" for="termometro3">Mancante</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="termometro" id="termometro4" value="Vedi note" required>
                                <label class="form-check-label" for="termometro4">Vedi note</label>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="form-group row" id="monossido">
                        <label class="col-sm-4 col-form-label" for="monossido"><b>Rilevatore gas tossici</b></label>
                        <div class="col col-sm-2">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="monossido" id="monossido1" value="OK" required>
                                <label class="form-check-label" for="monossido1">OK</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="monossido" id="monossido2" value="GUASTO" required>
                                <label class="form-check-label" for="monossido2">Guasto</label>
                            </div>
                        </div>
                        <div class="col col-sm-2">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="monossido" id="monossido3" value="MANCANTE" required>
                                <label class="form-check-label" for="monossido3">Mancante</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="monossido" id="monossido4" value="Vedi note" required>
                                <label class="form-check-label" for="monossido4">Vedi note</label>
                            </div>
                        </div>
                    </div>
                    <hr>
                <? endif; ?>
                <div class="form-group">
                    <label for="note"><b>Segnalazioni</b></label>
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