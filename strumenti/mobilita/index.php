<?php
header('Access-Control-Allow-Origin: *');
/**
 *
 * @author     Paolo Randone
 * @author     <paolo.randone@croceverde.org
 * @version    1.0
 * @note         Powered for Croce Verde Torino. All rights reserved
 *
 */
session_start();
include "../config/config.php";
include "../config/include/destinatari.php";

?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="Paolo Randone">
    <base href="/strumenti/mobilita/">
    <title>Portale Gruppo Mobilità</title>

    <? require "../config/include/header.html";?>

    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../config/include/custom.css?v=<?= time() ?>">

</head>
<body>
<div class="container-fluid">
    <nav aria-label="breadcrumb" class="sfondo">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../index.php" style="color: #078f40">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Mobilità</li>
        </ol>
    </nav>
</div>
<div class="container-fluid">
    <br>
    <div class="text-center">
        <div class="d-grid gap-2 col-12 col-sm-6 mx-auto sfondo">
                <a role="button" class="btn btn-outline-cv" href="vistaservizi.php">
                    <i class="fa-solid fa-chart-bar"></i> Elenco Servizi
                </a>
            <a role="button" class="btn btn-outline-cv" href="form_mobilita.php" target="_blank">
                <i class="far fa-plus"></i> Nuova richiesta
            </a>
        </div>
    </div>
    <br>
</div>

</body>

</html>