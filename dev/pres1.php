<?php
// connessione al database
session_start();
//parametri DB
include "../config/config.php";

// costruzione della tabella HTML
echo "<table>";
echo "<tr><th>Sezione</th><th>Squadra</th><th>IDTipoG 1</th><th>IDTipoG 2</th><th>IDTipoG 3</th></tr>";

// cicla su tutte le combinazioni di sezione e squadra
for ($i = 1; $i <= 5; $i++) {
    for ($j = 1; $j <= 9; $j++) {
        $sezione = "Sezione" . $i;
        $squadra = "Squadra" . $j;

        // query per la somma dei record raggruppati per IDTipoG e DataGuardia
        $sql = "SELECT SUM(CASE WHEN IDTipoG = 1 THEN 1 ELSE 0 END) AS IDTipoG1,
                SUM(CASE WHEN IDTipoG = 2 THEN 1 ELSE 0 END) AS IDTipoG2,
                SUM(CASE WHEN IDTipoG = 3 THEN 1 ELSE 0 END) AS IDTipoG3
                FROM presenze
                WHERE Sezione = '$sezione' AND Squadra = '$squadra'
                GROUP BY DataGuardia";

        $result = $db->query($sql);

        // costruzione della riga della tabella HTML
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                if ($row == reset($result->fetch_all())) {
                    echo "<td rowspan='" . $result->num_rows . "'>" . $sezione . "</td>";
                    echo "<td rowspan='" . $result->num_rows . "'>" . $squadra . "</td>";
                }
                echo "<td>" . $row["IDTipoG1"] . "</td>";
                echo "<td>" . $row["IDTipoG2"] . "</td>";
                echo "<td>" . $row["IDTipoG3"] . "</td>";
                echo "</tr>";
            }
        } else {
            // se non ci sono record per la combinazione di sezione e squadra, stampa una riga vuota
            echo "<tr><td>" . $sezione . "</td><td>" . $squadra . "</td><td>0</td><td>0</td><td>0</td></tr>";
        }
    }
}

echo "</table>";

// chiusura della connessione al database
$conn->close();
?>
