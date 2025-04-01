<?php

$nomeFile = "_RAPPORTO_ODV.xls";

header("Content-Type: application/vnd.ms-excel");

$numeromese = $_POST["selectmese"];
$numeroanno = $_POST["selectanno"];
header("Content-Disposition: inline; filename=$numeroanno/$numeromese$nomeFile");

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
        <th scope="col" style="color: white; background: lightblue;">ID SEGNALAZIONE</th>
        <th scope="col" style="color: white; background: lightblue;">CODICE MEZZO</th>
        <th scope="col" style="color: white; background: lightblue;">SEGNALAZIONE</th>
        <th scope="col" style="color: white; background: lightblue;">DATA SEGNALAZIONE</th>
        <th scope="col" style="color: white; background: lightblue;">DATA VERIFICA</th>
        <th scope="col" style="color: white; background: lightblue;">NOTE</th>
    </tr>
    </thead>
    <tbody>

    <?php
    if (isset($_POST["exportSEGNALAZIONI"])){

        $select = $db->query("SELECT * FROM SegnalazioniGuastiMezzi WHERE MONTH(SegnalazioniGuastiMezzi.DataOra)='$numeromese' AND YEAR(SegnalazioniGuastiMezzi.DataOra)='$numeroanno' order by  SegnalazioniGuastiMezzi.IDSegnalazione");

        while($ciclo = $select->fetch_array()){

            echo "
					<tr>
						<td>".$ciclo['IDSegnalazione']."</td>
						<td align='right'>".$ciclo['Sigla']."</td>
						<td>".$ciclo['Segnalazione']."</td>
						<td>".$ciclo['DataOra']."</td>
						<td>".$ciclo['DataVerificato']."</td>
						<td>".$ciclo['NoteVerificato']."</td>
					</tr>";
        }

    }
    //$row=((mysqli_num_rows($select))+1);
    ?>

    </tbody>
</table>