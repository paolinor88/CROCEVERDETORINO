<?php
session_start();
include "../config/config.php";
include "config/include/destinatari.php";
include "config/include/dictionary.php";

global $db;

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID richiesta non valido.");
}

$id_richiesta = intval($_GET['id']);

$stmt = $db->prepare("SELECT IDServizio, StatoServizio, DataOraServizio, Richiedente, Contatto, Partenza, Destinazione, TipoServizio, MezzoRichiesto, MezzoAssegnato, Carrozzina, SediaMotore, InfoPaziente, InfoServizio, Tariffa, Equipaggio, StatoTel FROM mobilita WHERE IDServizio = ?");
$stmt->bind_param("i", $id_richiesta);
$stmt->execute();
$stmt->bind_result($IDServizio, $StatoServizio, $DataOraServizio, $Richiedente, $Contatto, $Partenza, $Destinazione, $TipoServizio, $MezzoRichiesto, $MezzoAssegnato, $Carrozzina, $SediaMotore, $InfoPaziente, $InfoServizio, $Tariffa, $Equipaggio, $StatoTel);
$stmt->fetch();
$stmt->close();

?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestione Servizio</title>

    <?php include "../config/include/header.html"; ?>

    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 16px;
            background-color: #f9f9f9;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 95%;
            max-width: 500px;
            background: #ffffff;
            padding: 20px;
            margin: auto;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            text-align: left;
            margin-top: 30px;
        }
        h2 {
            color: #00A25E;
            font-size: 22px;
            text-align: center;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 15px;
            display: flex;
            flex-direction: column;
        }
        .form-group label {
            font-weight: bold;
            margin-bottom: 6px;
            color: #555;
        }
        select, input, textarea {
            width: 100%;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #ddd;
            font-size: 14px;
            background: #f8f8f8;
        }
        .button {
            display: block;
            width: 100%;
            max-width: 250px;
            margin: 20px auto;
            padding: 14px;
            background: linear-gradient(135deg, #00A25E, #007a47);
            color: white;
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            border-radius: 8px;
            text-decoration: none;
            box-shadow: 0 4px 10px rgba(0, 162, 94, 0.2);
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }
        .button:hover {
            background: linear-gradient(135deg, #007a47, #005e34);
            box-shadow: 0 6px 14px rgba(0, 122, 71, 0.3);
        }
        @media screen and (max-width: 480px) {
            .container {
                width: 90%;
                padding: 15px;
            }
            .button {
                font-size: 14px;
                padding: 12px;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Gestione Servizio</h2>

    <form method="post" action="salva_modifica.php">
        <input type="hidden" name="id_richiesta" value="<?= htmlspecialchars($id_richiesta) ?>">

        <div class="form-group">
            <label>Stato</label>
            <select name="StatoServizio" required>
                <option value=""  <?= empty($StatoServizio) ? 'selected' : '' ?>>Seleziona</option>
                <option value="1" <?= ($StatoServizio == 1) ? 'selected' : '' ?>>Richiesto</option>
                <option value="2" <?= ($StatoServizio == 2) ? 'selected' : '' ?>>Accettato</option>
                <option value="3" <?= ($StatoServizio == 3) ? 'selected' : '' ?>>Confermato</option>
                <option value="4" <?= ($StatoServizio == 4) ? 'selected' : '' ?>>Rifiutato</option>
                <option value="5" <?= ($StatoServizio == 5) ? 'selected' : '' ?>>Annullato</option>
                <option value="6" <?= ($StatoServizio == 6) ? 'selected' : '' ?>>Chiuso</option>
            </select>
        </div>
        <div class="form-group">
            <label>Telefonata di conferma</label>
            <select name="StatoTel">
                <option value="1" <?= ($StatoTel == 1) ? 'selected' : '' ?>>Da chiamare</option>
                <option value="2" <?= ($StatoTel == 2) ? 'selected' : '' ?>>Telefonata ok</option>
                <option value="3" <?= ($StatoTel == 3) ? 'selected' : '' ?>>Non risponde</option>
            </select>
        </div>
        <div class="form-group">
            <label for="DataOra">Data e ora (sul posto)</label>
            <input name="DataOra" type="datetime-local" value="<?= date("Y-m-d\TH:i", strtotime($DataOraServizio)) ?>">
        </div>
        <div class="form-group">
            <label for="richiedente">Richiedente (Cognome e Nome)</label>
            <input name="richiedente" type="text" value="<?= htmlspecialchars($Richiedente)?>" >
        </div>
        <div class="form-group">
            <label for="contatto">Contatto</label>
            <input name="contatto" type="text" value="<?= htmlspecialchars($Contatto)?>" >
        </div>
        <div class="form-group">
            <label for="partenza">Partenza</label>
            <input name="partenza" type="text" value="<?= htmlspecialchars($Partenza)?>" >
        </div>
        <div class="form-group">
            <label for="destinazione">Destinazione</label>
            <input name="destinazione" type="text" value="<?= htmlspecialchars($Destinazione)?>" >
        </div>
        <div class="form-group">
            <label for="tiposervizio">Tipo Servizio</label>
            <select name="tiposervizio" >
                <option value=""  <?= empty($TipoServizio) ? 'selected' : '' ?>>Seleziona</option>
                <option value="1" <?= ($TipoServizio == 1) ? 'selected' : '' ?>>A/R</option>
                <option value="2" <?= ($TipoServizio == 2) ? 'selected' : '' ?>>Sola andata</option>
            </select>
        </div>
        <div class="form-group">
            <label for="mezzorichiesto">Mezzo richiesto</label>
            <select name="mezzorichiesto" >
                <option value=""  <?= empty($MezzoRichiesto) ? 'selected' : '' ?>>Seleziona</option>
                <option value="1" <?= ($MezzoRichiesto == 1) ? 'selected' : '' ?>>Trasporto disabili</option>
                <option value="2" <?= ($MezzoRichiesto == 2) ? 'selected' : '' ?>>Autovettura</option>
            </select>
        </div>
        <div class="form-group">
            <label for="carrozzina">Carrozzina CVTO</label>
            <select name="carrozzina" >
                <option value=""  <?= empty($Carrozzina) ? 'selected' : '' ?>>Seleziona</option>
                <option value="0" <?= ($Carrozzina == 0) ? 'selected' : '' ?>>NO</option>
                <option value="1" <?= ($Carrozzina == 1) ? 'selected' : '' ?>>SI</option>
            </select>
        </div>
        <div class="form-group">
            <label for="motore">Sedia a motore</label>
            <select name="motore" >
                <option value=""  <?= empty($SediaMotore) ? 'selected' : '' ?>>Seleziona</option>
                <option value="0" <?= ($SediaMotore == 0) ? 'selected' : '' ?>>NO</option>
                <option value="1" <?= ($SediaMotore == 1) ? 'selected' : '' ?>>SI</option>
            </select>
        </div>
        <div class="form-group">
            <label for="infopaziente" >Informazioni paziente</label>
            <textarea  name="infopaziente" rows="3"  ><?= htmlspecialchars($InfoPaziente)?></textarea>
        </div>
        <div class="form-group">
            <label for="infoservizio">Informazioni servizio</label>
            <textarea  name="infoservizio" rows="3"  ><?= htmlspecialchars($InfoServizio)?></textarea>
        </div>
        <div class="form-group">
            <label for="tariffa">Tariffa</label>
            <select name="tariffa" >
                <option value=""  <?= empty($Tariffa) ? 'selected' : '' ?>>Seleziona</option>
                <option value="Pagamento" <?= ($Tariffa == "Pagamento") ? 'selected' : '' ?>>Pagamento</option>
                <option value="Gratuito" <?= ($Tariffa == "Gratuito") ? 'selected' : '' ?>>Gratuito</option>
            </select>
        </div>
        <hr>
        <div class="form-group">
            <label for="equipaggio">Equipaggio</label>
            <input name="equipaggio" type="text" value="<?= htmlspecialchars($Equipaggio)?>" >
        </div>
        <div class="form-group">
            <label for="assegnato">Mezzo Assegnato</label>
            <input name="assegnato" type="text" value="<?= htmlspecialchars($MezzoAssegnato)?>" >
        </div>
        <div style="display: flex; justify-content: space-between;">
            <button type="submit" class="button" name="update">
                <i class="far fa-paper-plane"></i> Salva e Invia
            </button>

            <button type="submit" class="button" name="no_email" style="background: #888; box-shadow: 0 4px 10px rgba(100, 100, 100, 0.2);">
                <i class="fas fa-save"></i> Salva
            </button>
        </div>

    </form>
</div>

</body>
</html>
