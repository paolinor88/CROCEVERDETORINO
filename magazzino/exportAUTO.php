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
        text-align: center;
        vertical-align: middle;
    },
    tr {
        height: 40px;
    }
</style>
<table>
    <thead>
    <tr height="40px">
        <th scope="col">AUTO</th>
        <th scope="col">TARGA</th>
        <th scope="col">DATA</th>
        <th scope="col">DISINFEZIONE</th>
        <th scope="col">PULIZIA INTERNA</th>
        <th scope="col">PULIZIA ESTERNA</th>
        <th scope="col">SOSTANZE IMPIEGATE</th>
        <th scope="col">DIRETTORE SANITARIO</th>
        <th scope="col">ISPETTORI S.I.S.P.</th>
    </tr>
    </thead>
    <tbody>
    <?php
    if ($_POST["selectauto"]!=="ALL"){
        if ($datastart!==""){//data filter ON
            $select = $db->query("SELECT * FROM mezzi LEFT OUTER JOIN lavaggio_mezzi ON mezzi.ID = lavaggio_mezzi.title WHERE lavaggio_mezzi.title='$numeroauto' AND lavaggio_mezzi.start_event BETWEEN '$datastart' AND '$dataend' order by lavaggio_mezzi.title, lavaggio_mezzi.start_event");

            //$select = $db->query("SELECT * FROM lavaggio_mezzi WHERE title='$numeroauto' AND start_event BETWEEN '$datastart' AND '$dataend' order by title, start_event");
        }else{//no data filter
            $select = $db->query("SELECT * FROM mezzi LEFT OUTER JOIN lavaggio_mezzi ON mezzi.ID = lavaggio_mezzi.title WHERE lavaggio_mezzi.title='$numeroauto' order by lavaggio_mezzi.title, lavaggio_mezzi.start_event");

            //$select = $db->query("SELECT * FROM lavaggio_mezzi WHERE title='$numeroauto' order by title, start_event");
        }
        while($ciclo = $select->fetch_array()){

            echo "
					<tr height='40px'>
						<td>".$ciclo['title']."</td>
						<td>".$ciclo['targa']."</td>
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
        if ($datastart!==""){//data filter ON
            $select = $db->query("SELECT * FROM mezzi LEFT OUTER JOIN lavaggio_mezzi ON mezzi.ID = lavaggio_mezzi.title WHERE lavaggio_mezzi.start_event BETWEEN '$datastart' AND '$dataend' order by lavaggio_mezzi.title, lavaggio_mezzi.start_event");
        }else{//no data filter
            $select = $db->query("SELECT * FROM mezzi LEFT OUTER JOIN lavaggio_mezzi ON mezzi.ID = lavaggio_mezzi.title WHERE lavaggio_mezzi.stato='2' AND mezzi.stato='1' order by lavaggio_mezzi.title, lavaggio_mezzi.start_event");
        }

        while($ciclo = $select->fetch_array()){

            echo "
					<tr height='40px'>
						<td>".$ciclo['title']."</td>
						<td>".$ciclo['targa']."</td>
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