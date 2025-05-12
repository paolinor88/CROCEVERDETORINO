<?php
session_start();
include "../config/config.php";

if (!isset($_SESSION['Livello']) || $_SESSION['Livello'] != 28 ) {
    header("Location: index.php");
    exit();
}

$id_corso = isset($_GET['id_corso']) ? intval($_GET['id_corso']) : null;
$id_edizione = isset($_GET['id_edizione']) ? intval($_GET['id_edizione']) : null;

if (!$id_corso) {
    die("Corso non specificato.");
}

// Query identica a quella della tua pagina
$query = "
    SELECT d.nome, d.cognome, d.codice_fiscale, 
           r.IDFiliale, r.IDSquadra,
           COUNT(pl.id_lezione) AS lezioni_seguite,
           SUM(COALESCE(pl.completata, 0)) AS lezioni_completate,
           SUM(COALESCE(pl.superato_test, 0)) AS test_superati,
           (SELECT COUNT(*) FROM lezioni WHERE id_corso = ?) AS totale_lezioni,
           e.data_inizio
    FROM discenti d
    JOIN autorizzazioni_corsi a ON d.id = a.discente_id
    JOIN edizioni_corso e ON a.id_edizione = e.id_edizione
    LEFT JOIN rubrica r ON d.codice_fiscale = r.CodFiscale
    LEFT JOIN progresso_lezioni pl ON d.id = pl.discente_id AND pl.id_corso = ?
    WHERE e.id_corso = ?
";

if ($id_edizione) {
    $query .= " AND e.id_edizione = ? ";
}

$query .= " GROUP BY d.id, e.data_inizio ";

$stmt = $db->prepare($query);
if ($id_edizione) {
    $stmt->bind_param("iiii", $id_corso, $id_corso, $id_corso, $id_edizione);
} else {
    $stmt->bind_param("iii", $id_corso, $id_corso, $id_corso);
}
$stmt->execute();
$result = $stmt->get_result();
$discenti = $result->fetch_all(MYSQLI_ASSOC);

// Output CSV
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=discenti_corso.csv');

$output = fopen('php://output', 'w');

// Intestazione
fputcsv($output, ['Nome', 'Cognome', 'Codice Fiscale', 'Sezione', 'Squadra', 'Lezioni Seguite', 'Lezioni Completate', 'Test Superati', 'Totale Lezioni', 'Data Inizio Edizione']);

foreach ($discenti as $row) {
    fputcsv($output, [
        $row['nome'],
        $row['cognome'],
        $row['codice_fiscale'],
        $row['IDFiliale'],
        $row['IDSquadra'],
        $row['lezioni_seguite'],
        $row['lezioni_completate'],
        $row['test_superati'],
        $row['totale_lezioni'],
        date('d/m/Y', strtotime($row['data_inizio']))
    ]);
}

exit();
?>
