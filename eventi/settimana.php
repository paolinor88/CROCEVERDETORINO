<?php

session_start();                     // inizializzo la sessione
include "../config/config.php";

$settimana = $_GET["numerosettimana"];
if(isset($_GET["provenienza"])){
    $calendario = $_GET["provenienza"];
}

?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <meta name="author" content="Paolo Randone">
    <title>Ciclico turni dipendenti</title>

    <? require "../config/include/header.html";?>

    <script>
        $(document).ready(function() {
            $('#myTable').DataTable({
                "language": {url: '../config/js/package.json'},
                "pagingType": "simple",
                "paging": false,
                "ordering": false,
                "info": false
            });
        } );
    </script> <!-- filtra -->

</head>
<body>


<!-- NAVBAR -->
<? if (isset($calendario)){
    echo "<div class=\"modal-header\">
                <h5>Settimana ".$settimana."</h5>
                <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\">
                    <span aria-hidden=\"true\">&times;</span>
                </button>
            </div>";
}else{
    echo "<div class=\"container-fluid\">
    <nav aria-label=\"breadcrumb\">
        <ol class=\"breadcrumb\">
            <li class=\"breadcrumb-item\"><a href=\"../index.php\" style=\"color: #078f40\">Home</a></li>
            <li class=\"breadcrumb-item\"><a href=\"index.php\" style=\"color: #078f40\">Eventi e calendario</a></li>
            <li class=\"breadcrumb-item active\" aria-current=\"page\">Settimana ".$settimana."</li>
        </ol>
    </nav>
</div>
</br>";
}
?>


<div class="container-fluid">
    <div class="jumbotron">
        <div class="table-responsive-sm">
            <table class="table table-hover table-sm" id="myTable">
                <thead>
                <th>Orario</th>
                <th>Postazione</th>
                <th>Dipendente</th>
                <th>Dipendente</th>
                </thead>
                <tbody>
                <?php
                if (isset($_GET["numerosettimana"])){
                    $crea_settimana = $db->query("SELECT t1.orario, t1.postazione, t1.dip_1, t1.dip_2, CONCAT(t2.nome,' ',t2.cognome) AS nomecompleto1, CONCAT(t3.nome,' ',t3.cognome) AS nomecompleto2 FROM modello_turni AS t1 LEFT JOIN utenti AS t2 ON t1.dip_1=t2.ciclico LEFT JOIN utenti AS t3 ON t1.dip_2=t3.ciclico WHERE settimana='$settimana' ORDER BY t1.postazione, t1.orario ASC");
                    while($ciclo = $crea_settimana->fetch_array()){
                        echo "
					<tr>
						<td>".$ciclo['orario']."</td>
						<td>".$ciclo['postazione']."</td>
						<td>".$ciclo['nomecompleto1']."</td>
						<td>".$ciclo['nomecompleto2']."</td>
					</tr>";
                    }
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>

<!-- FOOTER -->
<?php include('../config/include/footer.php'); ?>


</html>