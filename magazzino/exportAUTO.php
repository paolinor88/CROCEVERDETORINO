<?php
$nomeFile = "REG_";
$trattino= "_";

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


header("Content-Disposition: inline; filename=$nomeFile$numeroauto$trattino$datastart$trattino$dataend.xls");

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
    if ($_POST["selectauto"]!=="ALL"){//filtro auto SI
        if ($datastart!==""){//filtro data SI
            $select = $db->query("SELECT * FROM lavaggio_mezzi JOIN mezzi ON mezzi.ID = lavaggio_mezzi.title WHERE lavaggio_mezzi.title='$numeroauto' AND lavaggio_mezzi.start_event BETWEEN '$datastart' AND '$dataend' group by lavaggio_mezzi.title, lavaggio_mezzi.start_event");

        }else{//filtro data NO
            $select = $db->query("SELECT * FROM lavaggio_mezzi JOIN mezzi ON mezzi.ID = lavaggio_mezzi.title WHERE lavaggio_mezzi.title='$numeroauto' group by lavaggio_mezzi.title, lavaggio_mezzi.start_event");

        }
        while($ciclo = $select->fetch_array()){

            if (($ciclo["esterno"])==1){
                $esterno = "&#10003;";
            }else{
                $esterno =" ";
            }
            if (($ciclo["interno"])==1){
                $interno = "&#10003;";
            }else{
                $interno =" ";
            }
            if (($ciclo["neb"])==1){
                $neb = "&#10003;";
            }else{
                $neb =" ";
            }

            echo "
					<tr height='40px'>
						<td>".$ciclo['title']."</td>
						<td>".$ciclo['targa']."</td>
						<td>".$ciclo['start_event']."</td>
						<td align='center' style='font-size:20px;'>".$neb."</td>
						<td align='center' style='font-size:20px;'>".$interno."</td>
						<td align='center' style='font-size:20px;'>".$esterno."</td>
						<td></td>
						<td></td>
						<td></td>
					</tr>";
        }
    }else{//filtro auto NO
        if ($datastart!==""){//filtro data SI
            $select = $db->query("SELECT * FROM lavaggio_mezzi JOIN mezzi ON mezzi.ID = lavaggio_mezzi.title WHERE mezzi.stato='1' AND mezzi.tipo!='4' AND lavaggio_mezzi.start_event BETWEEN '$datastart' AND '$dataend' group by lavaggio_mezzi.title, lavaggio_mezzi.start_event");
        }else{//filtro data NO
            $select = $db->query("SELECT * FROM lavaggio_mezzi JOIN mezzi ON mezzi.ID = lavaggio_mezzi.title WHERE mezzi.stato='1' AND mezzi.tipo!='4' group by lavaggio_mezzi.title, lavaggio_mezzi.start_event ");
        }

        while($ciclo = $select->fetch_array()){

            if (($ciclo["esterno"])==1){
                $esterno = "&#10003;";
            }else{
                $esterno =" ";
            }
            if (($ciclo["interno"])==1){
                $interno = "&#10003;";
            }else{
                $interno =" ";
            }
            if (($ciclo["neb"])==1){
                $neb = "&#10003;";
            }else{
                $neb =" ";
            }

            echo "
					<tr height='40px'>
						<td>".$ciclo['title']."</td>
						<td>".$ciclo['targa']."</td>
						<td>".$ciclo['start_event']."</td>
						<td align='center' style='font-size:20px;'>".$neb."</td>
						<td align='center' style='font-size:20px;'>".$interno."</td>
						<td align='center' style='font-size:20px;'>".$esterno."</td>
						<td></td>
						<td></td>
						<td></td>
					</tr>";
        }
    }
    ?>
    </tbody>
</table>