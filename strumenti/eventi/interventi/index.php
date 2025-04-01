<?php
/**
 *
 * @author     Paolo Randone
 * @author     <paolo.randone@croceverde.org>
 * @version    8.2
 * @note       Powered for Croce Verde Torino. All rights reserved
 *
 */
session_start();
include "../config/config.php";

if (isset($_POST["LoginBTN"])) {
    $id = $_POST["matricolaOP"];
    $password = $_POST["passwordOP"];

    $hashedPassword = md5($password);

    $stmt = $db->prepare("SELECT * FROM Operatori WHERE LoginOperatore=?");
    $stmt->bind_param("s", $id);
    $stmt->execute();

    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        list($id, $NomeOperatore, $LoginOperatore, $dbHashedPassword, $Livello) = $result->fetch_array();

        if ($hashedPassword === $dbHashedPassword) {
            $_SESSION["ID"] = $id;
            $_SESSION["NomeOperatore"] = $NomeOperatore;
            $_SESSION["LoginOperatore"] = $LoginOperatore;
            $_SESSION["Livello"] = $Livello;

            header("Location: list.php");
            exit;
        } else {
            echo "<script type='text/javascript'>alert('Accesso negato')</script>";
        }
    } else {
        echo "<script type='text/javascript'>alert('Accesso negato')</script>";
    }

    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="Paolo Randone">

    <title>Gestione eventi CV-TO</title>

    <?php require "../config/include/header.html"; ?>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../config/include/custom.css?v=<?= time() ?>">

</head>
<body>
<?php include "../config/include/navbar.php"; ?>

<div class="container mb-5">
    <h2 class="text-center mb-4">Gestione Interventi</h2>
    <div class="card card-cv mx-auto" style="max-width: 600px;">
        <div class="d-grid gap-3">
            <a role="button" class="btn btn-outline-cv" href="new.php"><i class="fas fa-user-plus"></i> Inserisci intervento</a>
            <a role="button" class="btn btn-outline-cv" href="monitor.php" TARGET="_blank"><i class="fa-solid fa-desktop"></i> Stato risorse</a>
            <?if (isset($_SESSION['ID']) && in_array($_SESSION['Livello'], [1, 2, 20, 25, 26, 27])): ?>
                <a role="button" class="btn btn-outline-cv " href="list.php"><i class="fas fa-tasks"></i> Elenco interventi</a>
            <? endif; ?>
            <?if(!isset($_SESSION['ID'])):?>
                <button class="btn btn-outline-cv" data-bs-toggle="modal" data-bs-target="#modal3"><i class="fas fa-tasks"></i> Elenco interventi</button>
            <? endif; ?>
        </div>
    </div>
</div>
<!-- MODAL LOGIN -->
<div class="modal" id="modal3" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content">
            <form method="post" action="index.php">
                <div class="modal-header">
                    <h6 class="modal-title" id="modal1Title">INSERISCI CREDENZIALI</h6>
                </div>
                <div class="modal-body">
                    <input type="text" name="matricolaOP" class="form-control form-control-sm mb-2" placeholder="Login">
                    <input type="password" name="passwordOP" class="form-control form-control-sm" placeholder="Password" required>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-outline-success btn-sm" id="LoginBTN" name="LoginBTN">ACCEDI</button>
                </div>
            </form>
        </div>
    </div>
</div>


<?php include('../config/include/footer.php'); ?>
</body>
</html>