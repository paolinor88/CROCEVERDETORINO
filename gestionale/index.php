<?php global $db;
/**
 *
 * @author     Paolo Randone
 * @author     <paolo.randone@croceverde.org>
* @version    8.2
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
    <link rel="apple-touch-icon" sizes="57x57" href="config/favicon/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="config/favicon/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="config/favicon/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="config/favicon/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="config/favicon/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="config/favicon/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="config/favicon/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="config/favicon/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="config/favicon/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192"  href="config/favicon/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="config/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="config/favicon/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="config/favicon/favicon-16x16.png">
    <link rel="manifest" href="config/favicon/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">
    <title>Gestionale CV-TO - Home page</title>

    <link rel="stylesheet" href="config/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/css/bootstrap4-toggle.min.css" rel="stylesheet">
    <link href='https://use.fontawesome.com/releases/v5.0.6/css/all.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="config/css/custom.css">

    <script src="https://code.jquery.com/jquery-3.4.1.js" integrity="sha256-WpOohJOqMqqyKL9FccASB9O0KwACQJpFTUBLTYOVvVU=" crossorigin="anonymous"></script>
    <script src="config/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/js/bootstrap4-toggle.min.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
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
                <a role="button" class="btn btn-outline-cv btn-block <?if($livello<4){echo "disabled";}?>" href="utenti/index.php"><i class="fas fa-user"></i> Utenze</a>
                <a role="button" class="btn btn-outline-cv btn-block <?if($livello==3){echo "disabled";}?>" href="eventi/index.php"><i class="far fa-calendar-alt"></i> Calendario</a>
                <a role="button" class="btn btn-outline-cv btn-block " href="magazzino/index.php"><i class="fas fa-globe"></i> Autoparco</a>
                <a role="button" class="btn btn-outline-cv btn-block <?if($livello<4){echo "disabled";}?>" href="https://croceverde.org/strumenti/mobilita/vistaservizi.php"><i class="fas fa-wheelchair"></i> Mobilità</a>
                <?php if ($livello == 1 || $livello == 4 || $livello == 5 || $livello == 6): ?>
                    <a role='button' class='btn btn-outline-secondary btn-block' href='https://drive.google.com/drive/folders/1Vg-kOzzbAkhCk21CHqIKOUxuZ9cfD1wD?usp=sharing' target='_blank'><i class="fas fa-external-link-alt"></i> Documentazione</a>
                <?php endif; ?>
                <a role="button" class="btn btn-outline-secondary btn-block " href="http://galileoambulanze.eu" target="_blank"><i class="fas fa-external-link-alt"></i> GALILEO</a>
                <a role='button' class='btn btn-outline-secondary btn-block' href='https://docs.google.com/spreadsheets/d/12dRo15x-qgjuTBHQlqEz83wcG9ifCnuObOObzL_fwOQ/edit?usp=sharing/' target='_blank'><i class="fas fa-external-link-alt"></i> Censimento ossigeno</a>
                <?php if ($livello == 1 || $livello == 4 || $livello == 5 || $livello == 6): ?>
                    <a role='button' class='btn btn-outline-secondary btn-block' href='https://drive.google.com/drive/folders/1Vg-kOzzbAkhCk21CHqIKOUxuZ9cfD1wD?usp=sharing' target='_blank'><i class="fas fa-external-link-alt"></i> Documentazione</a>
                <?php endif; ?>
                <?php if ($livello == 1 || $livello == 4 || $livello == 5 || $livello == 6): ?>
                    <a role='button' class='btn btn-outline-secondary btn-block' href='https://infinitycloud.it/mipcampasso/jsp/login.jsp' target='_blank'><i class="fas fa-external-link-alt"></i> Cedolino online</a>
                <?php endif; ?>
                <? if ($livello == 6): ?>
                    <a role='button' class='btn btn-outline-secondary btn-block' href='https://login.siteground.com' target='_blank'><i class='fas fa-question'></i> ADMIN</a>
                <? endif; ?>
                <a role="button" class="btn btn-outline-danger btn-block" href="logout.php"><i class="fas fa-times"></i> Logout</a>
            </div>
        </div>
    </div>
</div>
</body>
<?php include('config/include/footer.php'); ?>
</html>