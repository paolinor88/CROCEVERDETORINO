<?php
$livello = $_SESSION["livello"];
$dictionaryLivello = array (
    1 => "Dipendente",
    2 => "Volontario",
    3 => "Altro",
    4 => "Logistica",
    5 => "Segreteria",
    6 => "ADMIN",
);
?>
<!-- FOOTER -->
<footer class="container-fluid">
    <div class="text-center">
        <font size="-4" style="color: lightgray; "><em>Powered for <a href="mailto:gestioneutenti@croceverde.org">Croce Verde Torino</a>. All rights reserved.<p>V 2.0 | <?=$dictionaryLivello[$livello]?> | <?=$_SESSION['nome'].' '.$_SESSION['cognome']?></p></em></font>
    </div>
</footer>
