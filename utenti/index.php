<?php
/**
 *
 * @author     Paolo Randone
 * @author     <mail@paolorandone.it>
 * @version    3.0
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
);
//apri dettaglio
if (isset($_GET["ID"])){
    $id = $_GET["ID"];
    $readonly = "readonly";
    $modifica = $db->query("SELECT * FROM utenti WHERE ID='$id'")->fetch_array();
}
//generatore password
function generatePassword ( $length = 8 ): string
{
    $password = '';
    $possibleChars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $i = 0;
    while ($i < $length) {
        $char = substr($possibleChars, mt_rand(0, strlen($possibleChars)-1), 1);
        if (!strstr($password, $char)) {
            $password .= $char;
            $i++;
        }
    }
    return $password;
}

$pwd = generatePassword(8);

?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <meta name="author" content="Paolo Randone">
    <title>Gestione utenze</title>

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
                                url:"script.php",
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
                "order": [[1, "asc"]],
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
                        "targets": [ 7 ],//stato hidden
                        "visible": false,
                        "searchable": true,

                    }]
            });
            //FILTRI TABELLA
            $('#attivi').on('click', function () {
                dataTables.columns(7).search("1").draw();
                $( "#attivi" ).removeClass( "btn-outline-secondary" ).addClass( "btn-secondary" );
                $( "#all" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
            });
            $('#dipendenti').on('click', function () {
                dataTables.columns(6).search("").draw();
                dataTables.columns(6).search("Dipendente").draw();
                $( "#dipendenti" ).removeClass( "btn-outline-secondary" ).addClass( "btn-secondary" );
                $( "#volontari" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#logistica" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#segreteria" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#admin" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#all" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
            });
            $('#volontari').on('click', function () {
                dataTables.columns(6).search("").draw();
                dataTables.columns(6).search("Volontario").draw();
                $( "#volontari" ).removeClass( "btn-outline-secondary" ).addClass( "btn-secondary" );
                $( "#dipendenti" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#logistica" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#segreteria" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#admin" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#all" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
            });
            $('#logistica').on('click', function () {
                dataTables.columns(6).search("").draw();
                dataTables.columns(6).search("Logistica").draw();
                $( "#logistica" ).removeClass( "btn-outline-secondary" ).addClass( "btn-secondary" );
                $( "#volontari" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#dipendenti" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#segreteria" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#admin" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#all" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
            });
            $('#segreteria').on('click', function () {
                dataTables.columns(6).search("").draw();
                dataTables.columns(6).search("Segreteria").draw();
                $( "#segreteria" ).removeClass( "btn-outline-secondary" ).addClass( "btn-secondary" );
                $( "#volontari" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#dipendenti" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#logistica" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#admin" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#all" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
            });
            $('#admin').on('click', function () {
                dataTables.columns(6).search("").draw();
                dataTables.columns(6).search("ADMIN").draw();
                $( "#admin" ).removeClass( "btn-outline-secondary" ).addClass( "btn-secondary" );
                $( "#volontari" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#dipendenti" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#logistica" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#segreteria" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#all" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
            });
            $('#all').on('click', function () {
                dataTables.columns(7).search("").draw();
                dataTables.columns(6).search("").draw();
                $( "#attivi" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#dipendenti" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#volontari" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#logistica" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#segreteria" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
                $( "#admin" ).removeClass( "btn-secondary" ).addClass( "btn-outline-secondary" );
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
            <li class="breadcrumb-item active" aria-current="page">Utenze</li>
        </ol>
    </nav>
</div>

<br>

<!-- TABELLA GENERALE -->
<body>

<div class="container-fluid">
    <div class="jumbotron">
        <div style="text-align: center;">
            <div class="btn-group" role="group" aria-label="">
                <button id="dipendenti" type="button" class="btn btn-outline-secondary btn-sm">Dipendenti</button>
                <button id="volontari" type="button" class="btn btn-outline-secondary btn-sm">Volontari</button>
                <button id="logistica" type="button" class="btn btn-outline-secondary btn-sm">Logistica</button>
                <button id="segreteria" type="button" class="btn btn-outline-secondary btn-sm">Segreteria</button>

            </div>
            <div class="btn-group" role="group" aria-label="">
                <button id="attivi" type="button" class="btn btn-outline-secondary btn-sm">Attivi</button>
                <button id="admin" type="button" class="btn btn-outline-secondary btn-sm">Admin</button>
                <button id="all" type="button" class="btn btn-secondary btn-sm">ALL</button>
            </div>
        </div>
        <div class="table-responsive-sm">
            <table class="table table-hover table-sm" id="myTable">
                <thead>
                <tr>
                    <th scope="col"><button class="btn btn-sm btn-outline-info" data-toggle="modal" data-target="#modal1"><i class="fas fa-user-plus"></i></button></th>
                    <th scope="col">Matricola</th>
                    <th scope="col">Cognome</th>
                    <th scope="col">Nome</th>
                    <th scope="col">Sezione</th>
                    <th scope="col">Squadra</th>
                    <th scope="col">Livello</th>
                    <th scope="col">Stato</th>
                </tr>
                </thead>
                <tbody>
                <?php

                $select = $db->query("SELECT ID, cognome, nome, sezione, squadra, livello, stato FROM utenti WHERE cognome !='ADMIN'order by ID");

                while($ciclo = $select->fetch_array()){
                    if (($ciclo['stato']==0)&&($_SESSION['ID']!='D9999')){
                        echo "
					<tr>
						<td>"."<a href=\"https://".$_SERVER['HTTP_HOST']."/gestionale/utenti/schedaoperatore.php?ID=".$ciclo['ID']."\" class=\"btn btn-sm btn-outline-dark disabled\"><i class=\"far fa-times-circle\"></i></a>"."</td>
						<td>".$ciclo['ID']."</td>
						<td>".$ciclo['cognome']."</td>
						<td>".$ciclo['nome']."</td>
						<td>".$ciclo=$dictionarySezione[$ciclo['sezione']]."</td>
						<td>".$ciclo=$dictionarySquadra[$ciclo['squadra']]."</td>
						<td>".$ciclo=$dictionaryLivello[$ciclo['livello']]."</td>
						<td>".$ciclo['stato']."</td>
					</tr>";
                    }else{
                        echo "
					<tr>
						<td>"."<a href=\"https://".$_SERVER['HTTP_HOST']."/gestionale/utenti/schedaoperatore.php?ID=".$ciclo['ID']."\" class=\"btn btn-sm btn-outline-success\"><i class=\"far fa-folder-open\"></i></a>"."</td>
						<td>".$ciclo['ID']."</td>
						<td>".$ciclo['cognome']."</td>
						<td>".$ciclo['nome']."</td>
						<td>".$ciclo=$dictionarySezione[$ciclo['sezione']]."</td>
						<td>".$ciclo=$dictionarySquadra[$ciclo['squadra']]."</td>
						<td>".$ciclo=$dictionaryLivello[$ciclo['livello']]."</td>
						<td>".$ciclo['stato']."</td>
					</tr>";
                    }
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
