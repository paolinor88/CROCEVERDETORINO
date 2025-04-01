<?php
header('Access-Control-Allow-Origin: *');
/**
 *
 * @author     Paolo Randone
 * @author     <paolo.randone@croceverde.org>
 * @version    8.2
 * @note         Powered for Croce Verde Torino. All rights reserved
 *
 */
session_start();

include "../config/config.php";
include "../config/include/destinatari.php";

if (!in_array($_SESSION['Livello'], [1, 20, 23, 24, 25, 26, 27, 28, 29, 30])) {
    header("Location: ../index.php");
    echo "<script type='text/javascript'>alert('Accesso negato');</script>";
    exit;
}

$dictionaryFiliale = array (
    1 => "Torino",
    2=> "Alpignano",
    3 => "Borgaro/Caselle",
    4 => "Ciriè",
    5 => "San Mauro",
    6 => "Venaria",
);

$dictionarySquadra = array (
    1 => "1",
    2=> "2",
    3 => "3",
    4 => "4",
    5 => "5",
    6 => "6",
    7 => "7",
    8 => "8",
    9 => "9",
    10 => "Sabato",
    11 => "Montagna",
    18 => "Diurno",
    19 => "Giovani",
    20 => "Serv. Generali",
    22 => "Serv. Cittadino",

);
//tutti gli autisti
$queryAutisti = "
    SELECT Codice, Cognome, Nome, IDFiliale, IDSquadra
    FROM rubrica
    WHERE (R = 3 OR MA = 4 OR MAU = 5)
      AND TIMESTAMPDIFF(YEAR, STR_TO_DATE(DataNascita, '%d/%m/%Y'), CURDATE()) < 75
";
$resultAutisti = $db->query($queryAutisti);
if ($resultAutisti === false) {
    die("Errore nella query: " . $db->error);
}
//senza base
$queryFormazione50 = "
    SELECT COUNT(*) AS conteggio
    FROM rubrica
    WHERE (R = 3 OR MA = 4 OR MAU = 5)
      AND Codice NOT IN (
          SELECT Codice
          FROM AUTISTI_FORMAZIONE
          WHERE IDQualifica = 50
      )
      AND TIMESTAMPDIFF(YEAR, STR_TO_DATE(DataNascita, '%d/%m/%Y'), CURDATE()) < 75
";

$resultFormazione50 = $db->query($queryFormazione50);
if ($resultFormazione50 === false) {
    die("Errore nella query Formazione 50: " . $db->error);
}
$countFormazione50 = $resultFormazione50->fetch_assoc()['conteggio'];
//senza plus
$queryFormazione62 = "
    SELECT COUNT(*) AS conteggio
    FROM rubrica
    WHERE (R = 3 OR MA = 4 OR MAU = 5)
      AND Codice NOT IN (
          SELECT Codice
          FROM AUTISTI_FORMAZIONE
          WHERE IDQualifica = 62
      )
      AND TIMESTAMPDIFF(YEAR, STR_TO_DATE(DataNascita, '%d/%m/%Y'), CURDATE()) < 75
";

$resultFormazione62 = $db->query($queryFormazione62);
if ($resultFormazione62 === false) {
    die("Errore nella query Formazione 62: " . $db->error);
}
$countFormazione62 = $resultFormazione62->fetch_assoc()['conteggio'];
// urg scaduta ok over
$queryUrgenzeScadute = "
    SELECT COUNT(*) AS conteggio
    FROM AUTISTI_URGENZE
    WHERE SCADENZAURGENZE < CURDATE()
      AND Codice NOT IN (
          SELECT Codice
          FROM AUTISTI_OVER
          WHERE SCADENZAOVER >= CURDATE()
      )
      AND Codice IN (
          SELECT Codice
          FROM rubrica
          WHERE TIMESTAMPDIFF(YEAR, STR_TO_DATE(DataNascita, '%d/%m/%Y'), CURDATE()) < 75
      )
";

$resultUrgenzeScadute = $db->query($queryUrgenzeScadute);
if ($resultUrgenzeScadute === false) {
    die("Errore nella query delle Urgenze Scadute: " . $db->error);
}
$countUrgenzeScadute = $resultUrgenzeScadute->fetch_assoc()['conteggio'];
// over ko
$queryOver65Scaduti = "
    SELECT COUNT(*) AS conteggio
    FROM AUTISTI_OVER
    WHERE SCADENZAOVER < CURDATE()
      AND Codice IN (
          SELECT Codice
          FROM rubrica
          WHERE TIMESTAMPDIFF(YEAR, STR_TO_DATE(DataNascita, '%d/%m/%Y'), CURDATE()) < 75
      )
";

$resultOver65Scaduti = $db->query($queryOver65Scaduti);
$countOver65Scaduti = $resultOver65Scaduti->fetch_assoc()['conteggio'];

$queryTotalAutisti = "
    SELECT COUNT(*) AS total
    FROM rubrica
    WHERE (R = 3 OR MA = 4 OR MAU = 5)
      AND TIMESTAMPDIFF(YEAR, STR_TO_DATE(DataNascita, '%d/%m/%Y'), CURDATE()) < 75
";

$resultTotalAutisti = $db->query($queryTotalAutisti);
if ($resultTotalAutisti === false) {
    die("Errore nella query Totale Autisti: " . $db->error);
}
$totalAutisti = $resultTotalAutisti->fetch_assoc()['total'];

function calculatePercentage($count, $total) {
    if ($total > 0) {
        return round(($count / $total) * 100, 2);
    } else {
        return 0;
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="Paolo Randone">
    <title>Report autisti</title>

    <?php require "../config/include/header.html"; ?>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../config/include/custom.css?v=<?= time() ?>">

    <script>
        $(document).ready(function() {
            var dataTables = $('#myTable').DataTable({
                "paging": false,
                "language": {url: '../config/include/js/package.json'},
                "order": [[1, "asc"]],
                searching: false,
                "columnDefs": [
                    {
                        "targets": [ 0 ],
                        "visible": true,
                        "searchable": true,
                        "orderable": true,
                    },
                    {
                        "targets": [ 3,4,5,6 ],
                        "visible": true,
                        "searchable": true,
                        "orderable": false,
                    },
                ],
            });

            var lastType = '';

            $('.count-link').click(function(e) {
                e.preventDefault();
                var type = $(this).data('type');
                lastType = type; // Memorizza il tipo di conteggio selezionato
                updateTable(type);
            });

            function updateTable(type) {
                var filiale = $('.btn-filiale.btn-secondary').data('filiale') || '';
                var squadra = $('.btn-squadra.btn-secondary').data('squadra') || '';
                $.ajax({
                    url: 'fetch_data.php',
                    type: 'GET',
                    data: {
                        type: type,
                        IDFiliale: filiale,
                        IDSquadra: squadra
                    },
                    success: function(response) {
                        $('#table-body').html(response);
                    }
                });
            }

            // FILTRI FILIALE
            $('.btn-filiale').on('click', function () {
                $(this).siblings().removeClass("btn-secondary").addClass("btn-outline-secondary");
                $(this).removeClass("btn-outline-secondary").addClass("btn-secondary");
                if (lastType) {
                    updateTable(lastType);
                }
            });

            // FILTRI SQUADRA
            $('.btn-squadra').on('click', function () {
                $(this).siblings().removeClass("btn-secondary").addClass("btn-outline-secondary");
                $(this).removeClass("btn-outline-secondary").addClass("btn-secondary");
                if (lastType) {
                    updateTable(lastType);
                }
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#openExportModal').on('click', function() {
                $('#exportModal').modal('show');
            });
            $('#exportExcel').on('click', function() {
                var filiale = $('#filialeSelect').val();
                var squadra = $('#squadraSelect').val();

                $('#exportModal').modal('hide');

                window.location.href = 'export_autisti.php?filiale=' + filiale + '&squadra=' + squadra;
            });
        });
    </script>
</head>
<body>

<?php include "../config/include/navbar.php"; ?>

<!-- CONTENUTO -->
<div class="container-fluid px-2 mb-4">
    <div class="card card-cv">
        <div class="row px-3">
            <div class="col-md-6">
                <p>Autisti che devono ancora fare il Corso Pratico Base:
                    <a href="#" class="count-link" data-type="formazione50"><?= $countFormazione50; ?></a>
                    (<?= calculatePercentage($countFormazione50, $totalAutisti); ?>%)
                </p>
                <p>Autisti che devono ancora fare il Corso Pratico Plus:
                    <a href="#" class="count-link" data-type="formazione62"><?= $countFormazione62; ?></a>
                    (<?= calculatePercentage($countFormazione62, $totalAutisti); ?>%)
                </p>
            </div>
            <div class="col-md-6">
                <p>Autisti con abilitazione URGENZE scaduta:
                    <a href="#" class="count-link" data-type="urgenze"><?= $countUrgenzeScadute; ?></a>
                    (<?= calculatePercentage($countUrgenzeScadute, $totalAutisti); ?>%)
                </p>
                <p>Autisti con abilitazione OVER 65 scaduta:
                    <a href="#" class="count-link" data-type="over65"><?= $countOver65Scaduti; ?></a>
                    (<?= calculatePercentage($countOver65Scaduti, $totalAutisti); ?>%)
                </p>
            </div>
        </div>
        <div class="text-center mb-3">
            <button id="openExportModal" class="btn btn-success btn-sm" <?php if ($_SESSION['Livello'] === 30) echo 'disabled'; ?>>Esporta in Excel</button>
        </div>
        <div class="text-center mb-3">
            <div class="btn-group btn-group-sm" role="group">

                <?php foreach ($dictionaryFiliale as $id => $name): ?>
                    <button type="button" class="btn btn-outline-secondary btn-filiale" data-filiale="<?= $id; ?>"><?= $name; ?></button>
                <?php endforeach; ?>
                <button type="button" class="btn btn-outline-secondary btn-filiale" data-filiale="">TUTTE</button>
            </div>
        </div>
        <div class="text-center mb-4">
            <div class="btn-group btn-group-sm" role="group">

                <?php foreach ($dictionarySquadra as $id => $name): ?>
                    <button type="button" class="btn btn-outline-secondary btn-squadra" data-squadra="<?= $id; ?>"><?= $name; ?></button>
                <?php endforeach; ?>
                <button type="button" class="btn btn-outline-secondary btn-squadra" data-squadra="">TUTTE</button>
            </div>
        </div>
        <div class="table-responsive-sm">
            <table class="table table-hover table-sm sfondo" id="myTable">
                <thead>
                <tr>
                    <th>Codice</th>
                    <th>Cognome</th>
                    <th>Nome</th>
                    <th>Sezione</th>
                    <th>Squadra</th>
                </tr>
                </thead>
                <tbody id="table-body">

                </tbody>
            </table>
        </div>
    </div>
</div>
<!-- MODALE EXPORT -->
<div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exportModalLabel">Seleziona Sezione e Squadra</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="exportForm">
                    <div class="mb-3">
                        <label for="filialeSelect" class="form-label">Sezione</label>
                        <select class="form-select form-select-sm" id="filialeSelect" name="filiale">
                            <option value="">Tutte</option>
                            <?php foreach ($dictionaryFiliale as $id => $name): ?>
                                <option value="<?= $id ?>"><?= $name ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="squadraSelect" class="form-label">Squadra</label>
                        <select class="form-select form-select-sm" id="squadraSelect" name="squadra">
                            <option value="">Tutte</option>
                            <?php foreach ($dictionarySquadra as $id => $name): ?>
                                <option value="<?= $id ?>"><?= $name ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Annulla</button>
                <button type="button" class="btn btn-success btn-sm" id="exportExcel">Esporta</button>
            </div>
        </div>
    </div>
</div>
</body>
<?php include "../config/include/footer.php"; ?>
</html>