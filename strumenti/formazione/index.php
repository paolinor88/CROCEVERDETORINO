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
    <title>Portale Formazione</title>

    <?php require "../config/include/header.html"; ?>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../config/include/custom.css?v=<?= time() ?>">

</head>

<body>

<?php include "../config/include/navbar.php"; ?>

<!-- CONTENUTO -->
<div class="container mb-5">
    <h2 class="text-center mb-4">Formazione</h2>

    <div class="card card-cv mx-auto" style="max-width: 480px;">
        <div class="d-grid gap-3">
            <a role="button" class="btn btn-outline-cv" href="corsi.php">
                <i class="far fa-folder-open me-2"></i> Elenco corsi
            </a>

            <?php if (isset($_SESSION['Livello']) && $_SESSION['Livello'] == 28): ?>
                <a role="button" class="btn btn-outline-cv" href="area_formatori.php">
                    <i class="fas fa-graduation-cap me-2"></i> Area Formatori
                </a>
            <?php else: ?>
                <a role="button" class="btn btn-outline-cv" href="#" data-bs-toggle="modal" data-bs-target="#modal3">
                    <i class="fas fa-graduation-cap me-2"></i> Area Formatori
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- MODALE LOGIN -->
<div class="modal fade" id="modal3" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <form method="post" action="login_formatori.php">
                <div class="modal-header bg-light">
                    <h6 class="modal-title">INSERISCI CREDENZIALI</h6>
                </div>
                <div class="modal-body">
                    <input type="text" name="matricolaOP" class="form-control form-control-sm mb-2" placeholder="Login">
                    <input type="password" name="passwordOP" class="form-control form-control-sm" placeholder="Password" required>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-outline-success btn-sm"  id="LoginBTN" name="LoginBTN">ACCEDI</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include "../config/include/footer.php"; ?>
</body>
</html>
