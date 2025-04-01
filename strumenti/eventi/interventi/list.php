<?php
header('Access-Control-Allow-Origin: *');

/**
 *
 * @author     Paolo Randone
 * @author     <paolo.randone@croceverde.org>
 * @version    1.0
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
            $('#myTable').hide();
            $('#IDEvento').change(function() {
                var selectedEvent = $(this).val();
                $.ajax({
                    url: 'get_interventi.php',
                    type: 'GET',
                    data: { IDEvento: selectedEvent, random: Math.random()  },
                    success: function(response) {
                        $('#myTable tbody').html(response);
                        $('#myTable').show();
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                        //   errori
                    }
                });
            });
        });
    </script>

    <!--PER EVENTI SINGOLI USA QUESTO
    <script>
        $(document).ready(function() {
            $.ajax({
                url: 'get_interventi.php',
                type: 'GET',
                data: { IDEvento: 1, random: Math.random() },
                success: function(response) {
                    $('#myTable tbody').html(response);
                    $('#myTable').show();
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                    //   errori
                }
            });
            $('#IDEvento').change(function() {
                var selectedEvent = $(this).val();
                $.ajax({
                    url: 'get_interventi.php',
                    type: 'GET',
                    data: { IDEvento: selectedEvent },
                    success: function(response) {
                        $('#myTable tbody').html(response);
                        $('#myTable').show();
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                        //   errori
                    }
                });
            });
        });
    </script>
    -->

    <script>
        $(document).ready(function() {
            var dataTables = $('#myTable').DataTable({
                //stateSave: true,
                "paging": false,
                "language": {url: '../config/include/js/package.json'},
                "order": [[1, "asc"]],
                "pagingType": "simple",
                "pageLength": 50,
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
        <div class="col-md-2">
            <label for="IDEvento"><B>EVENTO</B></label>
            <select class="form-select form-select-sm" id="IDEvento" name="IDEvento">
                <option value="0">Scegli...</option>
                <!--<option value="1">STADIO OLIMPICO</option>-->
                <option value="2">INALPI ARENA</option>
                <option value="3">CONCORDIA</option>
                <option value="4">ALTRO</option>
            </select>
        </div>
        <br>
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