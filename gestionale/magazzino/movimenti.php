<?php
header('Access-Control-Allow-Origin: *');
session_start();
include "../config/pdo.php";
include "../config/config.php";
/*
if ($_SESSION["livello"] < 4) {
    header("Location: ../error.php");
}
*/
// Funzione per ottenere conteggi parziali per tipo bombola e destinazione
function get_count_by_type($db, $destination)
{
    $types = ['2LT', '3LT', '7LT', 'CPAP'];
    $counts = [];

    foreach ($types as $type) {
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM ossigeno o
                              JOIN o_inventario i ON o.IDBombola = i.IDBombola
                              WHERE o.StatoMovimento = '1' AND i.TipoBombola = ? AND i.StatoBombola = '1'
                              AND (o.Destinazione = ? OR (? = 'ALTRO' AND o.Destinazione NOT IN ('MAGAZZINO', 'VUOTO')))");
        $stmt->bind_param("sss", $type, $destination, $destination);
        $stmt->execute();
        $counts[$type] = $stmt->get_result()->fetch_assoc()['count'];
    }

    // Conteggio per tipo NA (TipoBombola NULL o non in lista)
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM ossigeno o
                          LEFT JOIN o_inventario i ON o.IDBombola = i.IDBombola
                          WHERE o.StatoMovimento = '1' AND i.StatoBombola = '1' AND (i.TipoBombola NOT IN ('2LT', '3LT', '7LT', 'CPAP') OR i.TipoBombola IS NULL)
                          AND (o.Destinazione = ? OR (? = 'ALTRO' AND o.Destinazione NOT IN ('MAGAZZINO', 'VUOTO')))");
    $stmt->bind_param("ss", $destination, $destination);
    $stmt->execute();
    $counts['NA'] = $stmt->get_result()->fetch_assoc()['count'];

    return $counts;
}

// Conteggi principali e per tipo
$magazzino_count = $db->query("SELECT COUNT(*) as count FROM ossigeno WHERE Destinazione = 'MAGAZZINO' AND StatoMovimento='1'")->fetch_assoc()['count'];
$vuoto_count = $db->query("SELECT COUNT(*) as count FROM ossigeno WHERE Destinazione = 'VUOTO' AND StatoMovimento='1'")->fetch_assoc()['count'];
$altro_count = $db->query("SELECT COUNT(*) as count FROM ossigeno WHERE Destinazione NOT IN ('MAGAZZINO', 'VUOTO', 'SOL') AND StatoMovimento='1'")->fetch_assoc()['count'];

// Conteggi parziali per ciascuna destinazione
$magazzino_counts_by_type = get_count_by_type($db, 'MAGAZZINO');
$vuoto_counts_by_type = get_count_by_type($db, 'VUOTO');
$altro_counts_by_type = get_count_by_type($db, 'ALTRO');

// Calcola totale generale delle bombole
$total_general = $db->query("SELECT COUNT(*) as count FROM ossigeno WHERE StatoMovimento='1' AND Destinazione!='SOL'")->fetch_assoc()['count'];

// SOL Conteggi principali e per tipo

$count2LT_SOL = $db->query("SELECT  COUNT(*) as count FROM o_inventario WHERE StatoBombola = '2' AND TipoBombola='2LT' ")->fetch_assoc()['count'];
$count3LT_SOL = $db->query("SELECT  COUNT(*) as count FROM o_inventario WHERE StatoBombola = '2' AND TipoBombola='3LT' ")->fetch_assoc()['count'];
$count7LT_SOL = $db->query("SELECT  COUNT(*) as count FROM o_inventario WHERE StatoBombola = '2' AND TipoBombola='7LT' ")->fetch_assoc()['count'];
$countCPAP_SOL = $db->query("SELECT  COUNT(*) as count FROM o_inventario WHERE StatoBombola = '2' AND TipoBombola='CPAP' ")->fetch_assoc()['count'];

// SOL Calcola totale generale delle bombole
$total_general_SOL = $db->query("SELECT COUNT(*) as count FROM o_inventario WHERE StatoBombola='2'")->fetch_assoc()['count'];

if (isset($_POST["submitButton"])) {
    try {
        $IDBombole = $_POST["IDBombola"];
        $Tipi = $_POST["TipoBombola"];

        foreach ($IDBombole as $index => $IDBombola) {
            $TipoBombola = $Tipi[$index];

            if (!empty($IDBombola) && !empty($TipoBombola)) {
                // Controlla se la bombola è già inventariata
                $stmt_check = $connect->prepare("SELECT * FROM o_inventario WHERE IDBombola = :IDBombola");
                $stmt_check->bindParam(':IDBombola', $IDBombola);
                $stmt_check->execute();

                if ($stmt_check->rowCount() > 0) {
                    // Bombola già inventariata, aggiorna TipoBombola e StatoBombola MODIFICA PER CORREGGERE SE INVENTARIO SBAGLIATO
                    $stmt_update = $connect->prepare("UPDATE o_inventario SET TipoBombola = :TipoBombola, StatoBombola = 1 WHERE IDBombola = :IDBombola");
                    $stmt_update->bindParam(':TipoBombola', $TipoBombola); // Aggiorna con il valore del form
                    $stmt_update->bindParam(':IDBombola', $IDBombola);
                    $stmt_update->execute();
                } else {
                    // Bombola non inventariata, inserisci un nuovo record
                    $stmt_inventario = $connect->prepare("INSERT INTO o_inventario (IDBombola, TipoBombola) VALUES (:IDBombola, :TipoBombola)");
                    $stmt_inventario->bindParam(':IDBombola', $IDBombola);
                    $stmt_inventario->bindParam(':TipoBombola', $TipoBombola);
                    $stmt_inventario->execute();
                }

                /*if ($stmt_check->rowCount() > 0) {
                    // Bombola già inventariata, aggiorna StatoBombola a 1
                    $stmt_update = $connect->prepare("UPDATE o_inventario SET StatoBombola = 1 WHERE IDBombola = :IDBombola");
                    $stmt_update->bindParam(':IDBombola', $IDBombola);
                    $stmt_update->execute();
                } else {
                    // Bombola non inventariata, inserisci un nuovo record
                    $stmt_inventario = $connect->prepare("INSERT INTO o_inventario (IDBombola, TipoBombola) VALUES (:IDBombola, :TipoBombola)");
                    $stmt_inventario->bindParam(':IDBombola', $IDBombola);
                    $stmt_inventario->bindParam(':TipoBombola', $TipoBombola);
                    $stmt_inventario->execute();
                }*/

                /* Inserimento in ossigeno MODIFICATO PER AGGIORNARE A 2 MOVIMENTI VECCHI
                $stmt_ossigeno = $connect->prepare("INSERT INTO ossigeno (IDBombola, TipoMovimento, Destinazione) VALUES (:IDBombola, 'INVENTARIO', 'MAGAZZINO')");
                $stmt_ossigeno->bindParam(':IDBombola', $IDBombola);
                $stmt_ossigeno->execute();*/

                // Imposta StatoMovimento a 2 per tutti i movimenti precedenti per questa bombola
                $stmt_update_movimenti = $connect->prepare("UPDATE ossigeno SET StatoMovimento = 2 WHERE IDBombola = :IDBombola");
                $stmt_update_movimenti->bindParam(':IDBombola', $IDBombola);
                $stmt_update_movimenti->execute();

                // Inserimento del nuovo movimento
                $stmt_ossigeno = $connect->prepare("INSERT INTO ossigeno (IDBombola, TipoMovimento, Destinazione, StatoMovimento) VALUES (:IDBombola, 'INVENTARIO', 'MAGAZZINO', 1)");
                $stmt_ossigeno->bindParam(':IDBombola', $IDBombola);
                $stmt_ossigeno->execute();
            }
        }

        echo '<script type="text/javascript">
            window.onload = function() {
                Swal.fire({
                    title: "Fatto!",
                    icon: "success",
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    window.location.replace(window.location.pathname);
                });
            }
        </script>';
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
    <title>Movimenti O2</title>

    <?php require "../config/include/header.html"; ?>
<style>
    .highlight-green {
        background-color: #d4edda; /* Verde chiaro */
        border: 1px solid #28a745; /* Bordo verde */
    }

</style>
    <script>
        $(document).ready(function() {
            // Inizializzazione DataTable
            $('#myTable').DataTable({
                //stateSave: true,
                paging: false,
                language: { url: '../config/js/package.json' },
                //order: [[1, "asc"]],
                pagingType: "simple",
                pageLength: 50,
                columnDefs: [
                    { targets: [0], visible: true, searchable: false, orderable: false },
                    { targets: [1], visible: true, searchable: true, orderable: true }
                ]
            });

            // Inizializzazione dei popover
            $('[data-toggle="popover"]').popover();
            // Controllo per evitare invii multipli
            let lastScanTime = 0;
            $(document).on('keypress', '[name="IDBombola[]"], [name="TipoBombola[]"]', function(e) {
                const currentTime = new Date().getTime();
                if (e.which === 13 && currentTime - lastScanTime < 500) { // Ignora invii ravvicinati entro 500 ms
                    e.preventDefault();
                    return false;
                }
                lastScanTime = currentTime;
            })
            // Funzione per aggiungere una nuova riga di input e forzare il focus su IDBombola
            function addNewRow() {
                const newRow = `
            <div class="row mt-2">
                <div class="col">
                    <label for="IDBombola">IDBombola</label>
                    <input name="IDBombola[]" class="form-control" placeholder="Barcode bombola">
                </div>
                <div class="col">
                    <label for="TipoBombola">Tipo</label>
                    <input name="TipoBombola[]" class="form-control" placeholder="Barcode tipo">
                </div>
            </div>`;
                $('#inputContainer').append(newRow);

                // Forza il focus sul nuovo campo IDBombola, ignorando il TAB del lettore di barcode
                setTimeout(() => {
                    $('#inputContainer .row').last().find('[name="IDBombola[]"]').focus();
                }, 10); // Breve ritardo per assicurare che il focus sia gestito dopo l'inserimento
            }

            // Focus sul primo campo IDBombola quando si apre la modale
            $('#additem').on('shown.bs.modal', function() {
                $('#inputContainer [name="IDBombola[]"]').first().trigger('focus');
            });

            // AJAX per precompilare TipoBombola se IDBombola è già in o_inventario
            $(document).on('input', '[name="IDBombola[]"]', function() {
                const $currentIDBombola = $(this);
                let IDBombola = $currentIDBombola.val();

                if (IDBombola) {
                    $.ajax({
                        url: 'get_tipo_bombola.php',
                        type: 'POST',
                        data: { IDBombola: IDBombola },
                        dataType: 'json',
                        success: function(response) {
                            const $currentRow = $currentIDBombola.closest('.row');
                            const $tipoBombola = $currentRow.find('[name="TipoBombola[]"]');
                            $tipoBombola.val(response.tipo || '');

                            // Se TipoBombola è popolato automaticamente, evidenzia in verde e aggiungi nuova riga
                            if (response.tipo) {
                                $currentIDBombola.addClass('highlight-green');
                                $tipoBombola.addClass('highlight-green');
                                addNewRow();
                            }
                        },
                        error: function() {
                            console.error("Errore nella richiesta AJAX.");
                        }
                    });
                }
            });

            // Aggiungi nuova riga automaticamente quando entrambi i campi sono completati
            $(document).on('input', '[name="IDBombola[]"], [name="TipoBombola[]"]', function() {
                const lastRow = $('#inputContainer .row').last();
                const idBombolaFilled = lastRow.find('[name="IDBombola[]"]').val() !== '';
                const tipoBombolaValue = lastRow.find('[name="TipoBombola[]"]').val();
                const tipoBombolaFilled = ['2LT', '3LT', '7LT', 'CPAP'].includes(tipoBombolaValue);

                if (idBombolaFilled && tipoBombolaFilled) {
                    addNewRow();
                }
            });

            // Gestione modale "Storico Movimenti"
            $('#movementModal').on('show.bs.modal', function(event) {
                const button = $(event.relatedTarget);
                const idBombola = button.data('idbombola');
                const modal = $(this);

                modal.find('.modal-title').text('Storico Movimenti - IDBombola: ' + idBombola);

                $.ajax({
                    url: 'get_movements.php',
                    type: 'GET',
                    cache: false,
                    data: { IDBombola: idBombola, _: new Date().getTime() },
                    success: function(response) {
                        modal.find('.modal-body').html(response);
                    },
                    error: function() {
                        modal.find('.modal-body').html("<p>Errore nel caricamento dei movimenti.</p>");
                    }
                });
            });

            // Funzione per il pulsante "Svuota"
            const svuotaButton = document.getElementById("svuota");
            if (svuotaButton) {
                svuotaButton.addEventListener("click", function() {
                    Swal.fire({
                        title: "Sei sicuro?",
                        text: "Questa azione contrassegna le bombole vuote presenti in magazzino come 'RITIRATE'.",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonText: "Sì, svuota!",
                        cancelButtonText: "Annulla"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Effettua la richiesta AJAX per svuotare
                            fetch("svuota_bombole.php", {
                                method: "POST",
                                headers: { "Content-Type": "application/json" },
                                body: JSON.stringify({ action: "svuota" })
                            })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        Swal.fire("Fatto!", "Ora il magazzino del vuoto non contiene bombole", "success").then(() => {
                                            location.reload();
                                        });
                                    } else {
                                        Swal.fire("Errore!", "Si è verificato un errore durante l'operazione.", "error");
                                    }
                                })
                                .catch(error => Swal.fire("Errore!", "Errore nella connessione.", "error"));
                        }
                    });
                });
            } else {
                console.error("Elemento con ID 'svuota' non trovato.");
            }
        });

    </script>
<script>
    $(document).ready(function () {
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
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const filterButton = document.getElementById('filterErrorsButton');
            const resetButton = document.getElementById('resetFilterButton');
            const tableRows = document.querySelectorAll('#myTable tbody tr');

            filterButton.addEventListener('click', function () {
                tableRows.forEach(row => {
                    if (!row.querySelector('td[style*="color: red"]')) {
                        row.style.display = 'none';
                    }
                });
            });

            resetButton.addEventListener('click', function () {
                tableRows.forEach(row => {
                    row.style.display = '';
                });
            });
        });
    </script>

</head>
<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../index.php" style="color: #078f40">Home</a></li>
            <li class="breadcrumb-item"><a href="index.php" style="color: #078f40">Autoparco</a></li>
            <li class="breadcrumb-item active" aria-current="page">Ossigeno</li>
        </ol>
    </nav>
</div>

<body>
<div class="container-fluid">
    <div class="jumbotron">
        <div class="alert" role="alert" style="text-align: center; border-color: #078f40">
            <strong>TOTALE: <?= $total_general ?></strong> [ 2LT: <?= $magazzino_counts_by_type['2LT'] + $vuoto_counts_by_type['2LT'] + $altro_counts_by_type['2LT'] ?> / 3LT: <?= $magazzino_counts_by_type['3LT'] + $vuoto_counts_by_type['3LT'] + $altro_counts_by_type['3LT'] ?> / 7LT: <?= $magazzino_counts_by_type['7LT'] + $vuoto_counts_by_type['7LT'] + $altro_counts_by_type['7LT'] ?> / CPAP: <?= $magazzino_counts_by_type['CPAP'] + $vuoto_counts_by_type['CPAP'] + $altro_counts_by_type['CPAP'] ?> / NA: <?= $magazzino_counts_by_type['NA'] + $vuoto_counts_by_type['NA'] + $altro_counts_by_type['NA'] ?> ]

<!--            <strong>TOTALE SOL: --><?php //= $total_general_SOL ?><!--</strong> [ 2LT: --><?php //= $count2LT_SOL ?><!-- / 3LT: --><?php //= $count3LT_SOL ?><!-- / 7LT: --><?php //= $count7LT_SOL ?><!-- / CPAP: --><?php //= $countCPAP_SOL ?><!--  ]-->
        </div>
        <div class="row row-cols-1 row-cols-md-3" >
            <!-- Card per Bombole in Magazzino -->
            <div class="col mb-4">
                <div class="card" style="border-color: #078f40">
                    <div class="card-body">
                        <h5 class="card-title" style="text-align: center">MAGAZZINO: <?= $magazzino_count ?></h5>
                        <p class="card-text" style="text-align: center">2LT: <?= $magazzino_counts_by_type['2LT'] ?> / 3LT: <?= $magazzino_counts_by_type['3LT'] ?> / 7LT: <?= $magazzino_counts_by_type['7LT'] ?> / CPAP: <?= $magazzino_counts_by_type['CPAP'] ?> / NA: <?= $magazzino_counts_by_type['NA'] ?></p>
                    </div>
                </div>
            </div>

            <!-- Card per Bombole Vuote -->
            <div class="col mb-4">
                <div class="card" style="border-color: #078f40">
                    <div class="card-body">
                        <h5 class="card-title" style="text-align: center">VUOTE: <?= $vuoto_count ?></h5>
                        <p class="card-text" style="text-align: center">2LT: <?= $vuoto_counts_by_type['2LT'] ?> / 3LT: <?= $vuoto_counts_by_type['3LT'] ?> / 7LT: <?= $vuoto_counts_by_type['7LT'] ?> / CPAP: <?= $vuoto_counts_by_type['CPAP'] ?> / NA: <?= $vuoto_counts_by_type['NA'] ?></p>
                    </div>
                </div>
            </div>

            <!-- Card per Altre Destinazioni -->
            <div class="col mb-4">
                <div class="card" style="border-color: #078f40">
                    <div class="card-body">
                        <h5 class="card-title" style="text-align: center">IN USO: <?= $altro_count ?></h5>
                        <p class="card-text" STYLE="text-align: center">2LT: <?= $altro_counts_by_type['2LT'] ?> / 3LT: <?= $altro_counts_by_type['3LT'] ?> / 7LT: <?= $altro_counts_by_type['7LT'] ?> / CPAP: <?= $altro_counts_by_type['CPAP'] ?> / NA: <?= $altro_counts_by_type['NA'] ?></p>
                    </div>
                </div>
            </div>

        </div>
        <div style="text-align: center">
            <button type="button" class="btn btn-outline-warning" name="svuota" id="svuota"><i class="fas fa-exclamation"></i><i class="fas fa-exclamation"></i><i class="fas fa-exclamation"></i></button>
            <!--<button type="button" class="btn btn-outline-success" name="esporta" id="esporta"><i class="far fa-file-excel"></i></button>-->
            <a role="button" class="btn btn-outline-info" href="/movimentiossigeno"><i class="fas fa-barcode"></i></a>
            <button type="button" class="btn btn-outline-primary" id="searchButton" data-toggle="modal" data-target="#searchModal"><i class="fas fa-search"></i></button>
            <button id="filterErrorsButton" class="btn btn-outline-danger"><i class="fas fa-exclamation-triangle"></i></button>
            <button id="resetFilterButton" class="btn btn-outline-secondary"><i class="fas fa-globe"></i></button>
        </div>
        <hr>

        <div class="table-responsive-sm">
            <table class="table table-hover table-sm" id="myTable">
                <thead>
                <tr>
                    <th scope="col"><button class="btn btn-sm btn-outline-info" style="border: none" data-toggle="modal" data-target="#additem"><i class="fas fa-plus"></i></button></th>
                    <th scope="col">BOMBOLA</th>
                    <th scope="col">TIPO</th>
                    <th scope="col">POSIZIONE</th>
                </tr>
                </thead>
                <tbody>
                <?php
                // Query per recuperare le informazioni delle bombole
                $query = "
    SELECT o.IDBombola, o.Destinazione, i.TipoBombola
    FROM ossigeno o
    LEFT JOIN o_inventario i ON o.IDBombola = i.IDBombola
    WHERE o.StatoMovimento = '1' AND o.Destinazione != 'SOL'
    ORDER BY o.IDMovimento DESC
";
                $result = $db->query($query);

                // Array per conteggiare le bombole per destinazione
                $destinazione_data = [];

                // Memorizza i dati delle righe per evidenziare successivamente
                $rows = [];

                while ($row = $result->fetch_assoc()) {
                    $destinazione = $row['Destinazione'];
                    $tipo_bombola = $row['TipoBombola'] ?? 'N/A';

                    // Conteggia le bombole per ogni destinazione
                    if (!isset($destinazione_data[$destinazione])) {
                        $destinazione_data[$destinazione] = ['2LT' => 0, '3LT' => 0, '7LT' => 0, 'CPAP' => 0];
                    }

                    if (in_array($tipo_bombola, ['2LT', '3LT', '7LT', 'CPAP'])) {
                        $destinazione_data[$destinazione][$tipo_bombola]++;
                    }

                    // Salva la riga per il rendering successivo
                    $rows[] = $row;
                }

                // Calcola quali destinazioni non rispettano i requisiti
                $invalid_destinations = [];

                foreach ($destinazione_data as $destinazione => $count) {
                    if ($destinazione !== 'MAGAZZINO' && $destinazione !== 'VUOTO' && !in_array($destinazione, ['ALPIGNANO', 'BORGARO', 'CIRIE', 'SAN MAURO', 'VENARIA', 'MONTAGNA'])) {
                        $portable_units = $count['2LT'] + $count['3LT'] + $count['CPAP'];

                        // Verifica i criteri
                        if (
                            $portable_units < 2 || // Almeno 2 bombole portatili (2LT, 3LT, CPAP) combinate
                            $count['7LT'] > 2 || // Massimo 2 7LT
                            $count['CPAP'] > 3 || // Massimo 3 CPAP
                            $count['3LT'] > 2 || // Massimo 2 3LT
                            $count['2LT'] > 2 || // Massimo 2 2LT
                            ($portable_units ) > 3 // Somma totale di tutte le bombole portatili non deve superare 3
                        ) {
                            $invalid_destinations[$destinazione] = true;
                        }
                    }
                }

                // Output delle righe della tabella con evidenziazione
                foreach ($rows as $row) {
                    $destinazione = $row['Destinazione'];
                    $highlight = isset($invalid_destinations[$destinazione]) ? 'style="color: red; font-weight: bold;"' : '';

                    echo "<tr>
        <td><i class='fas fa-history' style='cursor: pointer;' data-toggle='modal' data-target='#movementModal' data-idbombola='" . $row['IDBombola'] . "'></i></td>
        <td>" . $row['IDBombola'] . "</td>
        <td>" . ($row['TipoBombola'] ?? 'N/A') . "</td>
        <td $highlight>" . $destinazione . "</td>
    </tr>";
                }
                ?>
                </tbody>


            </table>
        </div>
    </div>
</div>

<!-- Modal per aggiungere nuove bombole -->
<div class="modal" id="additem" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered" role="document">
        <form action="movimenti.php" method="post" id="inventarioForm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="additemtitle">Modalità inventario</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="inputContainer">
                    <div class="row">
                        <div class="col">
                            <label for="IDBombola">IDBombola</label>
                            <input id="IDBombola" name="IDBombola[]" class="form-control" placeholder="Barcode bombola">
                        </div>
                        <div class="col">
                            <label for="TipoBombola">Tipo</label>
                            <input id="TipoBombola" name="TipoBombola[]" class="form-control" placeholder="Barcode tipo">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Chiudi</button>
                    <button type="submit" class="btn btn-success" id="submitButton" name="submitButton">Salva</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal per visualizzare lo storico dei movimenti -->
<div class="modal fade" id="movementModal" tabindex="-1" role="dialog" aria-labelledby="movementModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="movementModalLabel">Storico Movimenti</h5>
            </div>
            <div class="modal-body">
                <!-- Contenuto dello storico dei movimenti caricato da get_movements.php -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Chiudi</button>
            </div>
        </div>
    </div>
</div>

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
<?php include('../config/include/footer.php'); ?>
</html>
