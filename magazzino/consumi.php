<?php
/**
 *
 * @author     Paolo Randone
 * @author     <paolo.randone@croceverde.org>
* @version    7.2
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
    <title>Consumi medi</title>

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
                "order": [[0, "asc"]],
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
            <li class="breadcrumb-item active" aria-current="page">Consumi</li>
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
                    <th scope="col">Sigla</th>
                    <th scope="col">Litri</th>
                    <th scope="col">Importo</th>
                    <th scope="col">Km Percorsi</th>
                    <th scope="col">Media Lt / 100 KM</th>
                </tr>
                </thead>
                <tbody>
                <?php

                $select = $db->query("SELECT * FROM consumi GROUP BY Sigla order by Sigla DESC");
                while($ciclo = $select->fetch_array()){
                    $kminiziali = $ciclo;
                    if($select->num_rows>0): ?>
                        <tr>
                            <td><?=$ciclo['Sigla']?></td>
                            <td><?=$ciclo['Giorno']?></td>
                            <td><?=$ciclo['Litri']?></td>
                            <td><?=$ciclo['Importo']?></td>
                            <td><?=$ciclo['']?></td>
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