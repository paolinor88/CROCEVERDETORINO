<?php
session_start();
include "../config/config.php";
include "config/include/destinatari.php";
include "config/include/dictionary.php";

global $db;

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $StatoServizio = isset($_POST['StatoServizio']) ? intval($_POST['StatoServizio']) : null;
    $DataOraServizio = isset($_POST['DataOra']) ? $_POST['DataOra'] : null;
    $Richiedente = isset($_POST['richiedente']) ? trim($_POST['richiedente']) : null;
    $Contatto = isset($_POST['contatto']) ? trim($_POST['contatto']) : null;
    $Partenza = isset($_POST['partenza']) ? trim($_POST['partenza']) : null;
    $Destinazione = isset($_POST['destinazione']) ? trim($_POST['destinazione']) : null;
    $TipoServizio = isset($_POST['tiposervizio']) ? intval($_POST['tiposervizio']) : null;
    $MezzoRichiesto = isset($_POST['mezzorichiesto']) ? intval($_POST['mezzorichiesto']) : null;
    $Carrozzina = isset($_POST['carrozzina']) ? intval($_POST['carrozzina']) : null;
    $SediaMotore = isset($_POST['motore']) ? intval($_POST['motore']) : null;
    $InfoPaziente = isset($_POST['infopaziente']) ? trim($_POST['infopaziente']) : null;
    $InfoServizio = isset($_POST['infoservizio']) ? trim($_POST['infoservizio']) : null;
    $Tariffa = isset($_POST['tariffa']) ? trim($_POST['tariffa']) : null;
    $Equipaggio = isset($_POST['equipaggio']) ? trim($_POST['equipaggio']) : null;
    $MezzoAssegnato = isset($_POST['assegnato']) ? trim($_POST['assegnato']) : null;

    if (!$StatoServizio || !$Partenza || !$Destinazione) {
        die("Errore: Campi obbligatori mancanti.");
    }

    $sql = "INSERT INTO mobilita 
        (StatoServizio, DataOraServizio, Richiedente, Contatto, Partenza, Destinazione, TipoServizio, MezzoRichiesto, MezzoAssegnato, Carrozzina, SediaMotore, InfoPaziente, InfoServizio, Tariffa, Equipaggio)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    if ($stmt = $db->prepare($sql)) {
        $stmt->bind_param("isssssiisiissss",
            $StatoServizio,
            $DataOraServizio,
            $Richiedente,
            $Contatto,
            $Partenza,
            $Destinazione,
            $TipoServizio,
            $MezzoRichiesto,
            $MezzoAssegnato,
            $Carrozzina,
            $SediaMotore,
            $InfoPaziente,
            $InfoServizio,
            $Tariffa,
            $Equipaggio
        );

        if ($stmt->execute()) {
            $IDServizio = $db->insert_id;
            $DataOraServizioFormattata = date("d/m/Y H:i", strtotime($DataOraServizio));
            $DataServizioFormattata = date("d/m/Y", strtotime($DataOraServizio));

            $to = "$mobilita";
            $subject = "Nuova richiesta di trasporto - $DataServizioFormattata - $Richiedente";
            $link_modifica = "https://croceverde.org/strumenti/mobilita/modifica_servizio.php?id="  . urlencode($IDServizio);

            $message = "
<!DOCTYPE html>
<html lang='it'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Nuova richiesta di servizio</title>
</head>
<body style='margin: 0; padding: 0;'>
    <table role='presentation' width='100%' cellspacing='0' cellpadding='0' border='0' 
           style='background-color: #ffffff; padding: 20px;'>
        <tr>
            <td align='center'>
                <table role='presentation' width='600' cellspacing='0' cellpadding='0' border='0' 
                       style='background-color: #f4f4f4; padding: 20px; border-radius: 5px; text-align: left;'>
                    <tr>
                        <td style='font-family: Arial, sans-serif; font-size: 14px; color: #333;'>
                            <h2 style='color: #333; text-align: center;'>Dettagli del Servizio</h2>
                            <p><strong>Stato:</strong> $dictionaryServizio[$StatoServizio]</p>
                            <p><strong>Data e Ora:</strong> $DataOraServizioFormattata</p>
                            <p><strong>Richiedente:</strong> $Richiedente</p>
                            <p><strong>Contatto:</strong> $Contatto</p>
                            <p><strong>Partenza:</strong> $Partenza</p>
                            <p><strong>Destinazione:</strong> $Destinazione</p>
                            <p><strong>Tipo Servizio:</strong> $dictionaryTipoServizio[$TipoServizio]</p>
                            <p><strong>Mezzo Richiesto:</strong> $dictionaryTipoMezzo[$MezzoRichiesto]</p>
                            <p><strong>Carrozzina CVTO:</strong> " . ($Carrozzina ? 'SI' : 'NO') . "</p>
                            <p><strong>Sedia a motore:</strong> " . ($SediaMotore ? 'SI' : 'NO') . "</p>
                            <p><strong>Informazioni Paziente:</strong> $InfoPaziente</p>
                            <p><strong>Informazioni Servizio:</strong> $InfoServizio</p>
                            <p><strong>Tariffa:</strong> $Tariffa</p>
                            <p><strong>Equipaggio:</strong> $Equipaggio</p>
                            <hr>

                            <table role='presentation' align='center' cellpadding='0' cellspacing='0' border='0'>
                                <tr>
                                    <td align='center' bgcolor='#00A25E' style='padding: 8px; width: 150px;'>
                                        <a href='" . htmlspecialchars($link_modifica) . "' target='_blank' 
                                           style='display: inline-block; text-align: center; font-size: 14px; font-weight: bold; 
                                           color: #ffffff; text-decoration: none; padding: 10px 15px; background-color: #00A25E; 
                                           border: 1px solid #00a25e;'>
                                            🛠 Gestisci
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
</html>";

            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers .= "Content-Transfer-Encoding: 8bit\r\n";
            $headers .= "From: Gestionale CVTO <gestioneutenti@croceverde.org>\r\n";
            $headers .= "CC: $ufficioautoparco, $centralino\r\n";

            if (mail($to, $subject, $message, $headers)) {
                error_log("Servizio inserito con successo!");
            } else {
                error_log("Errore nell'invio della mail per ID: $IDServizio");
            }

            header("Location: vistaservizi.php?success=1");
            exit();

        } else {
            echo "Errore nell'inserimento: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Errore nella preparazione della query.";
    }

} else {
    header("Location: index.php");
    exit();
}
?>
