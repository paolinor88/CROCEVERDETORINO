<?php
session_start();
/**
 *
 * @author     Paolo Randone
 * @author     <paolo.randone@croceverde.org>
 * @version    1.0
 * @note         Powered for Croce Verde Torino. All rights reserved
 *
 */
include "../config/config.php";

if (!isset($_SESSION['Livello']) || $_SESSION['Livello'] != 28 ) {
    header("Location: index.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Area Formatori</title>

    <?php require "../config/include/header.html"; ?>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../config/include/custom.css?v=<?= time() ?>">
</head>

<body>

<?php include "../config/include/navbar.php"; ?>

<!-- CONTENUTO -->
<div class="container mb-5">
    <h2 class="text-center mb-4">Area Formatori</h2>

    <div class="card card-cv mx-auto" style="max-width: 480px;">
        <div class="d-grid gap-3">
            <a href="gestione_corsi.php" class="btn btn-outline-cv">
                <i class="far fa-list-alt me-2"></i> Gestione Corsi
            </a>
            <a href="gestione_domande.php" class="btn btn-outline-cv">
                <i class="fas fa-question-circle me-2"></i> Gestione Domande
            </a>
            <a href="gestione_discenti.php" class="btn btn-outline-cv">
                <i class="fas fa-users me-2"></i> Gestione Discenti
            </a>
        </div>
    </div>
</div>

<?php include "config/include/footer.php"; ?>
</body>
</html>

