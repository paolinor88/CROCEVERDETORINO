<?php
header('Access-Control-Allow-Origin: *');

session_start();
/**
 *
 * @author     Paolo Randone
 * @author     <paolo.randone@croceverde.org>
 * @version    6.0
 * @note       Powered for Croce Verde Torino. All rights reserved
 *
 */

//parametri DB
include "../config/config.php";

//recupera variabili
if (isset($_GET["id_richiesta"])){
    $id = $_GET["id_richiesta"];

    $modifica = $db->query("SELECT * FROM richiesta_giacenza
                                LEFT OUTER JOIN utenti
                                ON richiesta_giacenza.ID_UTENTE = utenti.ID
                                LEFT OUTER JOIN giacenza
                                ON richiesta_giacenza.ID_ITEM = giacenza.id
                                WHERE richiesta_giacenza.ID_RICHIESTA = '$id'")->fetch_array();

}
//nicename sezioni
$dictionarySezione = array (
    1 => "TO",
    2 => "AL",
    3 => "BC",
    4 => "CI",
    5 => "SM",
    6 => "VE",
    7 => "DIP",
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
    12 => "Direzione",
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
);
//aggiorna
if(isset($_POST["updateordine"])){
    $id = $_POST["ID_RICHIESTA"];
    $quantita = $_POST["QUANTITA"];
    $note = $_POST["NOTE"];

    $update = $db->query("UPDATE richiesta_giacenza SET QUANTITA='$quantita', NOTE='$note' WHERE ID_RICHIESTA='$id'");

    if ($update){
        echo '<script type="text/javascript">
        alert("Operazione eseguita con successo");
        location.href="ordini.php";
        </script>';
    }else{
        echo '<script type="text/javascript">
        alert("ERRORE");
        location.href="ordini.php";
        </script>';
    }
}
//delete
if(isset($_POST["deleterichiesta"])){
    $id= $_POST["ID_RICHIESTA"];

    $delete = $db->query("DELETE FROM richiesta_giacenza WHERE ID_RICHIESTA='$id'");

    if($delete){
        echo '<script type="text/javascript">
        alert("Articolo cancellato con successo");
        location.href="ordini.php";
        </script>';
    }else{
        echo '<script type="text/javascript">
        alert("ERRORE");
        location.href="ordini.php";
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
<body>
<div class="container-fluid">

    <div class="jumbotron">
        <form method="post" action="dettagliordine.php">
            <input hidden id="ID_RICHIESTA" name="ID_RICHIESTA" value="<?=$id?>">
            <h5>Dettaglio ordine N° <?=$id?></h5>
            <hr>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="nome">RICHIESTO DA</label>
                    <input type="text" class="form-control form-control-sm" id="ID_UTENTE" name="ID_UTENTE" value="<?=$modifica['ID_UTENTE']?> <?=$modifica['cognome']?> (<?=$dictionarySezione[$modifica['sezione']]?> <?=$dictionarySquadra[$modifica['squadra']]?>)" readonly>
                </div>
                <div class="form-group col-md-6">
                    <label for="tipo">RICHIESTO IL</label>
                    <input type="text" class="form-control form-control-sm" id="DATA" name="DATA" value="<?=$modifica['DATA']?>" readonly>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="quantita">MATERIALE</label>
                    <input type="text" class="form-control form-control-sm" id="ID_ITEM" name="ID_ITEM" value="<?=$modifica['nome']?> <?=$modifica['tipo']?>" readonly>
                </div>
                <div class="form-group col-md-6">
                    <label for="quantita">QUANTITA</label>
                    <input type="text" class="form-control form-control-sm" id="QUANTITA" name="QUANTITA" value="<?=$modifica['QUANTITA']?>">
                </div>
            </div>
            <div class="form-group">
                <label for="note_evento">NOTE</label>
                <textarea rows="4" type="text" maxlength="250" class="form-control form-control-sm" id="NOTE" name="NOTE"><?=$modifica['NOTE']?></textarea>
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
            <div style="text-align: center;">
                <div class="btn-group" role="group">
                    <button type="submit" class="btn btn-sm btn-outline-success" id="updateordine" name="updateordine"><i class="fas fa-check"></i></button>
                    <a href="ordini.php" class="btn btn-sm btn-outline-secondary" id="indietro"><i class="fas fa-undo"></i></a>
                    <button type="button" class="btn btn-sm btn-outline-danger" id="doublemodal" data-toggle="modal" data-target="#modaldelete"><i class="far fa-trash-alt"></i></button>
                </div>
            </div>

            <div class="modal" id="modaldelete" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalCenterTitle">Confermi l'eliminazione?</h5>
                        </div>
                        <div class="modal-body">
                            <p>Premendo conferma, l'ordine selezionato verrà cancellato.</p>
                            <p>Questa azione non potrà essere annullata.</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary btn-sm" data-dismiss="modal">Annulla</button>
                            <button type="submit" class="btn btn-danger btn-sm" name="deleterichiesta">Conferma</button>
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
