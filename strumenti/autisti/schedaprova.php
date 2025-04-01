<?php
header('Access-Control-Allow-Origin: *');
session_start();
include "../config/config.php";
include "config/include/destinatari.php";
include "config/include/dictionary.php";

if (!isset($_SESSION['Livello']) && isset($_GET['livello'])) {
    $_SESSION['Livello'] = intval($_GET['livello']);
}

if (!isset($_GET['id']) ) {
    die("ID richiesta non valido.");
}

$id_richiesta = intval($_GET['id']);

$stmt = $db->prepare("
    SELECT 
        rubrica.Cognome, 
        rubrica.Nome, 
        rubrica.IDFiliale,
        rubrica.IDSquadra,
        AUTISTI_RICHIESTE.IDProva,
        AUTISTI_RICHIESTE.StatoRichiesta,
        AUTISTI_RICHIESTE.DataProva,
        AUTISTI_RICHIESTE.OraProva,
        AUTISTI_RICHIESTE.LuogoProva,
        AUTISTI_RICHIESTE.Esaminatore,
        AUTISTI_RICHIESTE.NoteProva,
        AUTISTI_RICHIESTE.EsitoProva,
        AUTISTI_RICHIESTE.NoteEsame
    FROM AUTISTI_RICHIESTE
    JOIN rubrica ON rubrica.IDUtente = AUTISTI_RICHIESTE.IDUtente
    WHERE AUTISTI_RICHIESTE.IDRichiesta = ?
");

$stmt->bind_param("i", $id_richiesta);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("<p class='text-center'>Nessuna informazione disponibile</p>");
}

$row = $result->fetch_assoc();
$stmt->close();

$data_prova = !empty($row['DataProva']) ? date("d/m/Y", strtotime($row['DataProva'])) : "Non assegnata";
$ora_prova = !empty($row['OraProva']) ? date("H:i", strtotime($row['OraProva'])) : "Non assegnata";
$luogo_prova = !empty($row['LuogoProva']) ? htmlspecialchars($row['LuogoProva']) : "Non assegnato";
$esaminatore = !empty($row['Esaminatore']) ? htmlspecialchars($row['Esaminatore']) : "Non assegnato";
$note_prova = !empty($row['NoteProva']) ? htmlspecialchars($row['NoteProva']) : "Nessuna nota";
$esito_prova = !empty($row['EsitoProva']) ? htmlspecialchars($row['EsitoProva']) : "Non valutato";
$esito_note = !empty($row['NoteEsame']) ? htmlspecialchars($row['NoteEsame']) : "Non valutato";

$link_modifica = "modifica_richiesta.php?id=" . urlencode($id_richiesta);

$link_valutazione = "";

if ($row['IDProva'] == 1) {
    $link_valutazione = "https://croceverde.org/strumenti/autisti/valutazione_rientri.php?id=" . urlencode($id_richiesta);
} elseif ($row['IDProva'] == 2) {
    $link_valutazione = "https://croceverde.org/strumenti/autisti/valutazione_normali.php?id=" . urlencode($id_richiesta);
}  else {
    $link_valutazione = "https://croceverde.org/strumenti/autisti/valutazione_urgenze.php?id=" . urlencode($id_richiesta);
}
?>

<div class="container-fluid">
    <h4 class="text-center text-success">Dettagli Prova di Guida</h4>
    <hr>

    <table class="table table-responsive-sm" >
        <tbody>
        <tr>
            <th>Candidato</th>
            <td><?= htmlspecialchars($row['Cognome']) . " " . htmlspecialchars($row['Nome']) ?></td>
        </tr>
        <tr>
            <th>Sezione - Squadra</th>
            <td><?= htmlspecialchars($dictionaryFiliale[$row['IDFiliale']]) . " - " . htmlspecialchars($dictionarySquadra[$row['IDSquadra']]) ?></td>
        </tr>
        <tr>
            <th>Tipo di Prova</th>
            <td><?= $tipoProvaDict[$row['IDProva']] ?? 'Sconosciuto' ?></td>
        </tr>
        <tr>
            <th>Stato</th>
            <td><?= $dictionaryStato[$row['StatoRichiesta']] ?? 'Sconosciuto' ?></td>
        </tr>
        <tr>
            <th>Data Prova</th>
            <td><?= $data_prova ?></td>
        </tr>
        <tr>
            <th>Ora Prova</th>
            <td><?= $ora_prova ?></td>
        </tr>
        <tr>
            <th>Luogo</th>
            <td><?= $luogo_prova ?></td>
        </tr>
        <tr>
            <th>Esaminatore</th>
            <td><?= $esaminatore ?></td>
        </tr>
        <tr>
            <th>Note</th>
            <td><?= $note_prova ?></td>
        </tr>
        <tr>
            <th>Esito Finale</th>
            <td><strong><?= $dictionaryEsito[$esito_prova] ?></strong></td>
        </tr>
        <tr>
            <th>Commenti</th>
            <td><?= $esito_note ?></td>
        </tr>
        </tbody>
    </table>
    <?php
    if (
        $row['StatoRichiesta'] != 3 &&
        (
            $_SESSION['Livello'] == 29 ||
            ($_SESSION['Livello'] == 30 && $row['IDProva'] == 3)
        )
    ): ?>
        <div class="text-center">
            <a href="<?= htmlspecialchars($link_modifica) ?>" TARGET="_blank" class="btn btn-primary btn-sm">
                ✏️ Modifica Richiesta
            </a>
        </div>
    <?php endif; ?>


    <br>
    <?php
    if (
        $row['StatoRichiesta'] == 2 &&
        (
            $_SESSION['Livello'] == 29 ||
            ($_SESSION['Livello'] == 30 && $row['IDProva'] == 3)
        )
    ): ?>
        <div class="text-center">
            <a href="<?= htmlspecialchars($link_valutazione) ?>" TARGET="_blank" class="btn btn-primary btn-sm">
                📝️ Valuta Esame
            </a>
        </div>
    <?php endif; ?>

</div>
