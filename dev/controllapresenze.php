<?php
/**
 *
 * @author     Paolo Randone
 * @author     <paolo.randone@croceverde.org>
 * @version    7.1
 * @note       Powered for Croce Verde Torino. All rights reserved
 *
 */
session_start();
//parametri DB
include "../config/config.php";

$inputanno = $_POST["inputanno"];
if((isset($_POST["submit"]))&&($_POST["inputmese"]!=0)){

    $inputmese = $_POST["inputmese"];
    $inputsezione = $_POST["inputsezione"];

    $sql = "SELECT IDTipoG, DataGuardia,
               SUM(CASE WHEN Sezione='$inputsezione' AND Squadra='1' THEN 1 ELSE 0 END) AS '1_1',
               SUM(CASE WHEN Sezione='$inputsezione' AND Squadra='2' THEN 1 ELSE 0 END) AS '1_2',
               SUM(CASE WHEN Sezione='$inputsezione' AND Squadra='3' THEN 1 ELSE 0 END) AS '1_3',
               SUM(CASE WHEN Sezione='$inputsezione' AND Squadra='4' THEN 1 ELSE 0 END) AS '1_4',
               SUM(CASE WHEN Sezione='$inputsezione' AND Squadra='5' THEN 1 ELSE 0 END) AS '1_5',
               SUM(CASE WHEN Sezione='$inputsezione' AND Squadra='6' THEN 1 ELSE 0 END) AS '1_6',
               SUM(CASE WHEN Sezione='$inputsezione' AND Squadra='7' THEN 1 ELSE 0 END) AS '1_7',
               SUM(CASE WHEN Sezione='$inputsezione' AND Squadra='8' THEN 1 ELSE 0 END) AS '1_8',
               SUM(CASE WHEN Sezione='$inputsezione' AND Squadra='9' THEN 1 ELSE 0 END) AS '1_9',
               SUM(CASE WHEN Sezione='$inputsezione' AND Squadra='10' THEN 1 ELSE 0 END) AS '1_10'
        FROM presenze
        WHERE YEAR (DataGuardia) = '$inputanno' and MONTH (DataGuardia) = '$inputmese' 
        GROUP BY DataGuardia, IDTipoG
        ORDER BY  IDTipoG, DataGuardia";

}else{
    $inputsezione = $_POST["inputsezione"];

    $sql = "SELECT IDTipoG, DataGuardia,
               SUM(CASE WHEN Sezione='$inputsezione' AND Squadra='1' THEN 1 ELSE 0 END) AS '1_1',
               SUM(CASE WHEN Sezione='$inputsezione' AND Squadra='2' THEN 1 ELSE 0 END) AS '1_2',
               SUM(CASE WHEN Sezione='$inputsezione' AND Squadra='3' THEN 1 ELSE 0 END) AS '1_3',
               SUM(CASE WHEN Sezione='$inputsezione' AND Squadra='4' THEN 1 ELSE 0 END) AS '1_4',
               SUM(CASE WHEN Sezione='$inputsezione' AND Squadra='5' THEN 1 ELSE 0 END) AS '1_5',
               SUM(CASE WHEN Sezione='$inputsezione' AND Squadra='6' THEN 1 ELSE 0 END) AS '1_6',
               SUM(CASE WHEN Sezione='$inputsezione' AND Squadra='7' THEN 1 ELSE 0 END) AS '1_7',
               SUM(CASE WHEN Sezione='$inputsezione' AND Squadra='8' THEN 1 ELSE 0 END) AS '1_8',
               SUM(CASE WHEN Sezione='$inputsezione' AND Squadra='9' THEN 1 ELSE 0 END) AS '1_9',
               SUM(CASE WHEN Sezione='$inputsezione' AND Squadra='10' THEN 1 ELSE 0 END) AS '1_10'
        FROM presenze
        WHERE YEAR (DataGuardia) = '$inputanno'
        GROUP BY DataGuardia, IDTipoG
        ORDER BY  IDTipoG, DataGuardia";

}
$result = $db->query($sql);
//query calendario
$sql2 = "SELECT * FROM guardie2023";
$result2 = $db->query($sql2);
//
$dictionaryTipoG = array (
    1 => "DIU",
    2 => "FES",
    3 => "NOT",
);
//
$dictionarySezione = array (
    1 => "TORINO",
    2 => "ALPIGNANO",
    3 => "BORGARO/CASELLE",
    4 => "CIRIE'",
    5 => "SAN MAURO",
    6 => "VENARIA",
);
//
$dictionarySquadra = array (
    1 => "SQ. 1",
    2 => "SQ. 2",
    3 => "SQ. 3",
    4 => "SQ. 4",
    5 => "SQ. 5",
    6 => "SQ. 6",
    7 => "SQ. 7",
    8 => "SQ. 8",
    9 => "SQ. 9",
    10 => "SQ. SAB",
    /*
    11 => "MONT",
    12 => "DIR",
    13 => "LUN",
    14 => "MAR",
    15 => "MER",
    16 => "GIO",
    17 => "VEN",
    18 => "DIU",
    19 => "SQ. GIOV",
    20 => "GEN",
    21 => "21",
    22 => "CIT",
    23 => "DIP",
    */
);
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <meta name="author" content="Paolo Randone">
    <title>CONTA PRESENZE</title>

    <style>
        table, th, td {
            border: 1px solid black;
        }
        table.center {
            margin-left: auto;
            margin-right: auto;
        }
        td.ok {
            background-color: #ffc107;
        }
        td.diu {
            background-color: #9fcdff;
        }
        td.fes {
            background-color: lightsalmon;
        }
        td.not {
            background-color: lightgreen;
        }
    </style>

</head>
<body style="font-family: Arial,serif">
<form action="controllapresenze.php" method="post"  >
    <table class="center">
        <tr>
            <th>
                ANNO
            </th>
            <th>
                <select name="inputanno" required>
                    <option value="">---</option>
                    <?php
                    $selectedAnno = isset($_POST['inputanno']) ? $_POST['inputanno'] : '';
                    for ($i = 2024; $i >= 2023; $i--) {
                        echo '<option value="'.$i.'" '.($selectedAnno == $i ? 'selected' : '').'>'.$i.'</option>';
                    }
                    ?>
                </select>
            </th>
            <th>
                MESE
            </th>
            <th>
                <select name="inputmese" required>
                    <option value="0">Tutti</option>
                    <?php
                    $selectedMese = $_POST['inputmese'] ?? '';
                    $mesi = array(
                        1 => 'GENNAIO',
                        2 => 'FEBBRAIO',
                        3 => 'MARZO',
                        4 => 'APRILE',
                        5 => 'MAGGIO',
                        6=> 'GIUGNO',
                        7=> 'LUGLIO',
                        8=> 'AGOSTO',
                        9=> 'SETTEMBRE',
                        10=> 'OTTOBRE',
                        11=> 'NOVEMBRE',
                        12=> 'DICEMBRE',
                    );
                    foreach ($mesi as $key => $value) {
                        echo '<option value="'.$key.'" '.($selectedMese == $key ? 'selected' : '').'>'.$value.'</option>';
                    }
                    ?>
                </select>
            </th>
        </tr>
        <tr>
            <th colspan="2">
                SEZIONE
            </th>
            <th colspan="2">
                <select name="inputsezione" required>
                    <option value="">---</option>
                    <?php
                    $selectedSezione = $_POST['inputsezione'] ?? '';
                    $sezioni = array(
                        1 => 'TORINO',
                        2 => 'ALPIGNANO',
                        3 => 'BORGARO',
                        4 => 'CIRIE\'',
                        5 => 'SAN MAURO',
                        6 => 'VENARIA'
                    );
                    foreach ($sezioni as $key => $value) {
                        echo '<option value="'.$key.'" '.($selectedSezione == $key ? 'selected' : '').'>'.$value.'</option>';
                    }
                    ?>
                </select>
            </th>
        </tr>
    </table>
    <br>
    <div style="text-align: center">
        <input type="submit" name="submit">
        <button type="button" onclick="location.href='controllapresenze.php';">Azzera</button>
    </div>
</form>
<hr>
<table class="center">
    <tr>
        <th colspan="12"><?= "Sezione: $dictionarySezione[$inputsezione]";?></th>
    </tr>
    <tr>
        <th>Data</th>
        <th>Tipo</th>
        <th>SQ. 1</th>
        <th>SQ. 2</th>
        <th>SQ. 3</th>
        <th>SQ. 4</th>
        <th>SQ. 5</th>
        <th>SQ. 6</th>
        <th>SQ. 7</th>
        <th>SQ. 8</th>
        <th>SQ. 9</th>
        <th>SQ. SAB</th>
    </tr>
    <?php

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            setlocale(LC_TIME, 'it_IT.utf8');
            $date = date('d-m-Y', strtotime($row["DataGuardia"]));
            $datelett = strftime('%a', strtotime($row["DataGuardia"]));
            echo "<tr>
            <td >" . $date . " " . $datelett . "</td>";
            if(($row["IDTipoG"])==1){
                echo "<td style='text-align: center' class='diu'>" . $dictionaryTipoG[$row["IDTipoG"]] . "</td>";
            }elseif (($row["IDTipoG"])==2){
                echo "<td style='text-align: center' class='fes'>" . $dictionaryTipoG[$row["IDTipoG"]] . "</td>";
            }else{
                echo "<td style='text-align: center' class='not'>" . $dictionaryTipoG[$row["IDTipoG"]] . "</td>";
            }
            for ($i=1; $i<=10; $i++) {
                if(($row["1_".$i])>0){
                    echo "<td style='text-align: center' class='ok'>" . $row["1_".$i] . "</td>";//TORINO 1/10
                }else{
                    echo "<td style='text-align: center'>" . $row["1_".$i] . "</td>";//TORINO 1/10
                }
            }
        }
    } else {
        echo "<tr><td colspan='12' STYLE='text-align: center'>Nessun risultato trovato</td></tr>";
    }

    ?>
</table>
</body>
</html>

