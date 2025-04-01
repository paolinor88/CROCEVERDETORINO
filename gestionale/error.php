<?php
header('Access-Control-Allow-Origin: *');

?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <meta name="author" content="Paolo Randone">
    <title>ERRORE</title>

    <link rel="stylesheet" href="config/css/bootstrap.min.css">
    <link href='https://use.fontawesome.com/releases/v5.0.6/css/all.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="config/css/custom.css">

    <script src="https://code.jquery.com/jquery-3.4.1.js" integrity="sha256-WpOohJOqMqqyKL9FccASB9O0KwACQJpFTUBLTYOVvVU=" crossorigin="anonymous"></script>
    <script src="config/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>

</head>
<body>
<br>
<div class="container container-fluid">
    <div class="card text-center border-danger">
        <div class="card-header text-danger">
            ACCESSO NEGATO
        </div>
        <div class="card-body">
            <h5 class="card-title">Attenzione, sembra che tu non possa accedere alla risorsa desiderata</h5>
            <p class="card-text">La sessione potrebbe essere scaduta, oppure non disponi delle autorizzazioni necessarie</p>
            <p class="card-text">Per effettuare nuovamente il login, premi il pulsante</p>
            <a href="login.php" class="btn btn-outline-success">Accedi</a>
        </div>
        <div class="card-footer text-muted">
            Se riscontri un errore, <a href="mailto:gestioneutenti@croceverde.org">contatta il webmaster</a>
        </div>
    </div>
</div>

</body>
</html>
