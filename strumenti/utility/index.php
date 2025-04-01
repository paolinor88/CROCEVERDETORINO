<?php
header('Access-Control-Allow-Origin: *');
/**
 *
 * @author     Paolo Randone
 * @author     <paolo.randone@croceverde.org>
 * @version    1.0
 * @note         Powered for Croce Verde Torino. All rights reserved
 *
 */
session_start();
include "../config/config.php";

?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="Paolo Randone">

    <title>Utility</title>

    <?php require "../config/include/header.html"; ?>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../config/include/custom.css?v=<?= time() ?>">



</head>
<body>
<?php include "../config/include/navbar.php"; ?>

<div class="container mb-5">
    <h2 class="text-center mb-4">Utility</h2>

    <div class="card card-cv mx-auto" style="max-width: 600px;">
        <div class="d-grid gap-3">
            <a role="button" class="btn btn-outline-cv" href="calcolaossigeno.php">
                <i class="fas fa-calculator"></i> Calcolo ossigeno viaggi
            </a>
            <a role="button" class="btn btn-outline-cv" href="calcolabombole.php">
                <i class="fas fa-calculator"></i> Calcolo durata bombole
            </a>
        </div>
    </div>
</div>

</body>
<?php include "../config/include/footer.php"; ?>
</html>