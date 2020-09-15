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
include "../config/config.php";
//controllo LOGIN / accesso consentito a logistica, segreteria e ADMIN
if (($_SESSION["livello"])<4){
    header("Location: ../error.php");
}
//GET variable
if (isset($_GET["ID"])){
    $id = $_GET["ID"];
    $modifica = $db->query("SELECT * FROM checklist WHERE IDCHECK='$id'")->fetch_array();
    $idoperatore = $modifica['IDOPERATORE'];
}
//OP variable
$select = $db->query("SELECT cognome, nome, squadra, sezione FROM utenti WHERE ID='$idoperatore'")->fetch_array();
$cognome = $select['cognome'];
$nome = $select['nome'];
$sezione = $select['sezione'];
$squadra = $select['squadra'];

//reset note
if (isset($_POST["cancellanota"])){
    $idcheck=$_POST["xcheck"];
    $aggiornacheck = $db->query("UPDATE checklist SET NOTE='' WHERE IDCHECK='$idcheck'");
    echo '<script type="text/javascript">
        alert("Modifica effettuata con successo");
        location.href="archivio.php";
        </script>';
}
//update note
if (isset($_POST["aggiornacheck"])){
    $idcheck=$_POST["xcheck"];
    $nuovanota=$_POST["xnote"];
    $aggiornacheck = $db->query("UPDATE checklist SET NOTE='$nuovanota' WHERE IDCHECK='$idcheck'");
    echo '<script type="text/javascript">
        alert("Modifica effettuata con successo");
        location.href="archivio.php";
        </script>';
}
//nicename livelli
$dictionaryLivello = array (
    1 => "Dipendente",
    2 => "Volontario",
    3 => "Altro",
    4 => "Logistica",
    5 => "Segreteria",
    6 => "ADMIN",
);
//nicename sezioni
$dictionarySezione = array (
    1 => "Torino",
    2 => "Alpignano",
    3 => "Borgaro/Caselle",
    4 => "Ciriè",
    5 => "San Mauro",
    6 => "Venaria",
    7 => "",
);
//nicename sezioni
$dictionarySquadra = array (
    1 => "Prima",
    2 => "Seconda",
    3 => "Terza",
    4 => "Quarta",
    5 => "Quinta",
    6 => "Sesta",
    7 => "Settima",
    8 => "Ottava",
    9 => "Nona",
    10 => "Sabato",
    11 => "Montagna",
    12 => "Direzione",
    13 => "Lunedì",
    14 => "Martedì",
    15 => "Mercoledì",
    16 => "Giovedì",
    17 => "Venerdì",
    18 => "Diurno",
    19 => "Giovani",
    20 => "Servizi Generali",
    21 => "Altro",
    22 => "",
);
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <meta name="author" content="Paolo Randone">
    <title>Gestisci segnalazione</title>

    <!-- CSS -->
    <link rel="stylesheet" href="../config/css/bootstrap.min.css"> <!-- 4.4.1 -->
    <link href='https://use.fontawesome.com/releases/v5.0.6/css/all.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="../config/css/custom.css">

    <!-- JS Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.js" integrity="sha256-WpOohJOqMqqyKL9FccASB9O0KwACQJpFTUBLTYOVvVU=" crossorigin="anonymous"></script>
    <script src="../config/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>

    <script>
        $(document).ready(function(){
            $('[data-toggle="popover"]').popover();
        });
    </script>
</head>
<!-- NAVBAR -->
<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../index.php" style="color: #078f40">Home</a></li>
            <li class="breadcrumb-item"><a href="index.php" style="color: #078f40">Checklist elettronica</a></li>
            <li class="breadcrumb-item"><a href="archivio.php" style="color: #078f40">Archivio</a></li>
            <li class="breadcrumb-item active" aria-current="page">Checklist</li>
        </ol>
    </nav>
</div>
<!--content-->
<body>
<div class="container-fluid">
    <div class="row text-left">
    <div class="col-md-3 col-md-offset-3"></div>
    <div class="col-md-6">
        <div class="jumbotron">
            <form method="post" action="details.php">
                <input hidden id="xcheck" name="xcheck" value="<?=$id?>">
                <p>AUTO: <?=$modifica['IDMEZZO']?></p>
                <p>DATA: <?=$modifica['DATACHECK']?></p>
                <p>SEZIONE: <?=$dictionarySezione[$sezione]?></p>
                <p>SQUADRA: <?=$dictionarySquadra[$squadra]?></p>
                <p>COMPILATORE: <?=$cognome?> <?=$nome?></p>
                <p>MATRICOLA: <?=$modifica['IDOPERATORE']?></p>
                <hr>
                <div class="form-group">
                    <textarea class="form-control" id="xnote" name="xnote" rows="10"><?=$modifica['NOTE']?></textarea>
                </div>
                <center>
                    <div class="btn-group" role="group">
                        <button type="submit" id="cancellanota" name="cancellanota" class="btn btn-sm btn-warning" aria-label="Cancella nota"><i class="far fa-trash-alt"></i></button>
                        <button type="submit" id="aggiornacheck" name="aggiornacheck" class="btn btn-sm btn-success" aria-label="Aggiorna record"><i class="far fa-save"></i></button>
                    </div>
                    <br>
                    <font size="-1"><em>Premendo il pulsante <i class="far fa-trash-alt" style="color: #faa732"></i> verrà cancellato il testo della segnalazione<br>Per modificare la segnalazione, aggiungere il testo e premere <i class="far fa-save" style="color: green"></i></em></font>
                </center>
            </form>
        </div>
    </div>
</div>
</body>
<!-- FOOTER -->
<?php include('../config/include/footer.php'); ?>

</html>