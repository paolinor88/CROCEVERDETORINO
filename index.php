<?php
/**
 *
 * @author     Paolo Randone
 * @author     <mail@paolorandone.it>
 * @version    1.3
 * @note       Powered for Croce Verde Torino. All rights reserved
 *
 */
session_start();
//parametri DB
include "config/config.php";
//login
if (!isset($_SESSION["ID"])){
    header("Location: login.php");
}
//ID SESSIONE
$id=$_SESSION["ID"];
//variabili di sessione
$select = $db->query("SELECT * FROM utenti WHERE ID='$id'");
while($list = $select->fetch_array()){
    $cognome=$list["cognome"];
    $nome=$list["nome"];
    $email=$list["email"];
    $password=$list["password"];
    $telefono=$list["telefono"];
    $ciclico=$list["ciclico"];
    $livello=$list["livello"];
    $stato=$list["stato"];
    $sezione=$list["sezione"];
    $squadra=$list["squadra"];
}
$dictionaryLivello = array (
    1 => "Dipendente",
    2 => "Volontario",
    3 => "Altro",
    4 => "Logistica",
    5 => "Segreteria",
    6 => "ADMIN",
);
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <meta name="author" content="Paolo Randone">
    <title>Gestionale CV-TO - Home page</title>

    <link rel="stylesheet" href="config/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/css/bootstrap4-toggle.min.css" rel="stylesheet">    <link href='https://use.fontawesome.com/releases/v5.0.6/css/all.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="config/css/custom.css">

    <script src="https://code.jquery.com/jquery-3.4.1.js" integrity="sha256-WpOohJOqMqqyKL9FccASB9O0KwACQJpFTUBLTYOVvVU=" crossorigin="anonymous"></script>
    <script src="config/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/js/bootstrap4-toggle.min.js"></script>    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>


</head>
<body>
<div class="container-fluid">
    <br>
    <div class="row text-center">
        <div class="col-md-3 col-md-offset-3"></div>
        <div class="text-center col-md-6">
            <div class="jumbotron">
                <div align="center"><img class="img-fluid" src="config/images/logo.png" alt="logoCVTO"/></div>
                <h4 class="text-center" style="color: #078f40">Gestionale</h4>
                <hr>
                <a role="button" class="btn btn-outline-cv btn-block <?if($livello<4){echo "disabled";}?>" href="utenti/index.php"><i class="fas fa-user"></i> Gestione utenze</a>
                <a role="button" class="btn btn-outline-cv btn-block <?if($livello<4){echo "disabled";}?>" href="doc/index.php"><i class="fas fa-file"></i> Documentazione</a>
                <a role="button" class="btn btn-outline-cv btn-block <?if($livello!=6){echo "disabled";}?>" href="checklist/index.php"><i class="fas fa-tasks"></i> Checklist elettronica</a>
                <a role="button" class="btn btn-outline-cv btn-block" href="eventi/index.php"><i class="far fa-calendar-alt"></i> Eventi e calendario</a>
                <a role="button" class="btn btn-outline-cv btn-block <?if($livello<4){echo "disabled";};?>" href="magazzino/index.php"><i class="fas fa-book"></i> Magazzino</a>
                <?
                if ($id=='D9999'){
                    echo
                    "<a role='button' class='btn btn-outline-secondary btn-block' href='https://login.siteground.com' target='_blank'><i class='fas fa-question'></i> ADMIN</a>
                    ";
                }
                ?>
                <a role="button" class="btn btn-outline-danger btn-block" href="logout.php"><i class="fas fa-times"></i> Logout</a>
            </div>
        </div>
    </div>
</div>
</body>
<?php include('config/include/footer.php'); ?>
</html>