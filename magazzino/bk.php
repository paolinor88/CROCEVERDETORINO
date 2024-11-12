<?php
header('Access-Control-Allow-Origin: *');
session_start();
include "../config/pdo.php";
include "../config/config.php";

if ($_SESSION["livello"] < 4) {
    header("Location: ../error.php");
}

// Funzione per ottenere conteggi parziali per tipo
function get_count_by_type($db, $destination)
{
    $types = ['2LT', '3LT', '7LT'];
    $counts = [];

    foreach ($types as $type) {
        if ($destination === 'ALTRO') {
            $stmt = $db->prepare("SELECT COUNT(*) as count FROM ossigeno o
                                  JOIN o_inventario i ON o.IDBombola = i.IDBombola
                                  WHERE o.Destinazione NOT IN ('MAGAZZINO', 'VUOTO') AND o.StatoMovimento = '1' AND i.TipoBombola = ?");
            $stmt->bind_param("s", $type);
        } else {
            $stmt = $db->prepare("SELECT COUNT(*) as count FROM ossigeno o
                                  JOIN o_inventario i ON o.IDBombola = i.IDBombola
                                  WHERE o.Destinazione = ? AND o.StatoMovimento = '1' AND i.TipoBombola = ?");
            $stmt->bind_param("ss", $destination, $type);
        }
        $stmt->execute();
        $counts[$type] = $stmt->get_result()->fetch_assoc()['count'];
    }

    // Controllo per tipo "ALTRO"
    if ($destination === 'ALTRO') {
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM ossigeno o
                              JOIN o_inventario i ON o.IDBombola = i.IDBombola
                              WHERE o.Destinazione NOT IN ('MAGAZZINO', 'VUOTO') AND o.StatoMovimento = '1' AND 
                              (i.TipoBombola NOT IN ('2LT', '3LT', '7LT') OR i.TipoBombola IS NULL)");
    } else {
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM ossigeno o
                              JOIN o_inventario i ON o.IDBombola = i.IDBombola
                              WHERE o.Destinazione = ? AND o.StatoMovimento = '1' AND 
                              (i.TipoBombola NOT IN ('2LT', '3LT', '7LT') OR i.TipoBombola IS NULL)");
        $stmt->bind_param("s", $destination);
    }
    $stmt->execute();
    $counts['ALTRO'] = $stmt->get_result()->fetch_assoc()['count'];

    return $counts;
}

// Conteggi principali e per tipo
$magazzino_count = $db->query("SELECT COUNT(*) as count FROM ossigeno WHERE Destinazione = 'MAGAZZINO' AND StatoMovimento='1'")->fetch_assoc()['count'];
$vuoto_count = $db->query("SELECT COUNT(*) as count FROM ossigeno WHERE Destinazione = 'VUOTO' AND StatoMovimento='1'")->fetch_assoc()['count'];
$altro_count = $db->query("SELECT COUNT(*) as count FROM ossigeno WHERE Destinazione NOT IN ('MAGAZZINO', 'VUOTO') AND StatoMovimento='1'")->fetch_assoc()['count'];

// Conteggi parziali per ciascuna destinazione
$magazzino_counts_by_type = get_count_by_type($db, 'MAGAZZINO');
$vuoto_counts_by_type = get_count_by_type($db, 'VUOTO');
$altro_counts_by_type = get_count_by_type($db, 'ALTRO');

// Calcola totale generale delle bombole
$total_general = $db->query("SELECT COUNT(*) as count FROM ossigeno WHERE StatoMovimento='1'")->fetch_assoc()['count'];

// Calcola parziali per tipo senza distinzione di destinazione
$counts_by_type = ['2LT' => 0, '3LT' => 0, '7LT' => 0, 'ALTRO' => 0];
$types = ['2LT', '3LT', '7LT'];
foreach ($types as $type) {
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM ossigeno o
                          JOIN o_inventario i ON o.IDBombola = i.IDBombola
                          WHERE o.StatoMovimento = '1' AND i.TipoBombola = ?");
    $stmt->bind_param("s", $type);
    $stmt->execute();
    $counts_by_type[$type] = $stmt->get_result()->fetch_assoc()['count'];
}

// Calcola parziali per tipo "ALTRO"
$stmt = $db->prepare("SELECT COUNT(*) as count FROM ossigeno o
                      JOIN o_inventario i ON o.IDBombola = i.IDBombola
                      WHERE o.StatoMovimento = '1' AND 
                      (i.TipoBombola NOT IN ('2LT', '3LT', '7LT') OR i.TipoBombola IS NULL)");
$stmt->execute();
$counts_by_type['ALTRO'] = $stmt->get_result()->fetch_assoc()['count'];

// Verifica che il form sia stato inviato
if (isset($_POST["submitButton"])) {
    try {
        $IDBombole = $_POST["IDBombola"];
        $Tipi = $_POST["TipoBombola"];

        // Preparazione delle query di inserimento per entrambe le tabelle
        $stmt_inventario = $connect->prepare("INSERT INTO o_inventario (IDBombola, TipoBombola) VALUES (:IDBombola, :TipoBombola)");
        $stmt_ossigeno = $connect->prepare("INSERT INTO ossigeno (IDBombola, TipoMovimento, Destinazione) VALUES (:IDBombola, 'INVENTARIO', 'MAGAZZINO')");

        // Cicla attraverso ogni coppia di valori di IDBombola e Tipo
        foreach ($IDBombole as $index => $IDBombola) {
            $TipoBombola = $Tipi[$index];

            // Inserisci solo se entrambi i campi non sono vuoti
            if (!empty($IDBombola) && !empty($TipoBombola)) {
                // Inserimento in o_inventario
                $stmt_inventario->bindParam(':IDBombola', $IDBombola);
                $stmt_inventario->bindParam(':TipoBombola', $TipoBombola);
                $stmt_inventario->execute();

                // Inserimento in ossigeno
                $stmt_ossigeno->bindParam(':IDBombola', $IDBombola);
                $stmt_ossigeno->execute();
            }
        }

        echo '<script type="text/javascript">
            window.onload = function() {
                Swal.fire({
                    title: "Fatto!",
                    text: "Inventario e ossigeno aggiunti!",
                    icon: "success",
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    window.location.replace(window.location.pathname); // Ricarica la pagina senza dati POST
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

    <script>
        $(document).ready(function() {
            $('[data-toggle="popover"]').popover();
            var dataTables = $('#myTable').DataTable({
                stateSave: true,
                "paging": false,
                "language": {url: '../config/js/package.json'},
                "order": [[1, "asc"]],
                "pagingType": "simple",
                "pageLength": 50,
                "columnDefs": [
                    {"targets": [0], "visible": true, "searchable": false, "orderable": false},
                    {"targets": [1], "visible": true, "searchable": true, "orderable": true}
                ]
            });
            $('#IDBombola').focus();
            function addNewRow() {
                const newRow = `
                    <div class="row mt-2">
                        <div class="col">
                            <label for="IDBombola">IDBombola</label>
                            <input name="IDBombola[]" class="form-control form-control-sm" placeholder="Barcode bombola">
                        </div>
                        <div class="col">
                            <label for="TipoBombola">Tipo</label>
                            <input name="TipoBombola[]" class="form-control form-control-sm" placeholder="Barcode tipo">
                        </div>
                    </div>`;
                $('#inputContainer').append(newRow);
            }

            $(document).on('input', '[name="IDBombola[]"], [name="TipoBombola[]"]', function() {
                const lastRow = $('#inputContainer .row').last();
                const idBombolaFilled = lastRow.find('[name="IDBombola[]"]').val() !== '';
                const tipoBombolaFilled = lastRow.find('[name="TipoBombola[]"]').val() !== '';

                if (idBombolaFilled && tipoBombolaFilled) {
                    addNewRow();
                }
            });

            $('#additem').on('shown.bs.modal', function() {
                $('#IDBombola').trigger('focus');
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#movementModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var idBombola = button.data('idbombola');

                var modal = $(this);
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
        <div class="alert alert-primary" role="alert">
            <strong>TOTALE BOMBOLE: <?= $total_general ?></strong> [ 2LT: <?= $counts_by_type['2LT'] ?> / 3LT: <?= $counts_by_type['3LT'] ?> / 7LT: <?= $counts_by_type['7LT'] ?> / NA: <?= $counts_by_type['ALTRO'] ?> ]<br>
        </div>

        <div class="row row-cols-1 row-cols-md-3">
            <!-- Card per Bombole in Magazzino -->
            <div class="col mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title" style="text-align: center">MAGAZZINO: <?= $magazzino_count ?></h5>
                        <p class="card-text">2LT: <?= $magazzino_counts_by_type['2LT'] ?> / 3LT: <?= $magazzino_counts_by_type['3LT'] ?> / 7LT: <?= $magazzino_counts_by_type['7LT'] ?> / NA: <?= $magazzino_counts_by_type['ALTRO'] ?></p>
                    </div>
                </div>
            </div>

            <!-- Card per Bombole Vuote -->
            <div class="col mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title" style="text-align: center">VUOTE: <?= $vuoto_count ?></h5>
                        <p class="card-text">2LT: <?= $vuoto_counts_by_type['2LT'] ?> / 3LT: <?= $vuoto_counts_by_type['3LT'] ?> / 7LT: <?= $vuoto_counts_by_type['7LT'] ?> / NA: <?= $vuoto_counts_by_type['ALTRO'] ?></p>
                    </div>
                </div>
            </div>

            <!-- Card per Altre Destinazioni -->
            <div class="col mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title" style="text-align: center">IN USO: <?= $altro_count ?></h5>
                        <p class="card-text">2LT: <?= $altro_counts_by_type['2LT'] ?> / 3LT: <?= $altro_counts_by_type['3LT'] ?> / 7LT: <?= $altro_counts_by_type['7LT'] ?> / NA: <?= $altro_counts_by_type['ALTRO'] ?></p>
                    </div>
                </div>
            </div>
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
                $select = $db->query("SELECT * FROM ossigeno WHERE StatoMovimento='1' ORDER BY IDMovimento DESC");
                while ($ciclo = $select->fetch_array()) {
                    $tipo_query = $db->prepare("SELECT TipoBombola FROM o_inventario WHERE IDBombola = ?");
                    $tipo_query->bind_param("i", $ciclo['IDBombola']);
                    $tipo_query->execute();
                    $tipo_result = $tipo_query->get_result();
                    $tipo = $tipo_result->fetch_assoc();

                    echo "<tr>
                        <td><i class='fas fa-search' style='cursor: pointer;' data-toggle='modal' data-target='#movementModal' data-idbombola='" . $ciclo['IDBombola'] . "'></i></td>
                        <td>" . $ciclo['IDBombola'] . "</td>
                        <td>" . ($tipo ? $tipo['TipoBombola'] : 'N/A') . "</td>
                        <td>" . $ciclo['Destinazione'] . "</td>
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
                            <input id="IDBombola" name="IDBombola[]" class="form-control form-control-sm" placeholder="Barcode bombola">
                        </div>
                        <div class="col">
                            <label for="TipoBombola">Tipo</label>
                            <input id="TipoBombola" name="TipoBombola[]" class="form-control form-control-sm" placeholder="Barcode tipo">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-dismiss="modal">Chiudi</button>
                    <button type="submit" class="btn btn-success btn-sm" id="submitButton" name="submitButton">Salva</button>
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

</body>
<?php include('../config/include/footer.php'); ?>
</html>
