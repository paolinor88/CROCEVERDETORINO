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
include "../config/include/destinatari.php";

if (!in_array($_SESSION['Livello'], [1, 20, 23, 24, 25, 26, 27, 28, 29,30])) {
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

$anagraficaQuery = $db->query("
    SELECT * 
    FROM rubrica 
    WHERE (R = 3 OR MA = 4 OR MAU = 5)
      AND TIMESTAMPDIFF(
            YEAR, 
            STR_TO_DATE(DataNascita, '%d/%m/%Y'), 
            CURDATE()
          ) < 75
    ORDER BY Cognome
");

$autocertificazioneQuery = $db->query("
    SELECT rubrica.Codice, AUTISTI_PATENTI_AUT.IDQualifica
    FROM rubrica
    LEFT JOIN AUTISTI_PATENTI_AUT ON rubrica.IDUtente = AUTISTI_PATENTI_AUT.IDUtente
");

$scadenzaQuery = $db->query("SELECT IDUtente, SCADENZAURGENZE FROM AUTISTI_URGENZE");
$scadenzaData = [];
while ($row = $scadenzaQuery->fetch_assoc()) {
    $scadenzaData[$row['IDUtente']] = strtotime($row['SCADENZAURGENZE']);
}

$autocertificazioneData = [];
while ($row = $autocertificazioneQuery->fetch_assoc()) {
    $autocertificazioneData[$row['Codice']] = $row['IDQualifica'] ?: 'SENZA';
}

$overQuery = $db->query("SELECT IDUtente, SCADENZAOVER FROM AUTISTI_OVER");
$overData = [];
while ($row = $overQuery->fetch_assoc()) {
    $overData[$row['IDUtente']] = strtotime($row['SCADENZAOVER']);
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Elenco Autisti</title>

    <?php require "../config/include/header.html"; ?>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../config/include/custom.css?v=<?= time() ?>">

    <script>
        $(document).ready(function() {
            var dataTables = $('#myTable').DataTable({
                //stateSave: true,
                "responsive": true,
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
                        "targets": [ 3,4,5,6 ],
                        "visible": true,
                        "searchable": true,
                        "orderable": false,
                    },
                ],
            });
            //FILTRI
            $('#torino').on('click', function () {
                dataTables.columns(6).search("").draw();
                dataTables.columns(6).search("Torino").draw();
                $( "#torino" ).removeClass( "btn-outline-secondary" ).addClass( "btn-secondary" );
                $( "#alpignano" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#borgaro" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#cirie" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#mauro" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#venaria" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#all" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
            });
            $('#alpignano').on('click', function () {
                dataTables.columns(6).search("").draw();
                dataTables.columns(6).search("Alpignano").draw();
                $( "#alpignano" ).removeClass( "btn-outline-secondary" ).addClass( "btn-secondary" );
                $( "#torino" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#borgaro" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#cirie" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#mauro" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#venaria" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#all" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
            });
            $('#borgaro').on('click', function () {
                dataTables.columns(6).search("").draw();
                dataTables.columns(6).search("Borgaro/Caselle").draw();
                $( "#borgaro" ).removeClass( "btn-outline-secondary" ).addClass( "btn-secondary" );
                $( "#alpignano" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#torino" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#cirie" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#mauro" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#venaria" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#all" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
            });
            $('#cirie').on('click', function () {
                dataTables.columns(6).search("").draw();
                dataTables.columns(6).search("Ciriè").draw();
                $( "#cirie" ).removeClass( "btn-outline-secondary" ).addClass( "btn-secondary" );
                $( "#alpignano" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#borgaro" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#torino" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#mauro" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#venaria" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#all" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
            });
            $('#mauro').on('click', function () {
                dataTables.columns(6).search("").draw();
                dataTables.columns(6).search("San Mauro").draw();
                $( "#mauro" ).removeClass( "btn-outline-secondary" ).addClass( "btn-secondary" );
                $( "#alpignano" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#borgaro" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#cirie" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#torino" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#venaria" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#all" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
            });
            $('#venaria').on('click', function () {
                dataTables.columns(6).search("").draw();
                dataTables.columns(6).search("Venaria").draw();
                $( "#venaria" ).removeClass( "btn-outline-secondary" ).addClass( "btn-secondary" );
                $( "#alpignano" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#borgaro" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#cirie" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#mauro" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#torino" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#all" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
            });
            $('#all').on('click', function () {
                dataTables.columns(6).search("").draw();
                $( "#torino" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#alpignano" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#borgaro" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#cirie" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#mauro" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#venaria" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#all" ).removeClass( "btn-outline-secondary" ).addClass( "btn-secondary" );
            });
            $('#sq1').on('click', function () {
                dataTables.columns(7).search("").draw();
                dataTables.columns(7).search("1").draw();
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
                dataTables.columns(7).search("").draw();
                dataTables.columns(7).search("2").draw();
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
                dataTables.columns(7).search("").draw();
                dataTables.columns(7).search("3").draw();
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
                dataTables.columns(7).search("").draw();
                dataTables.columns(7).search("4").draw();
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
                dataTables.columns(7).search("").draw();
                dataTables.columns(7).search("5").draw();
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
                dataTables.columns(7).search("").draw();
                dataTables.columns(7).search("6").draw();
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
                dataTables.columns(7).search("").draw();
                dataTables.columns(7).search("7").draw();
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
                dataTables.columns(7).search("").draw();
                dataTables.columns(7).search("8").draw();
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
                dataTables.columns(7).search("").draw();
                dataTables.columns(7).search("9").draw();
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
                dataTables.columns(7).search("").draw();
                dataTables.columns(7).search("Sabato").draw();
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
                dataTables.columns(7).search("").draw();
                dataTables.columns(7).search("Diurno").draw();
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
                dataTables.columns(7).search("").draw();
                dataTables.columns(7).search("Montagna").draw();
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
                dataTables.columns(7).search("").draw();
                dataTables.columns(7).search("Serv. Generali").draw();
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
                dataTables.columns(7).search("").draw();
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
            // Filtri per autocertificazioni
            $('#conQualifica').on('click', function () {
                dataTables.column(3).search('<i class="fa-solid fa-check" style="color: green"></i>', true, false).draw();
                $(this).addClass("btn-secondary").removeClass("btn-outline-secondary");
                $('#senzaQualifica').removeClass("btn-secondary").addClass("btn-outline-secondary");
                $('#tutteQualifiche').removeClass("btn-secondary").addClass("btn-outline-secondary");
            });

            $('#senzaQualifica').on('click', function () {
                dataTables.column(3).search('<i class="fa-solid fa-times" style="color: red"></i>', true, false).draw();
                $(this).addClass("btn-secondary").removeClass("btn-outline-secondary");
                $('#conQualifica').removeClass("btn-secondary").addClass("btn-outline-secondary");
                $('#tutteQualifiche').removeClass("btn-secondary").addClass("btn-outline-secondary");
            });

            $('#tutteQualifiche').on('click', function () {
                dataTables.column(3).search("").draw();
                $(this).addClass("btn-secondary").removeClass("btn-outline-secondary");
                $('#conQualifica').removeClass("btn-secondary").addClass("btn-outline-secondary");
                $('#senzaQualifica').removeClass("btn-secondary").addClass("btn-outline-secondary");
            });
        } );
    </script>
    <script>
        $(document).ready(function(){
            $('[data-toggle="popover"]').popover();
            $('.noterubrica').on('click', function (e) {
                e.preventDefault();
                var id = $(this).attr("id");
                var livelloUtente = <?= json_encode($_SESSION['Livello']) ?>;
                $.get("https://croceverde.org/strumenti/autisti/scheda.php", {id: id, livello: livelloUtente }, function (html) {
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
<!-- CONTENUTO -->
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
                        <th scope="col" style="text-align: center">Autocertificazione</th>
                        <th scope="col" style="text-align: center">NORMALI</th>
                        <th scope="col" style="text-align: center">URGENZE</th>
                        <th scope="col">SEZIONE</th>
                        <th scope="col">SQUADRA</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    while ($row = $anagraficaQuery->fetch_assoc()) {
                        $mostraX = false;
                        $autocertificazionePresente = ($autocertificazioneData[$row['Codice']] ?? 'SENZA') !== 'SENZA';
                        if ($anagraficaQuery->num_rows > 0): ?>
                            <tr style="<?= $style ?>">
                                <td class="align-middle" data-label="Codice">
                                    <button type="button" id="<?= $row['Codice'] ?>" class="btn btn-link  noterubrica">
                                        <?= $row['Codice'] ?>
                                    </button>
                                </td>
                                <td class="align-middle" data-label="Cognome">
                                    <?= $row['Cognome'] ?>
                                </td>
                                <td class="align-middle" data-label="Nome">
                                    <?= $row['Nome'] ?>
                                </td>
                                <td style="text-align: center" data-label="Autocertificazione">
                                    <?php
                                    echo $autocertificazionePresente
                                        ? '<i class="fa-solid fa-check" style="color: green"></i>'
                                        : '<i class="fa-solid fa-times" style="color: red"></i>';
                                    ?>
                                </td>
                                <!-- Colonna SOSP -->
                                <td class="align-middle" style="text-align: center" data-label="Normali">
                                    <?php
                                    if ($row['SOSP'] == 72) {
                                        $scadenza = DateTime::createFromFormat('d/m/Y', $row['ScadenzaSOSP']);
                                        $oggi = new DateTime();
                                        if ($scadenza && $scadenza > $oggi) {
                                            $mostraX = true;
                                            echo '<i class="fa-solid fa-times" style="color: red"></i>';
                                        }
                                    }
                                    if (!$mostraX && $row['MA'] == 4) {
                                        echo $autocertificazionePresente
                                            ? '<i class="fa-solid fa-check" style="color: green"></i>'
                                            : '<i class="fa-solid fa-times" style="color: red"></i>';
                                    }
                                    ?>
                                </td>
                                <!-- Colonna MAU -->
                                <td class="align-middle" style="text-align: center" data-label="Urgenze">
                                    <?php
                                    if (!$mostraX && $row['MAU'] == 5) {
                                        $scadenza_urgente = $scadenzaData[$row['IDUtente']] ?? null;
                                        $scadenza_over = $overData[$row['IDUtente']] ?? null;
                                        if (!$autocertificazionePresente || (($scadenza_urgente && $scadenza_urgente < strtotime(date('Y-m-d'))) && (!$scadenza_over || $scadenza_over < strtotime(date('Y-m-d'))))) {
                                            echo '<i class="fa-solid fa-times" style="color: red"></i>';
                                        } else {
                                            echo '<i class="fa-solid fa-check" style="color: green"></i>';
                                        }
                                    }
                                    ?>
                                </td>
                                <td class="align-middle" data-label="Sezione">
                                    <?= $dictionaryFiliale[$row['IDFiliale']] ?>
                                </td>
                                <td class="align-middle" data-label="Squadra">
                                    <?= $dictionarySquadra[$row['IDSquadra']] ?>
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

<!-- MODAL NOTE -->
<div class="modal bd-note" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body" id="modalnote">
                <!-- Contenuto AJAX -->
            </div>
        </div>
    </div>
</div>
</div>

<?php include "../config/include/footer.php"; ?>
</body>
</html>