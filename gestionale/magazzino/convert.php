<?php
session_start();
//parametri DB
include "../config/config.php";

//$query = "SELECT id, title, OraUscitaSede, OraSulPosto, OraDestinazione, Carico, Destinazione, Convenzione, STR_TO_DATE(start_event, '%Y/%m/%d %h%i%s'), STR_TO_DATE(end_event, '%Y/%m/%d %h%i%s') FROM programmazione ORDER BY id";
?>
<table>
    <tbody>
    <?php

    $select = $db->query("SELECT id, title, OraUscitaSede, OraSulPosto, OraDestinazione, Carico, Destinazione, Convenzione, STR_TO_DATE(start_event, '%d/%m/%Y %H:%i:%s') as startprova, STR_TO_DATE(end_event, '%Y/%m/%d %h%i%s') as endprova FROM programmazione ORDER BY startprova");

    while($ciclo = $select->fetch_array()){

        echo "
					<tr>
						<td>".$ciclo['startprova']."</td>
						<td>".$ciclo['OraUscitaSede']."</td>
						<td>".$ciclo['Convenzione']."</td> 
					</tr>";

    }

    ?>
    </tbody>