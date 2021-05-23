<?php
/**
 *
 * @author     Paolo Randone
 * @author     <mail@paolorandone.it>
 * @version    2.4
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
//statocheck
$dictionaryStatocheck = array(
    1 => "<i style='color: darkorange' class=\"fas fa-exclamation-triangle\"></i>",
    2 => "<i style=\"color: #5cb85c\" class=\"far fa-check-circle\"></i>",
);
$dictionaryStatocheck2 = array(
    1 => "<i style=\"color: darkorange\" class=\"far fa-clock\"></i>",
    2 => "<i style='color: #6c757d' class=\"fas fa-lock\"></i>",
)
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <meta name="author" content="Paolo Randone">
    <title>Archivio checklist</title>

    <?php require "../config/include/header.html";?>

    <script>
        $(document).ready(function(){
            $('[data-toggle="popover"]').popover();
        });
    </script>

    <script>
        $(document).ready(function() {
            var dataTables = $('#myTable').DataTable({
                stateSave: true,
                "paging": false,
                "language": {url: '../config/js/package.json'},
                //"order": [[1, "desc"]],
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
                        "targets": [ 3 ],//note hidden
                        "visible": true,
                        "searchable": true,

                    },
                    {
                        "targets": [ 4 ],//visto
                        "visible": true,
                        "orderable": false,
                        "searchable": true,

                    },
                    {
                        "targets": [ 5 ],//choiuso
                        "visible": true,
                        "orderable": false,
                        "searchable": true,

                    },
                    {
                        "targets": [ 6 ],//choiuso
                        "visible": true,
                        "orderable": false,
                        "searchable": false,

                    }]
            });
            $('#aperte').on('click', function () {
                dataTables.columns(5).search("<i class='fas fa-times'></i>").draw();
                $( "#aperte" ).removeClass( "btn-outline-secondary" ).addClass( "btn-secondary" );
                $( "#all" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
            });
            $('#all').on('click', function () {
                dataTables.columns(5).search("").draw();
                $( "#aperte" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#all" ).removeClass( "btn-outline-secondary" ).addClass( "btn-secondary" );
            });
        } );
    </script>

    <script>
        $(document).ready(function(){
            $('[data-toggle="popover"]').popover();

            $('.visto').on('click', function (e) {
                e.preventDefault();
                var id = $(this).attr("id");
                var visto = "2";
                swal({
                    text: "Conferma azione",
                    icon: "warning",
                    buttons:{
                        cancel:{
                            text: "Annulla",
                            value: null,
                            visible: true,
                            closeModal: true,
                        },
                        confirm:{
                            text: "Conferma",
                            value: true,
                            visible: true,
                            closeModal: true,
                        },
                    },
                })
                    .then((confirm) => {
                        if(confirm){
                            $.ajax({
                                url:"script.php",
                                type:"POST",
                                data:{id:id, visto:visto},
                                success:function(){
                                    swal({text:"Fatto", icon: "success", timer: 1000, button:false, closeOnClickOutside: false});
                                    setTimeout(function () {
                                            location.href='archivio.php';
                                        },1001
                                    )
                                }
                            });
                        } else {
                            swal({text:"Operazione annullata come richiesto!", timer: 1000, button:false, closeOnClickOutside: false});
                        }
                    })
            });
            $('.chiuso').on('click', function (e) {
                e.preventDefault();
                var id = $(this).attr("id");
                var chiuso = "2";
                swal({
                    text: "Conferma azione",
                    icon: "warning",
                    buttons:{
                        cancel:{
                            text: "Annulla",
                            value: null,
                            visible: true,
                            closeModal: true,
                        },
                        confirm:{
                            text: "Conferma",
                            value: true,
                            visible: true,
                            closeModal: true,
                        },
                    },
                })
                    .then((confirm) => {
                        if(confirm){
                            $.ajax({
                                url:"script.php",
                                type:"POST",
                                data:{id:id, chiuso:chiuso},
                                success:function(){
                                    swal({text:"Fatto", icon: "success", timer: 1000, button:false, closeOnClickOutside: false});
                                    setTimeout(function () {
                                            location.href='archivio.php';
                                        },1001
                                    )
                                }
                            });
                        } else {
                            swal({text:"Operazione annullata come richiesto!", timer: 1000, button:false, closeOnClickOutside: false});
                        }
                    })
            });
            $('.apri').on('click', function (e) {
                e.preventDefault();
                var id = $(this).attr("id");
                var chiuso = "1";
                swal({
                    text: "Conferma azione",
                    icon: "warning",
                    buttons:{
                        cancel:{
                            text: "Annulla",
                            value: null,
                            visible: true,
                            closeModal: true,
                        },
                        confirm:{
                            text: "Conferma",
                            value: true,
                            visible: true,
                            closeModal: true,
                        },
                    },
                })
                    .then((confirm) => {
                        if(confirm){
                            $.ajax({
                                url:"script.php",
                                type:"POST",
                                data:{id:id, chiuso:chiuso},
                                success:function(){
                                    swal({text:"Fatto", icon: "success", timer: 1000, button:false, closeOnClickOutside: false});
                                    setTimeout(function () {
                                            location.href='archivio.php';
                                        },1001
                                    )
                                }
                            });
                        } else {
                            swal({text:"Operazione annullata come richiesto!", timer: 1000, button:false, closeOnClickOutside: false});
                        }
                    })
            });
        });
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
        <!--<center>
            <div class="btn-group" role="group" aria-label="">
                <button id="aperte" type="button" class="btn btn-outline-secondary btn-sm">Aperte</button>
                <button id="all" type="button" class="btn btn-secondary btn-sm">ALL</button>
            </div>
        </center>-->
        <div class="table-responsive-sm">
            <table class="table table-hover table-sm" id="myTable">
                <thead>
                <tr>
                    <th scope="col"></th>
                    <th scope="col">Data</th>
                    <th scope="col">Mezzo</th>
                    <th scope="col">Note</th>
                    <th scope="col"></th>
                    <th scope="col"></th>
                    <th scope="col"></th>
                </tr>
                </thead>
                <tbody>
                <?php
                $select = $db->query("SELECT * FROM checklist WHERE NOTE!='' order by IDCHECK DESC ");
                while($ciclo = $select->fetch_array()){
                    if ($ciclo['NOTE']!=""): ?>
					<tr>
						<td class="align-middle"><a href="https://<?=$_SERVER['HTTP_HOST']?>/gestionale/checklist/details.php?ID=<?=$ciclo['IDCHECK']?>" class="btn btn-sm btn-outline-danger"><i class="fas fa-search"></i></a></td>
						<td class="align-middle"><?php $var=$ciclo['DATACHECK']; $var1=date_create("$var"); echo date_format($var1, "d-m-Y H:m")?></td>
						<td class="align-middle"><?=$ciclo['IDMEZZO']?></td>
						<td class="align-middle"><?=$ciclo['NOTE']?></td>
                        <td class="align-middle"><?=$dictionaryStatocheck[$ciclo['VISTO']]?></td>
                        <td class="align-middle"><?=$dictionaryStatocheck2[$ciclo['CHIUSO']]?></td>
                        <td class="align-middle">
                            <form>
                                <div class="btn-group" role="group">
                                    <button type='button' id='<?=$ciclo['IDCHECK']?>' class='btn-outline-success btn btn-sm visto'><i class="fas fa-check"></i></button>
                                    <button type='button' id='<?=$ciclo['IDCHECK']?>' class='btn-outline-danger btn btn-sm chiuso'><i class="fas fa-lock"></i></button>
                                    <button type='button' id='<?=$ciclo['IDCHECK']?>' class='btn-outline-secondary btn btn-sm apri'><i class="fas fa-unlock"></i></button>
                            </form>
                        </td>

                    </tr>
                    <?php endif; ?>
                    <?php if ($ciclo['NOTE']==""): ?>
					<tr>
						<td class="align-middle"><a href="" class="btn btn-sm btn-outline-secondary disabled" ><i class="far fa-times-circle"></i></a></td>
						<td class="align-middle"><?=$ciclo['DATACHECK']?></td>
						<td class="align-middle"><?=$ciclo['IDMEZZO']?></td>
						<td class="align-middle"><?=$ciclo['NOTE']?></td>
						<td class="align-middle"><?=$ciclo=$dictionaryStatocheck[$ciclo['VISTO']]?></td>
                        <td class="align-middle"><?=$ciclo=$dictionaryStatocheck2[$ciclo['CHIUSO']]?></td>
					</tr>
                    <?php endif;
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
