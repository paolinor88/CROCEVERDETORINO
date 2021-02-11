<?php
/**
 *
 * @author     Paolo Randone
 * @author     <mail@paolorandone.it>
 * @version    1.6
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
    <title>Lavaggi mezzi</title>

    <? require "../config/include/header.html";?>

    <script src="../config/js/gcal.js"></script>
    <script src="../config/js/it.js"></script>

    <script>
        $(document).ready(function () {
            var agendalavaggi = $('#agendalavaggi').fullCalendar({
                eventRender: function (event, element){
                    if ((event.stato) == '1'){
                        element.addClass('programmmato');
                    }else {
                        element.addClass('effettuato');
                    }
                    return(['all', event.id].indexOf($("#modalFilterID option:selected").val())>=0)&&(['all', event.start.format("HH:mm:ss")].indexOf($("#modalFilterTime option:selected").val())>=0);
                },
                header: {
                    left: 'prev ,today',
                    center: 'title',
                    right: 'basicWeek,month next',
                },
                /*
                validRange: function(nowDate) {
                    return {
                        start: nowDate.clone().subtract(1, 'years'),
                        end: nowDate.clone().add(1, 'months')
                    };
                },
                */
                eventOrder: "event.id",
                //aspectRatio: 3,
                editable: true,
                selectable: false,
                displayEventEnd: false,
                eventDurationEditable: false,
                //eventOverlap: false,
                //defaultView: 'basicWeek',
                themeSystem: 'bootstrap4',
                displayEventTime: false,
                googleCalendarApiKey: 'AIzaSyDUFn_ITtZMX10bHqcL0kVsaOKI0Sgg1yo',
                eventSources: [
                    {
                        // AGENDA LAVAGGI
                        url: 'loadlavaggi.php',
                        type: 'POST',
                        data: {
                            stato: 'stato',
                            id: 'id'
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
                dayClick: function(date) { //INSERISCI LAVAGGI
                    if(moment() <= date){
                        $('#modal4').modal('show');
                        $('#addButton').off('click').on('click', function () {
                            $('#modal4').modal('hide');
                            var user_id = $("#user_id").val();
                            var title = $("#IDMEZZO option:selected").val();
                            if (title !== ""){
                                var start_event = date.format("YYYY-MM-DD");
                                var stato = $("#modalAction option:selected").val();
                                //alert(user_id);

                                $.ajax({
                                    url: "insert.php",
                                    type: "POST",
                                    data: {title:title, start_event:start_event, user_id:user_id, stato:stato},
                                    success: function () {
                                        agendalavaggi.fullCalendar('refetchEvents');
                                        swal({text: "OK", icon: "success", timer: 1000, button: false, closeOnClickOutside: false});
                                        setTimeout(function () {
                                                location.reload();
                                            }, 1001
                                        )
                                    }
                                });

                            }else{
                                alert("Compila correttamente tutti i campi del form!")
                            }
                        });
                    }else{
                        swal({title: "ERRORE!", text:"Non è una macchina del tempo!", icon: "error", button:true, closeOnClickOutside: false});
                    }
                },
                eventClick:function(event, jsEvent){ //MODIFICA LAVAGGI
                    jsEvent.preventDefault();
                    //alert(event.id);
                    $('#modal1').modal('show');
                    $('#addButton').off('click').on('click', function () {
                        $('#modal1').modal('hide');
                },
            });
        });
    </script>

</head>
<!-- NAVBAR -->
<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../index.php" style="color: #078f40">Home</a></li>
            <li class="breadcrumb-item"><a href="index.php" style="color: #078f40">Autoparco</a></li>
            <li class="breadcrumb-item active" aria-current="page">Lavaggio mezzi</li>
        </ol>
    </nav>
</div>
<br>

<div class="container-fluid">
    <div id='agendalavaggi'</div>
</div>
<div align="center">Legenda: <span style="color: darkorange" >Programmato</span>, <span style="color: forestgreen" >Effettuato</span></div>

<!-- MODAL INSERIMENTO -->
<div id="modal4" class="modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content">
            <form>
                <div class="modal-body" align="center">
                    <input type="hidden" id="user_id" value="<?=$_SESSION['ID']?>">
                    <select id="IDMEZZO" name="IDMEZZO" class="form-control form-control-sm" required>
                        <option value="">Mezzo...</option>
                        <?
                        $select = $db->query("SELECT ID FROM mezzi ORDER BY ID, tipo");
                        while($ciclo = $select->fetch_array()){
                            echo "<option value=\"".$ciclo['ID']."\">".$ciclo['ID']."</option>";
                        }
                        ?>
                    </select> <!-- IDMEZZO -->
                    <hr>
                    <select class="form-control form-control-sm" id="modalAction">
                        <option value="">Azione...</option>
                        <option value="1">Programmato</option>
                        <option value="2">Effettuato</option>
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

<!-- MODAL MODIFICA -->
<div id="modal1" class="modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content">
            <form>
                <div class="modal-body" align="center">
                    <input type="hidden" id="user_id" value="<?=$_SESSION['ID']?>">
                    <select id="IDMEZZO" name="IDMEZZO" class="form-control form-control-sm" required>
                        <option value="">Mezzo...</option>
                        <?
                        $select = $db->query("SELECT ID FROM mezzi ORDER BY ID, tipo");
                        while($ciclo = $select->fetch_array()){
                            echo "<option value=\"".$ciclo['ID']."\">".$ciclo['ID']."</option>";
                        }
                        ?>
                    </select> <!-- IDMEZZO -->
                    <hr>
                    <select class="form-control form-control-sm" id="modalAction">
                        <option value="">Azione...</option>
                        <option value="1">Programmato</option>
                        <option value="2">Effettuato</option>
                    </select>
                </div>
                <div class="modal-footer justify-content-center">
                    <div class="btn-group btn-group" role="group">
                        <button type="button" class="btn btn-outline-success btn-sm" id="updateButton"><i class="far fa-check-circle"></i></button>
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