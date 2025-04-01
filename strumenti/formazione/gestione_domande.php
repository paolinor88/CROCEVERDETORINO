<?php
/**
 *
 * @author     Paolo Randone
 * @author     <paolo.randone@croceverde.org>
 * @version    8.2
 * @note         Powered for Croce Verde Torino. All rights reserved
 *
 */
session_start();
include "../config/config.php";

if (!isset($_SESSION['Livello']) || $_SESSION['Livello'] != 28 ) {
    header("Location: index.php");
    exit();
}

$query_corsi = "SELECT id_corso, titolo FROM corsi ORDER BY titolo";
$result_corsi = $db->query($query_corsi);
$corsi = $result_corsi->fetch_all(MYSQLI_ASSOC);

$lezioni = [];
$domande = [];
$id_corso = isset($_GET['id_corso']) ? intval($_GET['id_corso']) : null;
$id_lezione = isset($_GET['id_lezione']) ? intval($_GET['id_lezione']) : null;
$domanda_modifica = null;

if ($id_corso) {
    $stmt = $db->prepare("SELECT id_lezione, titolo FROM lezioni WHERE id_corso = ? ORDER BY ordine");
    $stmt->bind_param("i", $id_corso);
    $stmt->execute();
    $result_lezioni = $stmt->get_result();
    $lezioni = $result_lezioni->fetch_all(MYSQLI_ASSOC);
}

if ($id_lezione) {
    $stmt = $db->prepare("SELECT * FROM test_domande WHERE id_lezione = ?");
    $stmt->bind_param("i", $id_lezione);
    $stmt->execute();
    $result_domande = $stmt->get_result();
    $domande = $result_domande->fetch_all(MYSQLI_ASSOC);
}

if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $id_domanda = intval($_GET['edit']);
    $stmt = $db->prepare("SELECT * FROM test_domande WHERE id = ?");
    $stmt->bind_param("i", $id_domanda);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $domanda_modifica = $result->fetch_assoc();
    }
}

$aggiungi_domanda = isset($_GET['aggiungi']);
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestione Domande</title>
    <?php require "config/include/header.html"; ?>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../config/include/custom.css?v=<?= time() ?>">
    <style>
        .correct-answer {
            font-weight: bold;
            color: #008000;
        }

        ul.list-group ul {
            margin-top: 0.5rem;
            margin-bottom: 0.5rem;
        }
    </style>
</head>

<body>
<?php include "config/include/navbar.php"; ?>

<!-- SWEETALERT -->
<?php if (isset($_GET['msg']) && isset($_GET['icon'])): ?>
    <script>
        Swal.fire({
            title: "<?= htmlspecialchars($_GET['msg']); ?>",
            icon: "<?= htmlspecialchars($_GET['icon']); ?>",
            confirmButtonText: "OK"
        }).then(() => {
            window.location.href = "gestione_domande.php?id_corso=<?= $id_corso; ?>&id_lezione=<?= $id_lezione; ?>";
        });
    </script>
<?php endif; ?>

<!-- CONTENUTO -->
<div class="container-fluid px-2 mb-4">

    <div class="card card-cv">
        <h3 class="text mb-4">Gestione Test</h3>
        <!-- CORSO -->
        <form method="GET" action="gestione_domande.php" class="mb-4">
            <label for="id_corso">Seleziona un corso:</label>
            <select name="id_corso" id="id_corso" class="form-control" onchange="this.form.submit()">
                <option value="">-- Seleziona un corso --</option>
                <?php foreach ($corsi as $corso): ?>
                    <option value="<?= $corso['id_corso']; ?>" <?= ($id_corso == $corso['id_corso']) ? 'selected' : ''; ?>>
                        <?= htmlspecialchars($corso['titolo']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>

        <!-- LEZIONE -->
        <?php if (!empty($lezioni)): ?>
            <form method="GET" action="gestione_domande.php" class="mb-4">
                <input type="hidden" name="id_corso" value="<?= $id_corso; ?>">
                <label for="id_lezione">Seleziona una lezione:</label>
                <select name="id_lezione" id="id_lezione" class="form-control" onchange="this.form.submit()">
                    <option value="">-- Seleziona una lezione --</option>
                    <?php foreach ($lezioni as $lezione): ?>
                        <option value="<?= $lezione['id_lezione']; ?>" <?= ($id_lezione == $lezione['id_lezione']) ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($lezione['titolo']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>
        <?php endif; ?>

        <!-- FORM DOMANDA -->
        <?php if ($domanda_modifica || $aggiungi_domanda): ?>
            <?php include "form_domanda.php"; ?>
        <?php endif; ?>

        <!-- ELENCO DOMANDE -->
        <?php if (!empty($domande)): ?>
            <h4 class="mt-4">Domande:</h4>
            <ul class="list-group">
                <?php foreach ($domande as $domanda): ?>
                    <li class="list-group-item">
                        <strong><?= htmlspecialchars($domanda['domanda']); ?></strong>
                        <ul>
                            <?php for ($i = 1; $i <= 4; $i++): ?>
                                <li class="<?= ($domanda['risposta_corretta'] == $i) ? 'correct-answer' : ''; ?>">
                                    <?= htmlspecialchars($domanda["risposta$i"]); ?>
                                </li>
                            <?php endfor; ?>
                        </ul>
                        <a href="gestione_domande.php?edit=<?= $domanda['id']; ?>&id_corso=<?= $id_corso; ?>&id_lezione=<?= $id_lezione; ?>" class="btn btn-warning btn-sm">Modifica</a>
                        <button class="btn btn-danger btn-sm" onclick="confermaEliminazione(<?= $domanda['id']; ?>, '<?= $id_corso; ?>', '<?= $id_lezione; ?>')">Elimina</button>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <?php if ($id_corso && $id_lezione): ?>
            <a href="gestione_domande.php?id_corso=<?= $id_corso; ?>&id_lezione=<?= $id_lezione; ?>&aggiungi=1" class="btn btn-outline-success mt-4">Aggiungi Domanda</a>
        <?php endif; ?>
    </div>
</div>

<script>
    function confermaEliminazione(id, id_corso, id_lezione) {
        Swal.fire({
            title: "Sei sicuro?",
            text: "Questa operazione è irreversibile!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Sì, elimina!",
            cancelButtonText: "Annulla"
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = `elimina_domanda.php?id=${id}&id_corso=${id_corso}&id_lezione=${id_lezione}`;
            }
        });
    }
</script>

<?php include "config/include/footer.php"; ?>
</body>
</html>
