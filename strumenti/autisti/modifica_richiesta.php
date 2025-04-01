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

// richiesta
$stmt = $db->prepare("SELECT IDUtente, IDProva, StatoRichiesta, DataProva, OraProva, LuogoProva, Esaminatore, NoteProva FROM AUTISTI_RICHIESTE WHERE IDRichiesta = ?");
$stmt->bind_param("i", $id_richiesta);
$stmt->execute();
$stmt->bind_result($candidato_id, $tipoprova, $stato_richiesta, $DataProva, $OraProva, $LuogoProva, $Esaminatore, $NoteProva);
$stmt->fetch();
$stmt->close();

//candidato
$stmt = $db->prepare("SELECT Cognome, Nome, IDFiliale, IDSquadra, Codice, Mail, Cellulare FROM rubrica WHERE IDUtente = ?");
$stmt->bind_param("i", $candidato_id);
$stmt->execute();
$stmt->bind_result($candidato_cognome, $candidato_nome, $candidato_filiale, $candidato_squadra, $candidato_codice, $candidato_mail, $candidato_cellulare);
$stmt->fetch();
$stmt->close();
$candidato = "$candidato_cognome $candidato_nome";

?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestione Richiesta</title>

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
        select, input {
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
    <h2>Gestione Richiesta Esame di Guida</h2>

    <form method="post" action="salva_modifica.php">
        <input type="hidden" name="id_richiesta" value="<?= htmlspecialchars($id_richiesta) ?>">

        <div class="form-group">
            <label>Candidato</label>
            <input type="text" value="<?= htmlspecialchars($candidato).' ['. htmlspecialchars($dictionaryFiliale[$candidato_filiale]).' - '.htmlspecialchars($dictionarySquadra[$candidato_squadra]).']'?>" disabled>
        </div>

        <div class="form-group">
            <label>Contatti</label>
            <input type="text" value="<?= htmlspecialchars($candidato_cellulare).' - '. htmlspecialchars($candidato_mail)?>" disabled>
        </div>

        <div class="form-group">
            <label>Tipo di Prova</label>
            <select name="tipoprova" disabled>
                <option value="" disabled <?= empty($tipoprova) ? 'selected' : '' ?>>Seleziona</option>
                <option value="1" <?= ($tipoprova == 1) ? 'selected' : '' ?>>RIENTRI</option>
                <option value="2" <?= ($tipoprova == 2) ? 'selected' : '' ?>>NORMALI</option>
                <option value="3" <?= ($tipoprova == 3) ? 'selected' : '' ?>>URGENZE</option>
                <option value="4" <?= ($tipoprova == 4) ? 'selected' : '' ?>>OVER 65</option>
            </select>
        </div>

        <div class="form-group">
            <label>Stato Richiesta</label>
            <select name="stato_richiesta" required>
                <option value="" disabled <?= empty($stato_richiesta) ? 'selected' : '' ?>>Seleziona</option>
                <option value="1" <?= ($stato_richiesta == 1) ? 'selected' : '' ?>>In attesa</option>
                <option value="2" <?= ($stato_richiesta == 2) ? 'selected' : '' ?>>Programmata</option>
                <option value="3" <?= ($stato_richiesta == 3) ? 'selected' : '' ?>>Conclusa</option>
            </select>
        </div>

        <div class="form-group">
            <label>Esaminatore</label>
            <input name="esaminatore" type="text" value="<?= htmlspecialchars($Esaminatore)?>">
        </div>

        <div class="form-group">
            <label>Data prova</label>
            <input name="data_prova" type="date" value="<?= htmlspecialchars($DataProva)?>">
        </div>

        <div class="form-group">
            <label>Ora prova</label>
            <input name="ora_prova" type="time" value="<?= htmlspecialchars($OraProva)?>">
        </div>

        <div class="form-group">
            <label>Luogo prova</label>
            <input name="luogo_prova" type="text" value="<?= htmlspecialchars($LuogoProva)?>">
        </div>

        <div class="form-group">
            <label>Note</label>
            <input name="note_prova" type="text" value="<?= htmlspecialchars($NoteProva)?>">
        </div>

        <button type="submit" class="button">
            <i class="fas fa-save"></i> Salva Modifiche
        </button>
    </form>
</div>

</body>
</html>
