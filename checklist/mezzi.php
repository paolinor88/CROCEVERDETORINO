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
//connessione DB
include "../config/config.php";
//controllo LOGIN
//accesso consentito a logistica, segreteria e ADMIN
if (($_SESSION["livello"])<4){
    header("Location: ../error.php");
}
//nicename tipo
$dictionary = array (
    1 => "MSB",
    2 => "MSA",
    3 => "118",
    4 => "Altro",
);
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <meta name="author" content="Paolo Randone">
    <title>Gestione mezzi</title>

    <? require "../config/include/header.html";?>

    <!--insert-->
    <script>
        $(document).ready(function() {
            $('#submitButton').on('click', function(){
                $('#modal1').modal('hide');
                var ID = $("#ID").val();
                var targa = $("#targa").val();
                var tipo = $("#tipo option:selected").val();
                var note = $("#note").val();
                swal({
                    text: "Sei sicuro di voler inserire questo mezzo?",
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
                                url:"../checklist/script.php",
                                type:"POST",
                                data:{ID:ID, targa:targa, tipo:tipo, note:note},
                                success:function(){
                                    swal({text:"Mezzo inserito con successo", icon: "success", timer: 1000, button:false, closeOnClickOutside: false});
                                    setTimeout(function () {
                                        location.href='/gestionale/checklist/mezzi.php';
                                    },1001
                                    )
                                }
                            });
                        } else {
                            swal({text:"Operazione annullata come richiesto!", timer: 1000, button:false, closeOnClickOutside: false});
                        }
                    })
            })
        });
    </script>

    <script>
        $(document).ready(function(){
            $('[data-toggle="popover"]').popover();
        });
    </script>
    <!-- datatable -->
    <script>
        $(document).ready(function() {
            var dataTables = $('#myTable').DataTable({
                stateSave: true,
                "paging": false,
                "language": {url: '../config/js/package.json'},
                "order": [[1, "asc"]],
                "pagingType": "simple",
                "pageLength": 50,
                "columnDefs": [
                    {
                        "targets": [ 0 ],
                        "visible": true,
                        "searchable": false,
                        "orderable": false,
                    }],
            });
            //FILTRI TABELLA
            $('#msb').on('click', function () {
                dataTables.columns(3).search("").draw();
                dataTables.columns(3).search("MSB").draw();
                $( "#msb" ).removeClass( "btn-outline-secondary" ).addClass( "btn-secondary" );
                $( "#altro" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#msa" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#emergenza" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#all" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
            });
            $('#msa').on('click', function () {
                dataTables.columns(3).search("").draw();
                dataTables.columns(3).search("MSA").draw();
                $( "#msa" ).removeClass( "btn-outline-secondary" ).addClass( "btn-secondary" );
                $( "#altro" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#msb" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#emergenza" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#all" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
            });
            $('#emergenza').on('click', function () {
                dataTables.columns(3).search("").draw();
                dataTables.columns(3).search("118").draw();
                $( "#emergenza" ).removeClass( "btn-outline-secondary" ).addClass( "btn-secondary" );
                $( "#altro" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#msb" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#msa" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#all" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
            });
            $('#altro').on('click', function () {
                dataTables.columns(3).search("").draw();
                dataTables.columns(3).search("Altro").draw();
                $( "#altro" ).removeClass( "btn-outline-secondary" ).addClass( "btn-secondary" );
                $( "#emergenza" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#msb" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#msa" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#all" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
            });
            $('#all').on('click', function () {
                dataTables.columns(3).search("").draw();
                $( "#msb" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#msa" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#emergenza" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#altro" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
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
            <li class="breadcrumb-item"><a href="index.php" style="color: #078f40">Autoparco</a></li>
            <li class="breadcrumb-item active" aria-current="page">Lista mezzi</li>
        </ol>
    </nav>
</div>
<!-- content -->
<body>
<div class="container-fluid">
    <div class="jumbotron">
        <div style="text-align: center;">
            <div class="btn-group" role="group" aria-label="">
                <button id="msb" type="button" class="btn btn-outline-secondary btn-sm">MSB</button>
                <button id="msa" type="button" class="btn btn-outline-secondary btn-sm">MSA</button>
                <button id="emergenza" type="button" class="btn btn-outline-secondary btn-sm">Flotta 118</button>
                <button id="altro" type="button" class="btn btn-outline-secondary btn-sm">Altro</button>
                <button id="all" type="button" class="btn btn-secondary btn-sm">ALL</button>
            </div>
        </div>
        <div class="table-responsive-sm">
            <table class="table table-hover table-sm" id="myTable">
                <thead>
                <tr>
                    <th scope="col"><button class="btn btn-sm btn-outline-info" data-toggle="modal" data-target="#modal1"><i class="fas fa-plus"></i></button></th>
                    <th scope="col">Numero</th>
                    <th scope="col">Targa</th>
                    <th scope="col">Tipo</th>
                    <th scope="col">Note</th>
                </tr>
                </thead>
                <tbody>
                <?php

                $select = $db->query("SELECT ID, tipo, targa, note FROM mezzi WHERE stato='1' order by ID");
                while($ciclo = $select->fetch_array()){

                    echo "
					<tr>
						<td>"."<a href=\"https://".$_SERVER['HTTP_HOST']."/gestionale/checklist/schedamezzo.php?ID=".$ciclo['ID']."\" class=\"btn btn-sm btn-outline-dark\" \"><i class=\"fas fa-cogs\"></i></a>"."</td>
						<td>".$ciclo['ID']."</td>
						<td>".$ciclo['targa']."</td>
						<td>".$ciclo=$dictionary[$ciclo['tipo']]."</td>
						<td>".$ciclo['note']."</td>
					</tr>";

                }

                ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- MODAL INSERISCI -->
<div class="modal" id="modal1" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content">
            <form>
                <div class="modal-header">
                    <h6 class="modal-title" id="modal1Title">Aggiungi mezzo</h6>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="ID">Numero</label>
                        <input id="ID" class="form-control form-control-sm" required>
                    </div> <!-- numero -->
                    <div class="form-group">
                        <label for="targa">Targa</label>
                        <input id="targa" class="form-control form-control-sm" required>
                    </div> <!-- targa -->
                    <div class="form-group">
                        <label for="tipo">Tipo</label>
                        <select class="form-control form-control-sm" id="tipo" required>
                            <option value="1">MSB</option>
                            <option value="2">MSA</option>
                            <option value="3">118</option>
                            <option value="4">Altro</option>
                        </select>
                    </div> <!-- tipo -->
                    <div class="form-group">
                        <label for="note">Note</label>
                        <textarea class="form-control" id="note" rows="5"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-dismiss="modal">Chiudi</button>
                    <button type="button" class="btn btn-outline-success btn-sm" id="submitButton">Salva</button>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
<!-- FOOTER -->
<?php include('../config/include/footer.php'); ?>

</html>