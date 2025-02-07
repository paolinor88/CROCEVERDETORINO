<?php
header('Access-Control-Allow-Origin: *');
session_start();
include "../config/pdo.php";
include "../config/config.php";
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <meta name="author" content="Paolo Randone">
    <title>HOME</title>
    <base href="/gestionale/magazzino/">
    <?php require "../config/include/header.html"; ?>

    <script>
        $(document).ready(function(){
            $('[data-toggle="popover"]').popover();

            // Apertura della modale al click su INVENTARIO
            $('#openInventoryModal').click(function() {
                $('#inventoryModal').modal('show');
                setTimeout(function() {
                    $('#inventoryPassword').focus();
                }, 500);
            });

            // Gestione del submit della modale
            $('#passwordForm').submit(function(event) {
                event.preventDefault();
                const password = $('#inventoryPassword').val();
                if (password === "12345") { // Cambia con la tua logica di verifica
                    window.location.href = "movimenti.php";
                } else {
                    alert("Password errata. Riprova.");
                }
            });
        });
    </script>
    <script>
        $(document).ready(function () {
            $('#openSearchModal').click(function() {
                $('#searchModal').modal('show');
            });
            // Focus e selezione del testo quando la modale si apre
            $('#searchModal').on('shown.bs.modal', function () {
                const barcodeInput = $('#barcodeInput');
                barcodeInput.val('').trigger('focus').select();
                $('#bombolaInfo').hide();
                $('#movementInfo').hide();
                $('#searchError').hide();
            });

            // Pulsante di reset
            $('#resetButton').on('click', function () {
                $('#barcodeInput').val('').trigger('focus');
                $('#bombolaInfo').hide();
                $('#movementInfo').hide();
                $('#searchError').hide();
            });

            // Gestione ricerca automatica all'inserimento del barcode
            $('#barcodeInput').on('keypress', function (e) {
                if (e.which === 13) { // Tasto Invio premuto
                    $('#searchSubmitButton').trigger('click'); // Simula il click sul pulsante Cerca
                }
            });

            // Gestione del pulsante "Cerca"
            $('#searchSubmitButton').on('click', function () {
                const barcode = $('#barcodeInput').val().trim();

                if (!barcode) {
                    $('#searchError').text('Inserire un barcode valido.').show();
                    $('#bombolaInfo').hide();
                    $('#movementInfo').hide();
                    return;
                }

                $('#searchError').hide();
                $('#bombolaInfo').hide();
                $('#movementInfo').hide();

                // Chiamata AJAX per ottenere le informazioni della bombola
                $.ajax({
                    url: 'get_bombola_info.php',
                    type: 'POST',
                    data: { IDBombola: barcode },
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            const data = response.data;

                            // Mostra le informazioni della bombola
                            const infoList = $('#infoList');
                            infoList.empty();
                            for (const [key, value] of Object.entries(data)) {
                                infoList.append(`<li><strong>${key}:</strong> ${value}</li>`);
                            }
                            $('#bombolaInfo').show();

                            // Effettua una seconda chiamata AJAX per ottenere lo storico movimenti
                            $.ajax({
                                url: 'get_movements.php',
                                type: 'GET',
                                data: { IDBombola: barcode },
                                success: function (movementResponse) {
                                    if (movementResponse) {
                                        $('#movementTable').html(movementResponse);
                                        $('#movementInfo').show();
                                    } else {
                                        $('#movementTable').html('<p>Nessun movimento trovato.</p>');
                                        $('#movementInfo').show();
                                    }
                                },
                                error: function () {
                                    $('#movementTable').html('<p>Errore nel caricamento dello storico dei movimenti.</p>');
                                    $('#movementInfo').show();
                                }
                            });
                        } else {
                            $('#searchError').text(response.message || 'Errore durante la ricerca.').show();
                        }
                    },
                    error: function () {
                        $('#searchError').text('Errore nella richiesta. Riprova.').show();
                    }
                });
            });
        });

    </script>
    <style>
        .container-fluid {
            display: flex;
            flex-direction: column;
            align-items: center;
            /*justify-content: center;*/
            height: 100vh;
        }
        .row-container {
            display: flex;
            justify-content: center;
            gap: 20px;
            width: 100%;
        }
        .card-container {
            max-width: 400px;
            width: 100%;
            margin: 0 auto;
        }
        .card {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            border-radius: 10px;
            transition: transform 0.2s, box-shadow 0.2s;
            margin-bottom: 20px;
            width: 100%;
        }
        .card:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 12px rgba(7, 143, 64, 0.5);
        }
        .card-title {
            text-align: center;
            color: #078f40;
            font-weight: bold;
        }
    </style>

</head>
<body>
<div class="container-fluid">
    <br>
    <img class="img-fluid" src="../config/images/logo.png" alt="logoCVTO" />
    <div class="card-container">
        <br>
        <a href="/depositaossigeno" class="card-link">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">DEPOSITO BOMBOLA VUOTA</h4>
                </div>
            </div>
        </a>

        <a href="/prelevaossigeno" class="card-link">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">PRELIEVO BOMBOLA PIENA</h4>
                </div>
            </div>
        </a>
        <br>
        <div class="row-container">
            <a id="openInventoryModal" class="card-link">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title"><i class="fas fa-key"></i></h4>
                    </div>
                </div>
            </a>
            <a id="openSearchModal" class="card-link">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title"><i class="fas fa-search"></i></h4>
                    </div>
                </div>
            </a>
        </div>


    </div>
</div>

<!-- Modale per l'inserimento della password -->
<div class="modal" id="inventoryModal" tabindex="-1" role="dialog" aria-labelledby="inventoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="inventoryModalLabel">RICHIESTA PASSWORD</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="passwordForm">
                    <div class="form-group">
                        <p>ATTENZIONE: Stai per uscire dalla modalità chiosco!</p>
                        <input type="password" class="form-control" id="inventoryPassword" required placeholder="BARCODE PASSWORD" STYLE="text-align: center">
                    </div>
                    <button type="submit" class="btn btn-primary">Conferma</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Modale per visualizzare le informazioni -->
<div class="modal fade" id="searchModal" tabindex="-1" role="dialog" aria-labelledby="searchModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="searchModalLabel">Ricerca Bombola</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Campo per inserire il barcode -->
                <div class="form-group">
                    <label for="barcodeInput">Inserisci il Barcode:</label>
                    <input type="text" class="form-control" id="barcodeInput" placeholder="Scansiona o digita il barcode">
                </div>
                <!-- Contenitore per mostrare le informazioni -->
                <div id="bombolaInfo" style="display: none;">
                    <h6>Ultimo Movimento:</h6>
                    <ul id="infoList" class="list-unstyled"></ul>
                </div>
                <!-- Contenitore per mostrare lo storico dei movimenti -->
                <div id="movementInfo" style="display: none;">
                    <h6>Storico Movimenti:</h6>
                    <div id="movementTable"></div>
                </div>
                <div id="searchError" style="display: none;" class="alert alert-danger mt-3"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="resetButton"><i class="fas fa-redo"></i></button>
                <button type="button" class="btn btn-primary" id="searchSubmitButton"><i class="fa fa-search"></i></button>
            </div>
        </div>
    </div>
</div>
</body>
</html>
