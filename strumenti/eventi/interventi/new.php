<?php
/**
 *
 * @author     Paolo Randone
 * @author     <paolo.randone@croceverde.org>
 * @version    8.2
 * @note       Powered for Croce Verde Torino. All rights reserved
 *
 */
session_start();
include "../config/config.php";

?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="Paolo Randone">

    <title>Gestione eventi CV-TO</title>

    <?php require "../config/include/header.html"; ?>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../config/include/custom.css?v=<?= time() ?>">

    <script>
        (function() {
            'use strict';
            window.addEventListener('load', function() {
                var forms = document.getElementsByClassName('needs-validation');
                var validation = Array.prototype.filter.call(forms, function(form) {
                    form.addEventListener('add', function(event) {
                        if (form.checkValidity() === false) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
            }, false);
        })();
    </script>

    <script>
        $(document).ready(function() {
            $('input[type="text"]').on('keyup', function() {
                $(this).val($(this).val().toUpperCase());
            });
            $('#add').on('click', function(){
                var IDEvento = $("#IDEvento").val();
                var cognome = $("#cognome").val().trim();
                var patologia = $("#patologia option:selected").val();

                if(IDEvento === ""){
                    Swal.fire({
                        text: "Seleziona un evento prima di procedere.",
                        icon: "warning",
                        confirmButtonText: "OK"
                    });
                    return;
                }
                if(cognome === ""){
                    Swal.fire({
                        text: "Il campo COGNOME è obbligatorio.",
                        icon: "warning",
                        confirmButtonText: "OK"
                    });
                    return;
                }
                if(patologia === ""){
                    Swal.fire({
                        text: "Il campo PATOLOGIA è obbligatorio.",
                        icon: "warning",
                        confirmButtonText: "OK"
                    });
                    return;
                }
                var nome = $("#nome").val();
                var nascita = $("#nascita").val();
                var indirizzo = $("#indirizzo").val();
                var telefono = $("#telefono").val();
                var squadra = $("#squadra").val();
                var inizio = $("#inizio").val();
                var fine = $("#fine").val();
                var posizione = $("#posizione option:selected").val()+' '+$("#posizionealtro").val();;
                var gravita = $("#gravita option:selected").val();
                var esito = $("#esito").val();
                var stato = $("#stato option:selected").val();
                var note = $("#note").val();

                Swal.fire({
                    text: "Sei sicuro di voler aggiungere questo intervento?",
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
                                url:"../interventi/script.php",
                                type:"POST",
                                data:{cognome:cognome, nome:nome, nascita:nascita, indirizzo:indirizzo, telefono:telefono, squadra:squadra, posizione:posizione, inizio:inizio, patologia:patologia, gravita:gravita, esito:esito, stato:stato, note:note, fine:fine, IDEvento:IDEvento},
                                success:function(){
                                    Swal.fire({text:"Intervento inserito con successo", icon: "success", timer: 1000, button:false, closeOnClickOutside: false});
                                    setTimeout(function () {
                                            location.href='new.php';
                                        },1001
                                    )
                                }
                            });
                        } else {
                            Swal.fire({text:"Operazione annullata come richiesto!", timer: 1000, button:false, closeOnClickOutside: false});
                        }
                    })
            })
        });
    </script>
    <script>
        function yesnoCheck(that) {
            if (that.value == "") {
                alert("Inserisci la postazione nella casella 'ALTRA POSTAZIONE'");
                document.getElementById("ifYes").style.display = "block";
            } else {
                document.getElementById("ifYes").style.display = "none";
            }
        }
    </script>
</head>

<body>

<?php include "../config/include/navbar.php"; ?>

<br>
<div class="container-fluid px-2 mb-4">
    <div class="card card-cv">
        <form name="check" class="needs-validation row g-3" novalidate>
            <div class="row justify-content-center" style="text-align: center">
                <div class="col-md-2">
                    <label for="IDEvento"><B>EVENTO</B></label>
                    <select class="form-select form-select-sm" id="IDEvento" name="IDEvento">
                        <?php include "select_eventi.html"; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="inizio">Inizio</label>
                    <input type="time" class="form-control form-control-sm" id="inizio" name="inizio" autofocus required>
                </div>
                <div class="col-md-2">
                    <label for="stato">Stato</label>
                    <select class="form-select form-select-sm" id="stato" name="stato">
                        <option value="1" selected>IN CORSO</option>
                        <option value="2">OSPEDALIZZATO</option>
                        <option value="3">RIFIUTA</option>
                        <option value="4">DIMESSO</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="fine">Fine</label>
                    <input type="time" class="form-control form-control-sm" id="fine" name="fine">
                </div>
            </div>
            <hr>
            <div class="sfondo">
                <div class="row justify-content-center">
                    <div class="col-md-2">
                        <label for="cognome">Cognome *</label>
                        <input type="text" class="form-control form-control-sm" id="cognome" name="cognome" autofocus required>
                    </div>
                    <div class="col-md-2">
                        <label for="nome">Nome</label>
                        <input type="text" class="form-control form-control-sm" id="nome" name="nome" >
                    </div>
                    <div class="col-md-2">
                        <label for="nascita">Data di nascita</label>
                        <input type="date" class="form-control form-control-sm" id="nascita" name="nascita" placeholder="gg-mm-aaaa">
                    </div>
                    <div class="col-md-3">
                        <label for="indirizzo">Indirizzo</label>
                        <input type="text" class="form-control form-control-sm" id="indirizzo" name="indirizzo" >
                    </div>
                    <div class="col-md-3">
                        <label for="telefono">Telefono</label>
                        <input type="text" class="form-control form-control-sm" id="telefono" name="telefono" >
                    </div>
                    <div class="col-md-2">
                        <label for="squadra">Squadra intervenuta</label>
                        <input type="text" class="form-control form-control-sm" id="squadra" name="squadra">
                    </div>
                    <div class="col-md-2">
                        <label for="posizione">Postazione *</label>
                        <select class="form-select form-select-sm" id="posizione" name="posizione" required onchange="yesnoCheck(this);">
                            <option></option>
                            <optgroup label="INALPI ARENA">
                                <option value="INFERMERIA">INFERMERIA</option>
                                <option value="MSA">MSA</option>
                                <option value="MSB">MSB</option>
                            </optgroup>

                            <?php

                            $posizioniALFA = array("ALFA 1", "ALFA 2", "ALFA 3", "ALFA 4");
                            $posizioniPAPA = array("PAPA 1","PAPA 2","PAPA 3");

                            echo '<optgroup label="PAPA" disabled>';
                            foreach ($posizioniPAPA as $posizione) {
                                echo '<option value="' . $posizione . '">' . $posizione . '</option>';
                            }

                            echo '<optgroup label="ALFA" disabled>';
                            foreach ($posizioniALFA as $posizione) {
                                echo '<option value="' . $posizione . '">' . $posizione . '</option>';
                            }

                            echo '</optgroup>';
                            ?>
                            <option value="">Altro (specificare)</option>


                        </select>

                    </div>
                    <div class="col-md-2">
                        <label for="patologia">Patologia *</label>
                        <select class="form-select form-select-sm" id="patologia" name="patologia" required>
                            <option VALUE=""></option>
                            <option value="1">MEDICO</option>
                            <option value="2">TRAUMA</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="categoria">Gravità *</label>
                        <select class="form-select form-select-sm" id="gravita" name="gravita" required>
                            <option></option>
                            <option value="0">BIANCO</option>
                            <option value="1">VERDE</option>
                            <option value="2">GIALLO</option>
                            <option value="3">ROSSO</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="esito">Esito</label>
                        <input type="text" class="form-control form-control-sm" id="esito" name="esito" >

                    </div>
                    <div class="col-md-2" id="ifYes" style="display: none">
                        <label for="posizione" style="color: red">ALTRA POSTAZIONE</label>
                        <input type="text" class="form-control form-control-sm" id="posizionealtro" name="posizionealtro">
                    </div>
                    <div class="col-md-12">
                        <label for="note_evento">Note</label>
                        <textarea rows="4" type="text" maxlength="250" class="form-control form-control-sm" id="note" name="note"></textarea>
                        <span id="conteggio" style="font-size: small; color: grey"></span>
                        <script type="text/javascript">
                            $('textarea#note').keyup(function() {
                                var limite = 250;
                                var quanti = $(this).val().length;
                                $('span#conteggio').html(quanti + ' / ' + limite);
                                if(quanti >= limite) {
                                    $('span#conteggio').html('<strong>Non puoi inserire più di ' + limite + ' caratteri!</strong>');
                                    var $contenuto = $(this).val().substr(0,limite);
                                    $('textarea#note').val($contenuto);
                                }
                            });
                        </script>
                    </div>
                </div>
            </div>
            <hr>
            <div style="text-align: center;">

                <button type="button" class="btn btn-sm btn-success" id="add" name="add"><i class="fas fa-check"></i> Salva</button>
                <a href="index.php" class="btn btn-sm btn-outline-secondary" id="indietro"><i class="fas fa-undo"></i> Indietro</a>

            </div>
        </form>
    </div>
</div>
</body>
<br>
<?php include('../config/include/footer.php'); ?>

</html>