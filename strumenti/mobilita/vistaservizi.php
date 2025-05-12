<?php
header('Access-Control-Allow-Origin: *');
session_start();

include "../config/config.php";
include "config/include/destinatari.php";
include "config/include/dictionary.php";
global $db;
?>

    <!DOCTYPE html>
    <html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="author" content="Paolo Randone">
        <title>Elenco Servizi Mobilità</title>

        <?php require "../config/include/header.html"; ?>

        <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="../config/include/custom.css?v=<?= time() ?>">
        <style>
            .editable-cell {
                background-color: #fff9c4;
                border: 1px dashed #aaa;
            }
            .cursor-pointer {
                cursor: pointer;
            }
            .modified-cell {
                background-color: #d4edda !important; /* verde chiaro */
                border: 2px solid #28a745;
            }

        </style>
        <script>
            $(document).ready(function() {
                var table = $('#myTable').DataTable({
                    "paging": false,
                    "language": { url: '../config/include/js/package.json' },
                    "pagingType": "simple",
                    "pageLength": 50,
                });

                $('#filterPastRecords').on('click', function() {
                    var today = new Date();
                    today.setHours(0, 0, 0, 0);
                    var isActive = $(this).hasClass('active');

                    if (isActive) {
                        $.fn.dataTable.ext.search.pop(); // Rimuove il filtro attuale
                        table.draw();
                        $(this).removeClass('active btn-danger').addClass('btn-outline-primary').text('Nascondi');
                    } else {
                        $.fn.dataTable.ext.search.push(
                            function(settings, data, dataIndex) {
                                var rowDate = data[2].trim();

                                if (rowDate.match(/^\d{2}\/\d{2}\/\d{4} \d{2}:\d{2}$/)) {
                                    var dateParts = rowDate.split(' ')[0].split('/');
                                    var formattedRowDate = new Date(dateParts[2], dateParts[1] - 1, dateParts[0]);

                                    return formattedRowDate >= today;
                                }
                                return true;
                            }
                        );
                        table.draw();
                        $(this).addClass('active btn-danger').removeClass('btn-outline-primary').text('Storico');
                    }
                }).click();

                $('.dropdown-menu .dropdown-item').on('click', function() {
                    var selectedStatus = $(this).data('value');
                    $('.dropdown-toggle').text($(this).text());

                    if (selectedStatus === "") {
                        table.column(1).search('').draw();
                    } else {
                        table.column(1).search(selectedStatus, true, false).draw();
                    }
                });

                //modifica in-line
                let isEditing = false;

                $('#editToggle').on('click', function () {
                    isEditing = !isEditing;

                    if (isEditing) {
                        $('#saveTable').removeClass('d-none');

                        $('.editable').each(function () {
                            const original = $(this).text().trim();
                            $(this).data('original', original)
                                .attr('contenteditable', true)
                                .addClass('editable-cell');
                        });

                        $('.editable').on('input', function () {
                            const current = $(this).text().trim();
                            const original = $(this).data('original');

                            if (current !== original) {
                                $(this).addClass('modified-cell');
                            } else {
                                $(this).removeClass('modified-cell');
                            }
                        });

                        $(this).removeClass('btn-outline-warning').addClass('btn-outline-danger')
                            .html('<i class="fas fa-times fa-fw me-1"></i> Annulla');

                    } else {
                        $('#saveTable').addClass('d-none');

                        $('.editable').each(function () {
                            const original = $(this).data('original');
                            $(this).text(original)
                                .removeAttr('contenteditable')
                                .removeClass('editable-cell modified-cell');
                        });

                        $(this).removeClass('btn-outline-danger').addClass('btn-outline-warning')
                            .html('<i class="fas fa-edit fa-fw me-1"></i> Modifica');
                    }
                });

                $('#saveTable').on('click', function () {
                    const updatedData = [];

                    $('#myTable tbody tr').each(function () {
                        const $row = $(this);
                        const id = $row.data('id');
                        if (!id) return;

                        const richiedente = $row.find('.richiedente').text().trim();
                        const partenza = $row.find('.partenza').text().trim();
                        const destinazione = $row.find('.destinazione').text().trim();
                        const mezzo = $row.find('.mezzo').text().trim();
                        const equipaggio = $row.find('.equipaggio').text().trim();

                        const richiedenteOld = $row.find('.richiedente').data('original');
                        const partenzaOld = $row.find('.partenza').data('original');
                        const destinazioneOld = $row.find('.destinazione').data('original');
                        const mezzoOld = $row.find('.mezzo').data('original');
                        const equipaggioOld = $row.find('.equipaggio').data('original');

                        if (
                            richiedente !== richiedenteOld ||
                            partenza !== partenzaOld ||
                            destinazione !== destinazioneOld ||
                            mezzo !== mezzoOld ||
                            equipaggio !== equipaggioOld
                        ) {
                            updatedData.push({
                                id,
                                richiedente,
                                partenza,
                                destinazione,
                                mezzo,
                                equipaggio
                            });
                        }
                    });

                    if (updatedData.length === 0) {
                        Swal.fire("Nessuna modifica", "Nessun dato è stato cambiato.", "info");
                        return;
                    }

                    $.ajax({
                        url: 'update_inline.php',
                        method: 'POST',
                        contentType: 'application/json',
                        data: JSON.stringify({ servizi: updatedData }),
                        success: function (response) {
                            Swal.fire("Successo", "Modifiche salvate correttamente!", "success");
                            // Reset
                            $('.editable').removeAttr('contenteditable')
                                .removeClass('editable-cell modified-cell');
                            $('#saveTable').addClass('d-none');
                            $('#editToggle').removeClass('btn-outline-danger').addClass('btn-outline-warning')
                                .html('<i class="fas fa-edit fa-fw me-1"></i> Modifica');
                            isEditing = false;
                        },
                        error: function () {
                            Swal.fire("Errore", "Errore durante il salvataggio", "error");
                        }
                    });
                });


                //fine
            });

            $(document).on('click', '.noterubrica', function (e) {
                e.preventDefault();
                var id = $(this).data("id");

                $.get("https://croceverde.org/strumenti/mobilita/schedaservizio.php", {id:id}, function (html) {
                    $('#modalnote').html(html);
                    $('.bd-note').modal('toggle');
                }).fail(function (msg) {
                    console.log("Errore nel caricamento della scheda: ", msg);
                });
            });

        </script>
    </head>
    <body>
    <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                Swal.fire({
                    title: "Successo!",
                    text: "Il servizio è stato inserito correttamente.",
                    icon: "success",
                    confirmButtonColor: "#007bff",
                    confirmButtonText: "OK"
                }).then(() => {
                    const newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
                    window.history.replaceState({}, document.title, newUrl);
                });
            });

        </script>
    <?php endif; ?>

    <?php if (isset($_SESSION['livello'])):?>
        <div class="container-fluid">
            <nav aria-label="breadcrumb" class="sfondo">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="https://croceverde.org/gestionale/index.php" style="color: #078f40">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Mobilità</li>
                </ol>
            </nav>
        </div>
    <?php endif; ?>
    <br>
    <div class="container-fluid px-2 mb-4">
        <div class="card card-cv">
            <div class="mb-3 d-flex align-items-center gap-2 flex-wrap">
                <button id="filterPastRecords" class="btn btn-outline-primary">
                    <i class="fas fa-history fa-fw me-1"></i> Nascondi
                </button>

                <a role="button" class="btn btn-secondary" href="form_mobilita.php" target="_blank">
                    <i class="far fa-plus fa-fw me-1"></i> Nuova
                </a>

                <div class="dropdown">
                    <button class="btn btn-success dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-filter fa-fw me-1"></i> Stato
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#" data-value="">TUTTI</a></li>
                        <li><a class="dropdown-item" href="#" data-value="Richiesto">Richiesto</a></li>
                        <li><a class="dropdown-item" href="#" data-value="Accettato">Accettato</a></li>
                        <li><a class="dropdown-item" href="#" data-value="Confermato">Confermato</a></li>
                        <li><a class="dropdown-item" href="#" data-value="Rifiutato">Rifiutato</a></li>
                        <li><a class="dropdown-item" href="#" data-value="Annullato">Annullato</a></li>
                        <li><a class="dropdown-item" href="#" data-value="Chiuso">Chiuso</a></li>
                    </ul>
                </div>

                <div class="ms-auto">
                    <a role="button" class="btn btn-outline-success d-inline-flex align-items-center" data-bs-toggle="modal" data-bs-target="#exportModal">
                        <i class="fas fa-file-excel fa-fw me-1"></i> Esporta
                    </a>
                    <button id="editToggle" class="btn btn-outline-warning d-inline-flex align-items-center">
                        <i class="fas fa-edit fa-fw me-1"></i> Modifica
                    </button>
                    <button id="saveTable" class="btn btn-success d-none d-inline-flex align-items-center">
                        <i class="fas fa-save fa-fw me-1"></i> Salva
                    </button>
                </div>
            </div>

            <div class="table-wrapper">
                <div class="table-responsive">
                    <table class="table table-hover table-sm sfondo" id="myTable">
                        <thead>
                        <tr>
                            <th scope="col" style="text-align: center">#</th>
                            <th scope="col" style="text-align: center">STATO</th>
                            <th scope="col">DATA</th>
                            <th scope="col">RICHIEDENTE</th>
                            <th scope="col">PARTENZA</th>
                            <th scope="col">DESTINAZIONE</th>
                            <th scope="col">EQUIPAGGIO</th>
                            <th scope="col" style="text-align: center">MEZZO</th>
                            <th scope="col" style="text-align: center">TEL</th>

                        </tr>
                        </thead>
                        <tbody>

                        <?php
                        $query = " SELECT  * FROM mobilita order by DataOraServizio";

                        $select = $db->query($query);

                        if ($select->num_rows > 0) {
                            while ($ciclo = $select->fetch_assoc()) {
                                $data_servizio = date("d/m/Y H:i", strtotime($ciclo['DataOraServizio']));
                                ?>
                                <tr data-id="<?= $ciclo['IDServizio'] ?>">
                                    <td class="align-middle ">
                                        <a href="#" class="btn btn-sm btn-outline-info noterubrica" data-id="<?= htmlspecialchars($ciclo['IDServizio']) ?>"
                                           style="font-size: 14px; padding: 5px 10px;">
                                            📄
                                        </a>
                                    </td>
                                    <td class="align-middle text-center">
                                        <?php
                                        $stato = $ciclo['StatoServizio'];
                                        $badgeClass = '';
                                        $icon = '';
                                        $textColor = 'text-black';

                                        switch ($stato) {
                                            case 1:
                                                $badgeClass = 'badge-warning';
                                                $icon = '⏳';
                                                $statoLabel = 'Richiesto';
                                                break;
                                            case 2:
                                                $badgeClass = 'badge-success';
                                                $icon = '✔️';
                                                $statoLabel = 'Accettato';
                                                break;
                                            case 3:
                                                $badgeClass = 'badge-info';
                                                $icon = '✅';
                                                $statoLabel = 'Confermato';
                                                break;
                                            case 4:
                                                $badgeClass = 'badge-danger';
                                                $icon = '❌';
                                                $statoLabel = 'Rifiutato';
                                                break;
                                            case 5:
                                                $badgeClass = 'badge-secondary';
                                                $icon = '🚫';
                                                $statoLabel = 'Annullato';
                                                break;
                                            case 6:
                                                $badgeClass = 'badge-success';
                                                $icon = '🔒';
                                                $statoLabel = 'Chiuso';
                                                break;
                                            default:
                                                $badgeClass = 'badge-secondary';
                                                $icon = '❓';
                                                $statoLabel = 'Sconosciuto';
                                        }
                                        ?>
                                        <span class="badge <?= $badgeClass ?> <?= $textColor?>" style="font-size: 14px; padding: 6px 10px;">
                    <?= $icon . " " . $statoLabel ?>
                </span>
                                    </td>
                                    <td class="align-middle"><?= $data_servizio ?></td>
                                    <td class="align-middle editable richiedente"><?= htmlspecialchars($ciclo['Richiedente']) ?></td>
                                    <td class="align-middle editable partenza"><?= htmlspecialchars($ciclo['Partenza']) ?></td>
                                    <td class="align-middle editable destinazione"><?= htmlspecialchars($ciclo['Destinazione']) ?></td>
                                    <td class="align-middle editable equipaggio"><?= htmlspecialchars($ciclo['Equipaggio']) ?></td>
                                    <td class="align-middle editable mezzo " style="text-align: center"><?= htmlspecialchars($ciclo['MezzoAssegnato']) ?></td>
                                    <td class="align-middle" style="text-align: center"><?= htmlspecialchars($dictionaryStatoTel[$ciclo['StatoTel']]) ?></td>

                                </tr>
                                <?php
                            }
                        } else {
                            echo "<tr><td class='text-center'>NA</td><td class='text-center'>NA</td><td class='text-center'>NA</td><td class='text-center'>NA</td><td class='text-center'>NA</td><td class='text-center'>NA</td><td class='text-center'>NA</td><td class='text-center'>NA</td><td class='text-center'>NA</td></tr>";
                        }
                        ?>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal bd-note" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body" id="modalnote"></div>
            </div>
        </div>
    </div>

    <!-- MODALE ESPORTAZIONE -->
    <div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content card-cv">
                <div class="modal-header">
                    <h5 class="modal-title" id="exportModalLabel">Esporta in Excel</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Chiudi"></button>
                </div>
                <div class="modal-body">
                    <form id="exportForm" method="GET" action="export_mobilita.php" target="_blank">
                        <div class="mb-3">
                            <label for="dataFiltro" class="form-label">Periodo</label>
                            <select class="form-select" name="dataFiltro" id="dataFiltro">
                                <option value="futuri" selected>Programmati</option>
                                <option value="tutti">Tutti</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="statoFiltro" class="form-label">Stato del servizio</label>
                            <select class="form-select" name="statoFiltro" id="statoFiltro">
                                <option value="" selected>Tutti</option>
                                <option value="1">Richiesto</option>
                                <option value="2">Accettato</option>
                                <option value="3">Confermato</option>
                                <option value="4">Rifiutato</option>
                                <option value="5">Annullato</option>
                                <option value="6">Chiuso</option>
                            </select>
                        </div>
                        <div class="modal-footer px-0">
                            <button type="submit" class="btn btn-success">Esporta</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    </body>
    </html>
<?php
