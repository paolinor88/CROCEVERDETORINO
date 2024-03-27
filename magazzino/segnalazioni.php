<?php
/**
 *
 * @author     Paolo Randone
 * @author     <paolo.randone@croceverde.org>
 * @version    7.3
 * @note       Powered for Croce Verde Torino. All rights reserved
 *
 */
session_start();
//connessione DB
include "../config/config.php";
//controllo LOGIN
//accesso consentito a logistica, segreteria e ADMIN
if (($_SESSION["livello"])<4){
    header("Location: ../error.php");
}
//nicename tipo
$dictionary = array (
    1 => "MSB",
    2 => "MSA",
    3 => "118",
    4 => "Altro",
);
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <meta name="author" content="Paolo Randone">
    <title>Segnalazioni Guasti</title>

    <? require "../config/include/header.html";?>



    <script>
        $(document).ready(function(){
            $('[data-toggle="popover"]').popover();
        });
    </script>
    <!-- datatable -->
    <script>
        $(document).ready(function() {
            var dataTables = $('#myTable').DataTable({
                stateSave: true,
                "paging": false,
                "language": {url: '../config/js/package.json'},
                "order": [[3, "asc"]],
                "pagingType": "simple",
                "pageLength": 100,
            });

        } );
    </script>
</head>
<!-- NAVBAR -->
<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../index.php" style="color: #078f40">Home</a></li>
            <li class="breadcrumb-item"><a href="index.php" style="color: #078f40">Autoparco</a></li>
            <li class="breadcrumb-item active" aria-current="page">Segnalazioni Guasti</li>
        </ol>
    </nav>
</div>
<!-- content -->
<body>
<div class="container-fluid">
    <div class="jumbotron">
        <div class="table-responsive-sm">
            <table class="table table-hover table-sm" id="myTable">
                <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Auto</th>
                    <th scope="col">Segnalazione</th>
                    <th scope="col">Data</th>
                    <th scope="col">Verificato</th>
                    <th scope="col">Note</th>
                </tr>
                </thead>
                <tbody>
                <?php

                $select = $db->query("SELECT * FROM SegnalazioniGuastiMezzi ");
                while($ciclo = $select->fetch_array()){

                    if($select->num_rows>0): ?>
                        <tr>
                            <td><?=$ciclo['IDSegnalazione']?></td>
                            <td><?=$ciclo['Sigla']?></td>
                            <td><?=$ciclo['Segnalazione']?></td>
                            <td><?=$ciclo['DataOra']?></td>
                            <td><?=$ciclo['DataVerificato']?></td>
                            <td><?=$ciclo['NoteVerificato']?></td>
                        </tr>
                    <? endif;
                }?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>

<!-- FOOTER -->
<?php include('../config/include/footer.php'); ?>

</html>