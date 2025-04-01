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

<div class="container">
    <h4 class="text-center text-success">Dettagli Servizio</h4>
    <hr>

    <table class="table table-bordered">
        <tbody>
        <tr>
            <th>Stato</th>
            <td><?= $dictionaryServizio[$row['StatoServizio']] ?></td>
        </tr>
        <tr>
            <th>Conferma</th>
            <td><?= $dictionaryStatoTelLabel[$row['StatoTel']] ?></td>
        </tr>
        <tr>
            <th>Data</th>
            <td><?= $data_servizio ?></td>
        </tr>
        <tr>
            <th>Richiedente</th>
            <td><?= $richidente_servizio ?></td>
        </tr>
        <tr>
            <th>Contatto</th>
            <td><?= $contatto_servizio ?></td>
        </tr>
        <tr>
            <th>Partenza</th>
            <td><?= $partenza_servizio ?></td>
        </tr>
        <tr>
            <th>Destinazione</th>
            <td><?= $destinazione_servizio ?></td>
        </tr>
        <tr>
            <th>Tipo</th>
            <td><?= $dictionaryTipoServizio[$tipo_servizio] ?></td>
        </tr>
        <tr>
            <th>Mezzo richiesto</th>
            <td><?= $dictionaryTipoMezzo[$mezzorichiesto_servizio] ?></td>
        </tr>
        <tr>
            <th>Carrozzina CVTO</th>
            <td><?= $carrozzina_servizio ?></td>
        </tr>
        <tr>
            <th>Sedia a motore</th>
            <td><?= $sedia_servizio ?></td>
        </tr>
        <tr>
            <th>Informazioni Paziente</th>
            <td><?= $infopz_servizio ?></td>
        </tr>
        <tr>
            <th>Informazioni Servizio</th>
            <td><?= $infoser_servizio ?></td>
        </tr>
        <tr>
            <th>Tariffa</th>
            <td><?= $tariffa_servizio ?></td>
        </tr>
        <tr>
            <th>Equipaggio</th>
            <td><?= $equipaggio_servizio ?></td>
        </tr>
        <tr>
            <th>Mezzo Assegnato</th>
            <td><?= $mezzoassegnato_servizio ?></td>
        </tr>
        </tbody>
    </table>
    <div class="text-center">
        <a href="<?= htmlspecialchars($link_modifica) ?>" class="btn btn-primary btn-sm">
            ✏️ Modifica Richiesta
        </a>
    </div>
</div>
