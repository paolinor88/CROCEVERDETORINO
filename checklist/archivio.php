<?php
/**
 *
 * @author     Paolo Randone
 * @author     <mail@paolorandone.it>
 * @version    1.3
 * @note       Powered for Croce Verde Torino. All rights reserved
 *
 */
session_start();
//connessione DB
include "../config/config.php";
//controllo LOGIN / accesso consentito a logistica, segreteria e ADMIN
if (($_SESSION["livello"])<4){
    header("Location: ../error.php");
}
//nicename livelli
$dictionaryLivello = array (
    1 => "Dipendente",
    2 => "Volontario",
    3 => "Altro",
    4 => "Logistica",
    5 => "Segreteria",
    6 => "ADMIN",
);
//nicename sezioni
$dictionarySezione = array (
    1 => "Torino",
    2 => "Alpignano",
    3 => "Borgaro/Caselle",
    4 => "Ciriè",
    5 => "San Mauro",
    6 => "Venaria",
    7 => "",
);
//nicename sezioni
$dictionarySquadra = array (
    1 => "Prima",
    2 => "Seconda",
    3 => "Terza",
    4 => "Quarta",
    5 => "Quinta",
    6 => "Sesta",
    7 => "Settima",
    8 => "Ottava",
    9 => "Nona",
    10 => "Sabato",
    11 => "Montagna",
    12 => "Direzione",
    13 => "Lunedì",
    14 => "Martedì",
    15 => "Mercoledì",
    16 => "Giovedì",
    17 => "Venerdì",
    18 => "Diurno",
    19 => "Giovani",
    20 => "Servizi Generali",
    21 => "Altro",
    22 => "",
);
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <meta name="author" content="Paolo Randone">
    <title>Archivio checklist</title>

    <? require "../config/include/header.html";?>

    <script>
        $(document).ready(function(){
            $('[data-toggle="popover"]').popover();
        });
    </script>

    <script>
        $(document).ready(function() {
            var dataTables = $('#myTable').DataTable({
                "language": {url: '../config/js/package.json'},
                "order": [[1, "desc"]],
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
                        "targets": [ 4 ],//note hidden
                        "visible": false,
                        "searchable": true,

                    }]
            });
            $('#note').on('click', function () {
                dataTables.columns(4).search('^(?!\s*$).+', true, false).draw();
                $( "#note" ).removeClass( "btn-outline-secondary" ).addClass( "btn-secondary" );
                $( "#all" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
            });
            $('#all').on('click', function () {
                dataTables.columns(4).search("").draw();
                $( "#note" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#all" ).removeClass( "btn-outline-secondary" ).addClass( "btn-secondary" );
            });
        } );
    </script>
</head>
<!-- NAVBAR -->
<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../index.php" style="color: #078f40">Home</a></li>
            <li class="breadcrumb-item"><a href="index.php" style="color: #078f40">Checklist elettronica</a></li>
            <li class="breadcrumb-item active" aria-current="page">Archivio</li>
        </ol>
    </nav>
</div>
<!--content-->
<body>
<div class="container-fluid">
    <div class="jumbotron">
        <center>
            <div class="btn-group" role="group" aria-label="">
                <button id="note" type="button" class="btn btn-outline-secondary btn-sm">Segnalazioni</button>
                <button id="all" type="button" class="btn btn-secondary btn-sm">ALL</button>
            </div>
        </center>
        <div class="table-responsive-sm">
            <table class="table table-hover table-sm" id="myTable">
                <thead>
                <tr>
                    <th scope="col">Apri</th>
                    <th scope="col">Data</th>
                    <th scope="col">Mezzo</th>
                    <th scope="col">Operatore</th>
                    <th scope="col">Note</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $select = $db->query("SELECT * FROM checklist order by DATACHECK DESC ");
                while($ciclo = $select->fetch_array()){
                    if ($ciclo['NOTE']!="") {
                        echo "
					<tr>
						<td>"."<a href=\"https://".$_SERVER['HTTP_HOST']."/gestionale/checklist/details.php?ID=".$ciclo['IDCHECK']."\" class=\"btn btn-sm btn-outline-danger\"><i class=\"fas fa-search\"></i></a>"."</td>
						<td>".$ciclo['DATACHECK']."</td>
						<td>".$ciclo['IDMEZZO']."</td>
						<td>".$ciclo['IDOPERATORE']."</td>
						<td>".$ciclo['NOTE']."</td>
					</tr>";
                    }else{
                        echo "
					<tr>
						<td>"."<a href=\"https://".$_SERVER['HTTP_HOST']."/gestionale/checklist/details.php?ID=".$ciclo['IDCHECK']."\" class=\"btn btn-sm btn-outline-dark disabled\"><i class=\"far fa-times-circle\"></i></a>"."</td>
						<td>".$ciclo['DATACHECK']."</td>
						<td>".$ciclo['IDMEZZO']."</td>
						<td>".$ciclo['IDOPERATORE']."</td>
						<td>".$ciclo['NOTE']."</td>
					</tr>";
                    }
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
<!-- FOOTER -->
<?php include('../config/include/footer.php'); ?>

</html>
