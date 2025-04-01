<?php
header('Access-Control-Allow-Origin: *');
/**
 *
 * @author     Paolo Randone
 * @author     <paolo.randone@croceverde.org>
 * @version    1.0
 * @note         Powered for Croce Verde Torino. All rights reserved
 *
 */
session_start();

include "../config/config.php";
include "config/include/destinatari.php";
include "config/include/dictionary.php";
if (isset($_SESSION['email_error'])) {
    echo "<p class='error'>" . htmlspecialchars($_SESSION['email_error']) . "</p>";
    unset($_SESSION['email_error']);
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="Paolo Randone">
    <title>Elenco prove</title>

    <?php require "../config/include/header.html"; ?>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../config/include/custom.css?v=<?= time() ?>">

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
                    $.fn.dataTable.ext.search.pop();
                    table.draw();
                    $(this).removeClass('active btn-danger').addClass('btn-outline-primary').text('Nascondi Storico');
                } else {
                    $.fn.dataTable.ext.search.push(
                        function(settings, data, dataIndex) {
                            var rowDate = data[6].trim();

                            if (rowDate === "" || rowDate === "NA") {
                                return true;
                            }

                            if (rowDate.match(/^\d{2}\/\d{2}\/\d{4}$/)) {
                                var dateParts = rowDate.split('/');
                                var formattedRowDate = new Date(dateParts[2], dateParts[1] - 1, dateParts[0]);

                                return formattedRowDate >= today;
                            }
                            return true;
                        }
                    );
                    table.draw();
                    $(this).addClass('active btn-danger').removeClass('btn-outline-primary').text('Mostra Storico');
                }
            }).click();

            $('.dropdown-menu .dropdown-item').on('click', function() {
                var selectedStatus = $(this).data('value');
                $('.dropdown-toggle').text($(this).text());

                if (selectedStatus === "") {
                    table.column(5).search('').draw();
                } else {
                    table.column(5).search(selectedStatus, true, false).draw();
                }
            });
        });

        $(document).ready(function(){
            $('.noterubrica').on('click', function (e) {
                e.preventDefault();
                var id = $(this).attr("id");
                var livelloUtente = <?= json_encode($_SESSION['Livello']) ?>;
                //console.log("ID ricevuto:", id, "Lunghezza ID:", id.length);
                $.get("https://croceverde.org/strumenti/autisti/schedaprova.php", {id:id, livello: livelloUtente}, function (html) {
                    $('#modalnote').html(html);
                    $('.bd-note').modal('toggle');
                }).fail(function () {
                    Swal.fire("Errore", "Impossibile caricare la scheda" , "error");
                });
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
                text: "Azione eseguita correttamente",
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

<?php include "../config/include/navbar.php"; ?>
<div class="mb-3 text-center d-flex flex-wrap justify-content-center gap-2">
    <button id="filterPastRecords" class="btn btn-outline-cv btn-sm">Nascondi Storico</button>

    <div class="dropdown">
        <button class="btn btn-outline-cv btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            Seleziona Stato
        </button>
        <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="#" data-value="">Tutti gli Stati</a></li>
            <li><a class="dropdown-item" href="#" data-value="In Attesa">In attesa</a></li>
            <li><a class="dropdown-item" href="#" data-value="Confermata">Confermata</a></li>
            <li><a class="dropdown-item" href="#" data-value="Conclusa">Conclusa</a></li>
        </ul>
    </div>
</div>
<!-- CONTENUTO -->
<div class="container-fluid px-2 mb-4">
    <div class="card card-cv">
        <div class="table-wrapper">
            <div class="table-responsive">
                <table class="table table-hover table-sm sfondo" id="myTable">
                    <thead>
                    <tr>
                        <th scope="col">#ID</th>
                        <th scope="col">CANDIDATO</th>
                        <th scope="col">SEZIONE - SQ.</th>
                        <th scope="col">ESAMINATORE</th>
                        <th scope="col">PROVA</th>
                        <th scope="col" class="text-center">STATO</th>
                        <th scope="col">DATA</th>
                    </tr>
                    </thead>
                    <tbody>

                    <?php
                    $query = "
                SELECT 
                    rubrica.Cognome, 
                    rubrica.Nome, 
                    rubrica.IDFiliale, 
                    rubrica.IDSquadra,
                    AUTISTI_RICHIESTE.IDProva,
                    AUTISTI_RICHIESTE.IDRichiesta,
                    AUTISTI_RICHIESTE.StatoRichiesta,
                    AUTISTI_RICHIESTE.DataProva,
                    AUTISTI_RICHIESTE.Esaminatore
                FROM 
                    rubrica
                LEFT JOIN  
                    AUTISTI_RICHIESTE ON rubrica.IDUtente = AUTISTI_RICHIESTE.IDUtente
                WHERE  AUTISTI_RICHIESTE.IDRichiesta IS NOT NULL
            ";

                    $select = $db->query($query);

                    if ($select->num_rows > 0) {
                        while ($ciclo = $select->fetch_assoc()) {
                            //$datalabel = date("d/m/Y", strtotime($ciclo['DataProva']));
                            $datalabel = !empty($ciclo['DataProva']) ? date("d/m/Y", strtotime($ciclo['DataProva'])) : "";
                            ?>
                            <tr>
                                <td class="align-middle ">
                                    <a href="#" class="btn btn-sm btn-outline-info noterubrica" id="<?= trim($ciclo['IDRichiesta']) ?>" style="font-size: 14px; padding: 5px 10px;">
                                        📄
                                    </a>
                                </td>
                                <td class="align-middle"><?= htmlspecialchars($ciclo['Cognome'].' '.$ciclo['Nome']) ?></td>
                                <td class="align-middle"><?= $dictionaryFiliale[$ciclo['IDFiliale']] .' - '. $dictionarySquadra[$ciclo['IDSquadra']] ?? 'Sconosciuto' ?></td>
                                <td class="align-middle"><?= $ciclo['Esaminatore']?? 'N.A.' ?></td>
                                <td class="align-middle"><?= $tipoProvaDict[$ciclo['IDProva']] ?? 'Sconosciuto' ?></td>
                                <td class="align-middle text-center">
                                    <?php
                                    $stato = $ciclo['StatoRichiesta'];
                                    $badgeClass = '';
                                    $icon = '';
                                    $textColor = 'text-black';

                                    switch ($stato) {
                                        case 1:
                                            $badgeClass = 'badge-warning';
                                            $icon = '⏳';
                                            $statoLabel = 'In attesa';
                                            break;
                                        case 2:
                                            $badgeClass = 'badge-primary';
                                            $icon = '✅';
                                            $statoLabel = 'Confermata';
                                            break;
                                        case 3:
                                            $badgeClass = 'badge-success';
                                            $icon = '🏁';
                                            $statoLabel = 'Conclusa';
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
                                <td class="align-middle"><?= $datalabel ?></td>
                            </tr>
                            <?php
                        }
                    } else {
                        echo "<tr><td class='text-center'>NA</td><td class='text-center'>NA</td><td class='text-center'>NA</td><td class='text-center'>NA</td><td class='text-center'>NA</td><td class='text-center'>NA</td><td class='text-center'>NA</td></tr>";
                    }
                    ?>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- MODAL  -->
<div class="modal bd-note" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body" id="modalnote"></div>
        </div>
    </div>
</div>
<?php include "../config/include/footer.php"; ?>
</body>
</html>