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

$query = "
    SELECT r.discente_id, u.nome, u.cognome, u.codice_fiscale,
           ROUND(AVG(r.percentuale), 2) AS media_percentuale,
           COUNT(r.id) AS lezioni_superate
    FROM risultati_test r
    JOIN discenti u ON u.id = r.discente_id
    WHERE r.id_edizione = ?
    GROUP BY r.discente_id
    ORDER BY media_percentuale DESC
";
$stmt = $db->prepare($query);
$stmt->bind_param("i", $id_edizione);
$stmt->execute();
$result = $stmt->get_result();
$discenti = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Recupera dettagli per lezione per ogni discente
$lezioni = [];
foreach ($discenti as &$discente) {
    $id_disc = $discente['discente_id'];
    $stmt = $db->prepare("SELECT l.titolo, r.percentuale FROM risultati_test r JOIN lezioni l ON r.id_lezione = l.id_lezione WHERE r.discente_id = ? AND r.id_edizione = ? ORDER BY l.ordine");
    $stmt->bind_param("ii", $id_disc, $id_edizione);
    $stmt->execute();
    $res = $stmt->get_result();
    $lezioni_discente = [];
    while ($riga = $res->fetch_assoc()) {
        $lezioni_discente[] = [
            "titolo" => $riga['titolo'],
            "percentuale" => $riga['percentuale']
        ];
    }
    $discente['lezioni'] = $lezioni_discente;
    $stmt->close();
}
unset($discente);
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Valutazioni Individuali - <?= htmlspecialchars($nome_corso) ?></title>
    <?php require "../config/include/header.html"; ?>
    <link rel="stylesheet" href="../config/include/custom.css?v=<?= time() ?>">
</head>
<body>
<?php include "config/include/navbar.php"; ?>
<div class="container-fluid px-2 mb-4">
    <div class="card card-cv p-4">
        <h3>Valutazioni individuali – <?= htmlspecialchars($nome_corso) ?> (<?= date("d/m/Y", strtotime($data_edizione)) ?>)</h3>

        <?php if (empty($discenti)): ?>
            <p>Nessun risultato disponibile per questa edizione.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-light">
                    <tr>
                        <th>Codice Fiscale</th>
                        <th>Nome</th>
                        <th>Cognome</th>
                        <th>Punteggio Medio Corso</th>
                        <th>Esito</th>
                        <th>Dettaglio Lezioni</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($discenti as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['codice_fiscale']) ?></td>
                            <td><?= htmlspecialchars($row['nome']) ?></td>
                            <td><?= htmlspecialchars($row['cognome']) ?></td>
                            <td><?= $row['media_percentuale'] ?>%</td>
                            <td>
                                <?= ($row['media_percentuale'] >= 75)
                                    ? '<span class="badge bg-success">Superato</span>'
                                    : '<span class="badge bg-danger">Non superato</span>' ?>
                            </td>
                            <td>
                                <ul class="mb-0">
                                    <?php foreach ($row['lezioni'] as $lez): ?>
                                        <li><?= htmlspecialchars($lez['titolo']) ?>: <strong><?= $lez['percentuale'] ?>%</strong></li>
                                    <?php endforeach; ?>
                                </ul>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
        <div class="d-flex flex-wrap gap-2 mt-4">
        <a href="statistiche_test.php?id_edizione=<?= $id_edizione ?>"  class="btn btn-secondary">
            ⬅ Indietro
        </a>
        </div>
    </div>
</div>
</body>
</html>