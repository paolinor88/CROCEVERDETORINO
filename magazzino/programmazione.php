<?php
/**
 *
 * @author     Paolo Randone
 * @author     <paolo.randone@croceverde.org>
* @version    7.2
 * @note       Powered for Croce Verde Torino. All rights reserved
 *
 */
session_start();
//parametri DB
include "../config/config.php";
//login
/*
if (!isset($_SESSION["ID"])){
    header("Location: ../login.php");
}
*/
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <meta name="author" content="Paolo Randone">
    <title>Programmazione servizi</title>

    <? require "../config/include/header.html";?>

    <script src="../config/js/gcal.js"></script>
    <script src="../config/js/it.js"></script>

    <script>
        $(document).ready(function () {
            var calendarprogram = $('#calendarprogram').fullCalendar({

                eventRender: function (event, element) {
                    if ((event.Convenzione) === 'MARIA-VITTORIA-CONVENZIONE') {
                        element.addClass('MARIA-VITTORIA-CONVENZIONE');
                    } else if ((event.Convenzione) === 'MOLINETTE-CONVENZIONE INTEROSPEDALIERI') {
                        element.addClass('MOLINETTE-CONVENZIONE-INTEROSPEDALIERI');
                    } else if ((event.Convenzione) === 'ASLTO-ONCOLOGICI') {
                        element.addClass('ASLTO-ONCOLOGICI');
                    } else if ((event.Convenzione) === 'AMEDEO-DI-SAVOIA-CONVENZIONE') {
                        element.addClass('AMEDEO-DI-SAVOIA-CONVENZIONE');
                    } else if ((event.Convenzione) === 'ASLTO-ADI') {
                        element.addClass('ASLTO-ADI');
                    } else if ((event.Convenzione) === 'ASLTO4-ORDINARI-CONVENZIONE') {
                        element.addClass('ASLTO4-ORDINARI-CONVENZIONE');
                    } else if ((event.Convenzione) === 'ASLTO4-ADI-CONVENZIONE') {
                        element.addClass('ASLTO4-ADI-CONVENZIONE');
                    } else if ((event.Convenzione) === 'ASLTO-RSA') {
                        element.addClass('ASLTO-RSA');
                    } else if ((event.Convenzione) === 'SANT-ANNA-OIRM-SECONDARI') {
                        element.addClass('SANT-ANNA-OIRM-SECONDARI');
                    } else if ((event.Convenzione) === 'ASLTO-CAVS') {
                        element.addClass('ASLTO-CAVS');
                    } else if ((event.Convenzione) === 'ASLTO-RSA-DGR-23') {
                        element.addClass('ASLTO-RSA-DGR-23');
                    } else {
                        element.addClass('altro');
                    }
                    return (['all', event.id].indexOf($("#modalFilterID option:selected").val()) >= 0) && (['all', event.start.format("HH:mm:ss")].indexOf($("#modalFilterTime option:selected").val()) >= 0);
                },
                header: {
                    left: 'prev ,today',
                    center: 'title',
                    right: 'list, agendaDay, next',
                },
                /*
                validRange: function(nowDate) {
                    return {
                        start: nowDate.clone().subtract(1, 'years'),
                        end: nowDate.clone().add(8, 'days')
                    };
                },
                 */
                eventOrder: 'title',
                defaultDate: '2022-12-20',
                //aspectRatio: 3,
                editable: true,
                selectable: false,
                eventDurationEditable: false,
                eventOverlap: true,
                //slotEventOverlap: false;
                defaultView: 'agendaDay',
                allDaySlot: false,
                slotEventOverlap: false,
                slotDuration: '00:15:00',
                themeSystem: 'bootstrap4',
                displayEventEnd: false,
                displayEventTime: false,
                googleCalendarApiKey: 'AIzaSyDUFn_ITtZMX10bHqcL0kVsaOKI0Sgg1yo',
                eventSources: [
                    {
                        // programmazione
                        url: 'loadprogrammazione.php',
                        type: 'POST',
                        textColor: 'black',
                        data: {
                            id: 'id',
                            OraUscitaSede: 'OraUscitaSede',
                            OraSulPosto: 'OraSulPosto',
                            OraDestinazione: 'OraDestinazione',
                            Carico: 'Carico',
                            Destinazione: 'Destinazione',
                            Convenzione: 'Convenzione'
                        },
                    },
                    {
                        // festività nazionali
                        googleCalendarId: 'rpiguh13hptg6bq4imt5udgjpo@group.calendar.google.com',
                        color: 'red',
                        className: 'nolink',
                        rendering: 'background'

                    }
                ],

                eventClick: function(event, jsEvent) { //INSERISCI DISPONIBILITA
                        $('#modal4').modal('show');
                        $('#addButton').off('click').on('click', function () {
                            $('#modal4').modal('hide');
                            if (($("#numeroauto option:selected").val()) !== ""){
                                var title = $("#numeroauto option:selected").val();
                                var id = event.id;
                                $.ajax({
                                    url: "scriptprogrammazione.php",
                                    type: "POST",
                                    data: {title:title, id:id},
                                    success: function () {
                                        calendarprogram.fullCalendar('refetchEvents');
                                        swal({text: "Mezzo assegnato con successo", icon: "success", timer: 1000, button: false, closeOnClickOutside: false});
                                        setTimeout(function () {
                                                location.reload();
                                            }, 1001
                                        )
                                    }
                                });
                            }else{
                                alert("Seleziona una macchina dall'elenco a discesa!")
                            }
                        });

                },

        })
        })
    </script>

</head>
<!-- NAVBAR -->
<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../index.php" style="color: #078f40">Home</a></li>
            <li class="breadcrumb-item"><a href="index.php" style="color: #078f40">Autoparco</a></li>
            <li class="breadcrumb-item active" aria-current="page">Programmazione</li>
        </ol>
    </nav>
</div>
<br>

<div class="container-fluid">

<div id='calendarprogram'></div>



<!-- MODAL INSERIMENTO -->
<div id="modal4" class="modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content">
            <form>
                <div class="modal-header">
                    <h5 class="modal-title" id="modal4Title">Seleziona auto:</h5>
                </div>
                <div class="modal-body" align="center">
                    <select id="numeroauto" name="numeroauto" class="form-control form-control-sm" required>
                        <option value="all">Seleziona</option>
                        <?
                        $selectfilter = $db->query("SELECT ID FROM mezzi WHERE tipo!='3' AND stato!='2' ORDER BY ID");
                        while($ciclo = $selectfilter->fetch_array()){
                            echo "<option value=\"".$ciclo['ID']."\">".$ciclo['ID']."</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="modal-footer justify-content-center">
                    <div class="btn-group btn-group" role="group">
                        <button type="button" class="btn btn-outline-success btn-sm" id="addButton"><i class="far fa-check-circle"></i></button>
                    </div>
                </div>
            </form>
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
                </div>
                <div class="modal-body" align="center">
                    <div>Dipendente</div>
                    <select id="modalFilterID" name="modalFilterID" class="form-control form-control-sm" required>
                        <option value="all">Tutti</option>
                        <?
                        $selectfilter = $db->query("SELECT ID, cognome, nome FROM utenti WHERE livello='1' ORDER BY cognome");
                        while($ciclo = $selectfilter->fetch_array()){
                            echo "<option value=\"".$ciclo['ID']."\">".$ciclo['cognome'].' '.$ciclo['nome']."</option>";
                        }
                        ?>
                    </select>
                    <hr>
                    <div>Orario</div>
                    <select id="modalFilterTime" name="modalFilterTime" class="form-control form-control-sm" required>
                        <option value="all">Tutti</option>
                        <option value="06:00:00">Mattino</option>
                        <option value="08:00:00">Centrale</option>
                        <option value="13:00:00">Pomeriggio</option>
                    </select>

                </div>
                <div class="modal-footer justify-content-center">
                    <div class="btn-group btn-group" role="group">
                        <button type="button" class="btn btn-outline-warning btn-sm" id="resetButton"><i class="fas fa-reply"></i></button>
                        <button type="button" class="btn btn-outline-success btn-sm" id="filterButton"><i class="fas fa-filter"></i></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL ASSEGNA TURNO-->
<div id="modal2" class="modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content">
            <form>
                <div class="modal-header">
                    <h6 class="modal-title" id="modal2Title">Conferma straordinario</h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" align="center">
                    <select id="modalAssigned" name="modalAssigned" class="form-control form-control-sm" required>
                        <option value="2">Assegna</option>
                        <option value="1">Annulla</option>
                    </select>
                </div>
                <div class="modal-footer justify-content-center">
                    <div class="btn-group btn-group" role="group">
                        <button type="button" class="btn btn-outline-success btn-sm" id="assignButton"><i class="far fa-check-circle"></i></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include('../config/include/footer.php'); ?>
</html>