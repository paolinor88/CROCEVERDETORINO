<?php
header('Access-Control-Allow-Origin: *');
/**
 *
 * @author     Paolo Randone
 * @author     <paolo.randone@croceverde.org>
 * @version    8.2
 * @note         Powered for Croce Verde Torino. All rights reserved
 *
 */
session_start();

include "../config/config.php";
include "../config/include/destinatari.php";

if (!in_array($_SESSION['Livello'], [1, 20, 23, 24, 25, 26, 27, 28, 29, 30])) {
    header("Location: ../index.php");
    echo "<script type='text/javascript'>alert('Accesso negato');</script>";
    exit;
}

$dictionaryFiliale = array (
    1 => "Torino",
    2 => "Alpignano",
    3 => "Borgaro/Caselle",
    4 => "Ciriè",
    5 => "San Mauro",
    6 => "Venaria",
);

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
    10 => "Sabato",
    11 => "Montagna",
    12 => "Diurno",
    13 => "Diurno",
    14 => "Diurno",
    15 => "Diurno",
    16 => "Diurno",
    17 => "Diurno",
    18 => "Diurno",
    19 => "Giovani",
    20 => "Serv. Generali",
    22 => "Serv. Cittadino",
    23 => "Dipendenti",
);

?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="Paolo Randone">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    <title>Autocertificazioni</title>

    <?php require "../config/include/header.html"; ?>

    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../config/include/custom.css?v=<?= time() ?>">
    <script>
        $(document).ready(function() {
            var dataTables = $('#myTable').DataTable({
                "paging": false,
                "language": {url: '../config/include/js/package.json'},
                "order": [[1, "asc"]],
                "pagingType": "simple",
                "pageLength": 50,
                "columnDefs": [
                    {
                        "targets": [0],
                        "visible": true,
                        "searchable": true,
                        "orderable": true,
                    },
                    {
                        "targets": [5],
                        "visible": true,
                        "searchable": true,
                        "orderable": true,
                    },
                ],
            });
            //FILTRI
            $('#torino').on('click', function () {
                dataTables.columns(3).search("").draw();
                dataTables.columns(3).search("Torino").draw();
                $( "#torino" ).removeClass( "btn-outline-secondary" ).addClass( "btn-secondary" );
                $( "#alpignano" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#borgaro" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#cirie" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#mauro" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#venaria" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#all" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
            });
            $('#alpignano').on('click', function () {
                dataTables.columns(3).search("").draw();
                dataTables.columns(3).search("Alpignano").draw();
                $( "#alpignano" ).removeClass( "btn-outline-secondary" ).addClass( "btn-secondary" );
                $( "#torino" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#borgaro" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#cirie" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#mauro" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#venaria" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#all" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
            });
            $('#borgaro').on('click', function () {
                dataTables.columns(3).search("").draw();
                dataTables.columns(3).search("Borgaro/Caselle").draw();
                $( "#borgaro" ).removeClass( "btn-outline-secondary" ).addClass( "btn-secondary" );
                $( "#alpignano" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#torino" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#cirie" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#mauro" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#venaria" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#all" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
            });
            $('#cirie').on('click', function () {
                dataTables.columns(3).search("").draw();
                dataTables.columns(3).search("Ciriè").draw();
                $( "#cirie" ).removeClass( "btn-outline-secondary" ).addClass( "btn-secondary" );
                $( "#alpignano" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#borgaro" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#torino" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#mauro" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#venaria" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#all" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
            });
            $('#mauro').on('click', function () {
                dataTables.columns(3).search("").draw();
                dataTables.columns(3).search("San Mauro").draw();
                $( "#mauro" ).removeClass( "btn-outline-secondary" ).addClass( "btn-secondary" );
                $( "#alpignano" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#borgaro" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#cirie" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#torino" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#venaria" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#all" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
            });
            $('#venaria').on('click', function () {
                dataTables.columns(3).search("").draw();
                dataTables.columns(3).search("Venaria").draw();
                $( "#venaria" ).removeClass( "btn-outline-secondary" ).addClass( "btn-secondary" );
                $( "#alpignano" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#borgaro" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#cirie" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#mauro" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#torino" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#all" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
            });
            $('#all').on('click', function () {
                dataTables.columns(3).search("").draw();
                $( "#torino" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#alpignano" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#borgaro" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#cirie" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#mauro" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#venaria" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#all" ).removeClass( "btn-outline-secondary" ).addClass( "btn-secondary" );
            });
            $('#sq1').on('click', function () {
                dataTables.columns(4).search("").draw();
                dataTables.columns(4).search("1").draw();
                $( "#sq1" ).removeClass( "btn-outline-secondary" ).addClass( "btn-secondary" );
                $( "#sq2" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq3" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq4" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq5" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq6" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq7" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq8" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq9" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq10" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq18" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq11" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq20" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#allsq" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
            });
            $('#sq2').on('click', function () {
                dataTables.columns(4).search("").draw();
                dataTables.columns(4).search("2").draw();
                $( "#sq2" ).removeClass( "btn-outline-secondary" ).addClass( "btn-secondary" );
                $( "#sq1" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq3" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq4" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq5" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq6" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq7" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq8" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq9" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq10" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq18" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq11" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq20" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#allsq" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
            });
            $('#sq3').on('click', function () {
                dataTables.columns(4).search("").draw();
                dataTables.columns(4).search("3").draw();
                $( "#sq3" ).removeClass( "btn-outline-secondary" ).addClass( "btn-secondary" );
                $( "#sq2" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq1" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq4" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq5" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq6" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq7" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq8" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq9" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq10" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq18" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq11" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq20" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#allsq" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
            });
            $('#sq4').on('click', function () {
                dataTables.columns(4).search("").draw();
                dataTables.columns(4).search("4").draw();
                $( "#sq4" ).removeClass( "btn-outline-secondary" ).addClass( "btn-secondary" );
                $( "#sq2" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq3" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq1" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq5" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq6" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq7" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq8" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq9" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq10" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq18" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq11" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq20" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#allsq" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
            });
            $('#sq5').on('click', function () {
                dataTables.columns(4).search("").draw();
                dataTables.columns(4).search("5").draw();
                $( "#sq5" ).removeClass( "btn-outline-secondary" ).addClass( "btn-secondary" );
                $( "#sq2" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq3" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq4" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq1" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq6" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq7" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq8" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq9" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq10" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq18" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq11" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq20" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#allsq" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
            });
            $('#sq6').on('click', function () {
                dataTables.columns(4).search("").draw();
                dataTables.columns(4).search("6").draw();
                $( "#sq6" ).removeClass( "btn-outline-secondary" ).addClass( "btn-secondary" );
                $( "#sq2" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq3" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq4" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq5" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq1" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq7" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq8" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq9" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq10" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq18" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq11" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq20" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#allsq" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
            });
            $('#sq7').on('click', function () {
                dataTables.columns(4).search("").draw();
                dataTables.columns(4).search("7").draw();
                $( "#sq7" ).removeClass( "btn-outline-secondary" ).addClass( "btn-secondary" );
                $( "#sq2" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq3" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq4" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq5" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq6" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq1" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq8" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq9" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq10" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq18" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq11" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq20" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#allsq" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
            });
            $('#sq8').on('click', function () {
                dataTables.columns(4).search("").draw();
                dataTables.columns(4).search("8").draw();
                $( "#sq8" ).removeClass( "btn-outline-secondary" ).addClass( "btn-secondary" );
                $( "#sq2" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq3" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq4" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq5" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq6" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq7" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq1" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq9" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq10" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq18" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq11" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq20" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#allsq" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
            });
            $('#sq9').on('click', function () {
                dataTables.columns(4).search("").draw();
                dataTables.columns(4).search("9").draw();
                $( "#sq9" ).removeClass( "btn-outline-secondary" ).addClass( "btn-secondary" );
                $( "#sq2" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq3" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq4" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq5" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq6" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq7" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq8" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq1" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq10" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq18" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq11" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq20" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#allsq" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
            });
            $('#sq10').on('click', function () {
                dataTables.columns(4).search("").draw();
                dataTables.columns(4).search("Sabato").draw();
                $( "#sq10" ).removeClass( "btn-outline-secondary" ).addClass( "btn-secondary" );
                $( "#sq2" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq3" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq4" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq5" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq6" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq7" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq8" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq9" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq1" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq18" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq11" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq20" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#allsq" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
            });
            $('#sq18').on('click', function () {
                dataTables.columns(4).search("").draw();
                dataTables.columns(4).search("Diurno").draw();
                $( "#sq18" ).removeClass( "btn-outline-secondary" ).addClass( "btn-secondary" );
                $( "#sq2" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq3" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq4" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq5" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq6" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq7" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq8" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq9" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq10" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq1" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq11" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq20" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#allsq" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
            });
            $('#sq11').on('click', function () {
                dataTables.columns(4).search("").draw();
                dataTables.columns(4).search("Montagna").draw();
                $( "#sq11" ).removeClass( "btn-outline-secondary" ).addClass( "btn-secondary" );
                $( "#sq2" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq3" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq4" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq5" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq6" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq7" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq8" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq9" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq10" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq18" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq1" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq20" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#allsq" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
            });
            $('#sq20').on('click', function () {
                dataTables.columns(4).search("").draw();
                dataTables.columns(4).search("Serv. Generali").draw();
                $( "#sq20" ).removeClass( "btn-outline-secondary" ).addClass( "btn-secondary" );
                $( "#sq2" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq3" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq4" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq5" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq6" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq7" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq8" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq9" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq10" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq18" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq1" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq11" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#allsq" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
            });
            $('#allsq').on('click', function () {
                dataTables.columns(4).search("").draw();
                $( "#allsq" ).removeClass( "btn-outline-secondary" ).addClass( "btn-secondary" );
                $( "#sq2" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq3" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq4" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq5" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq6" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq7" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq8" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq9" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq10" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq18" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq11" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq1" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#sq20" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
            });
            $('#conQualifica').on('click', function () {
                dataTables.column(5).search("67").draw(); // Usa un filtro regex esatto
                $(this).removeClass("btn-outline-secondary").addClass("btn-secondary");
                $('#tutteQualifiche').removeClass("btn-secondary").addClass("btn-outline-secondary");
                $('#senzaQualifica').removeClass("btn-secondary").addClass("btn-outline-secondary");
            });

            $('#tutteQualifiche').on('click', function () {
                dataTables.column(5).search("").draw(); // Rimuove il filtro
                $(this).removeClass("btn-outline-secondary").addClass("btn-secondary");
                $('#conQualifica').removeClass("btn-secondary").addClass("btn-outline-secondary");
                $('#senzaQualifica').removeClass("btn-secondary").addClass("btn-outline-secondary");
            });
            $('#senzaQualifica').on('click', function () {
                dataTables.column(5).search("SENZA").draw(); // Cerca i record con "SENZA"
                $(this).removeClass("btn-outline-secondary").addClass("btn-secondary");
                $('#conQualifica').removeClass("btn-secondary").addClass("btn-outline-secondary");
                $('#tutteQualifiche').removeClass("btn-secondary").addClass("btn-outline-secondary");
            });


        });
    </script>
</head>
<body>

<?php include "../config/include/navbar.php"; ?>

<div class="container mb-3" style="text-align: center">
    <div class="btn-group-mobile mb-2" role="group" aria-label="Filtra per qualifica">
        <button id="conQualifica" type="button" class="btn btn-outline-secondary btn-sm">
            <i class="fa-solid fa-check" style="color: green"></i>
        </button>
        <button id="senzaQualifica" type="button" class="btn btn-outline-secondary btn-sm">
            <i class="fa-solid fa-times" style="color: red"></i>
        </button>
        <button id="tutteQualifiche" type="button" class="btn btn-secondary btn-sm">TUTTE</button>
    </div>

    <div class="btn-group-mobile mb-2" role="group" aria-label="Filtra per sede">
        <!-- Tutti i pulsanti sede -->
        <button id="torino" type="button" class="btn btn-outline-secondary btn-sm">Torino</button>
        <button id="alpignano" type="button" class="btn btn-outline-secondary btn-sm">Alpignano</button>
        <button id="borgaro" type="button" class="btn btn-outline-secondary btn-sm">Borgaro/Caselle</button>
        <button id="cirie" type="button" class="btn btn-outline-secondary btn-sm">Ciriè</button>
        <button id="mauro" type="button" class="btn btn-outline-secondary btn-sm">San Mauro</button>
        <button id="venaria" type="button" class="btn btn-outline-secondary btn-sm">Venaria</button>
        <button id="all" type="button" class="btn btn-secondary btn-sm">TUTTE</button>
    </div>

    <div class="btn-group-mobile" role="group" aria-label="Filtra per squadra">
        <!-- Tutti i pulsanti squadra -->
        <button id="sq1" type="button" class="btn btn-outline-secondary btn-sm">Sq. 1</button>
        <button id="sq2" type="button" class="btn btn-outline-secondary btn-sm">Sq. 2</button>
        <button id="sq3" type="button" class="btn btn-outline-secondary btn-sm">Sq. 3</button>
        <button id="sq4" type="button" class="btn btn-outline-secondary btn-sm">Sq. 4</button>
        <button id="sq5" type="button" class="btn btn-outline-secondary btn-sm">Sq. 5</button>
        <button id="sq6" type="button" class="btn btn-outline-secondary btn-sm">Sq. 6</button>
        <button id="sq7" type="button" class="btn btn-outline-secondary btn-sm">Sq. 7</button>
        <button id="sq8" type="button" class="btn btn-outline-secondary btn-sm">Sq. 8</button>
        <button id="sq9" type="button" class="btn btn-outline-secondary btn-sm">Sq. 9</button>
        <button id="sq10" type="button" class="btn btn-outline-secondary btn-sm">Sabato</button>
        <button id="sq18" type="button" class="btn btn-outline-secondary btn-sm">Diurno</button>
        <button id="sq11" type="button" class="btn btn-outline-secondary btn-sm">Montagna</button>
        <button id="sq20" type="button" class="btn btn-outline-secondary btn-sm">Generali</button>
        <button id="allsq" type="button" class="btn btn-secondary btn-sm">TUTTE</button>
    </div>
</div>

<div class="container-fluid px-2 mb-4">
    <div class="card card-cv">
        <div class="table-wrapper">
            <div class="table-responsive">
                <table class="table table-hover table-sm sfondo" id="myTable">
                    <thead>
                    <tr>
                        <th scope="col">CODICE</th>
                        <th scope="col">COGNOME</th>
                        <th scope="col">NOME</th>
                        <th scope="col">SEZIONE</th>
                        <th scope="col" style="text-align: center">SQUADRA</th>
                        <th scope="col" style="text-align: center">AUTOCERTIFICAZIONE</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $select = $db->query("
                        SELECT 
                            rubrica.Codice, 
                            rubrica.Cognome, 
                            rubrica.Nome, 
                            rubrica.IDFiliale, 
                            rubrica.IDSquadra, 
                            AUTISTI_PATENTI_AUT.IDQualifica
                        FROM 
                            rubrica
                        LEFT JOIN  
                            AUTISTI_PATENTI_AUT ON rubrica.IDUtente = AUTISTI_PATENTI_AUT.IDUtente
                        WHERE rubrica.IDSquadra != '19' AND rubrica.IDSquadra != '23'
                        ");
                    while ($ciclo = $select->fetch_array()) {
                        if ($select->num_rows > 0): ?>
                            <tr>
                                <td class="align-middle">
                                    <?= $ciclo['Codice'] ?>
                                </td>
                                <td class="align-middle">
                                    <?= $ciclo['Cognome'] ?>
                                </td>
                                <td class="align-middle">
                                    <?= $ciclo['Nome'] ?>
                                </td>
                                <td class="align-middle">
                                    <?= $dictionaryFiliale[$ciclo['IDFiliale']] ?>
                                </td>
                                <td class="align-middle" style="text-align: center">
                                    <?= $dictionarySquadra[$ciclo['IDSquadra']] ?>
                                </td>
                                <td class="align-middle" style="text-align: center">
                                    <span style="display:none"><?= $ciclo['IDQualifica'] ?: 'SENZA' ?></span>
                                    <?php echo $ciclo['IDQualifica'] ? '<i class="fa-solid fa-check" style="color: green"></i>' : '<i class="fa-solid fa-times" style="color: red"></i>' ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</body>
<?php include "../config/include/footer.php"; ?>
</html>