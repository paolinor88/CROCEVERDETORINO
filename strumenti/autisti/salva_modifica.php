<?php
session_start();
include "../config/config.php";
include "config/include/destinatari.php";
include "config/include/dictionary.php";

global $db;

if ($_SERVER["REQUEST_METHOD"] !== "POST" || empty($_POST['id_richiesta']) || !is_numeric($_POST['id_richiesta'])) {
    die("Errore: Dati non validi.");
}

$id_richiesta = intval($_POST['id_richiesta']);

$stato_richiesta = $_POST['stato_richiesta'] ?? null;
$data_prova = $_POST['data_prova'] ?? null;
$ora_prova = $_POST['ora_prova'] ?? null;
$luogo_prova = trim($_POST['luogo_prova'] ?? '');
$esaminatore = trim($_POST['esaminatore'] ?? '');
$note_prova = trim($_POST['note_prova'] ?? '');

if (empty($stato_richiesta) || !in_array((int)$stato_richiesta, [1, 2, 3, 4], true)) {
    die("Errore: Stato Richiesta non valido.");
}
if ($data_prova && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $data_prova)) {
    die("Errore: Data Prova non valida.");
}
if ($ora_prova && !preg_match('/^\d{2}:\d{2}$/', $ora_prova)) {
    die("Errore: Ora Prova non valida.");
}

$stmt = $db->prepare("UPDATE AUTISTI_RICHIESTE SET StatoRichiesta = ?, DataProva = ?, OraProva = ?, LuogoProva = ?, Esaminatore = ?, NoteProva = ? WHERE IDRichiesta = ?");
if (!$stmt) {
    error_log("Errore nella preparazione della query: " . $db->error);
    die("Si è verificato un errore durante l'aggiornamento della richiesta. Riprova più tardi.");
}

$stmt->bind_param("isssssi", $stato_richiesta, $data_prova, $ora_prova, $luogo_prova, $esaminatore, $note_prova, $id_richiesta);

if (!$stmt->execute()) {
    error_log("Errore nell'esecuzione della query: " . $stmt->error);
    die("Si è verificato un errore durante l'aggiornamento della richiesta. Riprova più tardi.");
}
$stmt->close();

$stmt = $db->prepare("SELECT r.Cognome, r.Nome, r.IDFiliale, r.IDSquadra, r.Codice, r.Mail, a.IDUtente, a.IDProva 
                      FROM AUTISTI_RICHIESTE a 
                      JOIN rubrica r ON a.IDUtente = r.IDUtente 
                      WHERE a.IDRichiesta = ?");
$stmt->bind_param("i", $id_richiesta);
$stmt->execute();
$stmt->bind_result($candidato_cognome, $candidato_nome, $candidato_filiale, $candidato_squadra, $candidato_codice, $candidato_mail, $candidato_id, $tipoprova);
$stmt->fetch();
$stmt->close();

$candidato = htmlspecialchars(trim("$candidato_cognome $candidato_nome"));
$tipoprova_label = htmlspecialchars($tipoProvaDict[$tipoprova] ?? 'Non specificato');
$link_modifica = "https://croceverde.org/strumenti/autisti/modifica_richiesta.php?id=" . urlencode($id_richiesta);
$link_valutazione = "https://croceverde.org/strumenti/autisti/valutazione_" .
    ($tipoprova == 1 ? "rientri" : ($tipoprova == 2 ? "normali" : "urgenze")) .
    ".php?id=" . urlencode($id_richiesta);

$subject = "Conferma prova {$tipoprova_label} - {$candidato_cognome}_{$candidato_nome}_{$candidato_codice}";
$message = "
    <html>
    <head>
        <title>Conferma Prova di Guida</title>
    </head>
    <body style='font-family: Arial, sans-serif; font-size: 16px; background-color: #f4f4f4; color: #333; margin: 0; padding: 0; text-align: center;'>
        <table align='center' width='100%' cellpadding='0' cellspacing='0' border='0'>
            <tr>
                <td align='center'>
                    <table width='480' cellpadding='20' cellspacing='0' border='0' style='background: #ffffff; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); text-align: left;'>
                        <tr>
                            <td>
                                <h2 style='color: #00A25E; text-align: center; font-size: 20px;'>Conferma Prova di Guida</h2>
                                <p><strong>Candidato:</strong> ".htmlspecialchars($candidato)." [".htmlspecialchars($dictionaryFiliale[$candidato_filiale])." - ".htmlspecialchars($dictionarySquadra[$candidato_squadra])."]</p>
                                <p><strong>Tipo di prova:</strong> ".htmlspecialchars($tipoprova_label)."</p>
                                <hr>
                                <p><strong>Data Prova:</strong> ".htmlspecialchars($data_prova)."</p>
                                <p><strong>Ora Prova:</strong> ".htmlspecialchars($ora_prova)."</p>
                                <p><strong>Luogo Prova:</strong> ".htmlspecialchars($luogo_prova)."</p>
                                <p><strong>Esaminatore:</strong> ".htmlspecialchars($esaminatore)."</p>
                                <p><strong>Note:</strong> ".htmlspecialchars($note_prova)."</p>

                                <table align='center' cellpadding='0' cellspacing='0' border='0'>
                                    <tr>
                                        <td align='center' bgcolor='#00A25E' style='border-radius: 6px; width: 150px;'>
                                            <a href='" . htmlspecialchars($link_modifica) . "' target='_blank' 
                                               style='display: inline-block; width: 150px; text-align: center; font-size: 14px; font-weight: bold; color: #ffffff; text-decoration: none; padding: 10px 0; border-radius: 6px; background-color: #00A25E; border: 1px solid #008A50; transition: background 0.3s ease;'>
                                                🛠 Gestisci
                                            </a>
                                        </td>
                                    </tr>
                                </table>
                                
                                <br>
                                
                                <table align='center' cellpadding='0' cellspacing='0' border='0'>
                                    <tr>
                                        <td align='center' bgcolor='#FFA500' style='border-radius: 6px; width: 150px;'>
                                            <a href='" . htmlspecialchars($link_valutazione) . "' target='_blank' 
                                               style='display: inline-block; width: 150px; text-align: center; font-size: 14px; font-weight: bold; color: #ffffff; text-decoration: none; padding: 10px 0; border-radius: 6px; background-color: #FFA500; border: 1px solid #E69500; transition: background 0.3s ease;'>
                                                📝 Valuta
                                            </a>
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
    </html>
";
$message_candidato = "
        <html>
        <head>
            <title>Conferma Prova di Guida</title>
        </head>
        <body style='font-family: Arial, sans-serif; font-size: 16px; background-color: #f4f4f4; color: #333; margin: 0; padding: 0; text-align: center;'>
            <table align='center' width='100%' cellpadding='0' cellspacing='0' border='0'>
                <tr>
                    <td align='center'>
                        <table width='480' cellpadding='20' cellspacing='0' border='0' style='background: #ffffff; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); text-align: left;'>
                            <tr>
                                <td>
                                    <h2 style='color: #00A25E; text-align: center; font-size: 20px;'>Conferma Prova di Guida</h2>
                                    <p>Gentile <strong>" . htmlspecialchars($candidato) . "</strong>,</p>
                                    <p>la tua <strong>prova " . htmlspecialchars($tipoprova_label) . "</strong> è stata confermata!</p>
                                    <hr>
                                    <p><strong>Quando:</strong> " . htmlspecialchars($data_prova) . " ore " . htmlspecialchars($ora_prova) . "</p>
                                    <p><strong>Dove:</strong> " . htmlspecialchars($luogo_prova) . "</p>
                                    <p><strong>Il tuo esaminatore è:</strong> " . htmlspecialchars($esaminatore) . "</p>
                                    <p><strong>Nota bene:</strong> " . htmlspecialchars($note_prova) . "</p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </body>
        </html>
";
$headers = "MIME-Version: 1.0\r\n";
$headers .= "Content-type: text/html; charset=UTF-8\r\n";
$headers .= "From: Gestionale CVTO <gestioneutenti@croceverde.org>\r\n";

$to = "$autisti";
if (!mail($to, $subject, $message, $headers)) {
    error_log("Errore nell'invio dell'email ai destinatari principali ($to) con soggetto: $subject");
    die("Si è verificato un errore durante l'invio dell'email ai destinatari principali. Riprova più tardi.");
}

if (!empty($candidato_mail) && filter_var($candidato_mail, FILTER_VALIDATE_EMAIL)) {
    if (!mail($candidato_mail, $subject, $message_candidato, $headers)) {
        error_log("Errore nell'invio dell'email al candidato ($candidato_mail)");
        $_SESSION['email_error'] = "Attenzione: L'email al candidato non è stata inviata correttamente.";
    }
}

header("Location: listaprove.php?success=1");
exit();