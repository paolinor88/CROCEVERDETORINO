<?php
$nomeFile = "registro";

header("Content-Type: application/vnd.ms-excel");

if ($_POST["selectauto"]!=="ALL"){
    $numeroauto = $_POST["selectauto"];
    $datastart = $_POST["datastart"];
    $dataend = $_POST["dataend"];
}else{
    $numeroauto = "GLOBALE";
    $datastart = $_POST["datastart"];
    $dataend = $_POST["dataend"];
}


header("Content-Disposition: inline; filename=$nomeFile$numeroauto.xls");

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
        <th scope="col">AUTO</th>
        <th scope="col">DATA</th>
        <th scope="col">DISINFEZIONE</th>
        <th scope="col">PULIZIA INTERNA</th>
        <th scope="col">PULIZIA ESTERNA</th>
        <th scope="col">SOSTANZE IMPIEGATE</th>
        <th scope="col">FIRMA DIRETTORE SANITARIO</th>
        <th scope="col">CONTROLLO ISPETTORI S.I.S.P.</th>
    </tr>
    </thead>
    <tbody>
    <?php
    if ($_POST["selectauto"]!=="ALL"){
        $select = $db->query("SELECT * FROM lavaggio_mezzi WHERE title='$numeroauto' order by title, start_event");

        while($ciclo = $select->fetch_array()){

            echo "
					<tr>
						<td>".$ciclo['title']."</td>
						<td>".$ciclo['start_event']."</td>
						<td align='center'>X</td>
						<td align='center'>X</td>
						<td align='center'>X</td>
						<td></td>
						<td></td>
						<td></td>
					</tr>";
        }
    }else{
        $select = $db->query("SELECT * FROM lavaggio_mezzi WHERE start_event BETWEEN '$datastart' AND '$dataend' order by title, start_event");

        while($ciclo = $select->fetch_array()){

            echo "
					<tr>
						<td>".$ciclo['title']."</td>
						<td>".$ciclo['start_event']."</td>
						<td align='center'>X</td>
						<td align='center'>X</td>
						<td align='center'>X</td>
						<td></td>
						<td></td>
						<td></td>
					</tr>";
        }
    }
    ?>
    </tbody>
</table>