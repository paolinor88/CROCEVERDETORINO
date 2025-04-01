<?php
include "../config/config.php";
include "config/include/dictionary.php";

$type = $_GET['type'];
$idFiliale = $_GET['IDFiliale'] ?? '';
$idSquadra = $_GET['IDSquadra'] ?? '';

$filialeFilter = $idFiliale ? " AND r.IDFiliale = '$idFiliale'" : "";
$squadraFilter = $idSquadra ? " AND r.IDSquadra = '$idSquadra'" : "";

$ageFilter = "AND TIMESTAMPDIFF(YEAR, STR_TO_DATE(r.DataNascita, '%d/%m/%Y'), CURDATE()) < 75";

switch ($type) {
    case 'formazione50':
        $query = "SELECT r.Codice, r.Cognome, r.Nome, r.IDFiliale, r.IDSquadra 
                  FROM rubrica r 
                  WHERE (r.R=3 OR r.MA=4 OR r.MAU=5) 
                    AND r.Codice NOT IN (SELECT Codice FROM AUTISTI_FORMAZIONE WHERE IDQualifica = 50)
                    $ageFilter $filialeFilter $squadraFilter 
                  ORDER BY r.Cognome";
        break;
    case 'formazione62':
        $query = "SELECT r.Codice, r.Cognome, r.Nome, r.IDFiliale, r.IDSquadra 
                  FROM rubrica r 
                  WHERE (r.R=3 OR r.MA=4 OR r.MAU=5) 
                    AND r.Codice NOT IN (SELECT Codice FROM AUTISTI_FORMAZIONE WHERE IDQualifica = 62)
                    $ageFilter $filialeFilter $squadraFilter 
                  ORDER BY r.Cognome";
        break;
    case 'urgenze':
        $query = "SELECT r.Codice, r.Cognome, r.Nome, r.IDFiliale, r.IDSquadra 
                  FROM AUTISTI_URGENZE au 
                  INNER JOIN rubrica r ON au.Codice = r.Codice 
                  WHERE au.SCADENZAURGENZE < CURDATE() 
                    AND au.Codice NOT IN (SELECT Codice FROM AUTISTI_OVER WHERE SCADENZAOVER >= CURDATE())
                    $ageFilter $filialeFilter $squadraFilter 
                  ORDER BY r.Cognome";
        break;
    case 'over65':
        $query = "SELECT r.Codice, r.Cognome, r.Nome, r.IDFiliale, r.IDSquadra 
                  FROM AUTISTI_OVER ao 
                  INNER JOIN rubrica r ON ao.Codice = r.Codice 
                  WHERE ao.SCADENZAOVER < CURDATE()
                    $ageFilter $filialeFilter $squadraFilter 
                  ORDER BY r.Cognome";
        break;
    default:
        echo "<tr><td colspan='5'>Tipo di conteggio non specificato o non valido</td></tr>";
        exit;
}

$result = $db->query($query);
if ($result === false) {
    echo "<tr><td colspan='5'>Errore nella query: " . htmlspecialchars($db->error) . "</td></tr>";
    exit;
}

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['Codice']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Cognome']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Nome']) . "</td>";
        echo "<td>" . htmlspecialchars($dictionaryFiliale[$row['IDFiliale']] ?? 'N/A') . "</td>";
        echo "<td>" . htmlspecialchars($dictionarySquadra[$row['IDSquadra']] ?? 'N/A') . "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='5'>Nessun dato disponibile</td></tr>";
}
?>
