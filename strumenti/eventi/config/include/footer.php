<?php


$Livello = $_SESSION["Livello"];
$dictionaryLivello = array (
    1 => "SUPERUSER",
    2 => "ACCESSO EFFETTUATO",
    11 => "SQUADRA DI MONTAGNA",
    12 => "OPERATORE AMBULANZA",
    20 => "CENTRALINO",
    23 => "RESPONSABILE DI SEZIONE",
    24 => "RESPONSABILE DI SQUADRA",
    25 => "AMMINISTRAZIONE",
    26 => "DIREZIONE DEI SERVIZI",
    27 => "SEGRETERIA VOLONTARI",
    28 => "FORMAZIONE",
    29 => "AUTISTI",
    30 => "RESPONSABILE SQUADRA SPECIALISTICA",
);
?>
<?php if(isset($_SESSION['ID'])):?>
<footer class="container-fluid">
    <div class="text-center">
        <font size="-4" style="color: lightgray; "><em>Powered for <a href="mailto:paolo.randone@croceverde.org">Croce Verde Torino</a>. All rights reserved.</em></font><BR>
        <font size="-4" style="color: lightgray; "><em><?=$dictionaryLivello[$Livello]?> | <?=$_SESSION['NomeOperatore']?></em></font>
    </div>
</footer>
<?php else: ?>
    <footer class="container-fluid">
        <div class="text-center">
            <font size="-4" style="color: lightgray; "><em>Powered for <a href="mailto:paolo.randone@croceverde.org">Croce Verde Torino</a>. All rights reserved.</em></font><BR>
            <font size="-4" style="color: lightgray; "><em>ACCESSO NON EFFETTUATO</em></font>
        </div>
    </footer>
<?php endif ?>