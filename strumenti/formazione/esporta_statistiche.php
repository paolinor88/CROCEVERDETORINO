<?php
session_start();
include "../config/config.php";

if (!isset($_SESSION['Livello']) || $_SESSION['Livello'] != 28) {
    header("Location: index.php");
    exit();
}

$id_edizione = isset($_GET['id_edizione']) ? intval($_GET['id_edizione']) : 0;
if (!$id_edizione) {
    die("ID edizione mancante.");
}

// Recupera info corso + edizione
$query = "
    SELECT c.titolo AS corso, e.data_inizio, e.id_corso
    FROM edizioni_corso e
    JOIN corsi c ON c.id_corso = e.id_corso
    WHERE e.id_edizione = ?
";
$stmt = $db->prepare($query);
$stmt->bind_param("i", $id_edizione);
$stmt->execute();
$stmt->bind_result($nome_corso, $data_edizione, $id_corso);
$stmt->fetch();
$stmt->close();

// Prepara intestazioni CSV
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="statistiche_test_edizione_' . $id_edizione . '.csv"');
$output = fopen('php://output', 'w');

// Intestazione
fputcsv($output, [
    'Corso', 'Data edizione', 'Domanda', 'Risposta sbagliata', 'Risposta corretta', 'Numero errori'
]);

// Recupera errori aggregati
$query = "
    SELECT 
        d.domanda,
        d.risposta1, d.risposta2, d.risposta3, d.risposta4,
        d.risposta_corretta,
        e.risposta_data,
        COUNT(*) as num_errori
    FROM errori_test_edizione e
    JOIN test_domande d ON d.id = e.id_domanda
    WHERE e.id_edizione = ?
    GROUP BY e.id_domanda, e.risposta_data
    ORDER BY num_errori DESC
";
$stmt = $db->prepare($query);
$stmt->bind_param("i", $id_edizione);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $testi_risposte = [
        1 => $row['risposta1'],
        2 => $row['risposta2'],
        3 => $row['risposta3'],
        4 => $row['risposta4'] ?? '(N/D)'
    ];

    fputcsv($output, [
        $nome_corso,
        date("d/m/Y", strtotime($data_edizione)),
        $row['domanda'],
        $testi_risposte[$row['risposta_data']] ?? 'Non disponibile',
        $testi_risposte[$row['risposta_corretta']] ?? 'Non disponibile',
        $row['num_errori']
    ]);
}

fclose($output);
exit();
