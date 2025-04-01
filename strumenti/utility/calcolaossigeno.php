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
    $oreviaggio = $_POST["oreviaggio"];
}
$ossigeno = $litriminuto * 60 * ($oreviaggio+1);

$capacita = 7 * 200;

$bombole = ceil($ossigeno / $capacita);

$max_ossigeno = floor($bombole * $capacita / $litriminuto / 60);

?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="Paolo Randone">

    <title>CALCOLATORE OSSIGENO</title>
    <?php require "../config/include/header.html"; ?>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../config/include/custom.css?v=<?= time() ?>">


</head>
<body>
<?php include "../config/include/navbar.php"; ?>
<div class="container mb-5">


    <div class="card card-cv mx-auto" style="max-width: 600px;">
        <h3 class="text-center mb-4">Consumo bombole</h3>
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
    </div>
</div>
</body>
