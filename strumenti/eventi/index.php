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

if(isset($_POST["submitButton"])){

    $id='V9997';
    $password = $_POST["typepassword"];
    $query = $db->query("SELECT * FROM utenti WHERE ID='$id' AND password='$password'");
    if ($query->num_rows>0) {
        list($id) = $query->fetch_array();
        $_SESSION["ID"] = $id;
        header("Location: ../eventi/interventi/list.php");
    }
    else{
        echo "<script type='text/javascript'>alert('Accesso negato')</script>";
    }
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
    <h2 class="text-center mb-4">Gestione eventi</h2>
    <div class="card card-cv mx-auto" style="max-width: 600px;">
        <div class="d-grid gap-3">
       <!--     <button class="btn btn-outline-cv " href="" disabled><i class="fas fa-tasks" ></i> Gestione eventi</button>-->
            <a role="button" class="btn btn-outline-cv" href="interventi/index.php"><i class="fas fa-user-plus"></i> Interventi</a>
        </div>
    </div>
</div>

<!-- MODAL LOGIN -->
<div class="modal" id="modal3" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content">
            <form method="post" action="index.php">
                <div class="modal-header">
                    <h6 class="modal-title" id="modal1Title">INSERISCI PASSWORD</h6>
                </div>
                <div class="modal-body">
                    <input id="typepassword" name="typepassword" class="form-control form-control-sm" type="password">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-dismiss="modal">Annulla</button>
                    <button type="submit" class="btn btn-outline-success btn-sm" id="submitButton" name="submitButton">LOGIN</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php include "../config/include/footer.php"; ?>
</body>
</html>