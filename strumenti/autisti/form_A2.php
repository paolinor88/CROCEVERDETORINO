<?php
global $db;
header('Access-Control-Allow-Origin: *');
/**
 *
 * @author     Paolo Randone
 * @version    5.0
 * @note       Powered for Croce Verde Torino. All rights reserved
 *
 */
session_start();

include "../config/config.php";
include "config/include/destinatari.php";
include "config/include/dictionary.php";

$livello=$_SESSION['livello'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $richiedente_id = $_POST['richiedente'] ?? null;
    $candidato_id = $_POST['candidato'] ?? null;
    $tipoprova = $_POST['tipoprova'] ?? null;
    $tipoprova_label = $tipoProvaDict[$tipoprova] ?? 'Non specificato';
    $tipopatente = $_POST['tipopatente'] ?? null;
    $NAut = $_POST['NAut'] ?? null;
    $RilasciataIl = isset($_POST['RilasciataIl']) ? date("d/m/Y", strtotime($_POST['RilasciataIl'])) : null;
    $ScadenzaAutUltimoRetraining = isset($_POST['ScadenzaAutUltimoRetraining']) ? date("d/m/Y", strtotime($_POST['ScadenzaAutUltimoRetraining'])) : null;
    $RilasciataDa = $_POST['RilasciataDa'] ?? null;
    $giudizio = $_POST['giudizio'] ?? 'Nessun giudizio';

    $stmt = $db->prepare("SELECT Cognome, Nome, IDFiliale, IDSquadra FROM rubrica WHERE IDUtente = ?");
    $stmt->bind_param("i", $richiedente_id);
    $stmt->execute();
    $stmt->bind_result($richiedente_cognome, $richiedente_nome, $richidente_filiale, $richiedente_squadra);
    $stmt->fetch();
    $stmt->close();
    $richiedente = trim("$richiedente_cognome $richiedente_nome");
    $IDRichiedente = $richiedente_id;

    $stmt = $db->prepare("SELECT Cognome, Nome, IDFiliale, IDSquadra, Codice, Mail, DataNascita FROM rubrica WHERE IDUtente = ?");
    $stmt->bind_param("i", $candidato_id);
    $stmt->execute();
    $stmt->bind_result($candidato_cognome, $candidato_nome, $candidato_filiale, $candidato_squadra, $candidato_codice, $candidato_mail, $candidato_datanascita);
    $stmt->fetch();
    $stmt->close();
    $candidato = trim("$candidato_cognome $candidato_nome");
    $candidato_stringa = htmlspecialchars($candidato)." [".htmlspecialchars($dictionaryFiliale[$candidato_filiale])." - ".htmlspecialchars($dictionarySquadra[$candidato_squadra])."]";
    function sanitizeFileName($string) {
        $string = preg_replace('/[\'"]/', '', $string);
        $string = preg_replace('/\s+/', '_', $string);
        $string = preg_replace('/[^A-Za-z0-9_\-]/', '', $string);
        return $string;
    }

    $stmt = $db->prepare("INSERT INTO AUTISTI_RICHIESTE (IDUtente, IDProva, IDRichiedente) VALUES (?, ?, $IDRichiedente)");
    if (!$stmt) {
        die("Errore nella preparazione della query: " . $db->error);
    }
    $stmt->bind_param("ii", $candidato_id, $tipoprova);

    if ($stmt->execute()) {
        $id_richiesta = $stmt->insert_id;
    } else {
        die("Errore nell'inserimento: " . $stmt->error);
    }
    $stmt->close();

    $file_path = null;
    if ($tipoprova == "2" && isset($_FILES['file_upload']) && $_FILES['file_upload']['error'] == UPLOAD_ERR_OK) {
        $upload_dir = "uploads/";
        $file_extension = strtolower(pathinfo($_FILES['file_upload']['name'], PATHINFO_EXTENSION));
        if (!in_array($file_extension, ['pdf', 'jpg', 'png', 'jpeg'])) {
            die("Errore: Solo file PDF, JPG o PNG sono accettati.");
        }
        $file_name = "FoglioGuide_RIENTRI_" . sanitizeFileName($candidato) . "." . $file_extension;
        $file_path = $upload_dir . $file_name;
        //$file_path = $upload_dir . basename($_FILES['file_upload']['name']);
        move_uploaded_file($_FILES['file_upload']['tmp_name'], $file_path);
    }
    $link_modifica = "https://croceverde.org/strumenti/autisti/modifica_richiesta.php?id=" . urlencode($id_richiesta);

    $to = "$autisti";
    $cc = ($candidato_filiale == 1) ? ($emailSquadre[$candidato_squadra] ?? '') : ($emailFiliali[$candidato_filiale] ?? '');

    $pulsante_modifica_to = "<a href='" . htmlspecialchars($link_modifica) . "' target='_blank' 
        style='display: inline-block; width: 150px; text-align: center; font-size: 14px; font-weight: bold; color: #ffffff; text-decoration: none; padding: 10px 0; border-radius: 6px; background-color: #00A25E; border: 1px solid #00a25e; transition: background 0.3s ease;'>
        🛠 Gestisci
    </a>";

    $pulsante_modifica_cc = "<a href='' target='_blank' 
        style='display: inline-block; width: 150px; text-align: center; font-size: 14px; font-weight: bold; color: #ffffff; text-decoration: none; padding: 10px 0; border-radius: 6px; background-color: #bcb5b5; border: 1px solid #bcb5b5; transition: background 0.3s ease;'>
        🛠 Gestisci
    </a>";

    function genera_email($pulsante_modifica) {
        global $candidato_datanascita, $tipopatente, $NAut, $RilasciataIl, $RilasciataDa, $ScadenzaAutUltimoRetraining, $richiedente, $giudizio, $tipoprova_label, $candidato_stringa;

        return "
    <html>
    <head>
        <title>Richiesta Prova di Guida</title>
    </head>
    <body style='font-family: Arial, sans-serif; font-size: 16px; background-color: #f4f4f4; color: #333; margin: 0; padding: 0; text-align: center;'>

        <table align='center' width='100%' cellpadding='0' cellspacing='0' border='0'>
            <tr>
                <td align='center'>
                    <table width='480' cellpadding='20' cellspacing='0' border='0' style='background: #ffffff; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); text-align: left;'>
                        <tr>
                            <td>
                                <h2 style='color: #00A25E; text-align: center; font-size: 20px;'>Richiesta Prova $tipoprova_label</h2>

                                <p><strong>Candidato:</strong> " . htmlspecialchars($candidato_stringa) . "</p>
                                <p><strong>Data di nascita:</strong> " . htmlspecialchars($candidato_datanascita) . "</p>
                                <p><strong>Tipo patente:</strong> " . htmlspecialchars($tipopatente) . "</p>
                                <p><strong>Numero patente:</strong> " . htmlspecialchars($NAut) . "</p>
                                <p><strong>Data rilascio:</strong> " . htmlspecialchars($RilasciataIl) . "</p>
                                <p><strong>Ente rilascio:</strong> " . htmlspecialchars($RilasciataDa) . "</p>
                                <p><strong>Scadenza:</strong> " . htmlspecialchars($ScadenzaAutUltimoRetraining) . "</p>
                                <hr>
                                <p><strong>Richiedente:</strong> " . htmlspecialchars($richiedente) . "</p>
                                <p><strong>Giudizio responsabile:</strong> " . htmlspecialchars($giudizio) . "</p>
                                <hr>
                                
                                <table align='center' cellpadding='0' cellspacing='0' border='0'>
                                    <tr>
                                        <td align='center' bgcolor='#00A25E' style='border-radius: 6px; width: 150px;'>
                                            $pulsante_modifica
                                        </td>
                                    </tr>
                                </table>

                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

    </body>
    </html>";
    }

    $sanitized_candidato = sanitizeFileName($candidato);
    $subject = "Richiesta prova $tipoProvaDict[$tipoprova] - $sanitized_candidato".'_'."$candidato_codice";

    // **Invio email a TO**
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8\r\n";
    $headers .= "From: Gestionale CVTO <gestioneutenti@croceverde.org>\r\n";
    $headers .= "Bcc: noreply@croceverde.org\r\n";

    if ($tipoprova == "2" && $file_path) {
        $file_content = chunk_split(base64_encode(file_get_contents($file_path)));
        $boundary = md5(time());
        $headers .= "Content-Type: multipart/mixed; boundary=\"$boundary\"\r\n";

        $message = "--$boundary\r\n";
        $message .= "Content-Type: text/html; charset=UTF-8\r\n\r\n";
        $message .= genera_email($pulsante_modifica_to) . "\r\n";
        $message .= "--$boundary\r\n";
        $message .= "Content-Type: application/octet-stream; name=\"" . basename($file_path) . "\"\r\n";
        $message .= "Content-Transfer-Encoding: base64\r\n";
        $message .= "Content-Disposition: attachment; filename=\"" . basename($file_path) . "\"\r\n\r\n";
        $message .= $file_content . "\r\n";
        $message .= "--$boundary--";
    } else {
        $message = genera_email($pulsante_modifica_to);
    }

    mail($to, $subject, $message, $headers);

    if (!empty($cc)) {
        $headers_cc = "MIME-Version: 1.0\r\n";
        $headers_cc .= "Content-type: text/html; charset=UTF-8\r\n";
        $headers_cc .= "From: Gestionale CVTO <gestioneutenti@croceverde.org>\r\n";
        //$headers_cc .= "Bcc: noreply@croceverde.org\r\n";

        mail($cc, $subject, genera_email($pulsante_modifica_cc), $headers_cc);
    }

    header("Location: listaprove.php?success=1");
    exit();
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>RIENTRI/NORMALI/OVER</title>

    <? require "../config/include/header.html";?>

    <link rel="stylesheet" href="../config/include/custom.css?v=<?= time() ?>">

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            document.querySelectorAll('input[name="tipoprova"]').forEach(function (el) {
                el.addEventListener("change", function () {
                    document.getElementById("file_upload_section").style.display = (this.value == "2") ? "block" : "none";
                });
            });
        });
    </script>
</head>
<body>
<div class="container-fluid">
    <br>
    <div class="sfondo">
        <div class="row m-auto">
            <div class="col-1"><img src="config/images/Logo_CV_green-piccolo.png" class="img-fluid" alt="LOGOCVTOESTESO"> </div>
            <div class="col-10">
                <h2 style="text-align: center; color: #00A25E;">RICHIESTA PROVA DI GUIDA <br> AL RESPONSABILE GRUPPO AUTISTI </h2>
            </div>
            <div class="col-1"><img src="config/images/Logo_CV_green-piccolo.png" class="img-fluid" alt="LOGOCVTOESTESO"></div>
        </div>

        <hr>
        <div class="alert alert-warning" role="alert" style="text-align: center">
            L'invio della domanda è riservato al responsabile di squadra/sezione o al responsabile autisti di squadra/sezione, che si fa carico di verificare il possesso dei requisiti previsti dal <a href="#" class="alert-link" data-bs-toggle="modal" data-bs-target="#modalRegolamento"">regolamento</a>
        </div>
        <form method="post" action="form_A2.php" enctype="multipart/form-data">
            <div class="row mb-3">
                <label for="richiedente" class="col-4 col-form-label">RICHIEDENTE</label>
                <div class="col-8">
                    <select class="form-select" aria-label=".form-select example" id="richiedente" name="richiedente">
                        <option>Seleziona...</option>
                        <?php
                        $stmt = $db->prepare("SELECT IDUtente, Cognome, Nome, IDFiliale, IDSquadra, DataNascita FROM rubrica WHERE IDSquadra!='19' ORDER BY Cognome, Nome");
                        $stmt->execute();
                        $result = $stmt->get_result();
                        while ($row = $result->fetch_assoc()) {
                            $filiale = $dictionaryFiliale[$row['IDFiliale']] ?? 'Sconosciuto';
                            $squadra = $dictionarySquadra[$row['IDSquadra']] ?? 'Sconosciuto';
                            echo '<option value="' . htmlspecialchars($row['IDUtente']) . '">' . htmlspecialchars($row['Cognome'] . ' ' . $row['Nome'] . ' [' . $filiale . ' - ' . $squadra.']') .'</option>';
                        }
                        $stmt->close();
                        ?>
                    </select>
                </div>
            </div>
            <hr>
            <div class="row mb-3">
                <label for="candidato" class="col-4 col-form-label"><b>CANDIDATO</b></label>
                <div class="col-8">
                    <select class="form-select" aria-label=".form-select example" id="candidato" name="candidato">
                        <option>Seleziona...</option>
                        <?php
                        $stmt = $db->prepare("SELECT IDUtente, Cognome, Nome, IDFiliale, IDSquadra FROM rubrica WHERE IDSquadra!='19' ORDER BY Cognome, Nome");
                        $stmt->execute();
                        $result = $stmt->get_result();
                        while ($row = $result->fetch_assoc()) {
                            $filiale = $dictionaryFiliale[$row['IDFiliale']] ?? 'Sconosciuto';
                            $squadra = $dictionarySquadra[$row['IDSquadra']] ?? 'Sconosciuto';
                            echo '<option value="' . htmlspecialchars($row['IDUtente']) . '">' . htmlspecialchars($row['Cognome'] . ' ' . $row['Nome'] . ' [' . $filiale . ' - ' . $squadra.']').'</option>';
                        }
                        $stmt->close();
                        ?>
                    </select>
                </div>
            </div>
            <div class="row mb-3">
                <label for="datanascita" class="col-4 col-form-label">NATO IL</label>
                <div class="col-8">
                    <input type="text" id="datanascita" name="datanascita" class="form-control" readonly>
                </div>
            </div>
            <div class="row mb-3">
                <label for="tipopatente" class="col-4 col-form-label">TIPO PATENTE:</label>
                <div class="col-8">
                    <input type="text" class="form-control" id="tipopatente" name="tipopatente" placeholder="Inserisci la categoria della patente" required>
                </div>
            </div>
            <div class="row mb-3">
                <label for="NAut" class="col-4 col-form-label">NUMERO PATENTE:</label>
                <div class="col-8">
                    <input type="text" class="form-control" id="NAut" name="NAut" placeholder="Inserisci il numero della patente" required>
                </div>
            </div>
            <div class="row mb-3">
                <label for="RilasciataIl" class="col-4 col-form-label">RILASCIATA IL:</label>
                <div class="col-8">
                    <input type="date" class="form-control" id="RilasciataIl" name="RilasciataIl" required>
                </div>
            </div>
            <div class="row mb-3">
                <label for="RilasciataDa" class="col-4 col-form-label">RILASCIATA DA:</label>
                <div class="col-8">
                    <input type="text" class="form-control" id="RilasciataDa" name="RilasciataDa" placeholder="Ente che ha rilasciato la patente" required>
                </div>
            </div>
            <div class="row mb-3">
                <label for="ScadenzaAutUltimoRetraining" class="col-4 col-form-label">SCADENZA:</label>
                <div class="col-8">
                    <input type="date" class="form-control" id="ScadenzaAutUltimoRetraining" name="ScadenzaAutUltimoRetraining" required>
                </div>
            </div>
            <hr>

            <div class="row mb-3">
                <label for="tipoprova" class="col-4 col-form-label">PROVA DA SOSTENERE</label>
                <div class="col-8">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="tipoprova" id="tipoprova1" value="1">
                        <label class="form-check-label" for="tipoprova1">RIENTRI</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="tipoprova" id="tipoprova2" value="2">
                        <label class="form-check-label" for="tipoprova2">NORMALI</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="tipoprova" id="tipoprova4" value="4" <?if (($_SESSION['Livello']==='23')||($_SESSION['Livello']==='24')){echo "disabled";}?>>
                        <label class="form-check-label" for="tipoprova4">
                            OVER 65
                        </label>
                    </div>
                </div>
            </div>
            <div id="file_upload_section" style="display: none;" >
                <label for="file_upload"><i>Carica qui il foglio dei "rientri" compilato e firmato, oppure lascialo nella buca delle lettere del gruppo autisti</i></label><br>
                <input type="file" name="file_upload" id="file_upload" >
            </div>
            <hr>

            <div class="mb-3">
                <label for="giudizio" class="form-label">Giudizio del responsabile di squadra sul milite:</label>
                <textarea class="form-control" id="giudizio" name="giudizio" rows="3" required></textarea>
            </div>
            <div class="d-flex justify-content-center mt-4">
                <button type="submit" class="btn btn-success btn-lg px-4">
                    <i class="fas fa-paper-plane"></i> Invia richiesta
                </button>
            </div>

        </form>
        <script>
            $(document).ready(function () {
                $('#candidato').change(function () {
                    var candidatoID = $(this).val();
                    if (candidatoID) {
                        $.ajax({
                            url: 'get_datanascita.php',
                            type: 'POST',
                            data: { candidato_id: candidatoID },
                            success: function (response) {
                                $('#datanascita').val(response);
                            }
                        });
                    } else {
                        $('#datanascita').val('');
                    }
                });
            });
        </script>
    </div>
</div>

<div class="modal fade" id="modalRegolamento" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Requisiti:</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <ul class="list-unstyled">
                    <li><b>RIENTRI:</b>
                        <ul>
                            <li>Età minima: <mark>23 anni</mark></li>
                            <li><mark>Patente B</mark> da almeno <mark>3 anni</mark></li>
                        </ul>
                    </li>
                    <br>
                    <li><b>NORMALI:</b>
                        <ul>
                            <li>Periodo minimo di 2 mesi con almeno <mark>20 rientri</mark> senza paziente a bordo</li>
                        </ul>
                    </li>
                    <br>
                    <li><b>OVER 65:</b>
                        <ul>
                            <li>(Visita medica)</li>
                        </ul>
                    </li>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal" id="btnHoCapito">Confermo i requisiti</button>
            </div>
        </div>
    </div>
</div>

</div>
<script>
    $(document).ready(function () {
        $("form :input").prop("disabled", true);
        $('#modalRegolamento').modal({
            backdrop: 'static',
            keyboard: false
        });

        $("#btnHoCapito").click(function () {
            $("form :input").prop("disabled", false);
            $(".alert-warning").removeClass("alert-warning").addClass("alert-success").html("Hai confermato il possesso dei requisiti del candidato. Ora puoi compilare la domanda..");
            $('#modalRegolamento').modal('hide');
        });
    });
</script>
<br>
</body>
<?php include "../config/include/footer.php"; ?>
</html>