<?php
/**
 *
 * @author     Paolo Randone
 * @author     <paolo.randone@croceverde.org>
 * @version    6.0
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
    <title>Inserisci segnalazione</title>

    <? require "../config/include/header.html";?>

    <script>
        $(document).ready(function(){
            $('[data-toggle="popover"]').popover();
        });
    </script>

    <!-- STRUTTURA CHECKLIST -->
    <script>
        $(document).ready(function() {

            $('#inviasegnalazione').on('click', function(){

                var IDMEZZO = $("#IDMEZZO").val();
                var IDOPERATORE = $("#IDOPERATORE").val();
                var tipo = $("#tipo").val();
                var DATACHECK = moment().format('YYYY-MM-DD HH:mm:ss');
                //
                var solonote = $("#note").val();
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
                                url:"sendnote.php",
                                type:"POST",
                                data:{IDMEZZO:IDMEZZO, IDOPERATORE:IDOPERATORE, tipo:tipo, DATACHECK:DATACHECK, solonote:solonote},
                                success:function(){
                                    swal({text:"Segnalazione inviata con successo", icon: "success", timer: 1000, button:false, closeOnClickOutside: false});
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
            <li class="breadcrumb-item active" aria-current="page">Nuova segnalazione</li>
        </ol>
    </nav>
</div>

<body>
<div class="container-fluid">
    <div class="jumbotron">
        <form>
            <div style="text-align: center;">
                <b><?=$idoperatore?> <?=$cognome?> <?=$nome?></b> / <b>AUTO <?=$idmezzo?></b> / <b><?=$dictionaryTipo[$select['tipo']]?></b>
            </div>
            <hr>
            <?php
            $notealert = $db->query("SELECT DATACHECK, NOTE FROM checklist WHERE IDMEZZO='$idmezzo' AND NOTE!='' AND STATO!='3' ORDER BY DATACHECK DESC");
            if ($notealert->num_rows > 0) {
                echo "<div class=\"alert alert-danger\" role=\"alert\">
                        <h5 class=\"alert-heading\" STYLE='text-align: center'>Segnalazioni attive!</h5>";
                while($ciclo = $notealert->fetch_array()){
                    $var=$ciclo['DATACHECK'];$var1=date_create("$var", timezone_open("Europe/Rome"));
                    echo "<p style='font-size: small'>".date_format($var1, "d-m-Y H:m")." -> ".$ciclo['NOTE']."</p>";
                }
                echo "</div><hr>";
            }
            ?>

            <input hidden id="IDMEZZO" value="<?=$idmezzo?>">
            <input hidden id="IDOPERATORE" value="<?=$idoperatore?>">
            <input hidden id="tipo" value="<?=$select['tipo']?>">
            <div class="form-group">
                <label for="note">TESTO:</label>
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
                <button type="button" id="inviasegnalazione" name="inviasegnalazione" class="btn btn-success"><i class="fas fa-check"></i></button>
            </div>
        </form>
    </div>
</div>


</body>

<!-- FOOTER -->
<?php include('../config/include/footer.php'); ?>

</html>