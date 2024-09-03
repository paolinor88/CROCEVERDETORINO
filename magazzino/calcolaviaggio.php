<?php
/**
 *
 * @author     Paolo Randone
 * @author     <paolo.randone@croceverde.org>
 * @version    7.5
 * @note       Powered for Croce Verde Torino. All rights reserved
 *
 */
//input
if(isset($_POST["submit"])){
    $km_percorsi = $_POST["km_percorsi"];
    $sosta_mezzore = $_POST["sosta_mezzore"];
}

// Costo base
$costo_base = 22.50;

// Costo per km
$costo_km = 0.85;

// Costo per sosta (ogni mezz'ora)
$costo_sosta_mezzora = 15.00;

// Calcolo del costo totale del viaggio
if ($km_percorsi <= 27) {
    // Tariffa fissa di 45 euro se i km sono <= 27
    $costo_totale = 45.00 + ($sosta_mezzore * $costo_sosta_mezzora);
} else {
    // Calcolo normale se i km sono > 27
    $costo_totale = $costo_base + ($km_percorsi * $costo_km) + ($sosta_mezzore * $costo_sosta_mezzora);
}

?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <meta name="author" content="Paolo Randone">
    <title>CALCOLATORE COSTO VIAGGIO</title>

    <style>
        table, th, td {
            border: 1px solid black;
        }
        table.center {
            margin-left: auto;
            margin-right: auto;
        }
    </style>

</head>
<body style="font-family: Arial,serif">
<BR>
<form action="calcolaviaggio.php" method="post">
    <table class="center">
        <tr>
            <th>
                KM PERCORSI
            </th>
            <th>
                <input type="text" name="km_percorsi" required>
            </th>
        </tr>
        <tr>
            <th>
                DURATA SOSTA
            </th>
            <th>
                <select name="sosta_mezzore" required>
                    <option value="0">0:00</option>
                    <option value="1">00:30</option>
                    <option value="2">01:00</option>
                    <option value="3">01:30</option>
                    <option value="4">02:00</option>
                    <option value="5">02:30</option>
                    <option value="6">03:00</option>
                    <option value="7">03:30</option>
                    <option value="8">04:00</option>
                    <option value="9">04:30</option>
                    <option value="10">05:00</option>
                </select>
            </th>
        </tr>
    </table>
    <br>
    <div style="text-align: center">
        <input type="submit" name="submit">
    </div>
</form>
<br>
<div style="text-align: center">
    <?php
    if (isset($km_percorsi) && isset($sosta_mezzore)){
        echo "<p>Il costo totale del viaggio è di €".number_format($costo_totale, 2).".</p>";
    }else{
        echo "Inserisci i parametri e premi INVIA";
    }
    ?>
</div>
</body>
</html>
