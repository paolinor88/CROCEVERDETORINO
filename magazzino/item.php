<?php
header('Access-Control-Allow-Origin: *');

session_start();
/**
 *
 * @author     Paolo Randone
 * @author     <mail@paolorandone.it>
 * @version    2.2
 * @note       Powered for Croce Verde Torino. All rights reserved
 *
 */

//parametri DB
include "../config/config.php";

//accesso consentito a logistica, segreteria,ADMIN
if (($_SESSION["livello"])<4){
    //header("Location: ../error.php");
}
//recupera variabili
if (isset($_GET["id"])){
    $id = $_GET["id"];
    $modifica = $db->query("SELECT * FROM giacenza WHERE id='$id'")->fetch_array();
}
//aggiorna
if(isset($_POST["update"])){
    $id = $_POST["id"];
    $nome = $_POST["nome"];
    $tipo = $_POST["tipo"];
    $quantita = $_POST["quantita"];
    $dettagli = $_POST["dettagli"];
    $posizione = $_POST["posizione"];
    $categoria = $_POST["categoria"];
    $fornitore = $_POST["fornitore"];
    $prezzo = $_POST["prezzo"];

    if (empty($_POST['scadenza'])){
        $update = $db->query("UPDATE giacenza SET nome='$nome', tipo='$tipo', quantita='$quantita', scadenza=NULL, dettagli='$dettagli', posizione='$posizione', categoria='$categoria', fornitore='$fornitore', prezzo='$prezzo' WHERE id='$id'");
    }else{
        $scadenza = $_POST["scadenza"]."-01";
        $update = $db->query("UPDATE giacenza SET nome='$nome', tipo='$tipo', quantita='$quantita', scadenza='$scadenza', dettagli='$dettagli', posizione='$posizione', categoria='$categoria', fornitore='$fornitore', prezzo='$prezzo' WHERE id='$id'");
    }

    if ($update){
        echo '<script type="text/javascript">
        alert("Operazione eseguita con successo");
        location.href="magazzino.php";
        </script>';
    }else{
        echo '<script type="text/javascript">
        alert("ERRORE");
        location.href="magazzino.php";
        </script>';
    }
}
//delete
if(isset($_POST["delete"])){
    $id= $_POST["id"];

    $delete = $db->query("DELETE FROM giacenza WHERE id='$id'");

    if($delete){
        echo '<script type="text/javascript">
        alert("Articolo cancellato con successo");
        location.href="magazzino.php";
        </script>';
    }else{
        echo '<script type="text/javascript">
        alert("ERRORE");
        location.href="magazzino.php";
        </script>';
    }
}
$dictionaryCategoria = array (
    1 => "Materiale di consumo",
    2 => "Ricambi",
    3 => "Altro",
    4 => "Vestiario",
);
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <meta name="author" content="Paolo Randone">
    <title>Dettaglio</title>

    <? require "../config/include/header.html";?>

    <script>
        $(document).ready(function(){
            $('[data-toggle="popover"]').popover();
        });
    </script>
    <script>
        $(document).ready(function () {
            $('input[type="text"]').on('keypress', function() {
                var $this = $(this), value = $this.val();
                if (value.length === 1) {
                    $this.val( value.charAt(0).toUpperCase() );
                }
            });
        })
    </script>
    <script>
        $(document).ready(function () {
            $('#indietro').on('click', function(){
                location.href='magazzino.php';
            });
        })
    </script>
    <script>
        $( function() {
            $( "#" ).datepicker({ dateFormat: 'dd-mm-yy' });
        } );
    </script>
</head>
<!-- NAVBAR
<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../index.php" style="color: #078f40">Home</a></li>
            <li class="breadcrumb-item"><a href="index.php" style="color: #078f40">Magazzino</a></li>
            <li class="breadcrumb-item"><a href="magazzino.php" style="color: #078f40">Giacenza</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?=$modifica['nome']?></li>
        </ol>
    </nav>
</div>-->
<body>
<div class="container-fluid">

    <div class="jumbotron">
        <form method="post" action="item.php">
            <input hidden id="id" name="id" value="<?=$id?>">
            <h4  style="text-align: center"><?=$modifica['nome']?></h4>
            <?
            $today = date("Y-m");
            $rif = strtotime("+2 months", strtotime($today));
            $scadenza = strtotime($modifica['scadenza']);

            if($modifica['scadenza']==NULL || $rif<$scadenza) {
            }else{
                echo "<div class=\"alert alert-danger\" role=\"alert\" style='text-align: center'><b>ARTICOLO IN SCADENZA</b></div>";
            };
            /*$minimo = 5;
            if($modifica['quantita']!=NULL && $modifica['quantita']<$minimo){
                echo "<div class=\"alert alert-danger\" role=\"alert\" style='text-align: center'><b>QUANTITA' LIMITATA</b></div>";
            }*/

            ?>
            <hr>
            <div class="form-row">
                <div class="form-group col-md-3">
                    <label for="nome">Nome</label>
                    <input type="text" class="form-control form-control-sm" id="nome" name="nome" value="<?=$modifica['nome']?>" autofocus>
                </div>
                <div class="form-group col-md-3">
                    <label for="tipo">Tipo</label>
                    <input type="text" class="form-control form-control-sm" id="tipo" name="tipo" value="<?=$modifica['tipo']?>">
                </div>
                <div class="form-group col-md-3">
                    <label for="quantita">Posizione</label>
                    <input type="text" class="form-control form-control-sm" id="posizione" name="posizione" value="<?=$modifica['posizione']?>">
                </div>
                <div class="form-group col-md-3">
                    <label for="quantita">Quantità</label>
                    <input type="text" class="form-control form-control-sm" id="quantita" name="quantita" value="<?=$modifica['quantita']?>">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-3">
                    <label for="scadenza">Scadenza</label>
                    <input type="text" class="form-control form-control-sm" id="scadenza" name="scadenza" placeholder="AAAA-MM" value="<?=substr($modifica['scadenza'], 0, 7)?>" pattern="[0-9]{4}-(0[1-9]|1[012])">
                </div>
                <div class="form-group col-md-3">
                    <label for="categoria">Categoria</label>
                    <select class="form-control form-control-sm" id="categoria" name="categoria">
                        <?
                        for($a=1;$a<5;$a++){
                            ($a==$modifica['categoria'])? $sel="selected" : $sel="";
                            echo "<option $sel value='$a'>".$dictionaryCategoria[$a]."</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <label for="fornitore">Fornitore</label>
                    <input type="text" class="form-control form-control-sm" id="fornitore" name="fornitore" value="<?=$modifica['fornitore']?>">
                </div>
                <div class="form-group col-md-3">
                    <label for="prezzo">Prezzo</label>
                    <input type="text" class="form-control form-control-sm" id="prezzo" name="prezzo" value="<?=$modifica['prezzo']?>">
                </div>
            </div>
            <div class="form-group">
                <label for="note_evento">Dettagli</label>
                <textarea rows="4" type="text" maxlength="250" class="form-control form-control-sm" id="dettagli" name="dettagli"><?=$modifica['dettagli']?></textarea>
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
            <hr>
            <center>
                <div class="btn-group" role="group">
                    <button type="submit" class="btn btn-sm btn-outline-success" id="update" name="update"><i class="fas fa-check"></i></button>
                    <a href="magazzino.php" class="btn btn-sm btn-outline-secondary" id="indietro"><i class="fas fa-undo"></i></a>
                    <button type="button" class="btn btn-sm btn-outline-danger" id="doublemodal" data-toggle="modal" data-target="#modaldelete"><i class="far fa-trash-alt"></i></button>
                </div>
                <!--<br>
                <font size="-1"><em><i class="fas fa-check" style="color: #1a712c"></i> Salva e chiudi<br>
                        <font size="-1"><em><i class="fas fa-check" style="color: grey"></i> Indietro<br>
                                <i class="far fa-trash-alt" style="color: #CC0000"></i> Cancella elemento</em></font>
-->            </center>

            <div class="modal" id="modaldelete" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalCenterTitle">Confermi l'eliminazione?</h5>
                        </div>
                        <div class="modal-body">
                            <p>Premendo conferma, l'articolo selezionato e tutti i suoi dettagli andranno persi.</p>
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
