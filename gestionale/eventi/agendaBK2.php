<?php
/**
 *
 * @author     Paolo Randone
 * @author     <paolo.randone@croceverde.org>
 * @version    8.1
 * @note       Powered for Croce Verde Torino. All rights reserved
 *
 */
session_start();
//parametri DB
include "../config/config.php";
//login
if (!isset($_SESSION["ID"])){
    header("Location: ../login.php");
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <meta name="author" content="Paolo Randone">
    <title>Agenda straordinario</title>

    <? require "../config/include/header.html";?>

    <script src="../config/js/gcal.js"></script>
    <script src="../config/js/it.js"></script>
    <!-- Moment.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
    <!-- Moment Timezone -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.31/moment-timezone-with-data.min.js"></script>

    <script>
        $(document).ready(function () {

            // Ottieni i limiti validi dal server
            $.ajax({
                url: 'getValidRange.php',
                type: 'GET',
                dataType: 'json',
                success: function (response) {
                    let validStart = response.validStart;
                    let validEnd = response.validEnd;
                    // calendario admin
                    var agendacal = $('#agendacal').fullCalendar({
                        validRange: {
                            start: validStart,
                            end: validEnd
                        },
                        eventRender: function (event, element) {
                            if (event.stato !== '1') {
                                element.addClass('confermato');
                            } else if (event.start.format("HH:mm:ss") === "06:00:00") {
                                element.addClass('mattino');
                            } else if (event.start.format("HH:mm:ss") === "08:00:00") {
                                element.addClass('centrale');
                            } else if ((event.start.format("HH:mm:ss") === "01:00:00") ||  (event.start.format("HH:mm:ss") === "03:00:00")){
                                element.addClass('giorno');
                            } else {
                                element.addClass('pomeriggio');
                            }

                            // Filtra eventi in base ai selettori nel modal
                            return (
                                ['all', event.user_id].indexOf($("#modalFilterID option:selected").val()) >= 0 &&
                                ['all', event.start.format("HH:mm:ss")].indexOf($("#modalFilterTime option:selected").val()) >= 0
                            );
                        },
                        customButtons: {
                            refreshBTN: {
                                text: 'Aggiorna',
                                click: function () {
                                    location.reload();
                                }
                            },
                            filterBTN: {
                                text: 'Filtra',
                                click: function () {
                                    $('#modal3').modal('show');
                                    $("#filterButton").off('click').on('click', function () {
                                        $('#modal3').modal('hide');
                                        agendacal.fullCalendar('refetchEvents');
                                    });
                                    $("#resetButton").off('click').on('click', function () {
                                        $('#modal3').modal('hide');
                                        location.reload();
                                    });
                                }
                            },
                        },
                        header: {
                            left: 'prev filterBTN,refreshBTN,today',
                            center: 'title',
                            right: 'basicWeek,month next',
                        },
                        /*
                        validRange: function (nowDate) {
                            return {
                                start: nowDate.clone().subtract(1, 'years'),
                                end: nowDate.clone().add(1, 'months')
                            };
                        },
                        */
                        eventOrder: "event.id",
                        editable: false,
                        selectable: false,
                        displayEventEnd: false,
                        eventDurationEditable: false,
                        defaultView: 'month',
                        themeSystem: 'bootstrap4',
                        displayEventTime: false,
                        googleCalendarApiKey: 'AIzaSyDUFn_ITtZMX10bHqcL0kVsaOKI0Sgg1yo',
                        eventSources: [
                            {
                                url: 'loadagenda.php',
                                type: 'POST',
                                data: { stato: 'stato', id: 'id', user_id: 'user_id' },
                            },
                            {
                                googleCalendarId: 'rpiguh13hptg6bq4imt5udgjpo@group.calendar.google.com',
                                color: 'red',
                                className: 'nolink',
                            }
                        ],
                        eventClick: function (event, jsEvent) {
                            jsEvent.preventDefault(); // Disabilita azioni predefinite
                        }
                    });
                    // calendario user
                    var calendaruser = $('#calendaruser').fullCalendar({
                        validRange: {
                            start: validStart,
                            end: validEnd
                        },
                        eventRender: function (event, element) {
                            if (event.stato !== '1') {
                                element.addClass('confermato');
                            } else if (event.start.format("HH:mm:ss") === "06:00:00") {
                                element.addClass('mattino');
                            } else if (event.start.format("HH:mm:ss") === "08:00:00") {
                                element.addClass('centrale');
                            } else if ((event.start.format("HH:mm:ss") === "01:00:00") ||  (event.start.format("HH:mm:ss") === "03:00:00")){
                                element.addClass('giorno');
                            } else {
                                element.addClass('pomeriggio');
                            }

                            return (
                                ['all', event.id].indexOf($("#modalFilterID option:selected").val()) >= 0 &&
                                ['all', event.start.format("HH:mm:ss")].indexOf($("#modalFilterTime option:selected").val()) >= 0
                            );
                        },
                        header: {
                            left: 'prev,today',
                            center: 'title',
                            right: 'basicWeek,month,next',
                        },
                        /*
                        validRange: function () {
                            let nowDate = moment.tz("Europe/Rome").startOf('day').add(3, 'hours');
                            return {
                                start: nowDate.clone().subtract(1, 'years'),
                                end: nowDate.clone().add(9, 'days')
                                //end: nowDate.clone().add(1, 'months')
                            };
                        },

                         */
                        eventOrder: "event.id",
                        editable: false,
                        selectable: false,
                        displayEventEnd: false,
                        eventDurationEditable: false,
                        defaultView: 'basicWeek',
                        themeSystem: 'bootstrap4',
                        displayEventTime: false,
                        googleCalendarApiKey: 'AIzaSyDUFn_ITtZMX10bHqcL0kVsaOKI0Sgg1yo',
                        eventSources: [
                            {
                                url: 'loadagenda.php',
                                type: 'POST',
                                data: { stato: 'stato', id: 'id' },
                            },
                            {
                                googleCalendarId: 'rpiguh13hptg6bq4imt5udgjpo@group.calendar.google.com',
                                color: 'red',
                                className: 'nolink',
                                rendering: 'background'
                            }
                        ],
                        //adesso vediamo chi è più furbo!!!
                        /*
                        dayClick: function (date) {
                            let day = date.format("YYYY-MM-DD");
                            $('#modal4').modal('show');
                            $('#addButton').off('click').on('click', function () {
                                $('#modal4').modal('hide');
                                let user_id = $("#user_id").val();
                                let title = $("#cognomenome").val();
                                let selectedTime = $("#modalAddStart option:selected").val();
                                if (selectedTime !== "") {
                                    // Forza il formato ISO 8601 con UTC per gli orari
                                    let start = new Date(day + "T" + selectedTime + "Z").toISOString();
                                    let end = new Date(new Date(start).getTime() + 60 * 60 * 1000).toISOString();
                                    //var serverNow = new Date().toLocaleString('it-IT', { timeZone: 'Europe/Rome' });
                                    $.ajax({
                                        url: "insert.php",
                                        type: "POST",
                                        data: { title, start, end, user_id },
                                        success: function () {
                                            calendaruser.fullCalendar('refetchEvents');
                                            Swal.fire({
                                                text: "Disponibilità inserita con successo",
                                                icon: "success",
                                                timer: 1000
                                            });
                                        },
                                        error: function (xhr) {
                                            if (xhr.status === 403) {
                                                Swal.fire({
                                                    title: "TENTATIVO BLOCCATO",
                                                    text: `Attenzione, li tuo tentativo di inserimento non è conforme alle regole e quindi è stato bloccato.`,
                                                    icon: "error",
                                                });
                                            } else {
                                                Swal.fire({
                                                    title: "Errore sconosciuto!",
                                                    text: "Riprova più tardi.",
                                                    icon: "error",
                                                });
                                            }
                                        }
                                    });
                                } else {
                                    Swal.fire({
                                        text: "Seleziona un turno dall'elenco!",
                                        icon: "warning",
                                    });
                                }
                            });
                        },
                         */
                        dayClick: function (date) {
                            let day = date.format("YYYY-MM-DD");
                            let now = new Date(); // Data attuale in locale
                            let utcNow = new Date(now.toISOString()); // Converte l'orario attuale in UTC
                            let italyTime = new Date(utcNow.getTime() + 3600000 * 1); // Aggiunge 1 ora per ottenere l'ora italiana (GMT+1)

                            // Controlla che l'orario attuale sia successivo alle 3 del mattino italiane
                            if (italyTime.getHours() < 3) {
                                Swal.fire({
                                    title: "Inserimento non consentito!",
                                    text: "Puoi aggiungere la disponibilità dalle 3:00!",
                                    icon: "warning",
                                });
                                return;
                            }

                            $('#modal4').modal('show');
                            $('#addButton').off('click').on('click', function () {
                                $('#modal4').modal('hide');
                                let user_id = $("#user_id").val();
                                let title = $("#cognomenome").val();
                                let selectedTime = $("#modalAddStart option:selected").val();

                                if (selectedTime !== "") {
                                    // Forza il formato ISO 8601 con UTC per gli orari
                                    let start = new Date(day + "T" + selectedTime + "Z").toISOString();
                                    let end = new Date(new Date(start).getTime() + 60 * 60 * 1000).toISOString();

                                    $.ajax({
                                        url: "insert.php",
                                        type: "POST",
                                        data: { title, start, end, user_id },
                                        success: function () {
                                            calendaruser.fullCalendar('refetchEvents');
                                            Swal.fire({
                                                text: "Disponibilità inserita con successo",
                                                icon: "success",
                                                timer: 1000
                                            });
                                        },
                                        error: function (xhr) {
                                            if (xhr.status === 403) {
                                                Swal.fire({
                                                    title: "TENTATIVO BLOCCATO",
                                                    text: `Attenzione, il tuo tentativo di inserimento non è conforme alle regole e quindi è stato bloccato.`,
                                                    icon: "error",
                                                });
                                            } else {
                                                Swal.fire({
                                                    title: "Errore sconosciuto!",
                                                    text: "Riprova più tardi.",
                                                    icon: "error",
                                                });
                                            }
                                        }
                                    });
                                } else {
                                    Swal.fire({
                                        text: "Seleziona un turno dall'elenco!",
                                        icon: "warning",
                                    });
                                }
                            });
                        },

                        eventClick: function (event, jsEvent) { //elimina disponibilità
                            jsEvent.preventDefault();
                            var title = $("#cognomenome").val();
                            //alert(event.start.format("YYYY-MM-DD"));
                            if (((moment().format("YYYY-MM-DD")) < (event.start.format("YYYY-MM-DD"))) && (title === event.title)) {
                                Swal.fire({
                                    text: "Sei sicuro di voler cancellare questa disponibilità?",
                                    icon: "warning",
                                    buttons: {
                                        cancel: {
                                            text: "Annulla",
                                            value: null,
                                            visible: true,
                                            closeModal: true,
                                        },
                                        confirm: {
                                            text: "Conferma",
                                            value: true,
                                            visible: true,
                                            closeModal: true,
                                        },
                                    },
                                })
                                    .then((confirm) => {
                                        if (confirm) {
                                            var id = event.id;
                                            $.ajax({
                                                url: "script.php",
                                                type: "POST",
                                                data: {id: id},
                                                success: function () {
                                                    calendaruser.fullCalendar('refetchEvents');
                                                    Swal.fire({
                                                        text: "Disponibilità eliminata con successo",
                                                        icon: "success",
                                                        timer: 1000,
                                                        button: false,
                                                        closeOnClickOutside: false
                                                    });
                                                    setTimeout(function () {
                                                            location.reload();
                                                        }, 1001
                                                    )
                                                }
                                            });
                                        } else {
                                            Swal.fire({
                                                text: "Operazione annullata come richiesto!",
                                                timer: 1000,
                                                button: false,
                                                closeOnClickOutside: false
                                            });
                                        }
                                    })
                            } else {
                                calendaruser.fullCalendar('refetchEvents');
                                Swal.fire({
                                    title: "ERRORE!",
                                    text: "Non puoi eseguire questa operazione",
                                    icon: "error",
                                    button: true,
                                    closeOnClickOutside: false
                                });
                            }
                        },
                    });
                },
                error: function () {
                    Swal.fire({
                        title: 'Errore!',
                        text: 'Impossibile ottenere i limiti dal server. Riprova più tardi.',
                        icon: 'error'
                    });
                }
            });
        });
    </script>

    <!-- Moment.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
    <!-- Moment Timezone -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.31/moment-timezone-with-data.min.js"></script>


    <script>
        async function updateClock() {
            try {
                // Richiedi l'ora al server
                const response = await fetch('https://worldtimeapi.org/api/timezone/Europe/Rome');
                const data = await response.json();

                // Estrai l'ora e formattala
                const dateTime = new Date(data.datetime);
                const formattedTime = dateTime.toLocaleString('it-IT', {
                    year: 'numeric',
                    month: '2-digit',
                    day: '2-digit',
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit'
                });

                // Mostra l'ora
                document.getElementById('clock').innerText = formattedTime;
            } catch (error) {
                console.error('Errore nel recupero dell\'ora:', error);
                document.getElementById('clock').innerText = 'Errore nel recupero dell\'ora.';
            }
        }

        // Aggiorna l'orologio ogni secondo
        setInterval(updateClock, 1000);
        updateClock(); // Aggiorna immediatamente all'avvio
    </script>



</head>
<!-- NAVBAR -->
<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../index.php" style="color: #078f40">Home</a></li>
            <li class="breadcrumb-item"><a href="index.php" style="color: #078f40">Calendario</a></li>
            <li class="breadcrumb-item active" aria-current="page">Agenda straordinario</li>
        </ol>
    </nav>
</div>

<!--<div id="clock" style="text-align: center; font-size: 1.2em; margin-top: 10px; font-weight: bold; color: grey"></div>-->
<div class="container-fluid">
    <div id='<?if ($_SESSION['livello']>=5)echo "agendacal"?>'</div>
<div id='<?if (($_SESSION['livello']==1) OR ($_SESSION['livello']==4)) echo "calendaruser"?>'</div>


<div align="center">Legenda: <span style="color: darkorange" >Mattino</span>, <span style="color: forestgreen" >Centrale</span>, <span style="color: royalblue" >Pomeriggio</span>, <span style="color: slategray" >Weekend e festività</span><br> <span style="color: darkred" >RICORDA DI CANCELLARTI IN CASO DI CAMBIO TURNO</span></div>

<!-- MODAL INSERIMENTO -->
<div id="modal4" class="modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content">
            <form>
                <div class="modal-header">
                    <h6 class="modal-title" id="modal4Title">Il mio turno è:</h6>
                </div>
                <div class="modal-body" align="center">
                    <input type="hidden" id="user_id" value="<?=$_SESSION['ID']?>">
                    <input type="hidden" id="cognomenome" value="<?=$_SESSION['cognome'].' '.$_SESSION['nome']?>">
                    <select class="form-control form-control-sm" id="modalAddStart">
                        <option value="">Seleziona...</option>
                        <option value="06:00:00">Mattino</option>
                        <option value="08:00:00">Centrale</option>
                        <option value="13:00:00">Pomeriggio</option>
                        <option value="03:00:00">Weekend e festività</option>
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