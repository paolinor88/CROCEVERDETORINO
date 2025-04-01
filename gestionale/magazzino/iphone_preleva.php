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
// Connessione al database
include "../config/pdo.php";
include "../config/config.php";

if (isset($_POST["IDBombola"])) {  // Rileva l'input dal lettore di barcode
    try {
        $IDBombola = $_POST["IDBombola"];
        $TipoMovimento = $_POST["TipoMovimento"];
        $Destinazione = $_POST["Destinazione"];

        // Disattiva il record esistente, se presente, con StatoMovimento=1
        $updateStmt = $connect->prepare("UPDATE ossigeno SET StatoMovimento = 2 WHERE IDBombola = :IDBombola AND StatoMovimento = 1");
        $updateStmt->bindParam(':IDBombola', $IDBombola);
        $updateStmt->execute();

        // Aggiungi movimento
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
               window.location.href = "iphone_preleva.php"; 
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
    <title>PRELIEVO BOMBOLA O2</title>
    <base href="/gestionale/magazzino/">
    <?php require "../config/include/header.html"; ?>

    <script src="https://cdn.jsdelivr.net/npm/@zxing/library@0.21.3/umd/index.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const codeReader = new ZXing.BrowserMultiFormatReader();

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
            box-shadow: 0 6px 12px rgba(7, 143, 64, 0.3);
        }
        video {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: 9999;
        }
    </style>
</head>
<body>
<div class="container-fluid">
    <br>
    <div class="jumbotron">
        <div class="alert alert-info" role="alert">
            <h4 class="alert-heading" style="text-align: center">PRELIEVO BOMBOLA PIENA</h4>
            <p>Premi il pulsante per avviare la fotocamera e scansionare il codice della bombola o inseriscilo manualmente.</p>
            <hr>
            <p class="mb-0">Premi ANNULLA per interrompere l'operazione!</p>
        </div>
        <form action="iphone_preleva.php" method="post" id="barcodeForm">
            <div class="row g-3">
                <div class="col">
                    <input type="text" class="form-control form-control-lg" name="IDBombola" placeholder="BARCODE BOMBOLA" aria-label="SN bombola" id="IDBombola">
                    <input type="hidden" class="form-control" name="TipoMovimento" id="TipoMovimento" value="USCITA">
                </div>
                <div class="col">
                    <input type="text" class="form-control form-control-lg" name="Destinazione" placeholder="Inserisci Destinazione" aria-label="Destinazione" id="Destinazione">
                </div>
            </div>

            <div style="text-align: center; margin-top: 20px;">
            <button type="button" class="btn btn-outline-primary custom-btn" id="startScannerButton">Avvia Scanner</button>
            </div>

            <div style="text-align: center; margin-top: 20px;">
                <div class="btn-group btn-group-lg custom-btn-group" role="group" aria-label="btnaction">
                    <button type="submit" class="btn btn-outline-success custom-btn" id="submitButton" name="submitButton">Salva</button>
                    <button type="button" class="btn btn-outline-danger custom-btn" id="cancelButton">Annulla</button>
                    <a type="button" class="btn btn-outline-secondary custom-btn" id="backButton" href="iphone_preleva.php">Indietro</a>
                </div>
            </div>
        </form>
        <br>
    </div>
</div>
</body>
<footer class="container-fluid">
    <div class="text-center">
        <font size="-4" style="color: lightgray; "><em>Powered for <a href="mailto:paolorandone@croceverde.org">Croce Verde Torino</a>. All rights reserved.<p>
    </div>
</footer>
</html>
