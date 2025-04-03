<?php
header('Access-Control-Allow-Origin: *');
session_start();
include "../config/config.php";
include "config/include/destinatari.php";
include "config/include/dictionary.php";
global $db;

if (!isset($_GET['id']) ) {
    die("ID richiesta non valido.");
}

$id_richiesta = intval($_GET['id']);

$stmt = $db->prepare(" SELECT  * FROM mobilita WHERE IDServizio=?");

$stmt->bind_param("i", $id_richiesta);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("<p class='text-center'>Nessuna informazione disponibile</p>");
}

$row = $result->fetch_assoc();
$stmt->close();

$data_servizio = !empty($row['DataOraServizio']) ? date("d/m/Y H:i", strtotime($row['DataOraServizio'])) : "Non assegnata";
$stato_servizio = !empty($row['StatoServizio']) ? htmlspecialchars($row['StatoServizio']) : "Non assegnato";
$stato_telefonata = !empty($row['StatoTel']) ? htmlspecialchars($row['StatoTel']) : "Non assegnato";
$richidente_servizio = !empty($row['Richiedente']) ? htmlspecialchars($row['Richiedente']) : "Non assegnato";
$contatto_servizio = !empty($row['Contatto']) ? htmlspecialchars($row['Contatto']) : "Non assegnato";
$partenza_servizio = !empty($row['Partenza']) ? htmlspecialchars($row['Partenza']) : "Non assegnato";
$destinazione_servizio = !empty($row['Destinazione']) ? htmlspecialchars($row['Destinazione']) : "Non assegnato";
$tipo_servizio = !empty($row['TipoServizio']) ? htmlspecialchars($row['TipoServizio']) : "Non assegnato";
$mezzorichiesto_servizio = !empty($row['MezzoRichiesto']) ? htmlspecialchars($row['MezzoRichiesto']) : "Non assegnato";
$mezzoassegnato_servizio = !empty($row['MezzoAssegnato']) ? htmlspecialchars($row['MezzoAssegnato']) : "Non assegnato";
$carrozzina_servizio = !empty($row['Carrozzina']) ? "SI" : "NO";
$sedia_servizio = !empty($row['SediaMotore']) ? "SI" : "NO";
$infopz_servizio = !empty($row['InfoPaziente']) ? htmlspecialchars($row['InfoPaziente']) : "***";
$infoser_servizio = !empty($row['InfoServizio']) ? htmlspecialchars($row['InfoServizio']) : "***";
$tariffa_servizio = !empty($row['Tariffa']) ? htmlspecialchars($row['Tariffa']) : "Non assegnato";
$equipaggio_servizio = !empty($row['Equipaggio']) ? htmlspecialchars($row['Equipaggio']) : "Non assegnato";

$link_modifica = "modifica_servizio.php?id=" . urlencode($id_richiesta);

?>

<div class="container-fluid px-2">
    <div class="card card-cv p-4 mb-3">
        <h4 class="text-center text-success mb-4">Dettagli Servizio</h4>

        <form class="row g-3">

            <div class="col-12">
                <label class="form-label fw-semibold text-muted">Stato</label>
                <div class="form-control" readonly><?= $dictionaryServizio[$row['StatoServizio']] ?></div>
            </div>

            <div class="col-12">
                <label class="form-label fw-semibold text-muted">Conferma</label>
                <div class="form-control" readonly><?= $dictionaryStatoTelLabel[$row['StatoTel']] ?></div>
            </div>

            <div class="col-12">
                <label class="form-label fw-semibold text-muted">Data</label>
                <div class="form-control" readonly><?= $data_servizio ?></div>
            </div>

            <div class="col-12">
                <label class="form-label fw-semibold text-muted">Richiedente</label>
                <div class="form-control" readonly><?= $richidente_servizio ?></div>
            </div>

            <div class="col-12">
                <label class="form-label fw-semibold text-muted">Contatto</label>
                <div class="form-control" readonly><?= $contatto_servizio ?></div>
            </div>

            <div class="col-12">
                <label class="form-label fw-semibold text-muted">Partenza</label>
                <div class="form-control" readonly><?= $partenza_servizio ?></div>
            </div>

            <div class="col-12">
                <label class="form-labe fw-semibold text-mutedl">Destinazione</label>
                <div class="form-control" readonly><?= $destinazione_servizio ?></div>
            </div>

            <div class="col-12">
                <label class="form-label fw-semibold text-muted">Tipo</label>
                <div class="form-control" readonly><?= $dictionaryTipoServizio[$tipo_servizio] ?></div>
            </div>

            <div class="col-12">
                <label class="form-label fw-semibold text-muted">Mezzo richiesto</label>
                <div class="form-control" readonly><?= $dictionaryTipoMezzo[$mezzorichiesto_servizio] ?></div>
            </div>

            <div class="col-12">
                <label class="form-label fw-semibold text-muted">Mezzo assegnato</label>
                <div class="form-control" readonly><?= $mezzoassegnato_servizio ?></div>
            </div>

            <div class="col-12">
                <label class="form-label fw-semibold text-muted">Carrozzina CVTO</label>
                <div class="form-control" readonly><?= $carrozzina_servizio ?></div>
            </div>

            <div class="col-12">
                <label class="form-label fw-semibold text-muted">Sedia a motore</label>
                <div class="form-control" readonly><?= $sedia_servizio ?></div>
            </div>

            <div class="col-12">
                <label class="form-label fw-semibold text-muted">Informazioni Paziente</label>
                <div class="form-control" readonly><?= $infopz_servizio ?></div>
            </div>

            <div class="col-12">
                <label class="form-label fw-semibold text-muted">Informazioni Servizio</label>
                <div class="form-control" readonly><?= $infoser_servizio ?></div>
            </div>

            <div class="col-12">
                <label class="form-label fw-semibold text-muted">Tariffa</label>
                <div class="form-control" readonly><?= $tariffa_servizio ?></div>
            </div>

            <div class="col-12">
                <label class="form-label fw-semibold text-muted">Equipaggio</label>
                <div class="form-control" readonly><?= $equipaggio_servizio ?></div>
            </div>

        </form>

        <div class="text-center mt-4">
            <a href="<?= htmlspecialchars($link_modifica) ?>" class="btn btn-outline-cv">
                ✏️ Modifica Richiesta
            </a>
        </div>
    </div>
</div>


