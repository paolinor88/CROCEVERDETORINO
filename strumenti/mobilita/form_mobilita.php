<?php
global $db;
header('Access-Control-Allow-Origin: *');
/**
 *
 * @author     Paolo Randone
 * @author     <paolo.randone@croceverde.org
 * @version    8.2
 * @note         Powered for Croce Verde Torino. All rights reserved
 *
 */

include "../config/config.php";
include "config/include/destinatari.php";
include "config/include/dictionary.php";

?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nuovo Servizio</title>

    <?php require "../config/include/header.html"; ?>

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
    </style>
</head>
<body>

<div class="container">
    <h2>Nuovo Servizio</h2>

    <form method="post" action="salva_nuovo.php">

        <div class="form-group">
            <label>Stato</label>
            <select name="StatoServizio" required>
                <option value="">Seleziona</option>
                <option value="1">Richiesto</option>
                <option value="2">Accettato</option>
                <option value="3">Confermato</option>
                <option value="4">Rifiutato</option>
                <option value="5">Annullato</option>
                <option value="6">Chiuso</option>
            </select>
        </div>

        <div class="form-group">
            <label for="DataOra">Data e ora (sul posto)</label>
            <input name="DataOra" type="datetime-local">
        </div>

        <div class="form-group">
            <label for="richiedente">Richiedente (Cognome e Nome)</label>
            <input name="richiedente" type="text">
        </div>

        <div class="form-group">
            <label for="contatto">Contatto</label>
            <input name="contatto" type="text">
        </div>

        <div class="form-group">
            <label for="partenza">Partenza</label>
            <input name="partenza" type="text">
        </div>

        <div class="form-group">
            <label for="destinazione">Destinazione</label>
            <input name="destinazione" type="text">
        </div>

        <div class="form-group">
            <label for="tiposervizio">Tipo Servizio</label>
            <select name="tiposervizio">
                <option value="">Seleziona</option>
                <option value="1">A/R</option>
                <option value="2">Sola andata</option>
            </select>
        </div>

        <div class="form-group">
            <label for="mezzorichiesto">Mezzo richiesto</label>
            <select name="mezzorichiesto">
                <option value="">Seleziona</option>
                <option value="1">Trasporto disabili</option>
                <option value="2">Autovettura</option>
            </select>
        </div>

        <div class="form-group">
            <label for="carrozzina">Carrozzina CVTO</label>
            <select name="carrozzina">
                <option value="">Seleziona</option>
                <option value="0">NO</option>
                <option value="1">SI</option>
            </select>
        </div>

        <div class="form-group">
            <label for="motore">Sedia a motore</label>
            <select name="motore">
                <option value="">Seleziona</option>
                <option value="0">NO</option>
                <option value="1">SI</option>
            </select>
        </div>

        <div class="form-group">
            <label for="infopaziente">Informazioni paziente</label>
            <textarea name="infopaziente" rows="3"></textarea>
        </div>

        <div class="form-group">
            <label for="infoservizio">Informazioni servizio</label>
            <textarea name="infoservizio" rows="3"></textarea>
        </div>

        <div class="form-group">
            <label for="tariffa">Tariffa</label>
            <select name="tariffa">
                <option value="">Seleziona</option>
                <option value="Pagamento">Pagamento</option>
                <option value="Gratuito">Gratuito</option>
            </select>
        </div>

        <hr>

        <div class="form-group">
            <label for="equipaggio">Equipaggio</label>
            <input name="equipaggio" type="text">
        </div>

        <button type="submit" class="button">
            <i class="fas fa-save"></i> Aggiungi Servizio
        </button>
    </form>
</div>

</body>
</html>

<?php include "../config/include/footer.php"; ?>
</html>