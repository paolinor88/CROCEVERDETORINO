<?php
session_start();
include "../config/config.php";

if (!isset($_SESSION['Livello']) || $_SESSION['Livello'] != 28) {
    header("Location: index.php");
    exit();
}

$id_edizione = isset($_GET['id_edizione']) ? intval($_GET['id_edizione']) : 0;
if (!$id_edizione) {
    echo "ID edizione mancante.";
    exit();
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

// Recupera errori aggregati per domanda
$query = "
    SELECT 
        e.id_domanda, 
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

$statistiche = [];
while ($row = $result->fetch_assoc()) {
    $id = $row['id_domanda'];
    $statistiche[$id]['domanda'] = $row['domanda'];
    $statistiche[$id]['corretta'] = $row['risposta_corretta'];
    $statistiche[$id]['risposte'][$row['risposta_data']] = $row['num_errori'];
    $statistiche[$id]['testi'] = [
        1 => $row['risposta1'],
        2 => $row['risposta2'],
        3 => $row['risposta3'],
        4 => $row['risposta4'] ?? "(N/D)"
    ];
}

// Calcola punteggio medio per edizione
$query = "
    SELECT COUNT(*) as tot_errori FROM errori_test_edizione WHERE id_edizione = ?
";
$stmt = $db->prepare($query);
$stmt->bind_param("i", $id_edizione);
$stmt->execute();
$stmt->bind_result($tot_errori);
$stmt->fetch();
$stmt->close();

$query = "SELECT COUNT(*) FROM autorizzazioni_corsi WHERE id_edizione = ?";
$stmt = $db->prepare($query);
$stmt->bind_param("i", $id_edizione);
$stmt->execute();
$stmt->bind_result($num_discenti);
$stmt->fetch();
$stmt->close();

$punteggio_medio = ($num_discenti > 0) ? round(100 * (1 - ($tot_errori / ($num_discenti * count($statistiche)))), 2) : 0;
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Statistiche Test - <?= htmlspecialchars($nome_corso) ?></title>
    <?php require "../config/include/header.html"; ?>
    <link rel="stylesheet" href="../config/include/custom.css?v=<?= time() ?>">
</head>
<body>
<?php include "config/include/navbar.php"; ?>
<div class="container mt-5">
    <div class="card card-cv mx-auto" style="max-width: 600px;">
        <h3>Statistiche Test – <?= htmlspecialchars($nome_corso) ?> (<?= date("d/m/Y", strtotime($data_edizione)) ?>)</h3>
        <p class="text-muted">Punteggio medio stimato: <strong><?= $punteggio_medio ?>%</strong> su <?= $num_discenti ?> partecipanti</p>
        <div class="d-flex flex-wrap gap-2 mt-2">
            <!--
            <a href="esporta_statistiche.php?id_edizione=<?= $id_edizione ?>" class="btn btn-outline-success">
                <i class="fas fa-file-excel"></i> Esporta
            </a>
            -->
            <a href="risultati_individuali.php?id_edizione=<?= $id_edizione ?>" class="btn btn-outline-primary"><i class="fa fa-user"></i> Valutazioni</a>
            <a href="gestisci_edizioni.php?id_corso=<?= $id_corso; ?>" class="btn btn-secondary">
                ⬅ Indietro
            </a>
        </div>
        <hr>
        <?php if (empty($statistiche)): ?>
            <p>Nessun errore registrato per questa edizione.</p>
        <?php else: ?>
            <?php foreach ($statistiche as $id_domanda => $data): ?>
                <div class="mb-4">
                    <h5 class="text-cv">❌ <?= htmlspecialchars($data['domanda']) ?></h5>
                    <ul>
                        <?php foreach ($data['risposte'] as $indice => $count): ?>
                            <li>
                                <strong><?= $count ?></strong> risposte: "<?= htmlspecialchars($data['testi'][$indice]) ?>"
                                <?= ($indice == $data['corretta']) ? "<span class='badge bg-success ms-2'>Corretta</span>" : "" ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

    </div>
</div>
</body>
</html>
