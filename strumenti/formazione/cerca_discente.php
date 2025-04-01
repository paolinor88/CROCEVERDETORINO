<?php
include "../config/config.php";

$termine_ricerca = isset($_GET['query']) ? trim($_GET['query']) : '';
$id_corso = isset($_GET['id_corso']) ? intval($_GET['id_corso']) : 0;

if (empty($termine_ricerca) || $id_corso <= 0) {
    echo '<p class="text-danger">Errore: Parametri mancanti.</p>';
    exit();
}

$query = "SELECT IDUtente, Cognome, Nome, CodFiscale FROM rubrica 
          WHERE Cognome LIKE ? OR Nome LIKE ? OR CodFiscale LIKE ? 
          ORDER BY Cognome, Nome 
          LIMIT 10";
$stmt = $db->prepare($query);
$param = "%{$termine_ricerca}%";
$stmt->bind_param("sss", $param, $param, $param);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo '<p class="text-warning">Nessun discente trovato.</p>';
    exit();
}

echo '<ul class="list-group">';
while ($row = $result->fetch_assoc()) {
    $id_discente = $row['IDUtente'];
    $nome = htmlspecialchars($row['Nome']);
    $cognome = htmlspecialchars($row['Cognome']);
    $codice_fiscale = htmlspecialchars($row['CodFiscale']);

    echo "<li class='list-group-item d-flex justify-content-between align-items-center'>
            <span>$cognome $nome <strong>($codice_fiscale)</strong></span>";

    if (!empty($id_corso) && is_numeric($id_corso)) {
        echo "<button class='btn btn-primary btn-sm' 
                onclick='selezionaEdizione($id_discente, \"$nome\", \"$cognome\", \"$codice_fiscale\", $id_corso)'>
                Aggiungi
              </button>";
    } else {
        echo "<span class='text-danger'>Errore: ID corso mancante</span>";
    }

    echo "</li>";
}
echo '</ul>';
?>