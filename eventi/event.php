<?php
/**
 *
 * @author     Paolo Randone
 * @author     <mail@paolorandone.it>
 * @version    1.4
 * @note       Powered for Croce Verde Torino. All rights reserved
 *
 */
session_start();
//parametri DB
include "../config/config.php";
//controllo LOGIN
if (($_SESSION["livello"])<4){
    header("Location: ../error.php");
}
//apri dettaglio
if (isset($_GET["id_evento"])){
    $id = $_GET["id_evento"];
    $readonly = "readonly";
    $modifica = $db->query("SELECT * FROM utenti WHERE ID='$id'")->fetch_array();
}
$dictionaryStato = array (
    1 => "Programmato",
    2 => "Pubblicato",
    3 => "Chiuso",
    4 => "Archiviato",
);
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <meta name="author" content="Paolo Randone">
    <title>Eventi</title>

    <? require "../config/include/header.html";?>

    <!-- Inserisci utente -->
    <script>
        $(document).ready(function() {
            $('#submitButton').on('click', function(){
                $('#modal1').modal('hide');
                var ID = $("#ID").val();
                var cognome = $("#cognome").val();
                var nome = $("#nome").val();
                var email = $("#email").val();
                var telefono = $("#telefono").val();
                var cf = ($("#cf").val()).toUpperCase();
                var password = $("#password").val();
                var livello = $("#livello option:selected").val();
                var sezione = $("#sezione option:selected").val();
                var squadra = $("#squadra option:selected").val();
                //alert(password);
                swal({
                    text: "Sei sicuro di voler inserire questo utente?",
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
                                url:"loadevents.php",
                                type:"POST",
                                data:{ID:ID, cognome:cognome, nome:nome, email:email, cf:cf, password:password, telefono:telefono, livello:livello, sezione:sezione, squadra:squadra},
                                success:function(){
                                    swal({text:"Utente inserito con successo", icon: "success", timer: 1000, button:false, closeOnClickOutside: false});
                                    setTimeout(function () {
                                            location.href='index.php';
                                        },1001
                                    )
                                },error:function () {
                                    alert('ERRORE, inserimento non riuscito')
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

    <!--datatable-->
    <script>
        $(document).ready(function() {
            var dataTables = $('#myTable').DataTable({
                "language": {url: '../config/js/package.json'},
                "order": [[5, "asc"]],
                "pagingType": "simple",
                "columnDefs": [
                    {
                        "targets": [ 0 ],
                        "visible": true,
                        "searchable": false,
                        "orderable": false,
                    },
                    {
                        "targets": [ 5 ],//id hidden
                        "visible": false,
                        "searchable": true,

                    }]
            });
            //FILTRI TABELLA
            $('#programmato').on('click', function () {
                dataTables.columns(4).search("").draw();
                dataTables.columns(4).search("Programmato").draw();
                $( "#programmato" ).removeClass( "btn-outline-secondary" ).addClass( "btn-secondary" );
                $( "#aperto" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#chiuso" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#archiviato" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#all" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
            });
            $('#aperto').on('click', function () {
                dataTables.columns(4).search("").draw();
                dataTables.columns(4).search("Aperto").draw();
                $( "#aperto" ).removeClass( "btn-outline-secondary" ).addClass( "btn-secondary" );
                $( "#programmato" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#chiuso" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#archiviato" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#all" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
            });
            $('#chiuso').on('click', function () {
                dataTables.columns(4).search("").draw();
                dataTables.columns(4).search("Chiuso").draw();
                $( "#chiuso" ).removeClass( "btn-outline-secondary" ).addClass( "btn-secondary" );
                $( "#programmato" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#aperto" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#archiviato" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#all" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
            });
            $('#archiviato').on('click', function () {
                dataTables.columns(4).search("").draw();
                dataTables.columns(4).search("Archiviato").draw();
                $( "#archiviato" ).removeClass( "btn-outline-secondary" ).addClass( "btn-secondary" );
                $( "#programmato" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#aperto" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#chiuso" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#all" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
            });
            $('#all').on('click', function () {
                dataTables.columns(4).search("").draw();
                $( "#programmato" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#aperto" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#chiuso" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#archiviato" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
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
            <li class="breadcrumb-item"><a href="index.php" style="color: #078f40">Eventi e calendario</a></li>
            <li class="breadcrumb-item active" aria-current="page">Eventi</li>
        </ol>
    </nav>
</div>

<br>

<!-- TABELLA GENERALE -->
<body>

<div class="container-fluid">
    <div class="jumbotron">
        <center>
            <div class="btn-group" role="group" aria-label="">
                <button id="programmato" type="button" class="btn btn-outline-secondary btn-sm">Programmato</button>
                <button id="aperto" type="button" class="btn btn-outline-secondary btn-sm">Aperto</button>
                <button id="chiuso" type="button" class="btn btn-outline-secondary btn-sm">Chiuso</button>
                <button id="archiviato" type="button" class="btn btn-outline-secondary btn-sm">Archiviato</button>
                <button id="all" type="button" class="btn btn-secondary btn-sm">ALL</button> 
            </div>
        </center>
        <div class="table-responsive-sm">
            <table class="table table-hover" id="myTable">
                <thead>
                <tr>
                    <th scope="col"><button disabled class="btn btn-sm btn-outline-info" data-toggle="modal" data-target="#modal1"><i class="fas fa-plus"></i></button></th>
                    <th scope="col">Nome</th>
                    <th scope="col">Luogo</th>
                    <th scope="col">Data</th>
                    <th scope="col">Stato</th>
                    <th scope="col">ID</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $select = $db->query("SELECT id, title, start_event, end_event, user_id, stato, luogo, note, msa, msb, pma, squadre FROM events order by id");
                while($ciclo = $select->fetch_array()){
                    echo "
					<tr>
						<td>"."<a href=\"https://".$_SERVER['HTTP_HOST']."/gestionale/eventi/schedaevento.php?id=".$ciclo['id']."\" class=\"btn btn-sm btn-outline-success\"><i class=\"far fa-folder-open\"></i></a>"."</td>
						<td>".$ciclo['title']."</td>
						<td>".$ciclo['luogo']."</td>
						<td>".$ciclo['start_event']."</td>
						<td>".$ciclo=$dictionaryStato[$ciclo['stato']]."</td>
						<td>".$ciclo['id']."</td>

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
                    <h4 class="modal-title" id="modal1Title">Inserisci utente</h4>
                </div>
                <div class="modal-body">
                    <input hidden id="password" value="<?=$pwd?>">

                    <div class="form-group">
                        <label for="id">Matricola</label>
                        <input id="ID" class="form-control form-control-sm" placeholder="Matricola (es. V4512)" required pattern="[D|V0-9]{5}">
                    </div> <!-- matricola -->
                    <div class="form-group">
                        <label for="cognome">Cognome</label>
                        <input id="cognome" class="form-control form-control-sm" required>
                    </div> <!-- cognome -->
                    <div class="form-group">
                        <label for="nome">Nome</label>
                        <input id="nome" class="form-control form-control-sm" required>
                    </div> <!-- nome -->
                    <div class="form-group">
                        <label for="cf">Codice Fiscale</label>
                        <input id="cf" class="form-control form-control-sm" required>
                    </div> <!-- nome -->
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control form-control-sm" id="email" aria-describedby="emailHelp" required>
                    </div> <!-- email -->
                    <div class="form-group">
                        <label for="telefono">Telefono</label>
                        <input class="form-control form-control-sm" id="telefono" required>
                    </div> <!-- telefono -->
                    <div class="form-group">
                        <label for="sezione">Sezione</label>
                        <select class="form-control form-control-sm" id="sezione" required>
                            <?
                            for($a=1;$a<8;$a++){
                                ($a==$modifica['sezione'])? $sel="selected" : $sel="";
                                echo "<option $sel value='$a'>".$dictionarySezione[$a]."</option>";
                            }
                            ?>
                        </select>
                    </div> <!-- sezione -->
                    <div class="form-group">
                        <label for="squadra">Squadra</label>
                        <select class="form-control form-control-sm" id="squadra" required>
                            <?
                            for($a=1;$a<23;$a++){
                                ($a==$modifica['squadra'])? $sel="selected" : $sel="";
                                echo "<option $sel value='$a'>".$dictionarySquadra[$a]."</option>";
                            }
                            ?>
                        </select>
                    </div> <!-- squadra -->
                    <div class="form-group">
                        <label for="livello">Livello</label>
                        <select class="form-control form-control-sm" id="livello" required>
                            <?
                            if (($_SESSION["livello"])!=6){
                                for($a=1;$a<6;$a++){
                                    ($a==$modifica['livello'])? $sel="selected" : $sel="";
                                    echo "<option $sel value='$a'>".$dictionaryLivello[$a]."</option>";
                                }
                            }else{
                                for($a=1;$a<7;$a++){
                                    ($a==$modifica['livello'])? $sel="selected" : $sel="";
                                    echo "<option $sel value='$a'>".$dictionaryLivello[$a]."</option>";
                                }
                            }

                            ?>
                        </select>
                    </div> <!-- livello -->
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
