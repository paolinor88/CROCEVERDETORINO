<?php
/**
 *
 * @author     Paolo Randone
 * @author     <paolo.randone@croceverde.org>
* @version    7.4
 * @note       Powered for Croce Verde Torino. All rights reserved
 *
 */
//input
if(isset($_POST["submit"])){
    $litriminuto = $_POST["litriminuto"];
    $pressione_bar = $_POST["pressione_bar"];
    $capacita_litri = $_POST["capacita_litri"];
}
// Calcolo della quantità di ossigeno richiesta in litri
$ossigeno = $pressione_bar * $capacita_litri;

// Calcolo della durata della bombola in minuti
$durata_minuti = $ossigeno / $litriminuto;

// Calcolo della durata della bombola in ore e minuti
$durata_ore = floor($durata_minuti / 60); // Ore intere
$durata_minuti_restanti = $durata_minuti % 60; // Minuti restanti

// Formattazione della durata come tempo (ore e minuti)
$durata_tempo = "$durata_ore ore e $durata_minuti_restanti minuti";


?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <meta name="author" content="Paolo Randone">
    <title>CALCOLATORE OSSIGENO</title>

    <style>
        table, th, td {
            border: 1px solid black;
        }
        table.center {
            margin-left: auto;
            margin-right: auto;
        }
        td.festivi {
            background-color: #ffc107;
        }
    </style>

</head>
<body style="font-family: Arial,serif">
<BR>
<form action="calcolabombole.php" method="post">
    <table class="center">
        <tr>
            <th>
                LITRI AL MINUTO
            </th>
            <th>
                <input type="text" name="litriminuto" required>
            </th>
        </tr>
        <tr>
            <th>
                PRESSIONE (bar)
            </th>
            <th>
                <input type="text" name="pressione_bar" required>
            </th>
        </tr>
        <tr>
            <th>
                CAPACITA' BOMBOLA (litri)
            </th>
            <th>
                <input type="text" name="capacita_litri" required>
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
    if (isset($litriminuto) && isset($pressione_bar) && isset($capacita_litri)){
        echo "<p>Erogando $litriminuto litri al minuto, una bombola da $capacita_litri litri alla pressione di $pressione_bar bar, durerà $durata_minuti minuti ($durata_tempo  circa).</p>";
    }else{
        echo "Inserisci i parametri e premi INVIA";
    }
    ?>
</div>
</body>
