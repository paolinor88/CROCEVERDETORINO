<?php
/**
 *
 * @author     Paolo Randone
 * @author     <mail@paolorandone.it>
 * @version    2.3
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
    <title>Check-list elettronica</title>

    <? require "../config/include/header.html";?>

    <script>
        $(document).ready(function(){
            $('[data-toggle="popover"]').popover();
        });
    </script>
</head>
<!-- NAVBAR -->
<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../index.php" style="color: #078f40">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Checklist elettronica</li>
        </ol>
    </nav>
</div>
<body>
<div class="container-fluid">
    <div class="row text-center">
        <div class="col-md-3 col-md-offset-3"></div>
        <div class="text-center col-md-6">
            <div class="jumbotron">
                <button class="btn btn-outline-cv btn-block" data-toggle="modal" data-target="#modal1"><i class="fas fa-plus"></i> Nuova checklist</button>
                <a role="button" class="btn btn-outline-cv btn-block <?if($_SESSION['livello']<4){echo "disabled";}?>" href="archivio.php"><i class="fas fa-search"></i> Archivio segnalazioni</a>
                <a role="button" class="btn btn-outline-cv btn-block <?if($_SESSION['livello']<4){echo "disabled";}?>" href="mezzi.php"><i class="fas fa-ambulance"></i> Gestione mezzi</a>
            </div>
        </div>
    </div>
</div>
</body>

<!-- MODAL NUOVA CHECK -->
<div class="modal" id="modal1" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content">
            <form method="post" action="new.php">
                <div class="modal-header">
                    <h6 class="modal-title" id="modal1Title">Seleziona mezzo</h6>
                </div>
                <div class="modal-body">
                    <select id="IDMEZZO" name="IDMEZZO" class="form-control form-control-sm" required>
                        <option value="">Mezzo...</option>
                        <?
                        $select = $db->query("SELECT ID FROM mezzi WHERE tipo !='4' AND stato!='2' ORDER BY ID");
                        while($ciclo = $select->fetch_array()){
                            echo "<option value=\"".$ciclo['ID']."\">".$ciclo['ID']."</option>";
                        }
                        ?>
                    </select> <!-- IDMEZZO -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-dismiss="modal">Chiudi</button>
                    <button type="submit" class="btn btn-outline-success btn-sm" id="submitButton" href="nuovacheck.php">Avanti</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- FOOTER -->
<?php include('../config/include/footer.php'); ?>

</html>