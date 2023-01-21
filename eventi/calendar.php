<?php
/**
 *
 * @author     Paolo Randone
 * @author     <mail@paolorandone.it>
 * @version    5.0
 * @note       Powered for Croce Verde Torino. All rights reserved
 *
 */
session_start();
//parametri DB
include "config/config.php";
//login
if (!isset($_SESSION["ID"])){
    header("Location: login.php");
}

?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <meta name="author" content="Paolo Randone">
    <title>Calendario</title>

    <? require "../config/include/header.html";?>

    <script src="../config/js/gcal.js"></script>
    <script src="../config/js/it.js"></script>


    <script>
        $(document).ready(function () {
            var calendaradmin = $('#calendaradmin').fullCalendar({
                header: {
                    left: 'prev',
                    center: 'title',
                    right: 'month,listMonth next',
                },
                themeSystem: 'bootstrap4',
                displayEventTime: false,
                googleCalendarApiKey: 'AIzaSyDUFn_ITtZMX10bHqcL0kVsaOKI0Sgg1yo',
                eventSources: [
                    {
                        // eventi CVTO
                        url: 'loadevents.php',
                        type: 'POST',
                        data:{
                            stato: 'stato',
                            id: 'id'
                        },
                    },
                    {
                        // festività nazionali
                        googleCalendarId: 'rpiguh13hptg6bq4imt5udgjpo@group.calendar.google.com',
                        color: 'red',
                        className: 'nolink',
                    },
                    {
                        //guardie notturne
                        googleCalendarId: 'croceverdetorino@gmail.com',
                        color: 'green',
                        className: 'nolink'
                    },
                    {
                        // guardie festive
                        googleCalendarId: '6uufgjtjvtvsj4snuluuunlmgo@group.calendar.google.com',
                        color: 'orange',
                        className: 'nolink'
                    }
                ],
                eventClick: function(calEvent) {
                    if (calEvent.url) {
                        return false;
                    }else{
                        $.get("https://croceverde.org/gestionale/eventi/schedaevento.php", {id:calEvent.id, provenienza:"calendario"}, function (html) {
                            $('#dettaglioevento').html(html);
                            $('.bd-example-modal-xl').modal('toggle');
                        }).fail(function (msg) {
                            console.log(msg);
                        })
                        //location.href='https://croceverde.org/gestionale/eventi/schedaevento.php?id='+calEvent.id;
                    }
                },
            });
            var calendardip = $('#calendardip').fullCalendar({
                header: {
                    left: 'prev',
                    center: 'title',
                    right: 'next',
                },
                themeSystem: 'bootstrap4',
                displayEventTime: false,
                googleCalendarApiKey: 'AIzaSyDUFn_ITtZMX10bHqcL0kVsaOKI0Sgg1yo',
                eventSources: [
                    {
                        // festività nazionali
                        googleCalendarId: 'rpiguh13hptg6bq4imt5udgjpo@group.calendar.google.com',
                        color: 'red',
                        className: 'nolink',
                        rendering: 'background'

                    },
                    {
                        //ciclico settimane
                        googleCalendarId: 'dipcvto@gmail.com',
                        color: 'green',
                    }
                ],
                eventClick: function (calEvent, jsEvent) {
                    jsEvent.preventDefault();
                    $.get("https://croceverde.org/gestionale/eventi/settimana.php", {numerosettimana:calEvent.title, provenienza:"calendario"}, function (html) {
                        $('#tabellaturni').html(html);
                        $('.bd-ciclico-modal-xl').modal('toggle');
                    }).fail(function (msg) {
                        console.log(msg);
                    })
                    //var numerosettimana = calEvent.title;
                    //window.open('https://croceverde.org/gestionale/eventi/settimana.php?numerosettimana='+numerosettimana, "_self");
                }
            });
            var calendarvulu = $('#calendarvulu').fullCalendar({
                header: {
                    left: 'prev',
                    center: 'title',
                    right: 'month,listMonth next',
                },
                themeSystem: 'bootstrap4',
                displayEventTime: false,
                googleCalendarApiKey: 'AIzaSyDUFn_ITtZMX10bHqcL0kVsaOKI0Sgg1yo',
                eventSources: [
                    {
                        // eventi CVTO
                        url: 'loadevents.php',
                        type: 'POST',
                        data:{
                            stato: 'stato',
                            id: 'id',
                            className: 'sqlevent'
                        },
                    },
                    {
                        // festività nazionali
                        googleCalendarId: 'rpiguh13hptg6bq4imt5udgjpo@group.calendar.google.com',
                        color: 'red',
                        className: 'nolink',
                    },
                    {
                        //guardie notturne
                        googleCalendarId: 'croceverdetorino@gmail.com',
                        color: 'green',
                        className: 'nolink'
                    },
                    {
                        // guardie festive
                        googleCalendarId: '6uufgjtjvtvsj4snuluuunlmgo@group.calendar.google.com',
                        color: 'orange',
                        className: 'nolink'
                    }
                ],
                customButtons: {
                    filterBTN: {
                        text: 'Filter',
                        click: function () {
                            $('#modal3').modal('show');
                            $("#filterButton").click(function () {
                                $('#modal3').modal('hide');
                                calendarvulu.fullCalendar('refetchEvents')
                            });
                            $("#resetButton").click(function () {
                                $('#modal3').modal('hide');
                                location.reload();
                            });
                        }
                    },
                },
                eventClick: function(calEvent) {
                    if (calEvent.url) {
                        return false;
                    }else{
                        $('#modaldisp').modal('show');
                        $('#prenota').off('click').on('click', function () {
                            //$('#modaldisp').modal('hide');
                            var id_evento = calEvent.id;
                            var id_utente = '<?=$_SESSION["ID"]?>';
                            if (id_utente) {
                                $.ajax({
                                    url: "script.php",
                                    type: "POST",
                                    data: {id_evento:id_evento, id_utente:id_utente},
                                    success: function () {
                                        calendarvulu.fullCalendar('refetchEvents');
                                        swal({text: "Disponibilità inserita con successo", icon: "success", timer: 1000, button: false, closeOnClickOutside: false});
                                        setTimeout(function () {
                                                location.reload();
                                            }, 1001
                                        )
                                    }
                                });
                            }
                        })
                    }
                },
            });
        })
    </script>
    <? //var_dump($_SESSION); ?>
</head>
<body>
<!-- NAVBAR -->
<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../index.php" style="color: #078f40">Home</a></li>
            <li class="breadcrumb-item"><a href="index.php" style="color: #078f40">Calendario</a></li>
            <li class="breadcrumb-item active" aria-current="page">Calendario</li>
        </ol>
    </nav>
</div>
<br>
<div class="container-fluid">
    <div id='<?if ($_SESSION['livello']==6){echo 'calendaradmin';}elseif (($_SESSION['livello']==1) OR ($_SESSION['livello']==4)){echo 'calendardip';}else {echo 'calendarvulu';}?>'></div>
</div>

</body>

<!-- FOOTER -->
<?php include('../config/include/footer.php'); ?>

<!--eventi-->
<div class="modal fade bd-example-modal-xl" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-body" id="dettaglioevento">

            </div>
        </div>
    </div>
</div>
<!--ciclico-->
<div class="modal fade bd-ciclico-modal-xl" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-body" id="tabellaturni">
            </div>
        </div>
    </div>
</div>
<!--disponibilità-->
<div class="modal fade" id="modaldisp" tabindex="-1" role="dialog" aria-labelledby="modaldisp" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modaldisptitle">Inserisci disponibilità</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary btn-sm" data-dismiss="modal">Esci</button>
                <button type="button" class="btn btn-success btn-sm" id="prenota">Conferma</button>
            </div>
        </div>
    </div>
</div>
<!-- MODAL FILTRO-->
<div id="modal3" class="modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content">
            <form>
                <div class="modal-header">
                    <h6 class="modal-title" id="modal3Title">Filtra per...</h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" align="center">
                    <div>Turni di guardia</div>
                    <select id="modalFilterGuardie" name="modalFilterGuardie" class="form-control form-control-sm" required>
                        <option value="all">Mostra</option>
                        <option value="nolink">Nascondi</option>
                    </select>
                    <hr>
                    <div>Eventi</div>
                    <select id="modalFilterEvent" name="modalFilterEvent" class="form-control form-control-sm" required>
                        <option value="all">Mostra</option>
                        <option value="sqlevent">Nascondi</option>
                    </select>
                </div>
                <div class="modal-footer justify-content-center">
                    <div class="btn-group btn-group" role="group">
                        <button type="button" class="btn btn-outline-warning" id="resetButton"><i class="fas fa-reply"></i></button>
                        <button type="button" class="btn btn-outline-success" id="filterButton"><i class="fas fa-filter"></i></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
</html>