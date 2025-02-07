
    $(document).ready(function () {
    // Ottieni i limiti validi dal server
    $.ajax({
        url: 'getValidRange.php',
        type: 'GET',
        dataType: 'json',
        success: function (response) {
            let validStart = response.validStart;
            let validEnd = response.validEnd;

            // Configura il calendario principale
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
                    } else if (event.start.format("HH:mm:ss") === "01:00:00") {
                        element.addClass('giorno');
                    } else {
                        element.addClass('pomeriggio');
                    }
                },
                header: {
                    left: 'prev,refreshBTN,filterBTN,today',
                    center: 'title',
                    right: 'basicWeek,month,next',
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
                        }
                    }
                },
                eventSources: [
                    {
                        url: 'loadagenda.php',
                        type: 'POST',
                        data: { stato: 'stato', id: 'id', user_id: 'user_id' }
                    },
                    {
                        googleCalendarId: 'rpiguh13hptg6bq4imt5udgjpo@group.calendar.google.com',
                        color: 'red',
                        className: 'nolink',
                    }
                ]
            });

            // Configura il calendario utente
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
                    } else if (event.start.format("HH:mm:ss") === "01:00:00") {
                        element.addClass('giorno');
                    } else {
                        element.addClass('pomeriggio');
                    }
                },
                dayClick: function (date) {
                    let day = date.format("YYYY-MM-DD");
                    $('#modal4').modal('show');
                    $('#addButton').off('click').on('click', function () {
                        $('#modal4').modal('hide');
                        let user_id = $("#user_id").val();
                        let title = $("#cognomenome").val();
                        let selectedTime = $("#modalAddStart option:selected").val();
                        if (selectedTime !== "") {
                            let start = `${day} ${selectedTime}`;
                            let end = moment(start).add(1, 'hours').format("YYYY-MM-DD HH:mm:ss");
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
                                            title: "Tentativo Bloccato!",
                                            text: "Inserimento fuori dai limiti permessi.",
                                            icon: "error",
                                        });
                                    } else {
                                        Swal.fire({
                                            title: "Errore!",
                                            text: "Si è verificato un problema, riprova.",
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
                }
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
