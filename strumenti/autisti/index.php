<?php
header('Access-Control-Allow-Origin: *');
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
include "../config/include/destinatari.php";

if (!in_array($_SESSION['Livello'], [1, 20, 23, 24, 25, 26, 27, 28, 29,30])) {
    header("Location: ../index.php");
    echo "<script type='text/javascript'>alert('Accesso negato');</script>";
    exit;
}

if (isset($_POST["LoginBTN"])) {
    $id = $_POST["matricolaOP"];
    $password = $_POST["passwordOP"];

    $hashedPassword = md5($password);

    $stmt = $db->prepare("SELECT * FROM Operatori_Montagna WHERE LoginOperatore=?");
    $stmt->bind_param("s", $id);
    $stmt->execute();

    $result = $stmt->get_result();
    if ($result->num_rows > 0) {

        list($id, $NomeOperatore, $LoginOperatore, $dbHashedPassword, $Livello, $Email, $TelegramID, $Stazione, $Tipo) = $result->fetch_array();

        if ($hashedPassword === $dbHashedPassword) {
            $_SESSION["ID"] = $id;
            $_SESSION["NomeOperatore"] = $NomeOperatore;
            $_SESSION["LoginOperatore"] = $LoginOperatore;
            $_SESSION["Livello"] = $Livello;
            $_SESSION["Stazione"] = $Stazione;
            $_SESSION["Tipo"] = $Tipo;

            header("Location: calendario.php");
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portale Gruppo Autisti</title>

    <?php require "../config/include/header.html"; ?>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../config/include/custom.css?v=<?= time() ?>">
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>

</head>
<body>

<?php include "../config/include/navbar.php"; ?>

<!-- CONTENUTO -->
<div class="container mb-5">
    <h2 class="text-center mb-4">Gruppo Autisti</h2>

    <div class="card card-cv mx-auto" style="max-width: 600px;">
        <div class="d-grid gap-3">
            <a class="btn btn-outline-cv" href="anagrafica2.php">
                <i class="fa-solid fa-address-book me-2"></i> Anagrafica autisti
            </a>
            <a class="btn btn-outline-cv" href="report.php">
                <i class="fa-solid fa-chart-bar me-2"></i> Rapporti
            </a>
            <a class="btn btn-outline-cv" href="autocertificazioni.php">
                <i class="far fa-folder-open me-2"></i> Autocertificazioni
            </a>
            <a class="btn btn-outline-cv" href="form_A1.php" target="_blank">
                <i class="fas fa-file-signature me-2"></i> Modulo autocertificazione
            </a>
            <a class="btn btn-outline-cv" href="listaprove.php">
                <i class="fas fa-graduation-cap me-2"></i> Gestione esami
            </a>
            <a class="btn btn-outline-cv" href="form_A2.php" target="_blank">
                <i class="fas fa-file-export me-2"></i> Richiedi prova RIENTRI / NORMALI / OVER 65
            </a>
            <?php if ($_SESSION['Livello'] != 23 && $_SESSION['Livello'] != 24): ?>
                <a class="btn btn-outline-cv" href="form_A3.php" target="_blank">
                    <i class="fas fa-file-export me-2"></i> Richiedi prova URGENZE
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- MODAL LOGIN -->
<div class="modal fade" id="modal3" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <form method="post" action="index.php">
                <div class="modal-header bg-light">
                    <h6 class="modal-title">INSERISCI CREDENZIALI</h6>
                </div>
                <div class="modal-body">
                    <input type="text" name="matricolaOP" class="form-control form-control-sm mb-2" placeholder="Matricola" required>
                    <input type="password" name="passwordOP" class="form-control form-control-sm" placeholder="Password" required>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-outline-success btn-sm" name="LoginBTN" id="LoginBTN">ACCEDI</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include "../config/include/footer.php"; ?>
</body>
</html>
