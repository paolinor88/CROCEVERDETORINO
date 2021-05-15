<?php
/**
 *
 * @author     Paolo Randone
 * @author     <mail@paolorandone.it>
 * @version    2.2
 * @note       Powered for Croce Verde Torino. All rights reserved
 *
 */
session_start();
//parametri DB
include "../config/config.php";

?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <meta name="author" content="Paolo Randone">
    <title>Gestionale CVTO</title>

    <? require "../config/include/header.html";?>

    <script>
        $(document).ready(function(){
            $('[data-toggle="popover"]').popover();
        });
    </script>
</head>
<body>
<!-- navbar -->
<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../index.php" style="color: #078f40">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Documentazione</li>
        </ol>
    </nav>
    <? if (($_SESSION['livello'])==6){
        echo "<iframe src=\"adminfilemanager.php\" style=\"width:100%; height: 90vh; border: 0;\"></iframe>";
    }else{
        echo "<iframe src=\"userfilemanager.php\" style=\"width:100%; height: 90vh; border: 0;\"></iframe>";
    }
    ?>
</div>
</body>
<!-- FOOTER -->
<footer class="container-fluid">
    <div class="text-center">
        <font size="-4" style="color: lightgray; "><em>Powered for <a href="mailto:info@croceverde.org">Croce Verde Torino</a>. All rights reserved.<p>V 1.0</p></em></font>
    </div>
</footer>
</html>