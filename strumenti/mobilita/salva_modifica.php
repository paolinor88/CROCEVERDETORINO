<?php
session_start();
include "../config/config.php";
include "config/include/destinatari.php";
include "config/include/dictionary.php";

global $db;

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Recupero l'ID della richiesta
    if (!isset($_POST['id_richiesta']) || !is_numeric($_POST['id_richiesta'])) {
        die("Errore: ID richiesta non valido.");
    }

    $id_richiesta = intval($_POST['id_richiesta']);

    $StatoServizio = isset($_POST['StatoServizio']) ? intval($_POST['StatoServizio']) : null;
    $DataOraServizio = isset($_POST['DataOra']) ? $_POST['DataOra'] : null;
    $Richiedente = isset($_POST['richiedente']) ? trim($_POST['richiedente']) : null;
    $StatoTel = isset($_POST['StatoTel']) ? trim($_POST['StatoTel']) : null;
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

    $sql = "UPDATE mobilita 
            SET StatoServizio = ?, DataOraServizio = ?, Richiedente = ?, Contatto = ?, Partenza = ?, Destinazione = ?, 
                TipoServizio = ?, MezzoRichiesto = ?, MezzoAssegnato = ?, Carrozzina = ?, SediaMotore = ?, 
                InfoPaziente = ?, InfoServizio = ?, Tariffa = ?, Equipaggio = ?, StatoTel = ?
            WHERE IDServizio = ?";

    if ($stmt = $db->prepare($sql)) {
        $stmt->bind_param("isssssiisiisssssi",
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
            $Equipaggio,
            $StatoTel,
            $id_richiesta
        );

        if ($stmt->execute()) {

            if (isset($_POST['no_email'])) {
                header("Location: vistaservizi.php?success=1");
                exit();
            }
            $DataOraServizioFormattata = date("d/m/Y H:i", strtotime($DataOraServizio));
            $DataServizioFormattata = date("d/m/Y", strtotime($DataOraServizio));

            $link_modifica = "https://croceverde.org/strumenti/mobilita/modifica_servizio.php?id=" . urlencode($id_richiesta);

            $to = "$mobilita";
            //$to = "ufficioautoparco@croceverde.org";
            $subject = "Aggiornamento richiesta di trasporto - $DataServizioFormattata - $Richiedente";

            $message = "
<!DOCTYPE html>
<html lang='it'>
<head>
    <meta charset='UTF-8'>
    <title>Nuova richiesta di servizio</title>
</head>
<body style='margin: 0; padding: 0; background-color: #f8f9fa; font-family: Arial, sans-serif;'>
    <table width='100%' cellpadding='0' cellspacing='0' border='0' style='padding: 20px; background-color: #f8f9fa;'>
        <tr>
            <td align='center'>
                <table width='600' cellpadding='20' cellspacing='0' border='0' style='background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);'>
                    <tr>
                        <td>
                            <h2 style='text-align: center; color: #00A25E; margin-bottom: 20px;'>Nuova richiesta di trasporto</h2>

                            <p><strong style='color: #333;'>Stato:</strong> " . $dictionaryServizio[$StatoServizio] . "</p>
                            <p><strong style='color: #333;'>Data e Ora:</strong> $DataOraServizioFormattata</p>
                            <p><strong style='color: #333;'>Richiedente:</strong> $Richiedente</p>
                            <p><strong style='color: #333;'>Contatto:</strong> $Contatto</p>
                            <p><strong style='color: #333;'>Partenza:</strong> $Partenza</p>
                            <p><strong style='color: #333;'>Destinazione:</strong> $Destinazione</p>
                            <p><strong style='color: #333;'>Tipo Servizio:</strong> " . $dictionaryTipoServizio[$TipoServizio] . "</p>
                            <p><strong style='color: #333;'>Mezzo Richiesto:</strong> " . $dictionaryTipoMezzo[$MezzoRichiesto] . "</p>
                            <p><strong style='color: #333;'>Carrozzina CVTO:</strong> " . ($Carrozzina ? 'SI' : 'NO') . "</p>
                            <p><strong style='color: #333;'>Sedia a motore:</strong> " . ($SediaMotore ? 'SI' : 'NO') . "</p>
                            <p><strong style='color: #333;'>Info Paziente:</strong> $InfoPaziente</p>
                            <p><strong style='color: #333;'>Info Servizio:</strong> $InfoServizio</p>
                            <p><strong style='color: #333;'>Tariffa:</strong> $Tariffa</p>
                            <p><strong style='color: #333;'>Equipaggio:</strong> $Equipaggio</p>

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

                            <p style='text-align: center; font-size: 12px; color: #999; margin-top: 30px;'>
                                Email generata automaticamente
                            </p>
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
            $headers .= "CC: $centralino\r\n";

            if (mail($to, $subject, $message, $headers)) {
                error_log("Servizio modificato con successo!");
            } else {
                error_log("Errore nell'invio della mail per ID: $id_richiesta");
            }

            header("Location: vistaservizi.php?success=1");
            exit();

        } else {
            echo "Errore nell'aggiornamento: " . $stmt->error;
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
