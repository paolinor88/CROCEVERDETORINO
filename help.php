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
include "config/config.php";
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <meta name="author" content="Paolo Randone">
    <title>Guida</title>

    <link rel="stylesheet" href="config/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/css/bootstrap4-toggle.min.css" rel="stylesheet">    <link href='https://use.fontawesome.com/releases/v5.0.6/css/all.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="config/css/custom.css">
    <script src="https://code.jquery.com/jquery-3.4.1.js" integrity="sha256-WpOohJOqMqqyKL9FccASB9O0KwACQJpFTUBLTYOVvVU=" crossorigin="anonymous"></script>

    <script src="config/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/javascript.util/0.12.12/javascript.util.min.js" integrity="sha512-oHBLR38hkpOtf4dW75gdfO7VhEKg2fsitvHZYHZjObc4BPKou2PGenyxA5ZJ8CCqWytBx5wpiSqwVEBy84b7tw==" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.4.4/umd/popper.min.js" integrity="sha512-eUQ9hGdLjBjY3F41CScH3UX+4JDSI9zXeroz7hJ+RteoCaY+GP/LDoM8AO+Pt+DRFw3nXqsjh9Zsts8hnYv8/A==" crossorigin="anonymous"></script>    <script src="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/js/bootstrap4-toggle.min.js"></script>    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>
</head>
<body>
<br>
<div class="container-fluid">
    <div class="jumbotron">
        <div align="center"><img class="img-fluid" src="config/images/logo.png"/></div>
        <h3 class="text-center" style="color: #078f40">GESTIONALE ONLINE</h3>
        <h3 class="text-center" style="color: #078f40">MANUALE UTENTE</h3>
        <hr>
        <div class="row">
            <div class="col-2">
                <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                    <a class="nav-link active" id="v-pills-accesso-tab" data-toggle="pill" href="#v-pills-accesso" role="tab" aria-controls="v-pills-accesso" aria-selected="true">Accesso</a>
                    <a class="nav-link" id="v-pills-checklist-tab" data-toggle="pill" href="#v-pills-checklist" role="tab" aria-controls="v-pills-checklist" aria-selected="false">Checklist</a>
                    <a class="nav-link" id="v-pills-calendario-tab" data-toggle="pill" href="#v-pills-calendario" role="tab" aria-controls="v-pills-calendario" aria-selected="false">Calendario</a>
                </div>
            </div>
            <div class="col-10">
                <div class="tab-content" id="v-pills-tabContent">
                    <div class="tab-pane fade show active" id="v-pills-accesso" role="tabpanel" aria-labelledby="v-pills-accesso-tab">
                        Istruzioni per registrarsi ed accedere al sistema
                    </div>
                    <div class="tab-pane fade" id="v-pills-checklist" role="tabpanel" aria-labelledby="v-pills-checklist-tab">
                        Istruzioni menù checklist
                    </div>
                    <div class="tab-pane fade" id="v-pills-calendario" role="tabpanel" aria-labelledby="v-pills-calendario-tab">
                        Istruzioni agenda calendario e straordinario
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
<!-- FOOTER -->
<footer class="container-fluid">
    <div class="text-center">
        <font size="-4" style="color: lightgray; "><em>Powered for <a href="mailto:info@croceverde.org">Croce Verde Torino</a>. All rights reserved.<p>V 1.0</p></em></font>
    </div>
</footer>
</html>
