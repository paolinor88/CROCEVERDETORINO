<?php
//header('Access-Control-Allow-Origin: *');
global $db;
/**
 *
 * @author     Paolo Randone
 * @author     <paolo.randone@croceverde.org>
 * @version    8.2
 * @note         Powered for Croce Verde Torino. All rights reserved
 *
 */ 
session_start();

include "config/config.php";

if (isset($_POST["LoginBTN"])) {
    $id = $_POST["matricolaOP"];
    $password = $_POST["passwordOP"];
    $destination = $_POST["destination"];

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

            header("Location: {$destination}/index.php");
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

    <title>Strumenti CV-TO</title>
    <link href="config/include/bootstrap.css" rel="stylesheet">

    <script src="https://kit.fontawesome.com/fa32a0bcb4.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="config/include/custom.css?v=<?= time() ?>">

    <link rel="apple-touch-icon" sizes="180x180" href="config/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="config/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="config/favicon/favicon-16x16.png">
    <link rel="manifest" href="config/favicon/site.webmanifest">
    <link rel="mask-icon" href="config/favicon/safari-pinned-tab.svg" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="theme-color" content="#ffffff">

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll('.btn-outline-cv').forEach(button => {
                button.addEventListener('click', function() {
                    document.getElementById('destination').value = this.getAttribute('value').split('/')[0];
                });
            });
        });
    </script>

</head>
<body class="bg-light">
<div class="container d-flex flex-column justify-content-center align-items-center min-vh-100">
    <div class="text-center mb-4">
        <img src="config/images/logo.png" class="img-fluid" style="max-width: 300px;" alt="LOGOCVTOESTESO">
    </div>

    <div class="card p-4 shadow-sm rounded-4" style="max-width: 500px; width: 100%;">
        <div class="d-grid gap-3">
            <a role="button" class="btn btn-outline-cv" href="eventi/index.php">
                <i class="fas fa-tasks me-2"></i> Eventi
            </a>

            <?php if (isset($_SESSION['ID']) && in_array($_SESSION['Livello'], [1, 20, 23, 24, 25, 26, 27, 28, 29, 30])): ?>
                <a role="button" class="btn btn-outline-cv" href="rubrica/index.php">
                    <i class="fa-solid fa-address-book me-2"></i> Rubrica
                </a>
                <a role="button" class="btn btn-outline-cv" href="autisti/index.php">
                    <i class="fa-solid fa-car me-2"></i> Autisti
                </a>
            <?php elseif (isset($_SESSION['ID'])): ?>
                <button class="btn btn-outline-cv" data-bs-toggle="modal" data-bs-target="#modal4">
                    <i class="fa-solid fa-address-book me-2"></i> Rubrica
                </button>
                <button class="btn btn-outline-cv" data-bs-toggle="modal" data-bs-target="#modal4">
                    <i class="fa-solid fa-car me-2"></i> Autisti
                </button>
            <?php else: ?>
                <button class="btn btn-outline-cv" data-bs-toggle="modal" data-bs-target="#modal3" value="rubrica">
                    <i class="fa-solid fa-address-book me-2"></i> Rubrica
                </button>
                <button class="btn btn-outline-cv" data-bs-toggle="modal" data-bs-target="#modal3" value="autisti">
                    <i class="fa-solid fa-car me-2"></i> Autisti
                </button>
            <?php endif; ?>

            <a role="button" class="btn btn-outline-cv" href="utility/index.php">
                <i class="fa-solid fa-screwdriver-wrench me-2"></i> Utility
            </a>
            <a role="button" class="btn btn-outline-secondary" href="http://galileoambulanze.eu" target="_blank">
                <i class="fas fa-external-link-alt me-2"></i> GALILEO
            </a>
            <a role="button" class="btn btn-outline-secondary" href="logout.php">
                <i class="fa-solid fa-sign-out me-2"></i> Logout
            </a>
        </div>
    </div>
    <?php include "config/include/footer.php"; ?>
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
                    <input type="hidden" id="destination" name="destination" value="">
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-outline-success btn-sm" id="LoginBTN" name="LoginBTN">ACCEDI</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal" id="modal4" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content">
            <form method="post" action="index.php">
                <div class="modal-header">
                    <h6 class="modal-title" id="modal1Title">ERRORE</h6>
                </div>
                <div class="modal-body">
                    <p>Operazione non consentita per questo livello di accesso</p>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>