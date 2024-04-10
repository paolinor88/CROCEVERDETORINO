<?php
/**
 *
 * @author     Paolo Randone
 * @author     <paolo.randone@croceverde.org>
* @version    7.4
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
echo date_format()
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <meta name="author" content="Paolo Randone">
    <title>Inserisci sanificazione</title>

    <? require "../config/include/header.html";?>

    <script>
        $(document).ready(function(){
            $('[data-toggle="popover"]').popover();
        });
    </script>

    <!-- STRUTTURA CHECKLIST -->
    <script>
        $(document).ready(function() {

            $('#inviasanificazione').on('click', function(){

                var IDMEZZO = $("#IDMEZZO").val();
                var IDOPERATORE = $("#IDOPERATORE").val();
                var DATACHECK = moment().format('YYYY-MM-DD HH:mm:ss');
                //
                var ESTERNO = $("#lavaggioesterno option:selected").val();
                var INTERNO = $("#lavaggiointerno option:selected").val();
                var SANIFICAZIONE = $("#disinfezione option:selected").val();
                //alert(IDMEZZO);
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
                            $.ajax({
                                url:"scriptsan.php",
                                type:"POST",
                                data:{IDMEZZO:IDMEZZO, IDOPERATORE:IDOPERATORE, DATACHECK:DATACHECK, ESTERNO:ESTERNO, INTERNO:INTERNO, SANIFICAZIONE:SANIFICAZIONE},
                                success:function(){
                                    swal({text:"Lavaggio inserito con successo", icon: "success", timer: 1000, button:false, closeOnClickOutside: false});
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
            <li class="breadcrumb-item active" aria-current="page">Nuova sanificazione</li>
        </ol>
    </nav>
</div>

<body>
<div class="container-fluid">
    <div class="jumbotron">
        <form>
            <div style="text-align: center;">
                Inserisci lavaggi <b>AUTO <?=$idmezzo?></b> / <b><?=$dictionaryTipo[$select['tipo']]?></b>
            </div>
            <hr>

            <input hidden id="IDMEZZO" value="<?=$idmezzo?>">
            <input hidden id="IDOPERATORE" value="<?=$idoperatore?>">
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
                        <option value="1" selected="selected">EFFETTUATA</option>
                        <option value="0" >NON EFFETTUATA</option>
                    </select>
                </div>
            </div>
            <div style="text-align: center;">
                <button type="button" id="inviasanificazione" name="inviasanificazione" class="btn btn-success"><i class="fas fa-check"></i></button>
            </div>
        </form>
    </div>
</div>


</body>

<!-- FOOTER -->
<?php include('../config/include/footer.php'); ?>

</html>