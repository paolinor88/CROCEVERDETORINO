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
    8 => "Servizio Civile",
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
    23 => "",
);
//statocheck
$dictionaryStatocheck = array(
    1 => "<i style='color: darkorange' class=\"far fa-clock\"></i>",
    2 => "<i style=\"color: #5cb85c\" class=\"far fa-check-circle\"></i>",
    3 => "<i style='color: #6c757d' class=\"fas fa-lock\"></i>",

);

if(isset($_POST['vistoALL'])){
    $vistoALL = $db->query("UPDATE checklist SET STATO='2' WHERE STATO='1'");
    header("Location: archivio.php");
}
if(isset($_POST['chiusoALL'])){
    $chiusoALL = $db->query("UPDATE checklist SET STATO='3' WHERE STATO='2'");
    header("Location: archivio.php");
}
if(isset($_POST['eliminaALL'])){
    $eliminaALL = $db->query("UPDATE checklist SET STATO='4' WHERE STATO='3'");
    header("Location: archivio.php");
}
if(isset($_POST['fotovistoALL'])){
    $fotovistoALL = $db->query("UPDATE images SET status='2' WHERE status='1'");
    header("Location: archivio.php");
}
if(isset($_POST['fotochiusoALL'])){
    $fotochiusoALL = $db->query("UPDATE images SET status='3' WHERE status='2'");
    header("Location: archivio.php");
}
if(isset($_POST['fotoeliminaALL'])){
    $eliminaALL = $db->query("UPDATE images SET status='4' WHERE status='3'");
    header("Location: archivio.php");
}

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
        //tabella segnalazioni
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
                        "targets": [ 3 ],//note
                        "visible": true,
                        "searchable": true,

                    },
                    {
                        "targets": [ 4 ],//stato
                        "visible": true,
                        "orderable": false,
                        "searchable": true,

                    },
                    {
                        "targets": [ 5 ],//azioni
                        "visible": true,
                        "orderable": false,
                        "searchable": false,

                    }]
            });
        } );
    </script>
    <script>
        //tabella foto
        $(document).ready(function() {
            var dataTables = $('#fototable').DataTable({
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
                        "targets": [ 3 ],//note
                        "visible": true,
                        "searchable": true,

                    },
                    {
                        "targets": [ 4 ],//stato
                        "visible": true,
                        "orderable": false,
                        "searchable": true,

                    },
                    {
                        "targets": [ 5 ],//azioni
                        "visible": true,
                        "orderable": false,
                        "searchable": false,

                    }]
            });
        } );
    </script>

    <script>
        $(document).ready(function(){
            $('[data-toggle="popover"]').popover();

            $('.visto').on('click', function (e) {
                e.preventDefault();
                var id = $(this).attr("id");
                var stato = "2";
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
                                data:{id:id, stato:stato},
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
                var stato = "3";
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
                                data:{id:id, stato:stato},
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
            $('.fotovisto').on('click', function (e) {
                e.preventDefault();
                var id = $(this).attr("id");
                var status = "2";
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
                                data:{id:id, status:status},
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
            $('.fotochiuso').on('click', function (e) {
                e.preventDefault();
                var id = $(this).attr("id");
                var status = "3";
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
                                data:{id:id, status:status},
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
            <li class="breadcrumb-item"><a href="index.php" style="color: #078f40">Checklist</a></li>
            <li class="breadcrumb-item active" aria-current="page">Archivio</li>
        </ol>
    </nav>
</div>
<!--content-->
<body>
<div class="container-fluid">
    <div class="jumbotron">
        <div class="accordion" id="accordionExample">
            <div class="card">
                <div class="card-header" id="headingOne">
                    <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                            Segnalazioni
                        </button>
                    </h2>
                </div>
                <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordionExample">
                    <div class="card-body">
                        <div style="text-align: center;">
                            <div class="btn-group" role="group">
                                <button type="button" class="btn-outline-success btn btn-sm" id="modalvitsto" data-toggle="modal" data-target="#modal2"><i class="far fa-check-circle"></i></button>
                                <button type="button" class="btn-outline-secondary btn btn-sm" id="modalchiuso" data-toggle="modal" data-target="#modal3"><i class="fas fa-lock"></i></button>
                                <button type="button" class="btn-outline-danger btn btn-sm" id="modalelimina" data-toggle="modal" data-target="#modal4"><i class="fas fa-trash-alt"></i></button>
                            </div>
                        </div>
                        <div class="table-responsive-sm">
                            <table class="table table-hover table-sm" id="myTable">
                                <thead>
                                <tr>
                                    <th scope="col"></th>
                                    <th scope="col">DATA</th>
                                    <th scope="col">MEZZO</th>
                                    <th scope="col">TESTO</th>
                                    <th scope="col">STATO</th>
                                    <th scope="col"></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $select = $db->query("SELECT * FROM checklist WHERE NOTE!='' AND STATO!=4 order by STATO, DATACHECK DESC ");
                                while($ciclo = $select->fetch_array()){
                                    if ($ciclo['NOTE']!=""): ?>
                                        <tr>
                                            <td class="align-middle"><a href="https://<?=$_SERVER['HTTP_HOST']?>/gestionale/checklist/details.php?ID=<?=$ciclo['IDCHECK']?>" class="btn btn-sm btn-outline-danger"><i class="fas fa-search"></i></a></td>
                                            <td class="align-middle"><?php $var=$ciclo['DATACHECK']; $var1=date_create("$var"); echo date_format($var1, "d-m-Y H:i")?></td>
                                            <td class="align-middle"><?=$ciclo['IDMEZZO']?></td>
                                            <td class="align-middle"><?=$ciclo['NOTE']?></td>
                                            <td class="align-middle"><?=$dictionaryStatocheck[$ciclo['STATO']]?></td>
                                            <td class="align-middle">
                                                <form>
                                                    <div class="btn-group" role="group">
                                                        <button type='button' id='<?=$ciclo['IDCHECK']?>' class='btn-outline-success btn btn-sm visto'><i class="far fa-check-circle"></i></button>
                                                        <button type='button' id='<?=$ciclo['IDCHECK']?>' class='btn-outline-secondary btn btn-sm chiuso'><i class="fas fa-lock"></i></button>
                                                </form>
                                            </td>
                                        </tr>
                                    <? endif;
                                }?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header" id="headingTwo">
                    <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                            Foto
                        </button>
                    </h2>
                </div>
                <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionExample">
                    <div class="card-body">
                        <div style="text-align: center;">
                            <div class="btn-group" role="group">
                                <button type="button" class="btn-outline-success btn btn-sm" id="modalvistofoto" data-toggle="modal" data-target="#modal5"><i class="far fa-check-circle"></i></button>
                                <button type="button" class="btn-outline-secondary btn btn-sm" id="modalchiusofoto" data-toggle="modal" data-target="#modal6"><i class="fas fa-lock"></i></button>
                                <button type="button" class="btn-outline-danger btn btn-sm" id="modaleliminafoto" data-toggle="modal" data-target="#modal7"><i class="fas fa-trash-alt"></i></button>
                            </div>
                        </div>
                        <div class="table-responsive-sm">
                            <table class="table table-hover table-sm" id="fototable">
                                <thead>
                                <tr>
                                    <th scope="col"></th>
                                    <th scope="col">DATA</th>
                                    <th scope="col">MEZZO</th>
                                    <th scope="col">TESTO</th>
                                    <th scope="col">STATO</th>
                                    <th scope="col"></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $select = $db->query("SELECT * FROM images WHERE status!=4 group by uploaded_on order by status, id DESC");
                                while($ciclo = $select->fetch_array()){?>
                                    <tr>
                                        <td class="align-middle"><a href="https://<?=$_SERVER['HTTP_HOST']?>/gestionale/checklist/foto.php?ID=<?=$ciclo['id']?>" class="btn btn-sm btn-outline-danger"><i class="fas fa-camera"></i></a></td>
                                        <td class="align-middle"><?php $var=$ciclo['uploaded_on']; $var1=date_create("$var"); echo date_format($var1, "d-m-Y H:i")?></td>
                                        <td class="align-middle"><?=$ciclo['id_mezzo']?></td>
                                        <td class="align-middle"><?=$ciclo['note']?></td>
                                        <td class="align-middle"><?=$dictionaryStatocheck[$ciclo['status']]?></td>
                                        <td class="align-middle">
                                            <form>
                                                <div class="btn-group" role="group">
                                                    <button type='button' id='<?=$ciclo['id']?>' class='btn-outline-success btn btn-sm fotovisto'><i class="far fa-check-circle"></i></button>
                                                    <button type='button' id='<?=$ciclo['id']?>' class='btn-outline-secondary btn btn-sm fotochiuso'><i class="fas fa-lock"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                <? } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<form action="archivio.php" method="post">
    <div class="modal" id="modal2" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalCenterTitle">Confermare azione</h5>
                </div>
                <div class="modal-body">
                    <p>Premendo conferma, <b>tutte le segnalazioni "in attesa" </b> <i style="color: darkorange" class="far fa-clock"></i> <u>passeranno allo stato "VISTO"</u> <i style="color: #5cb85c" class="far fa-check-circle"></i></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-dismiss="modal">Annulla</button>
                    <button type="submit" class="btn btn-danger btn-sm" name="vistoALL">Conferma</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal" id="modal3" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalCenterTitle">Confermare azione</h5>
                </div>
                <div class="modal-body">
                    <p>Premendo conferma, <b>tutte le segnalazioni "viste" </b> <i style="color: #5cb85c" class="far fa-check-circle"></i> <u>passeranno allo stato "RISOLTO"</u> <i style="color: #6c757d" class="fas fa-lock"></i></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-dismiss="modal">Annulla</button>
                    <button type="submit" class="btn btn-danger btn-sm" name="chiusoALL">Conferma</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal" id="modal4" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalCenterTitle">Confermare azione</h5>
                </div>
                <div class="modal-body">
                    <p>Premendo conferma, <b>tutte le segnalazioni "risolte"</b> <i style="color: #6c757d" class="fas fa-lock"></i> <u>verranno ELIMINATE</u></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-dismiss="modal">Annulla</button>
                    <button type="submit" class="btn btn-danger btn-sm" name="eliminaALL">Conferma</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal" id="modal5" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalCenterTitle">Confermare azione</h5>
                </div>
                <div class="modal-body">
                    <p>Premendo conferma, <b>tutte le foto "in attesa" </b> <i style="color: darkorange" class="far fa-clock"></i> <u>passeranno allo stato "VISTO"</u> <i style="color: #5cb85c" class="far fa-check-circle"></i></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-dismiss="modal">Annulla</button>
                    <button type="submit" class="btn btn-danger btn-sm" name="fotovistoALL">Conferma</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal" id="modal6" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalCenterTitle">Confermare azione</h5>
                </div>
                <div class="modal-body">
                    <p>Premendo conferma, <b>tutte le foto "viste" </b> <i style="color: #5cb85c" class="far fa-check-circle"></i> <u>passeranno allo stato "RISOLTO"</u> <i style="color: #6c757d" class="fas fa-lock"></i></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-dismiss="modal">Annulla</button>
                    <button type="submit" class="btn btn-danger btn-sm" name="fotochiusoALL">Conferma</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal" id="modal7" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalCenterTitle">Confermare azione</h5>
                </div>
                <div class="modal-body">
                    <p>Premendo conferma, <b>tutte le foto "risolte"</b> <i style="color: #6c757d" class="fas fa-lock"></i> <u>verranno ELIMINATE</u></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-dismiss="modal">Annulla</button>
                    <button type="submit" class="btn btn-danger btn-sm" name="fotoeliminaALL">Conferma</button>
                </div>
            </div>
        </div>
    </div>
</form>


</body>
<!-- FOOTER -->
<?php include('../config/include/footer.php'); ?>

</html>
