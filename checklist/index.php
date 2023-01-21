<?php
/**
 *
 * @author     Paolo Randone
 * @author     <mail@paolorandone.it>
 * @version    5.0
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
            <div class="alert alert-danger" style="text-align: center" role="alert">
                <b>ATTENZIONE!! I MODULI "CHECKLIST" E "SANIFICAZIONI" SONO STATI DISATTIVATI; UTILIZZARE LE FUNZIONI DEL NUOVO PORTALE GALILEO!!!</b>
            </div>
            <div class="jumbotron">
                <button class="btn btn-outline-cv btn-block" data-toggle="modal" data-target="#modal1" disabled><i class="fas fa-plus"></i> Nuova checklist</button>
                <button class="btn btn-outline-cv btn-block" data-toggle="modal" data-target="#modal2"><i class="fas fa-exclamation-triangle"></i>  Inserisci segnalazione</button>
                <button class="btn btn-outline-cv btn-block" data-toggle="modal" data-target="#modal3" disabled><i class="fas fa-shower"></i> Lavaggio / sanificazione</button>
                <button class="btn btn-outline-cv btn-block" data-toggle="modal" data-target="#modal4" disabled><i class="fas fa-camera"></i> Segnala danno</button>
                <a role="button" class="btn btn-outline-cv btn-block <?if($_SESSION['livello']<4){echo "disabled";}?>" href="archivio.php"><i class="fas fa-search"></i> Archivio</a>
                <a role="button" class="btn btn-outline-cv btn-block <?if($_SESSION['livello']<4){echo "disabled";}?>" href="../magazzino/mezzi.php"><i class="fas fa-ambulance"></i> Gestione mezzi</a>
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
                        while($ciclo = $select->fetch_array()){ ?>
                            <option value="<?=$ciclo['ID']?>"><?=$ciclo['ID']?></option>";
                        <? }
                        ?>
                    </select> <!-- IDMEZZO -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-dismiss="modal">Chiudi</button>
                    <button type="submit" class="btn btn-outline-success btn-sm" id="submitButton">Avanti</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- MODAL NUOVA segnalazione -->
<div class="modal" id="modal2" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content">
            <form method="post" action="segnalazione.php">
                <div class="modal-header">
                    <h6 class="modal-title" id="modal1Title">Seleziona mezzo</h6>
                </div>
                <div class="modal-body">
                    <select id="IDMEZZO" name="IDMEZZO" class="form-control form-control-sm" required>
                        <option value="">Mezzo...</option>
                        <?
                        $select = $db->query("SELECT ID FROM mezzi WHERE tipo !='4' AND stato!='2' ORDER BY ID");
                        while($ciclo = $select->fetch_array()){ ?>
                            <option value="<?=$ciclo['ID']?>"><?=$ciclo['ID']?></option>";
                        <? }
                        ?>
                    </select> <!-- IDMEZZO -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-dismiss="modal">Chiudi</button>
                    <button type="submit" class="btn btn-outline-success btn-sm" id="submitButton">Avanti</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- MODAL NUOVA sanificazione -->
<div class="modal" id="modal3" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content">
            <form method="post" action="sanificazione.php">
                <div class="modal-header">
                    <h6 class="modal-title" id="modal1Title">Seleziona mezzo</h6>
                </div>
                <div class="modal-body">
                    <select id="IDMEZZO" name="IDMEZZO" class="form-control form-control-sm" required>
                        <option value="">Mezzo...</option>
                        <?
                        $select = $db->query("SELECT ID FROM mezzi WHERE tipo !='4' AND stato!='2' ORDER BY ID");
                        while($ciclo = $select->fetch_array()){ ?>
                            <option value="<?=$ciclo['ID']?>"><?=$ciclo['ID']?></option>";
                        <? }
                        ?>
                    </select> <!-- IDMEZZO -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-dismiss="modal">Chiudi</button>
                    <button type="submit" class="btn btn-outline-success btn-sm" id="submitButton">Avanti</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- MODAL NUOVA foto -->
<div class="modal" id="modal4" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content">
            <form method="post" action="upload.php">
                <div class="modal-header">
                    <h6 class="modal-title" id="modal1Title">Seleziona mezzo</h6>
                </div>
                <div class="modal-body">
                    <select id="IDMEZZO" name="IDMEZZO" class="form-control form-control-sm" required>
                        <option value="">Mezzo...</option>
                        <?
                        $select = $db->query("SELECT ID FROM mezzi WHERE tipo !='4' AND stato!='2' ORDER BY ID");
                        while($ciclo = $select->fetch_array()){ ?>
                            <option value="<?=$ciclo['ID']?>"><?=$ciclo['ID']?></option>";
                        <? }
                        ?>
                    </select> <!-- IDMEZZO -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-dismiss="modal">Chiudi</button>
                    <button type="submit" class="btn btn-outline-success btn-sm" id="submitButton">Avanti</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- FOOTER -->
<?php include('../config/include/footer.php'); ?>

</html>