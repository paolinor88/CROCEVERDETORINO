<?php
session_start();
/**
 *
 * @author     Paolo Randone
 * @author     <paolo.randone@croceverde.org>
 * @version    8.2
 * @note         Powered for Croce Verde Torino. All rights reserved
 *
 */
include "../config/config.php";
global $db;

$query = "
    SELECT DISTINCT c.id_corso, c.titolo, c.descrizione, c.accesso_libero
    FROM corsi c
    JOIN edizioni_corso e ON c.id_corso = e.id_corso
    WHERE e.archiviata = 0
";

$result = $db->query($query);
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Corsi</title>

    <?php require "../config/include/header.html"; ?>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../config/include/custom.css?v=<?= time() ?>">
</head>

<body>

<?php include "../config/include/navbar.php"; ?>

<!-- CONTENUTO -->
<div class="container-fluid px-2 mb-4">
    <div class="card card-cv">

        <h3 class="mb-4">Elenco corsi</h3>

        <?php if ($result->num_rows > 0): ?>
            <div class="row g-3">
                <?php while ($corso = $result->fetch_assoc()): ?>
                    <div class="col-md-12">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title mb-2"><?= htmlspecialchars($corso['titolo']) ?></h5>
                                <p class="mb-3"><?= nl2br(htmlspecialchars($corso['descrizione'])) ?></p>

                                <div class="d-flex justify-content-between align-items-center">
                                    <a href="lezioni.php?id_corso=<?= $corso['id_corso'] ?>"
                                       class="btn btn-outline-cv btn-sm">
                                        <?= $corso['accesso_libero'] ? "Entra" : "Accedi"; ?>
                                    </a>

                                    <?php if ($corso['accesso_libero']): ?>
                                        <span class="badge badge-cv-success">Libero</span>
                                    <?php else: ?>
                                        <span class="badge badge-cv-warning">Riservato</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p class="text-muted">Nessun corso disponibile al momento.</p>
        <?php endif; ?>

        <?php if (isset($_SESSION['discente_id'])): ?>
            <div class="mt-4 text-end">
                <a href="logout.php" class="btn btn-outline-cv btn-sm">Logout</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include "config/include/footer.php"; ?>
</body>
</html>
