<?php
/**
 *
 * @author     Paolo Randone
 * @author     <mail@paolorandone.it>
 * @version    3.2
 * @note       Powered for Croce Verde Torino. All rights reserved
 *
 */
session_start();
//parametri DB
include "../config/config.php";
//login
if (!isset($_SESSION["ID"])){
    header("Location: ../login.php");
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <meta name="author" content="Paolo Randone">
    <title>Eventi e calendario</title>

    <? require "../config/include/header.html";?>

</head>
<!-- NAVBAR -->
<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../index.php" style="color: #078f40">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Calendario</li>
        </ol>
    </nav>
</div>
<body>
<div class="container-fluid">
    <div class="row text-center">
        <div class="col-md-3 col-md-offset-3"></div>
        <div class="text-center col-md-6">
            <div class="jumbotron">
                <a role="button" class="btn btn-outline-cv btn-block" href="calendar.php"><i class="far fa-calendar-alt"></i> Calendario</a>
                <a role="button" class="btn btn-outline-cv btn-block <?if($_SESSION["livello"]<5){echo "disabled";}?>" href="event.php"><i class="fas fa-thumbtack"></i> Eventi</a>
                <?if(($_SESSION["livello"]==1)||($_SESSION["livello"]>=4)){echo "
                     <a role=\"button\" class=\"btn btn-outline-cv btn-block\" href=\"agenda.php\"><i class=\"far fa-calendar-check\"></i> Agenda straordinario</a>
                     <button type=\"button\" class=\"btn btn-outline-cv btn-block\" data-toggle=\"modal\" data-target=\"#modal1\"><i class=\"fas fa-search\"></i> Cerca settimana</button>
                ";}?>

            </div>
        </div>
    </div>
</div>
</body>

<!-- MODAL CERCA SETTIMANA -->
<div class="modal" id="modal1" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content">
            <form method="get" action="settimana.php">
                <div class="modal-header">
                    <h6 class="modal-title" id="modal1Title">Seleziona la settimana</h6>
                </div>
                <div class="modal-body">
                    <select id="numerosettimana" name="numerosettimana" class="form-control form-control-sm" required>
                        <option value="">...</option>
                        <?
                        for($a=1;$a<38;$a++){
                            ($a==['settimana'])? $sel="selected" : $sel="";
                            echo "<option $sel value='$a'>".$a."</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-dismiss="modal">Chiudi</button>
                    <button type="submit" class="btn btn-outline-success btn-sm" id="submitSettimana" name="submitSettimana" href="settimana.php">Avanti</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php include('../config/include/footer.php'); ?>
</html>