<?php
global $db;
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

$cognome = $nome = $CodFiscale = $DataNascita = $IDComuneNascita = null;
$RilasciataDa = $NAut = $ScadenzaAutUltimoRetraining = null;

if (isset($_POST["LoginBTN"])) {
    $id = $_POST["matricolaOP"];
    $cf = $_POST["CodFiscaleOP"];

    $stmt = $db->prepare("
    SELECT r.IDUtente, r.Cognome, r.Nome, r.CodFiscale, r.DataNascita, r.Mail, r.Codice, c.DescComune,
           a.RilasciataDa, a.NAut, a.ScadenzaAutUltimoRetraining, a.DataInizio
    FROM rubrica r
    LEFT JOIN AUTISTI_PATENTI a ON r.IDUtente = a.IDUtente AND a.IDQualifica = 67
    LEFT JOIN Comuni c ON r.IDComuneNascita = c.IDComune
    WHERE r.Codice = ? AND r.CodFiscale = ?
    ");

    $stmt->bind_param("ss", $id, $cf);
    $stmt->execute();

    $result = $stmt->get_result();
    if ($result->num_rows > 0) {

        $row = $result->fetch_assoc();
        $mail= $row['Mail'];
        $cognome = $row['Cognome'];
        $nome = $row['Nome'];
        $CodFiscale = $row['CodFiscale'];
        $DataNascita = $row['DataNascita'];
        $IDComuneNascita = $row['DescComune'];
        $RilasciataDa = $row['RilasciataDa'];
        $NAut = $row['NAut'];
        $ScadenzaAutUltimoRetraining = $row['ScadenzaAutUltimoRetraining'];
        $DataInizio = $row['DataInizio'];
        $note = $row['note'];
        $Codice = $row['Codice'];

        $_SESSION['IDUtente'] = $row['IDUtente'];
    } else {
        echo "<script type='text/javascript'>alert('Accesso negato: dati non corrispondenti!');</script>";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="Paolo Randone">

    <title>AUTOCERTIFICAZIONE</title>
    <base href="/strumenti/autisti/">
    <?php require "../config/include/header.html"; ?>
    <link rel="stylesheet" href="../config/include/custom.css?v=<?= time() ?>">

    <style>

        .modal-content {
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
        }
        .modal-body {
            padding: 20px;
        }
        .modal-header,
        .modal-footer {
            padding: 15px 20px;
        }
        input {
            text-transform: uppercase;
        }
    </style>
    <script>
        window.onload = function() {
            <?php if (empty($cognome)) { ?>
            var modal = document.getElementById('modal3');
            modal.style.display = 'block';
            modal.classList.add('show');
            <?php } ?>
        };
    </script>
    <script>
        document.addEventListener('input', function(event) {
            if (event.target.tagName === 'INPUT' && event.target.type === 'text') {
                event.target.value = event.target.value.toUpperCase();
            }
        });
    </script>
</head>
<body>
<?php if (!empty($cognome)) { ?>
<div class="container mt-5 mb-5 p-4 bg-light rounded shadow lead">
    <form method="post" action="salva_autocertificazione.php">
        <input type="hidden" name="Cognome" value="<?= htmlspecialchars($cognome ?? ''); ?>">
        <input type="hidden" name="Nome" value="<?= htmlspecialchars($nome ?? ''); ?>">
        <input type="hidden" name="DescComune" value="<?= htmlspecialchars($IDComuneNascita ?? ''); ?>">
        <input type="hidden" name="DataNascita" value="<?= htmlspecialchars($DataNascita ?? ''); ?>">
        <input type="hidden" name="Mail" value="<?= htmlspecialchars($mail ?? ''); ?>">
        <input type="hidden" name="Codice" value="<?= htmlspecialchars($Codice ?? ''); ?>">

        <div class="mb-4">
            <div class="alert alert-danger" role="alert" style="text-align: center">
                Verificare la correttezza delle informazioni prima di inviare il form!
            </div>
        </div>
        <div class="mb-4">
            <p class="lead">
                Con la presente io sottoscritto
                <strong><?= htmlspecialchars($cognome); ?> <?= htmlspecialchars($nome); ?></strong>,
                nato a <strong><?= htmlspecialchars($IDComuneNascita); ?></strong> il <strong><?= htmlspecialchars($DataNascita); ?></strong>,
                Socio Volontario presso la Croce Verde Torino ODV,
                con riferimento alla patente categoria B di seguito indicata:
            </p>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="NAut" class="form-label">[5] Numero Patente:</label>
                <input type="text" class="form-control" id="NAut" name="NAut"
                       value="<?= htmlspecialchars($NAut ?? ''); ?>"
                       placeholder="Inserisci il numero della patente" required>
            </div>
            <div class="col-md-6 mb-3">
                <label for="RilasciataIl" class="form-label">[10] Rilasciata il:</label>
                <input type="date" class="form-control" id="RilasciataIl" name="RilasciataIl"
                       value="<?= htmlspecialchars($DataInizio ?? ''); ?>" required>
            </div>
            <div class="col-md-6 mb-3">
                <label for="RilasciataDa" class="form-label">[4c] Rilasciata da:</label>
                <input type="text" class="form-control" id="RilasciataDa" name="RilasciataDa"
                       value="<?= htmlspecialchars($RilasciataDa ?? ''); ?>"
                       placeholder="Ente che ha rilasciato la patente" required>
            </div>
            <div class="col-md-6 mb-3">
                <label for="ScadenzaAutUltimoRetraining" class="form-label">[11] Scadenza:</label>
                <input type="date" class="form-control" id="ScadenzaAutUltimoRetraining" name="ScadenzaAutUltimoRetraining"
                       value="<?= htmlspecialchars($ScadenzaAutUltimoRetraining ?? ''); ?>" required>
            </div>
        </div>
        <p style="text-align: center"><b>DICHIARO DI:</b><br> (spuntare solo se vero)</p>
        <div class="form-check mb-2">
            <input type="checkbox" class="form-check-input" id="Checkbox1" name="Checkbox1" value="1">
            <label class="form-check-label" for="Checkbox1">
                Non aver avuto la decurtazione completa dei punti della patente
            </label>
        </div>
        <div class="form-check mb-2">
            <input type="checkbox" class="form-check-input" id="Checkbox2" name="Checkbox2" value="1">
            <label class="form-check-label" for="Checkbox2">
                Non avere la patente di guida né sospesa né revocata
            </label>
        </div>
        <div class="mb-4">
            <textarea class="form-control" id="note" name="note" rows="3" placeholder="Eventuali annotazioni"></textarea>
        </div>
        <p>
            Inoltre mi impegno a comunicare prontamente alla Direzione dei Servizi, tramite il Gruppo Autisti,
            la perdita totale dei punti patente e ogni provvedimento ostativo alla guida di qualsiasi tipo
            (sospensione, sospensione breve, revoca).
        </p>
        <p>
            Una copia della presente sarà inviata all'indirizzo
            <?php if (!empty($mail)) { ?>
                <strong><?= htmlspecialchars($mail); ?></strong>
                <button type="button" class="btn btn-link btn-sm p-0 ms-2" id="editMailBtn" onclick="toggleMailInput()">(Modifica)</button>
            <?php } ?>
            <span id="mailInputWrapper" style="display: <?= empty($mail) ? 'inline' : 'none'; ?>;">
        <input type="email" class="form-control d-inline w-auto" name="Mail" id="MailInput" value="<?= htmlspecialchars($mail ?? ''); ?>" placeholder="Inserisci email" required>
    </span>
        </p>
        <hr>
        <button type="submit" class="btn btn-success btn-lg w-100">Invia Autocertificazione</button>
    </form>
    <?php } ?>

    <!-- MODAL LOGIN -->
    <div class="modal" id="modal3" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
            <div class="modal-content">
                <form method="post" action="">
                    <div class="modal-header">
                        <h6 class="modal-title" id="modal1Title">IDENTIFICAZIONE OPERATORE</h6>
                    </div>
                    <div class="modal-body">
                        <div class="input-group mb-3">
                            <input type="text" id="matricolaOP" name="matricolaOP" class="form-control form-control-sm" placeholder="Matricola" required>
                        </div>
                        <div class="input-group mb-3">
                            <input type="text" id="CodFiscaleOP" name="CodFiscaleOP" class="form-control form-control-sm" placeholder="Codice Fiscale" required>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-outline-success btn-sm" id="LoginBTN" name="LoginBTN">ACCEDI</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    function toggleMailInput() {
        const mailInputWrapper = document.getElementById('mailInputWrapper');
        const editMailBtn = document.getElementById('editMailBtn');

        if (mailInputWrapper.style.display === 'none') {
            mailInputWrapper.style.display = 'inline';
            editMailBtn.style.display = 'none';
        }
    }
</script>
</body>
</html>