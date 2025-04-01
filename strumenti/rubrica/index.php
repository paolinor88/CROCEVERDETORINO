<?php
header('Access-Control-Allow-Origin: *');
/**
 *
 * @author     Paolo Randone
 * @author     <paolo.randone@croceverde.org>
 * @version    1.0
 * @note         Powered for Croce Verde Torino. All rights reserved
 *
 */
session_start();
include "../config/config.php";

if (!in_array($_SESSION['Livello'], [1, 20, 23, 24, 25, 26, 27, 28, 29])) {
    header("Location: ../index.php");
    echo "<script type='text/javascript'>alert('Accesso negato');</script>";
    exit;
}

$dictionaryFiliale = array (
    1 => "Torino",
    2=> "Alpignano",
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
    <title>Rubrica personale</title>

    <?php require "../config/include/header.html"; ?>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../config/include/custom.css?v=<?= time() ?>">
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
                        "searchable": true,
                        "orderable": true,
                    },
                    {
                        "targets": [ 5 ],
                        "visible": true,
                        "searchable": true,
                        "orderable": false,
                    },
                    {
                        "targets": [ 6 ],
                        "visible": true,
                        "searchable": true,
                        "orderable": false,
                    },
                ],
            });
            //FILTRI
            $('#torino').on('click', function () {
                dataTables.columns(5).search("").draw();
                dataTables.columns(5).search("Torino").draw();
                $( "#torino" ).removeClass( "btn-outline-secondary" ).addClass( "btn-secondary" );
                $( "#alpignano" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#borgaro" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#cirie" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#mauro" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#venaria" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#all" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
            });
            $('#alpignano').on('click', function () {
                dataTables.columns(5).search("").draw();
                dataTables.columns(5).search("Alpignano").draw();
                $( "#alpignano" ).removeClass( "btn-outline-secondary" ).addClass( "btn-secondary" );
                $( "#torino" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#borgaro" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#cirie" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#mauro" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#venaria" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#all" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
            });
            $('#borgaro').on('click', function () {
                dataTables.columns(5).search("").draw();
                dataTables.columns(5).search("Borgaro/Caselle").draw();
                $( "#borgaro" ).removeClass( "btn-outline-secondary" ).addClass( "btn-secondary" );
                $( "#alpignano" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#torino" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#cirie" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#mauro" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#venaria" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#all" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
            });
            $('#cirie').on('click', function () {
                dataTables.columns(5).search("").draw();
                dataTables.columns(5).search("Ciriè").draw();
                $( "#cirie" ).removeClass( "btn-outline-secondary" ).addClass( "btn-secondary" );
                $( "#alpignano" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#borgaro" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#torino" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#mauro" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#venaria" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#all" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
            });
            $('#mauro').on('click', function () {
                dataTables.columns(5).search("").draw();
                dataTables.columns(5).search("San Mauro").draw();
                $( "#mauro" ).removeClass( "btn-outline-secondary" ).addClass( "btn-secondary" );
                $( "#alpignano" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#borgaro" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#cirie" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#torino" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#venaria" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#all" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
            });
            $('#venaria').on('click', function () {
                dataTables.columns(5).search("").draw();
                dataTables.columns(5).search("Venaria").draw();
                $( "#venaria" ).removeClass( "btn-outline-secondary" ).addClass( "btn-secondary" );
                $( "#alpignano" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#borgaro" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#cirie" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#mauro" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#torino" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#all" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
            });
            $('#all').on('click', function () {
                dataTables.columns(5).search("").draw();
                $( "#torino" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#alpignano" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#borgaro" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#cirie" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#mauro" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#venaria" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#all" ).removeClass( "btn-outline-secondary" ).addClass( "btn-secondary" );
            });
            $('#sq1').on('click', function () {
                dataTables.columns(6).search("").draw();
                dataTables.columns(6).search("1").draw();
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
                dataTables.columns(6).search("").draw();
                dataTables.columns(6).search("2").draw();
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
                dataTables.columns(6).search("").draw();
                dataTables.columns(6).search("3").draw();
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
                dataTables.columns(6).search("").draw();
                dataTables.columns(6).search("4").draw();
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
                dataTables.columns(6).search("").draw();
                dataTables.columns(6).search("5").draw();
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
                dataTables.columns(6).search("").draw();
                dataTables.columns(6).search("6").draw();
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
                dataTables.columns(6).search("").draw();
                dataTables.columns(6).search("7").draw();
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
                dataTables.columns(6).search("").draw();
                dataTables.columns(6).search("8").draw();
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
                dataTables.columns(6).search("").draw();
                dataTables.columns(6).search("9").draw();
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
                dataTables.columns(6).search("").draw();
                dataTables.columns(6).search("Sabato").draw();
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
                dataTables.columns(6).search("").draw();
                dataTables.columns(6).search("Diurno").draw();
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
                dataTables.columns(6).search("").draw();
                dataTables.columns(6).search("Montagna").draw();
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
                dataTables.columns(6).search("").draw();
                dataTables.columns(6).search("Serv. Generali").draw();
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
                dataTables.columns(6).search("").draw();
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
        } );
    </script>
    <script>
        $(document).ready(function(){
            $('[data-toggle="popover"]').popover();
            $('.noterubrica').on('click', function (e) {
                e.preventDefault();
                var id = $(this).attr("id");
                $.get("https://croceverde.org/strumenti/rubrica/noterubrica.php", {id:id}, function (html) {
                    $('#modalnote').html(html);
                    $('.bd-note').modal('toggle');

                }).fail(function (msg) {
                    console.log(msg);
                })
            });
        });
    </script>
</head>
<body>

<?php include "../config/include/navbar.php"; ?>

<!-- FILTRI -->
<div class="container mb-3" style="text-align: center">
    <div class="btn-group-mobile mb-2" role="group" aria-label="Filtra per sede">
        <button id="torino" type="button" class="btn btn-outline-secondary btn-sm">Torino</button>
        <button id="alpignano" type="button" class="btn btn-outline-secondary btn-sm">Alpignano</button>
        <button id="borgaro" type="button" class="btn btn-outline-secondary btn-sm">Borgaro/Caselle</button>
        <button id="cirie" type="button" class="btn btn-outline-secondary btn-sm">Ciriè</button>
        <button id="mauro" type="button" class="btn btn-outline-secondary btn-sm">San Mauro</button>
        <button id="venaria" type="button" class="btn btn-outline-secondary btn-sm">Venaria</button>
        <button id="all" type="button" class="btn btn-secondary btn-sm">TUTTE</button>
    </div>

    <div class="btn-group-mobile" role="group" aria-label="Filtra per squadra">
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
                        <th scope="col">TELEFONO</th>
                        <th scope="col">MAIL</th>
                        <th scope="col">SEZIONE</th>
                        <th scope="col">SQUADRA</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    if (isset($_SESSION['Livello']) && $_SESSION['Livello'] === 1) {
                        $query = "SELECT * FROM rubrica WHERE IDSquadra != '19' ORDER BY Cognome"; // Admin no giovani
                    } else {
                        $query = "SELECT * FROM rubrica WHERE IDSquadra != '19' AND IDSquadra != '23' ORDER BY Cognome"; // Tutti no giovani no dip
                    }

                    $select = $db->query($query);

                    if ($select->num_rows > 0) {
                        while ($ciclo = $select->fetch_array()) { ?>
                            <tr style="<?= $style ?>">
                                <td class="align-middle">
                                    <form>
                                        <button type='button' id='<?= $ciclo['Codice'] ?>'
                                                class='btn-link btn btn-sm noterubrica'
                                                style="font-size:16px"
                                                value='<?= $ciclo['Codice'] ?>'>
                                            <?= $ciclo['Codice'] ?>
                                        </button>
                                    </form>
                                </td>
                                <td class="align-middle"><?= $ciclo['Cognome'] ?></td>
                                <td class="align-middle"><?= $ciclo['Nome'] ?></td>
                                <td class="align-middle"><?= $ciclo['Cellulare'] ?></td>
                                <td class="align-middle"><?= $ciclo['Mail'] ?></td>
                                <td class="align-middle"><?= $dictionaryFiliale[$ciclo['IDFiliale']] ?></td>
                                <td class="align-middle" style="text-align: center"><?= $dictionarySquadra[$ciclo['IDSquadra']] ?></td>
                            </tr>
                        <?php }
                    } ?>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- MODAL -->
<div class="modal bd-note" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered " role="document">
        <div class="modal-content">
            <div class="modal-body" id="modalnote"></div>
        </div>
    </div>
</div>
<?php include "../config/include/footer.php"; ?>
</body>
</html>