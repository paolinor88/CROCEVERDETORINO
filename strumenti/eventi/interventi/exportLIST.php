<?php

$nomeFile = "elencointerventi.xls";

header("Content-Type: application/vnd.ms-excel");
/*
$datetime= date_create('now', timezone_open("Europe/Rome"));
$data= date("Y-m-d H:i");
*/
header("Content-Disposition: inline; filename=$nomeFile");

include "../config/config.php";

$dictionaryPatologia = array (
    1 => "MEDICO",
    2 => "TRAUMA",
);
$dictionaryGravita = array (
    1 => "Verde",
    2 => "Giallo",
    3 => "Rosso",
    4 => "Nero",
);
$dictionaryStato = array (
    1 => "IN CORSO",
    2 => "OSPEDALIZZATO",
    3 => "RIFIUTA",
    4 => "DIMESSO",
);
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
        <th colspan="15">PRIMA DI LAVORARE SUL FILE: SALVA CON NOME -> SELEZIONARE TIPO FILE .XLSX</th>
    </tr>
    <tr>
        <th scope="col" style="color: white; background: lightblue;">ID EVENTO</th>
        <th scope="col" style="color: white; background: lightblue;">N. INTERVENTO</th>
        <th scope="col" style="color: white; background: lightblue;">INIZIO</th>
        <th scope="col" style="color: white; background: lightblue;">COGNOME</th>
        <th scope="col" style="color: white; background: lightblue;">NOME</th>
        <th scope="col" style="color: white; background: lightblue;">DATA DI NASCITA'</th>
        <th scope="col" style="color: white; background: lightblue;">INDIRIZZO</th>
        <th scope="col" style="color: white; background: lightblue;">TELEFONO</th>
        <th scope="col" style="color: white; background: lightblue;">SQUADRA</th>
        <th scope="col" style="color: white; background: lightblue;">POSIZIONE</th>
        <th scope="col" style="color: white; background: lightblue;">PATOLOGIA</th>
        <th scope="col" style="color: white; background: lightblue;">GRAVITA'</th>
        <th scope="col" style="color: white; background: lightblue;">STATO</th>
        <th scope="col" style="color: white; background: lightblue;">ESITO</th>
        <th scope="col" style="color: white; background: lightblue;">FINE</th>
        <th scope="col" style="color: white; background: lightblue;">NOTE</th>
        <th scope="col" style="color: white; background: lightblue;">DATAORA</th>
    </tr>
    </thead>
    <tbody>

    <?php
    if (($_POST["selectstato"])!='ALL'){

        $select = $db->query("SELECT * FROM interventi WHERE STATO='4' order by IDEvento, ID_INTERVENTO");

    }else{
        $select = $db->query("SELECT * FROM interventi order by IDEvento, ID_INTERVENTO");

    }
    while($ciclo = $select->fetch_array()){

        echo "
                <tr>
                    <td>".$ciclo['IDEvento']."</td>
                    <td>".$ciclo['ID_INTERVENTO']."</td>
                    <td>".$ciclo['ORAINIZIO']."</td>
                    <td>".$ciclo['COGNOME']."</td>
                    <td>".$ciclo['NOME']."</td>
                    <td>".$ciclo['NASCITA']."</td>
                    <td>".$ciclo['INDIRIZZO']."</td>
                    <td>".$ciclo['TELEFONO']."</td>
                    <td>".$ciclo['SQUADRA']."</td>
                    <td>".$ciclo['POSTAZIONE']."</td>
                    <td>".$dictionaryPatologia[$ciclo['CODICEPATOLOGIA']]."</td>
                    <td>".$dictionaryGravita[$ciclo['CODICEGRAVITA']]."</td>
                    <td>".$dictionaryStato[$ciclo['STATO']]."</td>
                    <td>".$ciclo['ESITO']."</td>
                    <td>".$ciclo['ORAFINE']."</td>
                    <td>".$ciclo['NOTE']."</td>
                    <td>".$ciclo['DATAORA']."</td>
                </tr>";
    }
    ?>
    </tbody>
</table>