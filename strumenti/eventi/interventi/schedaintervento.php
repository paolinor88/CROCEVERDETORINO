<?php
header('Access-Control-Allow-Origin: *');

session_start();
/**
 *
 * @author     Paolo Randone
 * @author     <paolo.randone@croceverde.org>
 * @version    8.2
 * @note       Powered for Croce Verde Torino. All rights reserved
 *
 */

include "../config/config.php";

/*
if (($_SESSION["livello"])<4){
    //header("Location: ../error.php");
}
*/

if (isset($_GET["ID_INTERVENTO"])){
    $id = $_GET["ID_INTERVENTO"];
    $modifica = $db->query("SELECT * FROM interventi WHERE ID_INTERVENTO='$id'")->fetch_array();
}

if(isset($_POST["update"])){
    $id = $_POST['id'];
    $inizio = $_POST["inizio"];
    $cognome = $_POST["cognome"];
    $nome = $_POST["nome"];
    $nascita = $_POST["nascita"];
    $indirizzo = $_POST["indirizzo"];
    $telefono = $_POST["telefono"];
    $squadra = $_POST["squadra"];
    $posizione = $_POST["posizione"];
    $patologia = $_POST["patologia"];
    $gravita = $_POST["gravita"];
    $esito = $_POST["esito"];
    $stato = $_POST["stato"];
    $note = $_POST["note"];
    $fine = $_POST["fine"];

    $stmt = $db->prepare("UPDATE interventi SET ORAINIZIO=?, COGNOME=?, NOME=?, NASCITA=?, INDIRIZZO=?, TELEFONO=?, SQUADRA=?, POSTAZIONE=?, CODICEPATOLOGIA=?, CODICEGRAVITA=?, ESITO=?, STATO=?, NOTE=?, ORAFINE=? WHERE ID_INTERVENTO=?");
    $stmt->bind_param("sssssssssssssss", $inizio, $cognome, $nome, $nascita, $indirizzo, $telefono, $squadra, $posizione, $patologia, $gravita, $esito, $stato, $note, $fine, $id);
    $update = $stmt->execute();

    if ($update){
        $message_type = 'success';
    }else{
        $message_type = 'error';
    }

    header('Location: list.php?message=' . $message_type);
    exit();
}


if(isset($_POST["delete"])){
    $id = $_POST['id'];
    $delete = $db->query("DELETE FROM interventi WHERE ID_INTERVENTO='$id'");

    if ($delete){
        $message_type = 'success';
    }else{
        $message_type = 'error';
    }

    header('Location: list.php?message=' . $message_type);
    exit();
}

$dictionaryPatologia = array (
    1 => "MEDICO",
    2 => "TRAUMA",
);
$dictionaryGravita = array (
    1 => "Verde",
    2 => "Giallo",
    3 => "Rosso",
);
$dictionaryStato = array (
    1 => "IN CORSO",
    2 => "OSPEDALIZZATO",
    3 => "RIFIUTA",
    4 => "DIMESSO",
);
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="Paolo Randone">

    <title>Gestione eventi CV-TO</title>

    <?php require "../config/include/header.html"; ?>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../config/include/custom.css?v=<?= time() ?>">

    <script>
        $(document).ready(function(){
            $('[data-toggle="popover"]').popover();
            moment.updateLocale('it', null);
        });
    </script>
    <script>
        $(document).ready(function () {
            $('input[type="text"]').on('keyup', function() {
                $(this).val($(this).val().toUpperCase());
            });
        })
    </script>
    <script>
        function yesnoCheck(that) {
            if (that.value == "") {
                alert("Inserisci la postazione nella casella 'ALTRA POSTAZIONE'");
                document.getElementById("ifYes").style.display = "block";
            } else {
                document.getElementById("ifYes").style.display = "none";
            }
        }
    </script>
</head>
<body>
<?php include "../config/include/navbar.php"; ?>

<div class="container-fluid px-2 mb-4">
    <div class="card card-cv">
        <form name="check" method="post" action="schedaintervento.php">
            <input hidden id="id" name="id" value="<?=$id?>">
            <h4 class="sfondo" style="text-align: center">INTERVENTO N. <?=$modifica['ID_INTERVENTO']?> / ORE: <?php $var=$modifica['ORAINIZIO']; $var1=date_create("$var"); echo date_format($var1, "H:i") ?><?if($modifica['ORAFINE']!= null): ?> -> <?$var=$modifica['ORAFINE']; $var1=date_create("$var"); echo date_format($var1, "H:i"); endif; ?></h4>
            <div class="row justify-content-center" style="text-align: center">
                <div class="col-md-2">
                    <label for="inizio">Inizio</label>
                    <input type="time" class="form-control form-control-sm" id="inizio" name="inizio" autofocus required  value="<?=$modifica['ORAINIZIO']?>">
                </div>
                <div class="col-md-2">
                    <label for="stato">Stato</label>
                    <select class="form-select form-select-sm" id="stato" name="stato">
                        <?
                        for($a=1;$a<5;$a++){
                            ($a==$modifica['STATO'])? $sel="selected" : $sel="";
                            echo "<option $sel value='$a'>".$dictionaryStato[$a]."</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="fine">Fine</label>
                    <input type="time" class="form-control form-control-sm" id="fine" name="fine" value="<?=$modifica['ORAFINE']?>">
                </div>
            </div>
            <hr>
            <div class="row justify-content-center sfondo">
                <div class="col-md-2">
                    <label for="cognome">Cognome *</label>
                    <input type="text" class="form-control form-control-sm" id="cognome" name="cognome" required value="<?=$modifica['COGNOME']?>">
                </div>
                <div class="col-md-2">
                    <label for="nome">Nome</label>
                    <input type="text" class="form-control form-control-sm" id="nome" name="nome" value="<?=$modifica['NOME']?>" >
                </div>
                <div class="col-md-2">
                    <label for="nascita">Data di nascita</label>
                    <input type="date" class="form-control form-control-sm" id="nascita" name="nascita" placeholder="gg-mm-aaaa" value="<?=$modifica['NASCITA']?>">
                </div>
                <div class="col-md-3">
                    <label for="indirizzo">Indirizzo</label>
                    <input type="text" class="form-control form-control-sm" id="indirizzo" name="indirizzo" value="<?=$modifica['INDIRIZZO']?>" >
                </div>
                <div class="col-md-3">
                    <label for="telefono">Telefono</label>
                    <input type="text" class="form-control form-control-sm" id="telefono" name="telefono" value="<?=$modifica['TELEFONO']?>">
                </div>
                <div class="col-md-2">
                    <label for="squadra">Squadra</label>
                    <input type="text" class="form-control form-control-sm" id="squadra" name="squadra" value="<?=$modifica['SQUADRA']?>">
                </div>
                <div class="col-md-2">
                    <label for="posizione">Postazione *</label>
                    <input type="text" class="form-control form-control-sm" id="posizione" name="posizione" value="<?=$modifica['POSTAZIONE']?>">
                </div>
                <div class="col-md-2">
                    <label for="patologia">Patologia *</label>
                    <select class="form-select form-select-sm" id="patologia" name="patologia">
                        <?
                        for($a=1;$a<3;$a++){
                            ($a==$modifica['CODICEPATOLOGIA'])? $sel="selected" : $sel="";
                            echo "<option $sel value='$a'>".$dictionaryPatologia[$a]."</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="categoria">Gravità *</label>
                    <select class="form-select form-select-sm" id="gravita" name="gravita" required >
                        <option value="0">Bianco</option>
                        <?
                        for($a=1;$a<4;$a++){
                            ($a==$modifica['CODICEGRAVITA'])? $sel="selected" : $sel="";
                            echo "<option $sel value='$a'>".$dictionaryGravita[$a]."</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="esito">Esito</label>
                    <input type="text" class="form-control form-control-sm" id="esito" name="esito" value="<?=$modifica['ESITO']?>" >
                </div>
                <div class="col-md-12">
                    <label for="note_evento">Note</label>
                    <textarea rows="4" type="text" maxlength="250" class="form-control form-control-sm" id="note" name="note"><?=$modifica['NOTE']?></textarea>
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
                <div style="text-align: right; font-size: smaller; color: #6c757d">
                    <i>Intervento creato il <?= $modifica['DATAORA'] ?></i>
                </div>
            </div>
            <hr>
            <div style="text-align: center;">
                <div class="btn-group" role="group">
                    <button type="submit" class="btn btn-sm btn-outline-success" id="update" name="update"><i class="fas fa-check"></i></button>
                    <a href="list.php" class="btn btn-sm btn-outline-secondary" id="indietro"><i class="fas fa-undo"></i></a>
                    <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#modaldelete"><i class="far fa-trash-alt"></i></button>
                </div>
            </div>
            <div class="modal" id="modaldelete" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalCenterTitle">Confermi l'eliminazione?</h5>
                        </div>
                        <div class="modal-body">
                            <p>Premendo conferma, l'intervento selezionato e tutti i suoi dati andranno persi.</p>
                            <p>Questa azione non potrà essere annullata.</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Annulla</button>
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

<?php include('../config/include/footer.php'); ?>

</html>
