<?php
header('Access-Control-Allow-Origin: *');

/**
 *
 * @author     Paolo Randone
 * @author     <paolo.randone@croceverde.org>
 * @version    8.2
 * @note       Powered for Croce Verde Torino. All rights reserved
 *
 */
session_start();
include "../config/config.php";

if (!isset($_SESSION["ID"])){
    header("Location: index.php");
}

$dictionaryPatologia = array (
    1 => "MEDICO",
    2 => "TRAUMA",
);

if (isset($_GET['message'])){
    if ($_GET['message'] == 'success'){
        $alert_class = 'alert-success';
        $alert_message = '<i class="fa-regular fa-circle-check"></i> Modifica eseguita con successo';
    }else{
        $alert_class = 'alert-danger';
        $alert_message = '<i class="fa-solid fa-triangle-exclamation"></i> ERRORE';
    }
}
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

    <!--PER EVENTI MULTIPLI-->
    <script>
        $(document).ready(function() {
            const $table = $('#myTable');
            const $select = $('#IDEvento');

            const loadEventData = (eventId) => {
                if (!eventId) return;
                $.ajax({
                    url: 'get_interventi.php',
                    type: 'GET',
                    data: { IDEvento: eventId, random: Math.random() },
                    success: function(response) {
                        $table.find('tbody').html(response);
                        // Conta i codici gravità nelle righe caricate
                        let countBianchi = 0, countVerdi = 0, countGialli = 0, countRossi = 0;

                        $table.find('tbody tr').each(function () {
                            const btn = $(this).find('td:first-child .btn');
                            if (btn.hasClass('btn-outline-dark')) countBianchi++;
                            if (btn.hasClass('btn-success')) countVerdi++;
                            if (btn.hasClass('btn-warning')) countGialli++;
                            if (btn.hasClass('btn-danger')) countRossi++;
                        });

                        // Aggiorna badge
                        $('#countBianchi').text(countBianchi);
                        $('#countVerdi').text(countVerdi);
                        $('#countGialli').text(countGialli);
                        $('#countRossi').text(countRossi);

                        const numeroRighe = $table.find('tbody tr').length;
                        $('#totaleContatore').text('Totale: ' + numeroRighe);
                        $table.show();
                    },
                    error: function(xhr) {
                        console.error('Errore AJAX:', xhr.responseText);
                    }
                });
            };

            const initialEventId = $select.val();
            loadEventData(initialEventId);

            $select.on('change', function () {
                loadEventData($(this).val());
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            var dataTables = $('#myTable').DataTable({
                //stateSave: true,
                "paging": false,
                "language": {url: '../config/include/js/package.json'},
                "order": [[1, "asc"]],
                "pagingType": "simple",
                "pageLength": 50,
                "info": false,
                "columnDefs": [
                    {
                        "targets": [ 0 ],
                        "visible": true,
                        "searchable": false,
                        "orderable": false,
                    },
                    {
                        "targets": [ 7 ],
                        "visible": true,
                        "searchable": false,
                        "orderable": false,
                    }],
            });
            $('#reload').on('click', function () {
                dataTables
                    .search('')
                    .columns().search('')
                    .draw();
                //location.reload();
                location.href="new.php";
            });
        } );
    </script>

    <script>
        $(document).ready(function(){
            $('[data-toggle="popover"]').popover();
            $('.details').on('click', function (e) {
                e.preventDefault();
                var id = $(this).attr("id");
                $.get("https://croceverde.org/eventi/interventi/schedaintervento.php", {id:id}, function (html) {
                    $('#modaldetails').html(html);
                    $('.bd-details').modal('toggle');

                }).fail(function (msg) {
                    console.log(msg);
                })
            });
            $('#export').on('click', function () {
                $('#modalexportLIST').modal('show');
            })
        });
    </script>

</head>
<body>

<?php include "../config/include/navbar.php"; ?>

<div class="container container-sm">
    <?php if (isset($_GET['message'])) { ?>
        <div class="alert <?php echo $alert_class; ?>" role="alert">
            <?php echo $alert_message; ?>
            <script type="text/javascript">
                setTimeout(function(){
                    location.href="list.php";
                }, 2000);
            </script>
        </div>
    <?php } ?>
</div>
<div class="container-fluid px-2 mb-4">
    <div class="card card-cv">
        <div class="card-body">
            <form class="row g-3 align-items-center">
                <div class="col-auto">
                    <label for="IDEvento" class="col-form-label fw-bold">Evento</label>
                </div>
                <div class="col-md-6">
                    <select class="form-select form-select-sm" id="IDEvento" name="IDEvento">
                        <?php include "select_eventi.html"; ?>
                    </select>
                </div>
            </form>
        </div>

        <div class="table-wrapper">
            <div class="table-responsive">
                <table class="table table-hover table-sm sfondo" id="myTable">
                    <thead>
                    <tr>
                        <th scope="col"><button id="reload" type="button" class="btn btn-outline-info btn-sm" ><i class="fas fa-plus"></i></button></th>
                        <th scope="col">Inizio</th>
                        <th scope="col">Nominativo</th>
                        <th scope="col">Posizione</th>
                        <th scope="col">Tipo</th>
                        <th scope="col">Esito</th>
                        <th scope="col">Note</th>
                        <th scope="col">Stato</th>
                        <th scope="col">Fine</th>
                    </tr>
                    </thead>
                </table>
                <div class="d-flex flex-wrap gap-2 justify-content-end my-2" id="conteggioGravita">
                    <span class="badge text-dark border border-secondary bg-white" id="countBianchi">0</span>
                    <span class="badge bg-success text-white" id="countVerdi">0</span>
                    <span class="badge bg-warning text-dark" id="countGialli">0</span>
                    <span class="badge bg-danger" id="countRossi">0</span>
                    <span class="badge bg-secondary" id="totaleContatore">Totale: 0</span>
                </div>

            </div>
            <hr>
            <?if( $_SESSION['Livello']===1):?>
            <div style="text-align: center;">
                <div class="btn-group" role="group">
                    <button id="export" type="button" class="btn btn-outline-success btn-sm" >Esporta <i class="far fa-file-excel"></i></button>
                </div>
            </div>
        </div>
    </div>
</div>
<?endif;?>

<div id="modalexportLIST" class="modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content">
            <form action="exportLIST.php" method="post">
                <div class="modal-body" align="center">
                    <h6 class="modal-title">Esporta interventi</h6>
                    <br>
                    <select id="selectstato" name="selectstato" class="form-control form-control-sm" required>
                        <option value="ALL">Tutti</option>
                        <option value="1">Solo conclusi</option>
                    </select>
                    <br>
                    <button type="submit" class="btn btn-outline-success btn-sm" id="exportBTN" name="exportBTN">Crea file <i class="far fa-file-excel"></i></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal bd-details" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog modal-fullscreen" role="document">
        <div class="modal-content">
            <div class="modal-body" id="modaldetails">
            </div>
        </div>
    </div>
</div>
</body>

<?php include('../config/include/footer.php'); ?>

</html>