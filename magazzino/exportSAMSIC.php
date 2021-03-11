<?php

$nomeFile = "SAMSIC.xls";

header("Content-Type: application/vnd.ms-excel");

$numeromese = $_POST["selectmese"];
$numeroanno = $_POST["selectanno"];
header("Content-Disposition: inline; filename=$numeroanno/$numeromese $nomeFile");

include "../config/config.php";

?>
<style>
    table, th, td {
        border: 1px solid black;
        border-collapse: collapse;
    }
</style>
<table>
    <thead>
    <tr>
        <th scope="col" style="color: white; background: lightblue;">CODICE FORNITORE</th>
        <th scope="col" style="color: white; background: lightblue;">CODICE MEZZO</th>
        <th scope="col" style="color: white; background: lightblue;">TARGA</th>
        <th scope="col" style="color: white; background: lightblue;">DATA</th>
        <th scope="col" style="color: white; background: lightblue;">TIPO LAVAGGIO</th>
        <th scope="col" style="color: white; background: lightblue;">QUANTITA</th>
    </tr>
    </thead>
    <tbody>

    <?php
    if (isset($_POST["exportSAMSIC"])){

        $select = $db->query("SELECT * FROM mezzi LEFT OUTER JOIN lavaggio_mezzi ON mezzi.ID = lavaggio_mezzi.title WHERE MONTH(lavaggio_mezzi.start_event)='$numeromese' AND YEAR(lavaggio_mezzi.start_event)='$numeroanno' AND lavaggio_mezzi.stato='2' AND mezzi.stato='1' order by lavaggio_mezzi.start_event, lavaggio_mezzi.title");

        while($ciclo = $select->fetch_array()){

            echo "
					<tr>
						<td>SAMSIC</td>
						<td align='right'>".$ciclo['title']."</td>
						<td>".$ciclo['targa']."</td>
						<td>".$ciclo['start_event']."</td>
						<td>Normale</td>
						<td>1</td>
					</tr>";
        }

    }
    $row=((mysqli_num_rows($select))+1);
    ?>
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td style="background: lightgrey;"><b>TOTALE MEZZI</b></td>
        <td style="color: darkorange; background: lightgrey;"><b>=SOMMA(F2:F<?=$row?>)</b></td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td style="background: lightgrey;"><b>UNITARIO</b></td>
        <td style="color: darkorange; background: lightgrey;"><b>12€</b></td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td style="background: lightgrey;"><b>IMPONIBILE</b></td>
        <td style="color: darkorange; background: lightgrey;"><b>=F<?=($row+1)?>*F<?=($row+2)?></b></td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td style="background: lightgrey;"><b>IVA</b></td>
        <td style="color: darkorange; background: lightgrey;"><b>=F<?=($row+3)?>*22%</b></td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td style="background: lightgrey;"><b>TOTALE A PAGARE</b></td>
        <td style="color: darkorange; background: lightgrey;"><b>=F<?=($row+3)?>+F<?=($row+4)?></b></td>
    </tr>
    </tbody>
</table>