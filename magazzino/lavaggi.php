<?php
/**
 *
 * @author     Paolo Randone
 * @author     <mail@paolorandone.it>
 * @version    2.3
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
        $( function() {
            $( "#datastart" ).datepicker({
                dateFormat: "yy-mm-dd"
            });
        } );
    </script>
    <script>
        $( function() {
            $( "#dataend" ).datepicker({
                dateFormat: "yy-mm-dd"
            });
        } );
    </script>

    <style>
        td {
            padding: 5px;
            text-align:center;
        }
    </style>

    <script>
        $(document).ready(function () {
            var agendalavaggi = $('#agendalavaggi').fullCalendar({
                eventRender: function (event, element) {
                    if ((event.stato) == '1') {
                        element.addClass('checklist');
                    } else {
                        element.addClass('SAMSIC');
                    }
                    return (['all', event.user_id].indexOf($("#modalFilterID option:selected").val()) >= 0) && (['all', event.title].indexOf($("#modalFilterAuto option:selected").val()) >= 0);
                },
                customButtons: {
                    refreshBTN: {
                        text: 'Aggiorna',
                        click: function () {
                            location.reload();
                        }
                    },
                    exportBTN: {
                        text: 'Esporta',
                        click: function () {
                            $('#modalexportauto').modal('show');
                            //location.href='list.php'
                            //$('<a href="https://croceverde.org/gestionale/magazzino/list.php" target="blank"></a>')[0].click();
                        }
                    },
                    filterBTN: {
                        text: 'Filter',
                        click: function () {
                            $('#modal3').modal('show');
                            $("#filterButton").click(function () {
                                $('#modal3').modal('hide');
                                agendalavaggi.fullCalendar('refetchEvents')
                            });
                            $("#resetButton").click(function () {
                                $('#modal3').modal('hide');
                                location.reload();
                            });
                        }
                    },
                    checkBTN: {
                        text: 'Check',
                        click: function () {
                            $('#modal2').modal('show');
                        }
                    },
                },
                header: {
                    left: 'prev filterBTN,refreshBTN,exportBTN,checkBTN',
                    center: 'title',
                    right: 'listWeek,month,today next',
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
                editable: false,
                droppable: false,
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
                            id: 'id',
                            user_id: 'user_id',
                            title: 'title'
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
                    $('#modal4').modal('show');
                    $('#addButton').off('click').on('click', function () {
                        $('#modal4').modal('hide');
                        var user_id = $("#user_id").val();
                        var title = $("#IDMEZZO option:selected").val();
                        if (title !== ""){
                            var start_event = date.format("YYYY-MM-DD");
                            var stato = $("#modalAction option:selected").val();
                            //alert(title);

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
                },
                eventClick:function(event, jsEvent){ //elimina disponibilità
                    jsEvent.preventDefault();
                    swal({
                        text: "Sei sicuro di voler cancellare questo lavaggio?",
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
                                var id = event.id;
                                var title = event.title;
                                $.ajax({
                                    url:"script.php",
                                    type:"POST",
                                    data:{id:id, title:title},
                                    success:function(){
                                        agendalavaggi.fullCalendar('refetchEvents');
                                        swal({text:"Lavaggio eliminato con successo", icon: "success", timer: 1000, button:false, closeOnClickOutside: false});
                                        setTimeout(function () {
                                                location.reload();
                                            },1001
                                        )
                                    }
                                });
                            } else {
                                swal({text:"Operazione annullata come richiesto!", timer: 1000, button:false, closeOnClickOutside: false});
                            }
                        })
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
    <div id='agendalavaggi'></div>
    <div align="center">Legenda: <span style="color: royalblue" >Checklist</span>, <span style="color: forestgreen" >SAMSIC</span></div>
</div>

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
                        $select = $db->query("SELECT ID FROM mezzi WHERE stato='1' ORDER BY ID, tipo");
                        while($ciclo = $select->fetch_array()){
                            echo "<option value=\"".$ciclo['ID']."\">".$ciclo['ID']."</option>";
                        }
                        ?>
                    </select> <!-- IDMEZZO -->
                    <hr>
                    <select class="form-control form-control-sm" id="modalAction">
                        <option value="2">SAMSIC</option>
                        <option value="1">Checklist</option>
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
                        $selectfilter = $db->query("SELECT ID, cognome, nome FROM utenti WHERE livello IN ('1', '4') ORDER BY cognome");
                        while($ciclo = $selectfilter->fetch_array()){
                            echo "<option value=\"".$ciclo['ID']."\">".$ciclo['cognome'].' '.$ciclo['nome']."</option>";
                        }
                        ?>
                    </select>
                    <hr>
                    <div>Auto</div>
                    <select id="modalFilterAuto" name="modalFilterAuto" class="form-control form-control-sm" required>
                        <option value="all">Tutti</option>
                        <?
                        $selectfilter = $db->query("SELECT ID FROM mezzi WHERE stato!='2' ORDER BY ID, tipo");
                        while($ciclo = $selectfilter->fetch_array()){
                            echo "<option value=\"".$ciclo['ID']."\">".$ciclo['ID']."</option>";
                        }
                        ?>
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

<!-- MODAL CONTROLLA -->
<div id="modal2" class="modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content">
            <form>
                <div class="modal-body" align="center">
                    <table class="table table-sm" id="myTable">
                        <thead>
                        <tr>
                            <th scope="col">AUTO</th>
                            <th scope="col">LAVAGGI</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $macchine = $db->query("
                                    SELECT ID
                                    FROM mezzi
                                    WHERE stato!='2'
                                    ");

                        while ($rowmacchina = $macchine->fetch_array()){

                            $arraymacchine[]=$rowmacchina['ID'];

                            foreach ($arraymacchine as $key => $value){
                                $meseattuale = date("n");
                                $annoattuale = date("Y");

                                $select = $db->query("
                                            SELECT COUNT(title)
                                            AS contatore
                                            FROM
                                            lavaggio_mezzi
                                            WHERE title='$value'
                                            AND stato='2'
                                            AND MONTH(start_event)='$meseattuale'
                                            AND YEAR(start_event)='$annoattuale'
                                            order by start_event, title
                                            ");

                                $numerolavaggi = $select->fetch_array();
                            }
                            if (($numerolavaggi['contatore'])=='0'){
                                echo "<tr class='table-danger'>
                                        <td>".$rowmacchina['ID']."</td>
                                        <td>".$numerolavaggi['contatore']."</td>
                                      </tr>";
                            }else{
                                echo "<tr class='table-success'>
                                        <td>".$rowmacchina['ID']."</td>
                                        <td>".$numerolavaggi['contatore']."</td>
                                      </tr>";
                            }
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </form>
        </div>
    </div>
</div>

<!--esporta AUTO-->
<div id="modalexportauto" class="modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content">
            <form action="exportAUTO.php" method="post">
                <div class="modal-body" align="center">
                    <h6 class="modal-title">Registro lavaggi</h6>
                    <input type="hidden" id="user_id" value="<?=$_SESSION['ID']?>">
                    <select id="selectauto" name="selectauto" class="form-control form-control-sm" required>
                        <option value="ALL">Tutti</option>
                        <?
                        $select = $db->query("SELECT ID FROM mezzi WHERE stato!='2' ORDER BY ID, tipo");
                        while($ciclo = $select->fetch_array()){
                            echo "<option value=\"".$ciclo['ID']."\">".$ciclo['ID']."</option>";
                        }
                        ?>
                    </select> <!-- IDMEZZO -->
                    <br>
                    <div class="input-group">
                        <input type="text" class="form-control form-control-sm" id="datastart" name="datastart" placeholder="Dal">
                        <input type="text" class="form-control form-control-sm" id="dataend" name="dataend" placeholder="Al">
                    </div>
                    <br>
                    <button type="submit" class="btn btn-success btn-sm btn-block" id="exportButton" name="exportButton"><i class="far fa-file-excel"></i></button>
                </div>
            </form>
            <form action="exportSAMSIC.php" method="post">
                <hr>
                <div class="modal-body" align="center">
                    <h6 class="modal-title">SAMSIC</h6>
                    <input type="hidden" id="user_id" value="<?=$_SESSION['ID']?>">
                    <select id="selectmese" name="selectmese" class="form-control form-control-sm" required>
                        <option value="1">Gennaio</option>
                        <option value="2">Febbraio</option>
                        <option value="3">Marzo</option>
                        <option value="4">Aprile</option>
                        <option value="5">Maggio</option>
                        <option value="6">Giugno</option>
                        <option value="7">Luglio</option>
                        <option value="8">Agosto</option>
                        <option value="9">Settembre</option>
                        <option value="10">Ottobre</option>
                        <option value="11">Novembre</option>
                        <option value="12">Dicembre</option>
                    </select>
                    <br>
                    <select id="selectanno" name="selectanno" class="form-control form-control-sm" required>
                        <option value="2021">2021</option>
                    </select>
                    <br>
                    <button type="submit" class="btn btn-success btn-sm btn-block" id="exportSAMSIC" name="exportSAMSIC"><i class="far fa-file-excel"></i></button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include('../config/include/footer.php'); ?>
</html>