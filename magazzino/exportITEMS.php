<?php

$nomeFile = "giacenza.xls";

header("Content-Type: application/vnd.ms-excel");

$datetime= date_create('now');
$data= date("Y-m-d");

$categoria= $_POST["selectcategoria"];

$dictionaryCategoria = array (
    1 => "Materiale di consumo",
    2 => "Ricambi",
    3 => "Altro",
    4 => "Vestiario",
);
header("Content-Disposition: inline; filename=$data $dictionaryCategoria[$categoria] $nomeFile");

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
        <th scope="col" style="color: white; background: lightblue;">NOME</th>
        <th scope="col" style="color: white; background: lightblue;">TIPO</th>
        <th scope="col" style="color: white; background: lightblue;">DETTAGLI</th>
        <th scope="col" style="color: white; background: lightblue;">QUANTITA'</th>
        <th scope="col" style="color: white; background: lightblue;">SCADENZA</th>
        <th scope="col" style="color: white; background: lightblue;">POSIZIONE</th>
        <th scope="col" style="color: white; background: lightblue;">CATEGORIA</th>
        <th scope="col" style="color: white; background: lightblue;">FORNITORE</th>
        <th scope="col" style="color: white; background: lightblue;">PREZZO</th>
    </tr>
    </thead>
    <tbody>

    <?php
    if (($_POST["selectcategoria"])!='ALL'){

        $select = $db->query("SELECT * FROM giacenza WHERE categoria='$categoria' order by categoria, nome, tipo");

    }else{
        $select = $db->query("SELECT * FROM giacenza order by categoria, nome, tipo");

    }
    while($ciclo = $select->fetch_array()){

        echo "
                <tr>
                    <td>".$ciclo['nome']."</td>
                    <td>".$ciclo['tipo']."</td>
                    <td>".$ciclo['dettagli']."</td>
                    <td>".$ciclo['quantita']."</td>
                    <td>".$ciclo['scadenza']."</td>
                    <td>".$ciclo['posizione']."</td>
                    <td>".$dictionaryCategoria[$ciclo['categoria']]."</td>
                    <td>".$ciclo['fornitore']."</td>
                    <td>".$ciclo['prezzo']."</td>
                </tr>";
    }
    ?>
    </tbody>
</table>