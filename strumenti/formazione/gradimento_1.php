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
error_reporting(E_ALL);
ini_set('display_errors', 1);
include "../config/config.php";
global $db;

$inviato = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //$IDCorso = intval($_POST["IDCorso"] ?? 0);
    $IDCorso = 1;
    $commento = trim($_POST["commento"]);
    $fields = ["IDCorso"];
    $placeholders = ["?"];
    $types = "i";
    $values = [$IDCorso];

    for ($i = 1; $i <= 25; $i++) {
        $val = isset($_POST["q$i"]) ? intval($_POST["q$i"]) : null;
        $fields[] = "q$i";
        $placeholders[] = "?";
        $types .= "i";
        $values[] = $val;
    }

    $fields[] = "commento";
    $placeholders[] = "?";
    $types .= "s";
    $values[] = $commento;

    $query = "INSERT INTO questionari_gradimento (" . implode(",", $fields) . ") VALUES (" . implode(",", $placeholders) . ")";
    $stmt = $db->prepare($query);

    if (!$stmt) {
        die("<p class='text-danger'>Errore nella query: " . $db->error . "</p><pre>$query</pre>");
    }

    $stmt->bind_param($types, ...$values);
    $stmt->execute();


    $inviato = true;
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Questionario di Gradimento</title>
    <?php require "../config/include/header.html"; ?>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../config/include/custom.css?v=<?= time() ?>">
</head>
<body>

<div class="container mt-4">
    <div class="card card-cv p-4">
        <?php if ($inviato): ?>
            <h4 class="text-success">✅ Grazie per aver compilato il questionario!</h4>
            <p>Ora puoi accedere con il tuo <strong>Codice Fiscale</strong> e <strong>Password</strong> per scaricare il tuo certificato.</p>
            <a href="login.php?redirect=<?= urlencode('download_attestati.php?IDCorso=1') ?>" class="btn btn-outline-cv">Accedi per scaricare il certificato</a>
        <?php else: ?>
            <h2 class=" mb-4">CORSO PBLS-D</h2>
            <h4 class="mb-2">Questionario di Gradimento</h4>
        <p>
            Il nostro scopo è di assicurarci di aver fornito un programma efficace rispetto alle tue necessità professionali e alle tue aspettative. Per noi è importante la tua opinione e abbiamo bisogno del tuo feed-back. L’organizzatore rivedrà le tue valutazioni per porre in atto tutte le modifiche necessarie per il miglioramento continuo del corso.
        </p>
        <p>
            Ti chiediamo pertanto di dare una valutazione ad ogni item assegnando un punteggio da 1 a 5 dove 1 corrisponde a “non sono assolutamente d’accordo” e 5 a “sono molto d’accordo”. <b>Ti ricordiamo che il questionario è completamente anonimo e che, al termine, potrai scaricare il tuo attestato di completamento.</b>
        </p>
            <form method="POST">
                <input type="hidden" name="IDCorso" value="<?= htmlspecialchars($_GET['IDCorso'] ?? 0) ?>">

                <?php
                $res = $db->query("SELECT * FROM questionari_domande ORDER BY id ASC");
                $sezione_corrente = "";
                while ($row = $res->fetch_assoc()):
                    if ($sezione_corrente != $row['sezione']):
                        if ($sezione_corrente != "") echo "<hr>";
                        $sezione_corrente = $row['sezione'];
                        echo "<h5 class='mt-3 text-cv text-uppercase'>" . ucfirst($sezione_corrente) . "</h5>";
                    endif;
                    ?>
                    <div class="mb-3">
                        <label><strong><?= htmlspecialchars($row['testo_domanda']) ?></strong></label><br>
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="<?= $row['campo'] ?>" value="<?= $i ?>" required>
                                <label class="form-check-label"><?= $i ?></label>
                            </div>
                        <?php endfor; ?>
                    </div>
                <?php endwhile; ?>
                <hr>
                <div class="mb-4">
                    <label for="commento">Commento facoltativo</label>
                    <textarea name="commento" id="commento" class="form-control" rows="3" placeholder="Scrivi un commento se vuoi..."></textarea>
                </div>

                <button type="submit" class="btn btn-outline-cv">Invia Questionario</button>
            </form>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
