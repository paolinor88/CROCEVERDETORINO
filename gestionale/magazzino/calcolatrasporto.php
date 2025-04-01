<?php
/**
*
* @author     Paolo Randone
* @author     <paolo.randone@croceverde.org>
* @version    1.0
* @note       Powered for Croce Verde Torino. All rights reserved
*
*/
//input
if(isset($_POST["submit"])){
$km_totali = $_POST["km_totali"];
}

// Calcolo del costo del carburante
$km_per_litro = 8;
$costo_per_litro = 1.85;
$litri_consumati = $km_totali / $km_per_litro;
$costo_carburante = $litri_consumati * $costo_per_litro;

// Calcolo del coefficiente di ammortamento
$coefficiente_ammortamento = 0.20;
$costo_ammortamento = $km_totali * $coefficiente_ammortamento;

// Calcolo del costo vivo totale
$costo_totale = $costo_carburante + $costo_ammortamento;

?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <meta name="author" content="Paolo Randone">
    <title>CALCOLATORE COSTO TRASPORTO</title>

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
<form action="calcolatrasporto.php" method="post">
    <table class="center">
        <tr>
            <th>
                KM TOTALI
            </th>
            <th>
                <input type="text" name="km_totali" required>
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
    if (isset($km_totali)){
        echo "<p>Per un totale di $km_totali km, i costi vivi sono:</p>";
        echo "<p>Costo carburante: €" . number_format($costo_carburante, 2) . "</p>";
        echo "<p>Costo ammortamento: €" . number_format($costo_ammortamento, 2) . "</p>";
        echo "<p><b>Costo totale del trasporto: €" . number_format($costo_totale, 2) . "</b></p>";
    }else{
        echo "Inserisci i km totali e premi INVIA";
    }
    ?>
</div>
</body>
</html>
