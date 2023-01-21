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
//controllo LOGIN
//accesso consentito a logistica, segreteria e ADMIN
if (($_SESSION["livello"])<4){
    header("Location: ../error.php");
}
//nicename tipo
$dictionary = array (
    1 => "MSB",
    2 => "MSA",
    3 => "118",
    4 => "Altro",
);
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <meta name="author" content="Paolo Randone">
    <title>Gestione manutenzioni</title>

    <? require "../config/include/header.html";?>



    <script>
        $(document).ready(function(){
            $('[data-toggle="popover"]').popover();
        });
    </script>
    <!-- datatable -->
    <script>
        $(document).ready(function() {
            var dataTables = $('#myTable').DataTable({
                stateSave: true,
                "paging": false,
                "language": {url: '../config/js/package.json'},
                "order": [[0, "asc"]],
                "pagingType": "simple",
                "pageLength": 100,

            });

        } );
    </script>
    <script>
        $(document).ready(function(){
            $('[data-toggle="popover"]').popover();
            $('.notecomplete').on('click', function (e) {
                e.preventDefault();
                var id = $(this).attr("id");
                $.get("https://croceverde.org/gestionale/magazzino/notemanutenzioni.php", {id:id}, function (html) {
                    $('#modalnotecomplete').html(html);
                    $('.bd-notecomplete').modal('toggle');

                }).fail(function (msg) {
                    console.log(msg);
                })
            });
            $('.note').on('click', function (e) {
                e.preventDefault();
                var id = $(this).attr("id");
                $.get("https://croceverde.org/gestionale/magazzino/notetagliandi.php", {id:id}, function (html) {
                    $('#modalnote').html(html);
                    $('.bd-note').modal('toggle');

                }).fail(function (msg) {
                    console.log(msg);
                })
            });
            $('#export').on('click', function () {
                $('#modalexportITEMS').modal('show');

            })
        });
    </script>
</head>
<!-- NAVBAR -->
<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../index.php" style="color: #078f40">Home</a></li>
            <li class="breadcrumb-item"><a href="index.php" style="color: #078f40">Autoparco</a></li>
            <li class="breadcrumb-item active" aria-current="page">Lista manutenzioni</li>
        </ol>
    </nav>
</div>
<!-- content -->
<body>
<div class="container-fluid">
    <div class="jumbotron">
        <div class="table-responsive-sm">
            <table class="table table-hover table-sm" id="myTable">
                <thead>
                <tr>
                    <th scope="col">Sigla</th>
                    <th scope="col">Ultimo tagliando</th>
                    <th scope="col">KM attuali</th>
                    <th scope="col">KM mancanti</th>
                    <th scope="col">Scadenza revisione</th>

                </tr>
                </thead>
                <tbody>
                <?php

                $select = $db->query("SELECT * FROM mezzi_tagliandi WHERE TIPOMANUTENZIONE=7 GROUP BY ID_MEZZO order by ID_MEZZO DESC");
                while($ciclo = $select->fetch_array()){
                    $diffKM= ($ciclo['KMATTUALI']-$ciclo['KMTAGLIANDO']);

                    if($select->num_rows>0): ?>
                        <tr>
                            <td class="align-middle"><form><button type='button' id='<?=$ciclo['ID_MEZZO']?>' class='btn-link btn btn-sm notecomplete' style="font-size:16px" value='<?=$ciclo['ID_MEZZO']?>'><?=$ciclo['ID_MEZZO']?></button></form></td>
                            <td class="align-middle"><form><button type='button' id='<?=$ciclo['ID_TAGLIANDO']?>' class='btn-link btn btn-sm note' style="font-size:16px" value='<?=$ciclo['ID_TAGLIANDO']?>'><?=$ciclo['DATATAGLIANDO']?></button></form></td>
                            <td class="align-middle"><?=$ciclo['KMATTUALI']?></td>
                            <td class="align-middle" <?if ($ciclo['KMATTUALI']>($ciclo['KMTAGLIANDO']+23000)){echo " style='color: red' ";}?>><?if ($ciclo['KMATTUALI']>($ciclo['KMTAGLIANDO']+23000)){echo "<i class=\"fas fa-exclamation-triangle\"></i>";}else{echo (23000-$diffKM);}?></td>
                            <td class="align-middle"><?=$ciclo['SCADENZAREVISIONE']?></td>
                        </tr>
                    <? endif;
                }?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
<div class="modal bd-note" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-body" id="modalnote">
            </div>
        </div>
    </div>
</div>
<div class="modal bd-notecomplete" role="dialog" aria-hidden="true" id="test">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <H6>Elenco lavori</H6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="modalnotecomplete">
            </div>
        </div>
    </div>
</div>
<!-- FOOTER -->
<?php include('../config/include/footer.php'); ?>

</html>