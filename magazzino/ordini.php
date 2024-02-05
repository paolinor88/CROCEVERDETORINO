<?php
/**
 *
 * @author     Paolo Randone
 * @author     <paolo.randone@croceverde.org>
 * @version    7.1
 * @note       Powered for Croce Verde Torino. All rights reserved
 *
 */
session_start();
//parametri DB
include "../config/config.php";
include "../config/include/destinatari.php";
//test
if (!isset($_SESSION["ID"])){
    header("Location: ../error.php");
}
//nicename stati
$dictionaryStato = array (
    1 => '<i style="color: darkorange" class="far fa-clock"></i>',
    2 => '<i style="color: #5cb85c" class="far fa-check-circle"></i>',
    3 => '<i style="color: #6c757d" class="fas fa-lock"></i>',
);
//nicename sezioni
$dictionarySezione = array (
    1 => "TO",
    2 => "AL",
    3 => "BC",
    4 => "CI",
    5 => "SM",
    6 => "VE",
    7 => "DIP",
);
//nicename sezioni
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
    10 => "SAB",
    11 => "MON",
    12 => "Direzione",
    13 => "Lunedì",
    14 => "Martedì",
    15 => "Mercoledì",
    16 => "Giovedì",
    17 => "Venerdì",
    18 => "DIU",
    19 => "Giovani",
    20 => "Servizi Generali",
    21 => "Altro",
    22 => "TO",
);
//input item
if( isset($_POST['form_item_id_list']) ) {
    $array_item = explode( ',' , $_POST['form_item_id_list'] );
    foreach( $array_item as $id_item ) {
        if( isset($_POST['form_qt_' . $id_item]) and ($_POST['form_qt_' . $id_item] > 0) ) {
            $quantita = $_POST['form_qt_' . $id_item];
            $prova = $db->query("SELECT nome, tipo FROM giacenza WHERE id='$id_item'")->fetch_array();
            $tabella .= $prova['nome'].' '.$prova['tipo'].': '.$quantita.'<br>';
        }
    }
    //TODO modificare destinatario
    $to=$bechis;
    $nome_mittente="Gestionale CVTO";
    $mail_mittente=$gestionale;
    $headers = "From: " .  $nome_mittente . " <" .  $mail_mittente . ">\r\n";
    $headers .= "Bcc: ".$randone."\r\n";
    $headers .= "Reply-To: " .  $_SESSION['email'] . "\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=iso-8859-1";
    $data = date("d-m-Y");
    $replace = array(
        '{{tabella}}',
        '{{ID}}',
        '{{cognome}}',
        '{{nome}}',
        '{{data}}',
        '{{sezione}}',
        '{{squadra}}',
        '{{note}}',
    );
    $with = array(
        $tabella,
        $_SESSION['ID'],
        $_SESSION['cognome'],
        $_SESSION['nome'],
        $data,
        $dictionarySezione[$_SESSION['sezione']],
        $dictionarySquadra[$_SESSION['squadra']],
        $_POST['note'],
    );
    $message = file_get_contents('../config/template/request_item.html');
    $corpo = str_replace ($replace, $with, $message);

    $subject = 'RICHIESTA MATERIALE';

    mail($to, $subject, $corpo, $headers);
    echo '<script type="text/javascript">
        alert("La richiesta è stata inviata correttamente");
        location.reload();
        </script>';

    // <- fine parametri mail
}

if(isset($_POST['prontoALL'])){
    $prontoALL = $db->query("UPDATE richiesta_giacenza SET STATO='2' WHERE STATO='1'");
    header("Location: ordini.php");
}
if(isset($_POST['consegnatoALL'])){
    $consegnatoALL = $db->query("UPDATE richiesta_giacenza SET STATO='3' WHERE STATO='2'");
    header("Location: ordini.php");
}
if(isset($_POST['eliminaALL'])){
    $eliminaALL = $db->query("UPDATE richiesta_giacenza SET STATO='4' WHERE STATO='3'");
    header("Location: ordini.php");
}

?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <meta name="author" content="Paolo Randone">
    <title>Richiesta materiale</title>

    <? require "../config/include/header.html";?>

    <script>
        $(document).ready(function() {
            var dataTables = $('#myTable').DataTable({
                "paging": false,
                "language": {url: '../config/js/package.json'},
                //"order": [[5, "desc"]],
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
                        "targets": [ 4 ],//quantita
                        "visible": true,
                        "searchable": false,
                        "orderable": false

                    },
                    {
                        "targets": [ 5 ],//stato
                        "visible": true,
                        "searchable": true,
                        "orderable": true

                    },
                ]
            });
            //FILTRI TABELLA
            $('#richiesto').on('click', function () {
                dataTables.columns(5).search("").draw();
                dataTables.columns(5).search("Richiesto").draw();
                $( "#richiesto" ).removeClass( "btn-outline-secondary" ).addClass( "btn-secondary" );
                $( "#pronto" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#consegnato" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#all" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
            });
            $('#pronto').on('click', function () {
                dataTables.columns(5).search("").draw();
                dataTables.columns(5).search("Pronto").draw();
                $( "#pronto" ).removeClass( "btn-outline-secondary" ).addClass( "btn-secondary" );
                $( "#richiesto" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#consegnato" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#all" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
            });
            $('#consegnato').on('click', function () {
                dataTables.columns(5).search("").draw();
                dataTables.columns(5).search("Consegnato").draw();
                $( "#consegnato" ).removeClass( "btn-outline-secondary" ).addClass( "btn-secondary" );
                $( "#pronto" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#richiesto" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#all" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
            });
            $('#all').on('click', function () {
                dataTables.columns(5).search("").draw();
                dataTables.columns(5).search("").draw();
                $( "#pronto" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#richiesto" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#consegnato" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#all" ).removeClass( "btn-outline-secondary" ).addClass( "btn-secondary" );
            });
        } );
    </script>

    <script>
        $(document).ready(function(){
            $('[data-toggle="popover"]').popover();
            $('.detailsORDER').on('click', function (e) {
                e.preventDefault();
                var id_richiesta = $(this).attr("id");
                //alert(id_richiesta);
                $.get("https://croceverde.org/gestionale/magazzino/dettagliordine.php", {id_richiesta:id_richiesta}, function (html) {
                    $('#modaldetailsORDER').html(html);
                    $('.bd-detailsORDER').modal('toggle');

                }).fail(function (msg) {
                    console.log(msg);
                })
            });
        });
    </script>

    <script>
        $(document).ready(function(){
            $('[data-toggle="popover"]').popover();

            $('.pronto').on('click', function (e) {
                e.preventDefault();
                var id_richiesta = $(this).attr("id");
                var statoF = "2";
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
                                data:{id_richiesta:id_richiesta, statoF:statoF},
                                success:function(){
                                    swal({text:"Fatto", icon: "success", timer: 1000, button:false, closeOnClickOutside: false});
                                    setTimeout(function () {
                                            location.href='ordini.php';
                                        },1001
                                    )
                                }
                            });
                        } else {
                            swal({text:"Operazione annullata come richiesto!", timer: 1000, button:false, closeOnClickOutside: false});
                        }
                    })
            });
            $('.consegnato').on('click', function (e) {
                e.preventDefault();
                var id_richiesta = $(this).attr("id");
                var statoF = "3";
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
                                data:{id_richiesta:id_richiesta, statoF:statoF},
                                success:function(){
                                    swal({text:"Fatto", icon: "success", timer: 1000, button:false, closeOnClickOutside: false});
                                    setTimeout(function () {
                                            location.href='ordini.php';
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
<body>
<!-- NAVBAR -->
<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../index.php" style="color: #078f40">Home</a></li>
            <li class="breadcrumb-item"><a href="index.php" style="color: #078f40">Autoparco</a></li>
            <li class="breadcrumb-item"><a href="request.php" style="color: #078f40">Richiesta materiale</a></li>
            <li class="breadcrumb-item active" aria-current="page">Ordini</li>
        </ol>
    </nav>
</div>

<!-- TABELLA GENERALE -->
<body>

<div class="container-fluid">
    <div class="jumbotron">
        <div style="text-align: center;">
            <div class="btn-group" role="group">
                    <button type="button" class="btn-outline-success btn btn-sm" id="modalpronti" data-toggle="modal" data-target="#modal2"><i class="far fa-check-circle"></i></button>
                    <button type="button" class="btn-outline-secondary btn btn-sm" data-toggle="modal" data-target="#modal3"><i class="fas fa-lock"></i></button>
                    <button type="button" class="btn-outline-danger btn btn-sm" id="modalelimina" data-toggle="modal" data-target="#modal4"><i class="fas fa-trash-alt"></i></button>
            </div>
        </div>
        <div class="table-responsive-sm">
            <table class="table table-hover table-sm" id="myTable">
                <thead>
                <tr>
                    <th scope="col"></th>
                    <th scope="col">DATA</th>
                    <th scope="col">DA</th>
                    <th scope="col">MATERIALE</th>
                    <th style='text-align: center' scope="col">QUANTITA'</th>
                    <th style='text-align: center' scope="col">STATO</th>
                    <th style='text-align: center' scope="col"></th>

                </tr>
                </thead>
                <tbody>
                <?php

                $select = $db->query("SELECT * FROM richiesta_giacenza
                                            LEFT OUTER JOIN utenti
                                            ON richiesta_giacenza.ID_UTENTE = utenti.ID
                                            LEFT OUTER JOIN giacenza
                                            ON richiesta_giacenza.ID_ITEM = giacenza.id
                                            WHERE richiesta_giacenza.STATO!='4'
                                            order by richiesta_giacenza.STATO, richiesta_giacenza.ID_RICHIESTA DESC");

                while($ciclo = $select->fetch_array()){
                        echo "
					<tr>
                        <td class='align-middle' style='text-align: center'><form><button type='button' id='".$ciclo['ID_RICHIESTA']."' class='btn-outline-dark btn btn-sm detailsORDER'><i class='fas fa-search'></i></button></form></td>
						<td class='align-middle'>".$ciclo['DATA']."</td>
						<td class='align-middle'>".$ciclo['ID'].' '.$ciclo['cognome'].' ('.$dictionarySezione[$ciclo['sezione']].' '.$dictionarySquadra[$ciclo['squadra']].')'."</td>
						<td class='align-middle'>".$ciclo['nome'].' '.$ciclo['tipo']."</td>
						<td class='align-middle' style='text-align: center'>".$ciclo['QUANTITA']."</td>
						<td class='align-middle' style='text-align: center'>".$ciclo=$dictionaryStato[$ciclo['STATO']]."</td>
                        <td class='align-middle' style='text-align: center'>
                            <form>
                                <div class='btn-group ' role='group'>
                                    <button type='button' id=".$ciclo['ID_RICHIESTA']." class='btn-outline-success btn btn-sm pronto'><i class=\"far fa-check-circle\"></i></button>
                                    <button type='button' id=".$ciclo['ID_RICHIESTA']." class='btn-outline-secondary btn btn-sm consegnato'><i class=\"far fa-lock\"></i></button>

                                </div>
                            </form>
                        </td>
					</tr>";
                    }
                ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal bd-stato" role="dialog" aria-hidden="true" id="test">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-body" id="modalstato">
            </div>
        </div>
    </div>
</div>

<div class="modal bd-detailsORDER" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-body" id="modaldetailsORDER">
            </div>
        </div>
    </div>
</div>

<form action="ordini.php" method="post">
    <div class="modal" id="modal2" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalCenterTitle">Confermare azione</h5>
                </div>
                <div class="modal-body">
                    <p>Premendo conferma, <b>tutti gli ordini "RICHIESTI"</b> <i style="color: darkorange" class="far fa-clock"></i> <u>passeranno allo stato "PRONTO"</u> <i style="color: #5cb85c" class="far fa-check-circle"></i></p>
                    <p>Questa azione non potrà essere annullata e modificherà il conteggio del materiale in giacenza.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-dismiss="modal">Annulla</button>
                    <button type="submit" class="btn btn-danger btn-sm" name="prontoALL">Conferma</button>
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
                    <p>Premendo conferma, <b>tutti gli ordini "PRONTI" </b> <i style="color: #5cb85c" class="far fa-check-circle"></i> <u>passeranno allo stato "CONSEGNATO"</u> <i style="color: #6c757d" class="fas fa-lock"></i></p>
                    <p>Questa azione non potrà essere annullata.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-dismiss="modal">Annulla</button>
                    <button type="submit" class="btn btn-danger btn-sm" name="consegnatoALL">Conferma</button>
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
                    <p>Premendo conferma, <b>tutte le richieste "CONSEGNATE"</b> <i style="color: #6c757d" class="fas fa-lock"></i> <u>verranno ELIMINATE</u></p>
                    <p>Questa azione non potrà essere annullata.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-dismiss="modal">Annulla</button>
                    <button type="submit" class="btn btn-danger btn-sm" name="eliminaALL">Conferma</button>
                </div>
            </div>
        </div>
    </div>
</form>

<br>
</body>
<?php include('../config/include/footer.php'); ?>
</html>

