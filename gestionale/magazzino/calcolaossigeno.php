<?php
/**
 *
 * @author     Paolo Randone
 * @author     <paolo.randone@croceverde.org>
* @version    8.2
 * @note       Powered for Croce Verde Torino. All rights reserved
 *
 */
//input
if(isset($_POST["submit"])){
    $litriminuto = $_POST["litriminuto"];
    $oreviaggio = $_POST["oreviaggio"];
}
// Quantità di ossigeno richiesta in 7 ore
$ossigeno = $litriminuto * 60 * ($oreviaggio+1);

// Capacità di una bombola di ossigeno
$capacita = 7 * 200;

// Calcolo della quantità di bombole necessarie
$bombole = ceil($ossigeno / $capacita);

//Calcolo tempo massimo
$max_ossigeno = floor($bombole * $capacita / $litriminuto / 60);

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
<form action="calcolaossigeno.php" method="post">
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
                DURATA VIAGGIO (IN ORE)
            </th>
            <th>
                <input type="text" name="oreviaggio" required>
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
    if (isset($litriminuto)){
        echo "<p>Sono necessarie $bombole bombole di ossigeno da 7 litri per erogare $litriminuto litri al minuto per $oreviaggio ore. </p>";
        echo "<p>Con $bombole bombole, erogando $litriminuto litri al minuto, avrai ossigeno per massimo $max_ossigeno ore</p>";
    }else{
        echo "Inserisci i parametri e premi INVIA";
    }
    ?>
</div>
</body>
