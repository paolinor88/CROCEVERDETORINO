<?php
header('Access-Control-Allow-Origin: *');

/**
 *
 * @author     Paolo Randone
 * @version    8.1
 * @note       Powered for Croce Verde Torino. All rights reserved
 *
 */
session_start();
// Connessione al database
include "../config/pdo.php";
include "../config/config.php";
/*
if (($_SESSION["livello"]) < 4) {
    header("Location: ../error.php");
}
*/
if (isset($_POST["IDBombola"])) {  // Rileva l'input dal lettore di barcode
    try {
        $IDBombola = $_POST["IDBombola"];
        $TipoMovimento = $_POST["TipoMovimento"];
        $Destinazione = $_POST["Destinazione"];

        // Disattiva il record esistente, se presente, con StatoMovimento=1
        $updateStmt = $connect->prepare("UPDATE ossigeno SET StatoMovimento = 2 WHERE IDBombola = :IDBombola AND StatoMovimento = 1");
        $updateStmt->bindParam(':IDBombola', $IDBombola);
        $updateStmt->execute();

        // Inserisce il nuovo record con StatoMovimento di default a 1
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
                    window.location.href = "/movimentiossigeno"; 
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
    <base href="/gestionale/magazzino/">
    <?php require "../config/include/header.html"; ?>

    <script>
        $(document).ready(function(){
            $('[data-toggle="popover"]').popover();
            $('#IDBombola').focus();  // Focus sul campo IDBombola all'inizio

            let submitTimeout;
            let countdownInterval;
            let countdown = 5;  // Imposta il ritardo di conferma a 5 secondi
            let lastScanTime = 0;

            $('#barcodeForm input').on('keypress', function (e) {
                // Ignora gli invii multipli ravvicinati
                const currentTime = new Date().getTime();
                if (e.which === 13 && currentTime - lastScanTime < 500) { // 500ms di tolleranza
                    e.preventDefault();
                    return false;
                }
                lastScanTime = currentTime;
            });

            // Gestione del submit con ritardo e conto alla rovescia
            $('#barcodeForm input').on('input', function () {
                clearTimeout(submitTimeout);
                clearInterval(countdownInterval);

                const idBombola = $('#IDBombola').val().trim(); // Rimuove spazi o caratteri invisibili

                // Controllo validità IDBombola solo se la lunghezza è esattamente 12
                if (idBombola.length === 12 && !idBombola.endsWith('10119')) {
                    // Mostra alert con SweetAlert e ricarica la pagina
                    Swal.fire({
                        icon: 'error',
                        title: 'ERRORE!',
                        text: 'Hai scansionato un codice a barre non valido. Utilizza quello della etichetta posta sul collo della bombola',
                        timer: 4000, // Mostra l'alert per 4 secondi
                        showConfirmButton: false
                    }).then(() => {
                        location.reload(); // Ricarica la pagina dopo l'alert
                    });

                    // Reset form e blocco
                    clearTimeout(submitTimeout);
                    clearInterval(countdownInterval);
                    $('#countdownPrompt').hide();
                    $('#barcodeForm')[0].reset();
                    $('#IDBombola').focus();
                    return;
                }

                // Procedi solo quando tutti i campi richiesti sono completi
                if ($('#IDBombola').val() && $('#TipoMovimento').val() && $('#Destinazione').val()) {
                    countdown = 5;  // Reimposta il conto alla rovescia

                    // Mostra il prompt con conto alla rovescia
                    $('#countdownPrompt').text(`Invio in ${countdown} secondi...`).show();

                    countdownInterval = setInterval(function() {
                        countdown--;
                        $('#countdownPrompt').text(`Invio in ${countdown} secondi...`);
                        if (countdown <= 0) clearInterval(countdownInterval);
                    }, 1000);

                    // Imposta il submit con ritardo
                    submitTimeout = setTimeout(function() {
                        $('#barcodeForm').submit();
                    }, 5000);  // Ritardo di 5 secondi
                }
            });

            // Tasto "Annulla" manuale
            $('#cancelButton').on('click', function (e) {
                e.preventDefault();
                var audio = new Audio("662345__fmaudio__interface-erase-3.wav");
                audio.play();
                clearTimeout(submitTimeout);
                clearInterval(countdownInterval);
                $('#countdownPrompt').hide();  // Nasconde il prompt
                $('#barcodeForm')[0].reset();
                $('#IDBombola').focus();  // Riporta il focus su IDBombola
            });
        });
    </script>

    <style>
        input {
            text-align: center;
        }
        ::-webkit-input-placeholder {
            text-align: center;
        }
        :-moz-placeholder {
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
        .custom-btn-group .btn-outline-success:hover {
            background-color: #078f40; /* Colore di sfondo al passaggio per il pulsante Salva */
            color: white;
        }
        .custom-btn-group .btn-outline-danger:hover {
            background-color: #dc3545; /* Colore di sfondo al passaggio per il pulsante Annulla */
            color: white;
        }
        .custom-btn-group .btn-outline-secondary:hover {
            background-color: #6c757d; /* Colore di sfondo al passaggio per il pulsante Indietro */
            color: white;
        }
    </style>
</head>
<!-- content -->
<body>
<div class="container-fluid">
    <br>
    <div class="jumbotron">
        <div class="alert alert-info"  role="alert">
            <h4 class="alert-heading" style="text-align: center">DEPOSITO BOMBOLA VUOTA</h4>
            <p>Utilizza il lettore di codici a barre per scansionare l'etichetta identificativa che trovi sulla bombola.</p>
            <hr>
            <p class="mb-0">Il movimento verrà registrato automaticamente! Premi ANNULLA entro la fine del conto alla rovescia per interrompere l'operazione!</p>
        </div>
        <form action="o_deposita.php" method="post" id="barcodeForm">
            <div class="row g-3">
                <div class="col">
                    <input type="text" style="border-color: #078f40" class="form-control form-control-lg" name="IDBombola" placeholder="BARCODE BOMBOLA" aria-label="SN bombola" id="IDBombola">
                    <input type="hidden" class="form-control" name="TipoMovimento" id="TipoMovimento" value="ENTRATA">
                    <input type="hidden" class="form-control" name="Destinazione" id="Destinazione" value="VUOTO">
                </div>
            </div>
            <br>
            <!-- Prompt conto alla rovescia -->
            <div id="countdownPrompt" style="display: none; color: red; text-align: center; font-weight: bold; margin-top: 10px;"></div>
            <br>
            <div style="text-align: center; margin-top: 20px;">
                <div class="btn-group btn-group-lg custom-btn-group" role="group" aria-label="btnaction">
                    <button type="submit" class="btn btn-outline-success custom-btn" id="submitButton" name="submitButton">Salva</button>
                    <button type="button" class="btn btn-outline-danger custom-btn" id="cancelButton">Annulla</button>
                    <a type="button" class="btn btn-outline-secondary custom-btn" id="backButton" href="/movimentiossigeno">Indietro</a>
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
