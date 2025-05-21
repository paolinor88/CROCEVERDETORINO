<?php
session_start();
include "../config/config.php";
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestione eventi CV-TO</title>

    <?php require "../config/include/header.html"; ?>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../config/include/custom.css?v=<?= time() ?>">

    <script>
        window.addEventListener('load', function () {
            var forms = document.getElementsByClassName('needs-validation');
            Array.prototype.forEach.call(forms, function (form) {
                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        }, false);
    </script>

    <script>
        $(document).ready(function () {
            // Imposta orario attuale nel campo "inizio"
            const now = new Date();
            const hours = now.getHours().toString().padStart(2, '0');
            const minutes = now.getMinutes().toString().padStart(2, '0');
            $('#inizio').val(`${hours}:${minutes}`);

            // Forza il maiuscolo per tutti i text input
            $('input[type="text"]').on('keyup', function () {
                $(this).val($(this).val().toUpperCase());
            });

            // Limite caratteri nel campo note
            $('#note').on('input', function () {
                const limite = 250;
                let valore = $(this).val();
                let quanti = valore.length;
                if (quanti > limite) {
                    valore = valore.substring(0, limite);
                    $(this).val(valore);
                    quanti = limite;
                }
                $('#conteggio').html(quanti + ' / ' + limite);
            });

            // Azione al click sul pulsante Salva
            $('#add').on('click', function () {
                const IDEvento = $("#IDEvento").val();
                const cognome = $("#cognome").val().trim();
                const patologia = $("#patologia").val();

                if (!IDEvento) {
                    Swal.fire({text: "Seleziona un evento prima di procedere.", icon: "warning"});
                    return;
                }
                if (!cognome) {
                    Swal.fire({text: "Il campo COGNOME è obbligatorio.", icon: "warning"});
                    return;
                }
                if (!patologia) {
                    Swal.fire({text: "Il campo PATOLOGIA è obbligatorio.", icon: "warning"});
                    return;
                }

                const data = {
                    IDEvento,
                    cognome,
                    nome: $("#nome").val(),
                    nascita: $("#nascita").val(),
                    indirizzo: $("#indirizzo").val(),
                    telefono: $("#telefono").val(),
                    squadra: $("#squadra").val(),
                    inizio: $("#inizio").val(),
                    fine: $("#fine").val(),
                    patologia,
                    gravita: $("#gravita").val(),
                    esito: $("#esito").val(),
                    stato: $("#stato").val(),
                    note: $("#note").val(),
                    posizione: $("#posizione").val() + ' ' + $("#posizionealtro").val()
                };

                Swal.fire({
                    text: "Sei sicuro di voler aggiungere questo intervento?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Conferma",
                    cancelButtonText: "Annulla"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.post("../interventi/script.php", data, function (response) {
                            if (response.success) {
                                const id_formattato = response.id_intervento.toString().padStart(3, '0');
                                Swal.fire({
                                    title: "Intervento registrato",
                                    html: "IDENTIFICATIVO: <strong>" + id_formattato + "</strong><br>Annotalo sul braccialetto del paziente.",
                                    icon: "success",
                                    confirmButtonText: "OK"
                                }).then(() => {
                                    location.href = 'new.php';
                                });

                            } else {
                                Swal.fire({
                                    title: "Errore",
                                    text: response.error || "Errore imprevisto durante l'inserimento.",
                                    icon: "error"
                                });
                            }
                        });
                    }
                });
            });
        });

        // Mostra campo postazione alternativa
        function yesnoCheck(that) {
            const altro = document.getElementById("ifYes");
            if (that.value === "") {
                Swal.fire("Inserisci la postazione nella casella 'ALTRA POSTAZIONE'");
                altro.style.display = "block";
            } else {
                altro.style.display = "none";
            }
        }
    </script>

</head>
<body>

<?php include "../config/include/navbar.php"; ?>
<br>

<div class="container-fluid px-2 mb-4">
    <div class="card card-cv p-3">
        <form class="needs-validation row g-3" novalidate>

            <div class="row g-3 justify-content-center text-center">
                <div class="col-md-2">
                    <label for="IDEvento" class="form-label fw-bold">Evento *</label>
                    <select class="form-select form-select-sm" id="IDEvento" name="IDEvento" required>
                        <?php include "select_eventi.html"; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="inizio" class="form-label">Inizio</label>
                    <input type="time" class="form-control form-control-sm" id="inizio" name="inizio" required>
                </div>
                <div class="col-md-2">
                    <label for="stato" class="form-label">Stato</label>
                    <select class="form-select form-select-sm" id="stato" name="stato">
                        <option value="1" selected>IN CORSO</option>
                        <option value="2">OSPEDALIZZATO</option>
                        <option value="3">RIFIUTA</option>
                        <option value="4">DIMESSO</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="fine" class="form-label">Fine</label>
                    <input type="time" class="form-control form-control-sm" id="fine" name="fine">
                </div>
            </div>

            <hr class="mt-4 mb-3">

            <div class="row g-3">
                <div class="col-md-2">
                    <label for="cognome" class="form-label">Cognome *</label>
                    <input type="text" class="form-control form-control-sm" id="cognome" name="cognome" required>
                </div>
                <div class="col-md-2">
                    <label for="nome" class="form-label">Nome</label>
                    <input type="text" class="form-control form-control-sm" id="nome" name="nome">
                </div>
                <div class="col-md-2">
                    <label for="nascita" class="form-label">Data di nascita</label>
                    <input type="date" class="form-control form-control-sm" id="nascita" name="nascita">
                </div>
                <div class="col-md-3">
                    <label for="indirizzo" class="form-label">Indirizzo</label>
                    <input type="text" class="form-control form-control-sm" id="indirizzo" name="indirizzo">
                </div>
                <div class="col-md-3">
                    <label for="telefono" class="form-label">Telefono</label>
                    <input type="text" class="form-control form-control-sm" id="telefono" name="telefono">
                </div>
                <div class="col-md-2">
                    <label for="squadra" class="form-label">Squadra intervenuta</label>
                    <input type="text" class="form-control form-control-sm" id="squadra" name="squadra">
                </div>
                <div class="col-md-2">
                    <label for="posizione" class="form-label">Postazione *</label>
                    <select class="form-select form-select-sm" id="posizione" name="posizione" required onchange="yesnoCheck(this);">
                        <option value="">Altro (specificare)</option>
                        <optgroup label="INALPI ARENA">
                            <option value="INFERMERIA">INFERMERIA</option>
                            <option value="MSA">MSA</option>
                            <option value="MSB">MSB</option>
                        </optgroup>
                        <optgroup label="PAPA" disabled>
                            <option value="PAPA 1">PAPA 1</option>
                            <option value="PAPA 2">PAPA 2</option>
                            <option value="PAPA 3">PAPA 3</option>
                        </optgroup>
                        <optgroup label="ALFA" disabled>
                            <option value="ALFA 1">ALFA 1</option>
                            <option value="ALFA 2">ALFA 2</option>
                            <option value="ALFA 3">ALFA 3</option>
                            <option value="ALFA 4">ALFA 4</option>
                        </optgroup>
                    </select>
                </div>
                <div class="col-md-2" id="ifYes" style="display: none;">
                    <label for="posizionealtro" class="form-label text-danger">ALTRA POSTAZIONE</label>
                    <input type="text" class="form-control form-control-sm" id="posizionealtro" name="posizionealtro">
                </div>
                <div class="col-md-2">
                    <label for="patologia" class="form-label">Patologia *</label>
                    <select class="form-select form-select-sm" id="patologia" name="patologia" required>
                        <option value=""></option>
                        <option value="1">MEDICO</option>
                        <option value="2">TRAUMA</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="gravita" class="form-label">Gravità *</label>
                    <select class="form-select form-select-sm" id="gravita" name="gravita" required>
                        <option value=""></option>
                        <option value="0">BIANCO</option>
                        <option value="1">VERDE</option>
                        <option value="2">GIALLO</option>
                        <option value="3">ROSSO</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="esito" class="form-label">Esito</label>
                    <input type="text" class="form-control form-control-sm" id="esito" name="esito">
                </div>
                <div class="col-md-12">
                    <label for="note" class="form-label">Note</label>
                    <textarea class="form-control form-control-sm" id="note" name="note" rows="4" maxlength="250"></textarea>
                    <span id="conteggio" class="text-muted small"></span>
                </div>
            </div>

            <hr class="my-4">
            <div class="text-center">
                <button type="button" class="btn btn-outline-cv me-2" id="add" name="add">
                    <i class="fas fa-check"></i> Salva
                </button>

                <button type="button" class="btn btn-outline-cv-grey" onclick="location.href='index.php'">
                    <i class="fas fa-undo"></i> Indietro
                </button>



            </div>
        </form>
    </div>
</div>

<?php include('../config/include/footer.php'); ?>
</body>
</html>
