<?php
header('Access-Control-Allow-Origin: *');
/**
 *
 * @author     Paolo Randone
 * @author     <paolo.randone@croceverde.org>
 * @version    8.2
 * @note         Powered for Croce Verde Torino. All rights reserved
 *
 */

session_start();
include "../config/config.php";

if(isset($_POST["submit"])){
    $litriminuto = $_POST["litriminuto"];
    $pressione_bar = $_POST["pressione_bar"];
    $capacita_litri = $_POST["capacita_litri"];
}
$ossigeno = $pressione_bar * $capacita_litri;

$durata_minuti = $ossigeno / $litriminuto;

$durata_ore = floor($durata_minuti / 60); // Ore intere
$durata_minuti_restanti = $durata_minuti % 60; // Minuti restanti

$durata_tempo = "$durata_ore ore e $durata_minuti_restanti minuti";


?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="Paolo Randone">

    <title>CALCOLATORE BOMBOLE</title>
    <?php require "../config/include/header.html"; ?>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../config/include/custom.css?v=<?= time() ?>">


</head>
<body>
<?php include "../config/include/navbar.php"; ?>
<div class="container mb-5">


    <div class="card card-cv mx-auto" style="max-width: 600px;">
        <h3 class="text-center mb-4">Durata bombole</h3>
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
        <BR>
        <div style="text-align: center">
            <?php
            if (isset($litriminuto) && isset($pressione_bar) && isset($capacita_litri)){
                echo "<p>Erogando $litriminuto litri al minuto, una bombola da $capacita_litri litri alla pressione di $pressione_bar bar, durerà $durata_minuti minuti ($durata_tempo  circa).</p>";
            }else{
                echo "Inserisci i parametri e premi INVIA";
            }
            ?>
        </div>
    </div>
</div>

<br>

</body>
