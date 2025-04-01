<?php
session_start();
include "../config/config.php";
include "config/include/destinatari.php";
include "config/include/dictionary.php";

global $db;

/*
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID richiesta non valido.");
}
*/
$id_richiesta = intval($_GET['id']);

$stmt = $db->prepare("SELECT IDUtente, IDProva, Esaminatore FROM AUTISTI_RICHIESTE WHERE IDRichiesta = ?");
$stmt->bind_param("i", $id_richiesta);
$stmt->execute();
$stmt->bind_result($candidato_id, $tipoprova, $Esaminatore);
$stmt->fetch();
$stmt->close();

$tipoprova_label = $tipoProvaDict[$tipoprova] ?? 'Non specificato';

$stmt = $db->prepare("SELECT Cognome, Nome, Mail, Codice FROM rubrica WHERE IDUtente = ?");
$stmt->bind_param("i", $candidato_id);
$stmt->execute();
$stmt->bind_result($candidato_cognome, $candidato_nome, $candidato_mail, $candidato_codice);
$stmt->fetch();
$stmt->close();
$candidato = "$candidato_cognome $candidato_nome";
$candidatostringa = "$candidato_cognome".'_'."$candidato_nome".'_'."$candidato_codice";

?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Valutazione RIENTRI</title>

    <?php include "../config/include/header.html"; ?>

    <style>
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #00A25E;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            font-weight: bold;
            margin-bottom: 5px;
        }
        .rating {
            display: flex;
            gap: 10px;
        }
        .rating input {
            display: none;
        }
        .rating label {
            background: #ddd;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
        }
        .rating input:checked + label {
            background: #00A25E;
            color: white;
        }
        .btn {
            display: block;
            width: 100%;
            background: #00A25E;
            color: white;
            text-align: center;
            padding: 14px;
            border-radius: 8px;
            text-decoration: none;
            border: none;
            font-size: 16px;
        }
        .btn:hover {
            background: #007a47;
        }
        select, input {
            width: 100%;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #ddd;
            font-size: 14px;
            background: #f8f8f8;
        }
        .choices {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        .choices input {
            display: none;
        }
        .choices label {
            background: #ddd;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
        }
        .choices input:checked + label {
            background: #00A25E;
            color: white;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>SCHEDA DI VALUTAZIONE RIENTRI</h2>
    <br>
    <form method="post" action="genera_pdf_prova_rientri.php">
        <input type="hidden" name="id_utente" value="<?= htmlspecialchars($candidato_id)?>">
        <input type="hidden" name="id_richiesta" value="<?= htmlspecialchars($id_richiesta) ?>">
        <input type="hidden" name="candidato_mail" value="<?= htmlspecialchars($candidato_mail) ?>">
        <input type="hidden" name="candidato" value="<?= htmlspecialchars($candidato) ?>">
        <input type="hidden" name="esaminatore" value="<?= htmlspecialchars($Esaminatore) ?>">
        <input type="hidden" name="tipoprovalabel" value="<?= htmlspecialchars($tipoprova_label) ?>">
        <input type="hidden" name="candidatostringa" value="<?= htmlspecialchars($candidatostringa) ?>">
        <div class="form-group">
            <p>Candidato: <?= "<b>". htmlspecialchars($candidato) ."</b>"?></p>
            <p>Esaminatore: <?= htmlspecialchars($Esaminatore)?></p>
        </div>
        <hr>
        <?php
        $domande = [
            "normecd" => "Rispetta le norme del Codice della strada?",
            "dimensioni" => "Considera le dimensioni del mezzo?",
            "sicurezza" => "Sicurezza (utilizzo cinture da parte di tutto l'equipaggio etc)"
        ];


        foreach ($domande as $name => $label): ?>
            <div class="form-group">
                <label><?= $label ?></label>
                <div class="choices">
                    <input type="radio" name="<?= $name ?>" id="<?= $name ?>_si" value="SI" required>
                    <label for="<?= $name ?>_si">Sì</label>
                    <input type="radio" name="<?= $name ?>" id="<?= $name ?>_no" value="NO" required>
                    <label for="<?= $name ?>_no">No</label>
                </div>
            </div>
        <?php endforeach; ?>
        <div class="form-group">
            <label>Attenzione nelle manovre</label>
            <div class="rating">
                <?php for ($i = 1; $i <= 5; $i++): ?>
                    <input type="radio" name="attenzione" id="attenzione<?= $i ?>" value="<?= $i ?>" required>
                    <label for="attenzione<?= $i ?>"><?= $i ?></label>
                <?php endfor; ?>
            </div>
        </div>
        <div class="form-group">
            <label for="commentiesame" class="form-label">Commenti esaminatore:</label>
            <textarea class="form-control" id="commentiesame" name="commentiesame" rows="3"></textarea>
        </div>
        <hr>
        <div class="form-group">
            <label>Esito finale</label>
            <select name="esito" required>
                <option value="">Seleziona...</option>
                <option value="1">PROMOSSO</option>
                <option value="2">BOCCIATO</option>
            </select>
        </div>

        <button type="submit" class="btn" >Concludi esame e invia valutazione</button>
    </form>
</div>
</body>
</html>
