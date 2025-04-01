<?php
header('Access-Control-Allow-Origin: *');

/**
 *
 * @author     Paolo Randone
 * @version    8.2
 * @note       Powered for Croce Verde Torino. All rights reserved
 *
 */
session_start();
include "../config/pdo.php";
include "../config/config.php";

if (isset($_POST["IDBombola"])) {
    try {
        $IDBombola = $_POST["IDBombola"];
        $TipoMovimento = $_POST["TipoMovimento"];
        $Destinazione = $_POST["Destinazione"];

        $updateStmt = $connect->prepare("UPDATE ossigeno SET StatoMovimento = 2 WHERE IDBombola = :IDBombola AND StatoMovimento = 1");
        $updateStmt->bindParam(':IDBombola', $IDBombola);
        $updateStmt->execute();

        $stmt = $connect->prepare("INSERT INTO ossigeno (IDBombola, TipoMovimento, Destinazione, StatoMovimento) VALUES (:IDBombola, :TipoMovimento, :Destinazione, 1)");
        $stmt->bindParam(':IDBombola', $IDBombola);
        $stmt->bindParam(':TipoMovimento', $TipoMovimento);
        $stmt->bindParam(':Destinazione', $Destinazione);

        if ($stmt->execute()) {
            echo '<script type="text/javascript">
        window.onload = function() {
                var audio = new Audio("109662__grunz__success.wav");
                audio.play();
                Swal.fire({
                title: "Fatto!",
                text: "Movimentazione bombola registrata!",
                icon: "success",
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                document.getElementById("barcodeForm").reset();
                document.getElementById("IDBombola").focus();
               window.location.href = "iphone_deposita.php"; 
            });
        }
    </script>';
        } else {
            echo '<script type="text/javascript">
        window.onload = function() {
            Swal.fire({
                title: "Errore",
                text: "Errore nel salvataggio dei dati",
                icon: "error",
                confirmButtonText: "OK"
            });
        }
    </script>';
        }
    } catch (PDOException $e) {
        echo "Errore nel database: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <meta name="author" content="Paolo Randone">
    <title>DEPOSITO BOMBOLA O2</title>
<!--    <base href="/gestionale/magazzino/">-->
    <?php require "../config/include/header.html"; ?>

    <script src="https://cdn.jsdelivr.net/npm/@zxing/library@0.21.3/umd/index.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const codeReader = new ZXing.BrowserMultiFormatReader();
            const searchInput = document.getElementById('searchInput');
            const scannerContainer = document.getElementById('scannerContainer');
            const scannerVideo = document.getElementById('scannerVideo');
            const movementTable = document.getElementById('movementTable');
            const searchError = document.getElementById('searchError');

            document.getElementById('startScannerButton').addEventListener('click', async function () {
                const videoElement = document.createElement('video');
                document.body.appendChild(videoElement);

                try {
                    const constraints = {
                        video: {
                            facingMode: { exact: 'environment' } // Forza la fotocamera posteriore
                        }
                    };

                    await codeReader.decodeFromVideoDevice(null, videoElement, (result, err) => {
                        if (result) {
                            document.getElementById('IDBombola').value = result.text;
                            codeReader.reset();
                            videoElement.remove();
                            alert('Codice scansionato: ' + result.text);
                        }
                    });
                } catch (err) {
                    console.error('Errore:', err);
                    alert('Errore avvio dello scanner.');
                    videoElement.remove();
                }
            });

            // Apertura modale di ricerca
            document.getElementById('openSearchModal').addEventListener('click', function () {
                $('#searchModal').modal('show');

                // Avvia lo scanner nella modale
                codeReader.decodeFromVideoDevice(null, scannerVideo, (result, err) => {
                    if (result) {
                        searchInput.value = result.text; // Inserisce il risultato nel campo input
                        codeReader.reset();
                    } else if (err) {
                        console.error('Errore scanner:', err);
                    }
                }).catch(err => {
                    console.error('Errore avvio scanner nella modale:', err);
                });
            });

            // Pulsante di chiusura modale
            $('#searchModal').on('hidden.bs.modal', function () {
                codeReader.reset(); // Ferma lo scanner quando la modale si chiude
            });

            // Gestione submit ricerca
            document.getElementById('searchSubmitButton').addEventListener('click', function () {
                const barcode = searchInput.value.trim();

                if (!barcode) {
                    searchError.textContent = 'Inserisci un barcode valido.';
                    searchError.style.display = 'block';
                    return;
                }

                searchError.style.display = 'none';

                // Chiamata AJAX per ottenere lo storico
                fetch('get_bombola_info.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({ IDBombola: barcode })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Formattazione tabella dati
                            const info = data.data;
                            movementTable.innerHTML = `
                            <table class="table table-sm">
                                <tbody>
                                    ${Object.entries(info).map(([key, value]) => `
                                        <tr>
                                            <td>${key}</td>
                                            <td>${value}</td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        `;
                            movementTable.style.display = 'block';
                        } else {
                            searchError.textContent = data.message || 'Errore durante la ricerca.';
                            searchError.style.display = 'block';
                        }
                    })
                    .catch(err => {
                        console.error('Errore nella richiesta:', err);
                        searchError.textContent = 'Errore nella richiesta. Dettagli: ' + err.message;
                        searchError.style.display = 'block';
                    });
            });
        });
    </script>

    <style>
        input {
            text-align: center;
        }
        .custom-btn-group .custom-btn {
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .custom-btn-group .custom-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
        }
        video {
            width: 100%;
            height: auto;
            margin-top: 10px;
        }
        #searchError {
            color: red;
            display: none;
        }
        #movementTable {
            margin-top: 20px;
            display: none;
        }
    </style>
</head>
<body>
<div class="container-fluid">
    <br>
    <div class="jumbotron">
        <div class="alert alert-info" role="alert">
            <h4 class="alert-heading" style="text-align: center">DEPOSITO BOMBOLA VUOTA</h4>
            <p>Premi il pulsante per avviare la fotocamera e scansionare il codice della bombola o inseriscilo manualmente.</p>
        </div>
        <form action="iphone_deposita.php" method="post" id="barcodeForm">
            <div class="row g-3">
                <div class="col">
                    <input type="text" class="form-control form-control-lg" name="IDBombola" placeholder="BARCODE BOMBOLA" aria-label="SN bombola" id="IDBombola">
                    <input type="hidden" class="form-control" name="TipoMovimento" id="TipoMovimento" value="ENTRATA">
                    <input type="hidden" class="form-control" name="Destinazione" id="Destinazione" value="VUOTO">
                </div>
            </div>

            <div style="text-align: center; margin-top: 20px;">
                <button type="button" class="btn btn-outline-primary custom-btn" id="startScannerButton"><i class="fas fa-camera"></i></button>
                <button type="button" class="btn btn-outline-secondary custom-btn" id="openSearchModal"><i class="fas fa-search"></i></button>
            </div>
            <div style="text-align: center; margin-top: 20px;">
                <div class="btn-group btn-group custom-btn-group" role="group" aria-label="btnaction">
                    <button type="submit" class="btn btn-outline-success custom-btn" id="submitButton" name="submitButton">Salva</button>
<!--                    <button type="button" class="btn btn-outline-danger custom-btn" id="cancelButton">Annulla</button>-->
                    <a class="btn btn-outline-secondary custom-btn" href="iphone_deposita.php">Ricarica</a>
                </div>
            </div>
        </form>
        <br>
    </div>
</div>

<div class="modal fade" id="searchModal" tabindex="-1" role="dialog" aria-labelledby="searchModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="searchModalLabel">Ricerca Bombola</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Inserisci o scansiona il barcode per cercare la bombola.</p>
                <div id="scannerContainer" style="margin-bottom: 15px;">
                    <video id="scannerVideo" style="width: 100%; height: auto; border: 1px solid #ccc;"></video>
                </div>
                <input type="text" class="form-control mt-3" id="searchInput" placeholder="Inserisci il barcode">
                <p id="searchError" style="color: red; margin-top: 10px; display: none;"></p>
                <div id="movementTable" style="margin-top: 20px; display: none;"></div>
            </div>
            <div class="modal-footer">
<!--                <button type="button" class="btn btn-secondary" data-dismiss="modal">Chiudi</button>-->
                <button type="button" class="btn btn-primary" id="searchSubmitButton">Cerca</button>
            </div>
        </div>
    </div>
</div>

</body>
</html>
