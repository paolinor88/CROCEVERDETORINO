<?php
/**
 *
 * @author     Paolo Randone
 * @author     <mail@paolorandone.it>
 * @version    1.0
 * @note       Powered for Croce Verde Torino. All rights reserved
 *
 */
session_start();
//parametri DB
include "../config/config.php";
//accesso consentito a logistica, segreteria e ADMIN
if (($_SESSION["ID"])!='D9999'){
    header("Location: ../error.php");
}

?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <meta name="author" content="Paolo Randone">
    <title>Richiesta materiale</title>

    <? require "../config/include/header.html";?>

</head>
<body>
<!-- NAVBAR -->
<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../index.php" style="color: #078f40">Home</a></li>
            <li class="breadcrumb-item"><a href="index.php" style="color: #078f40">Magazzino</a></li>
            <li class="breadcrumb-item active" aria-current="page">Richiesta materiale</li>
        </ol>
    </nav>
    <div class="card text-center border-danger">
        <div class="card-header text-danger">
            OOOPS!
        </div>
        <div class="card-body">
            <h5 class="card-title">PAGINA IN COSTRUZIONE</h5>
            <p class="card-text">Spiacenti, la risorsa che stai cercando non è ancora disponibile</p>
        </div>
        <div class="card-footer text-muted">
            Se riscontri un errore, <a href="mailto:gestioneutenti@croceverde.org">contatta il webmaster</a>
        </div>
    </div>
    <div class="jumbotron">
        <?php

        $select = $db->query("SELECT * FROM giacenza WHERE categoria='1' order by nome");

        while($ciclo = $select->fetch_array()){

                echo "
					<div class=\"form-group form-check\">
                        <input type=\"checkbox\" class=\"form-check-input\" id=\"".$ciclo['nome']." ".$ciclo['tipo']."\">
                        <label class=\"form-check-label\" for=\"".$ciclo['nome']." ".$ciclo['tipo']."\">".$ciclo['nome']." ".$ciclo['tipo']."</label>
                    </div>
					";

        }

        ?>
    </div>
</div>


</body>
<?php include('../config/include/footer.php'); ?>
</html>

