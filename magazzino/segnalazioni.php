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
    <title>Segnalazioni Guasti</title>

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
                "order": [[0, "desc"]],
                "pagingType": "simple",
                "pageLength": 100,
                "columnDefs": [
                    {
                        "targets": [ 0 ],
                        "visible": true,
                        "searchable": false,
                        "orderable": false,
                    }],
            });

        } );
        $(document).ready(function() {
            $('#export').on('click', function () {
                $('#modalexportsegnalazioni').modal('show');
            });
            $('button[name="dettagli"]').on('click', function() {
                var noteVerificato = $(this).closest('tr').find('td:eq(5)').text();
                $('#noteVerificatoTextarea').val(noteVerificato);
                $('#saveChangesBtn').data('ID', $(this).data('id'));

            });
            $('#saveChangesBtn').on('click', function() {
                var ID = $(this).data('ID');
                var noteAggiornate = $('#noteVerificatoTextarea').val();

                $.ajax({
                    url: 'salva_note.php',
                    type: 'POST',
                    data: {
                        id: ID,
                        note: noteAggiornate,
                    },
                    success: function(response) {

                        var dataAttuale = new Date().toISOString().slice(0, 19).replace('T', ' ');
                        $('button[name="dettagli"]:contains("' + ID + '")').closest('tr').find('td:eq(4)').text(dataAttuale); // Imposta la data di verifica
                        $('button[name="dettagli"]:contains("' + ID + '")').closest('tr').find('td:eq(5)').text(noteAggiornate); // Aggiorna le note

                        $('#modalDetails').modal('hide'); // Chiude la modale
                        alert("Note aggiornate con successo!");
                        window.location.reload();
                    },

                    error: function(xhr, status, error) {
                        alert("Si è verificato un errore nell'aggiornamento delle note.");
                    }
                });
            });
        });
    </script>
</head>
<!-- NAVBAR -->
<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../index.php" style="color: #078f40">Home</a></li>
            <li class="breadcrumb-item"><a href="index.php" style="color: #078f40">Autoparco</a></li>
            <li class="breadcrumb-item active" aria-current="page">Segnalazioni Guasti</li>
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
                    <th scope="col"></th>
                    <th scope="col">Auto</th>
                    <th scope="col">Segnalazione</th>
                    <th scope="col">Data</th>
                    <th scope="col">Verificato</th>
                    <th scope="col">Note</th>
                </tr>
                </thead>
                <tbody>
                <?php

                $select = $db->query("SELECT * FROM SegnalazioniGuastiMezzi");
                while($ciclo = $select->fetch_array()){
                    $dataFormattata = date('d/m/Y H:i', strtotime($ciclo['DataOra']));
                    if($select->num_rows>0): ?>
                        <tr>
                            <td><button class="btn btn-sm btn-outline-info" data-toggle="modal" data-target="#modalDetails" name="dettagli" data-id="<?=$ciclo['ID']?>"><i class="fas fa-search"></i></button>
                            </td>
                            <td align="center"><?=$ciclo['Sigla']?></td>
                            <td><?=$ciclo['Segnalazione']?></td>
                            <td><?=$dataFormattata?></td>
                            <td align="center">
    <span class="checkmark-container">
        <?if(($ciclo['DataVerificato'])!=""){echo "<i class='fas fa-check' style='color: #1a712c'></i>";}?>
    </span>
                            </td>
                            <td><?=$ciclo['NoteVerificato']?></td>
                        </tr>
                    <? endif;
                }?>
                </tbody>
            </table>
        </div>
        <div style="text-align: center;">
            <div class="btn-group" role="group">
                <button id="export" type="button" class="btn btn-outline-success btn-sm" >Esporta <i class="far fa-file-excel"></i></button>
            </div>
        </div>
        <!-- Modale per dettagli segnalazione -->
        <div class="modal" id="modalDetails" tabindex="-1" role="dialog" aria-labelledby="modalDetailsLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalDetailsLabel">Note autoparco</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <textarea id="noteVerificatoTextarea" class="form-control" rows="5"></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Chiudi</button>
                        <button type="button" class="btn btn-sm btn-success" id="saveChangesBtn">Verifica</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
</body>
<!--esporta AUTO-->
<div id="modalexportsegnalazioni" class="modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content">
            <form action="export_SEGNALAZIONI.php" method="post">
                <div class="modal-body" align="center">
                    <h6 class="modal-title">Esporta per O.d.V.</h6>
                    <HR>
                    <select id="selectmese" name="selectmese" class="form-control form-control-sm" required>
                        <option value="01">Gennaio</option>
                        <option value="02">Febbraio</option>
                        <option value="03">Marzo</option>
                        <option value="04">Aprile</option>
                        <option value="05">Maggio</option>
                        <option value="06">Giugno</option>
                        <option value="07">Luglio</option>
                        <option value="08">Agosto</option>
                        <option value="09">Settembre</option>
                        <option value="10">Ottobre</option>
                        <option value="11">Novembre</option>
                        <option value="12">Dicembre</option>
                    </select>
                    <br>
                    <select id="selectanno" name="selectanno" class="form-control form-control-sm" required>
                        <option value="2024">2024</option>
                        <option value="2023">2023</option>
                        <option value="2022">2022</option>
                        <option value="2021">2021</option>
                    </select>
                    <br>
                    <button type="submit" class="btn btn-success btn-sm btn-block" id="exportSEGNALAZIONI" name="exportSEGNALAZIONI"><i class="far fa-file-excel"></i></button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- FOOTER -->
<?php include('../config/include/footer.php'); ?>

</html>