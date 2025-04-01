<?php
header('Access-Control-Allow-Origin: *');

/**
 *
 * @author     Paolo Randone
 * @author     <paolo.randone@croceverde.org>
 * @version    1.0
 * @note       Powered for Croce Verde Torino. All rights reserved
 *
 */
session_start();
include "../config/config.php";
/*
if (!isset($_SESSION["ID"])){
    header("Location: index.php");
}
*/
$dictionaryPatologia = array (
    1 => "MEDICO",
    2 => "TRAUMA",
);

if (isset($_GET['message'])){
    if ($_GET['message'] == 'success'){
        $alert_class = 'alert-success';
        $alert_message = '<i class="fa-regular fa-circle-check"></i> Modifica eseguita con successo';
    }else{
        $alert_class = 'alert-danger';
        $alert_message = '<i class="fa-solid fa-triangle-exclamation"></i> ERRORE';
    }
}


?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="Paolo Randone">

    <title>Monitor real-time</title>

    <? require "../config/include/header.html";?>

    <link rel="stylesheet" href="../config/include/custom.css?v=<?= time() ?>">

    <script>
        $(document).ready(function(){
            $('[data-toggle="popover"]').popover();
        });
    </script>
</head>
<!-- NAVBAR -->
<body>
<div class="container-fluid">
    <div class="alert alert-secondary" role="alert" style="text-align: center">
        Questa pagina si ricarica automaticamente tra <span id="countdown">60</span> secondi.
    </div>
    <script>
        function startCountdown(seconds) {
            const countdownElement = document.getElementById("countdown");

            const interval = setInterval(function() {
                seconds--;

                if (seconds >= 0) {
                    countdownElement.innerText = seconds;
                } else {
                    clearInterval(interval);
                    location.reload();
                }
            }, 1000);
        }
        startCountdown(60);
    </script>
    <div class="container container-sm">
        <?php if (isset($_GET['message'])) { ?>
            <div class="alert <?php echo $alert_class; ?>" role="alert">
                <?php echo $alert_message; ?>
                <script type="text/javascript">
                    setTimeout(function(){
                        location.href="list.php";
                    }, 2000);
                </script>
            </div>
        <?php } ?>
    </div>
    <br>
    <div class="row">
        <div class="col-sm-4 mb-3 mb-sm-0">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title" style="text-align: center">PAPA 1</h5>
                    <div class="color-box" style="display: flex; justify-content: space-between;">
                        <div class="color-item" style="background-color: #ffffff;">
                            <?php
                            $queryuno = "SELECT COUNT(*) AS count_codicegravita_0 FROM interventi WHERE POSTAZIONE='PAPA 1' AND STATO=1 AND CODICEGRAVITA = 0";
                            $result = $db->query($queryuno);
                            $row = $result->fetch_assoc();
                            echo "BIANCHI: " . $row['count_codicegravita_0'];
                            ?>
                        </div>
                        <div class="color-item" style="background-color: #198754;">
                            <?php
                            $querydue = "SELECT COUNT(*) AS count_codicegravita_1 FROM interventi WHERE POSTAZIONE='PAPA 1' AND STATO=1 AND CODICEGRAVITA = 1";
                            $result = $db->query($querydue);
                            $row = $result->fetch_assoc();
                            echo "VERDI: " . $row['count_codicegravita_1'];
                            ?>
                        </div>
                        <div class="color-item" style="background-color: #ffc107;">
                            <?php
                            $querytre = "SELECT COUNT(*) AS count_codicegravita_2 FROM interventi WHERE POSTAZIONE='PAPA 1' AND STATO=1 AND CODICEGRAVITA = 2";
                            $result = $db->query($querytre);
                            $row = $result->fetch_assoc();
                            echo "GIALLI: " . $row['count_codicegravita_2'];
                            ?>
                        </div>
                        <div class="color-item" style="background-color: #dc3545;">
                            <?php
                            $queryquattro = "SELECT COUNT(*) AS count_codicegravita_3 FROM interventi WHERE POSTAZIONE='PAPA 1' AND STATO=1 AND CODICEGRAVITA = 3";
                            $result = $db->query($queryquattro);
                            $row = $result->fetch_assoc();
                            echo "ROSSI: " . $row['count_codicegravita_3'];
                            ?>
                        </div>
                    </div>
                </div>
                <div class="card-footer" style="text-align: center">
                    <p>
                        <?php
                        $querytot = "SELECT COUNT(*) AS count_total FROM interventi WHERE POSTAZIONE='PAPA 1' AND STATO=1";
                        $result = $db->query($querytot);
                        $row = $result->fetch_assoc();
                        $count_total = $row['count_total'];
                        echo "TOTALE: " . $count_total;
                        ?>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-sm-4 mb-3 mb-sm-0">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title" style="text-align: center">PAPA 2</h5>
                    <div class="color-box" style="display: flex; justify-content: space-between;">
                        <div class="color-item" style="background-color: #ffffff;">
                            <?php
                            $queryuno = "SELECT COUNT(*) AS count_codicegravita_0 FROM interventi WHERE POSTAZIONE='PAPA 2' AND STATO=1 AND CODICEGRAVITA = 0";
                            $result = $db->query($queryuno);
                            $row = $result->fetch_assoc();
                            echo "BIANCHI: " . $row['count_codicegravita_0'];
                            ?>
                        </div>
                        <div class="color-item" style="background-color: #198754;">
                            <?php
                            $querydue = "SELECT COUNT(*) AS count_codicegravita_1 FROM interventi WHERE POSTAZIONE='PAPA 2' AND STATO=1 AND CODICEGRAVITA = 1";
                            $result = $db->query($querydue);
                            $row = $result->fetch_assoc();
                            echo "VERDI: " . $row['count_codicegravita_1'];
                            ?>
                        </div>
                        <div class="color-item" style="background-color: #ffc107;">
                            <?php
                            $querytre = "SELECT COUNT(*) AS count_codicegravita_2 FROM interventi WHERE POSTAZIONE='PAPA 2' AND STATO=1 AND CODICEGRAVITA = 2";
                            $result = $db->query($querytre);
                            $row = $result->fetch_assoc();
                            echo "GIALLI: " . $row['count_codicegravita_2'];
                            ?>
                        </div>
                        <div class="color-item" style="background-color: #dc3545;">
                            <?php
                            $queryquattro = "SELECT COUNT(*) AS count_codicegravita_3 FROM interventi WHERE POSTAZIONE='PAPA 2' AND STATO=1 AND CODICEGRAVITA = 3";
                            $result = $db->query($queryquattro);
                            $row = $result->fetch_assoc();
                            echo "ROSSI: " . $row['count_codicegravita_3'];
                            ?>
                        </div>
                    </div>
                </div>
                <div class="card-footer" style="text-align: center">
                    <p>
                        <?php
                        $querytot = "SELECT COUNT(*) AS count_total FROM interventi WHERE POSTAZIONE='PAPA 2' AND STATO=1";
                        $result = $db->query($querytot);
                        $row = $result->fetch_assoc();
                        $count_total = $row['count_total'];
                        echo "TOTALE: " . $count_total;
                        ?>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title" style="text-align: center">PAPA 3</h5>
                    <div class="color-box" style="display: flex; justify-content: space-between;">
                        <div class="color-item" style="background-color: #ffffff;">
                            <?php
                            $queryuno = "SELECT COUNT(*) AS count_codicegravita_0 FROM interventi WHERE POSTAZIONE='PAPA 3' AND STATO=1 AND CODICEGRAVITA = 0";
                            $result = $db->query($queryuno);
                            $row = $result->fetch_assoc();
                            echo "BIANCHI: " . $row['count_codicegravita_0'];
                            ?>
                        </div>
                        <div class="color-item" style="background-color: #198754;">
                            <?php
                            $querydue = "SELECT COUNT(*) AS count_codicegravita_1 FROM interventi WHERE POSTAZIONE='PAPA 3' AND STATO=1 AND CODICEGRAVITA = 1";
                            $result = $db->query($querydue);
                            $row = $result->fetch_assoc();
                            echo "VERDI: " . $row['count_codicegravita_1'];
                            ?>
                        </div>
                        <div class="color-item" style="background-color: #ffc107;">
                            <?php
                            $querytre = "SELECT COUNT(*) AS count_codicegravita_2 FROM interventi WHERE POSTAZIONE='PAPA 3' AND STATO=1 AND CODICEGRAVITA = 2";
                            $result = $db->query($querytre);
                            $row = $result->fetch_assoc();
                            echo "GIALLI: " . $row['count_codicegravita_2'];
                            ?>
                        </div>
                        <div class="color-item" style="background-color: #dc3545;">
                            <?php
                            $queryquattro = "SELECT COUNT(*) AS count_codicegravita_3 FROM interventi WHERE POSTAZIONE='PAPA 3' AND STATO=1 AND CODICEGRAVITA = 3";
                            $result = $db->query($queryquattro);
                            $row = $result->fetch_assoc();
                            echo "ROSSI: " . $row['count_codicegravita_3'];
                            ?>
                        </div>
                    </div>
                </div>
                <div class="card-footer" style="text-align: center">
                    <p>
                        <?php
                        $querytot = "SELECT COUNT(*) AS count_total FROM interventi WHERE POSTAZIONE='PAPA 3' AND STATO=1";
                        $result = $db->query($querytot);
                        $row = $result->fetch_assoc();
                        $count_total = $row['count_total'];
                        echo "TOTALE: " . $count_total;
                        ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
    <BR>
    <!--<div class="row">
        <div class="col-sm-4 mb-3 mb-sm-0">

        </div>
        <div class="col-sm-4 mb-3 mb-sm-0">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title" style="text-align: center">INALPI ARENA</h5>
                    <div class="color-box" style="display: flex; justify-content: space-between;">
                        <div class="color-item" style="background-color: #ffffff;">
                            <?php
/*                            $queryuno = "SELECT COUNT(*) AS count_codicegravita_0 FROM interventi WHERE ID_INTERVENTO=2 AND STATO=1 AND CODICEGRAVITA = 0";
                            $result = $db->query($queryuno);
                            $row = $result->fetch_assoc();
                            echo "BIANCHI: " . $row['count_codicegravita_0'];
                            */?>
                        </div>
                        <div class="color-item" style="background-color: #198754;">
                            <?php
/*                            $querydue = "SELECT COUNT(*) AS count_codicegravita_1 FROM interventi WHERE ID_INTERVENTO=2 AND STATO=1 AND CODICEGRAVITA = 1";
                            $result = $db->query($querydue);
                            $row = $result->fetch_assoc();
                            echo "VERDI: " . $row['count_codicegravita_1'];
                            */?>
                        </div>
                        <div class="color-item" style="background-color: #ffc107;">
                            <?php
/*                            $querytre = "SELECT COUNT(*) AS count_codicegravita_2 FROM interventi WHERE ID_INTERVENTO=2 AND STATO=1 AND CODICEGRAVITA = 2";
                            $result = $db->query($querytre);
                            $row = $result->fetch_assoc();
                            echo "GIALLI: " . $row['count_codicegravita_2'];
                            */?>
                        </div>
                        <div class="color-item" style="background-color: #dc3545;">
                            <?php
/*                            $queryquattro = "SELECT COUNT(*) AS count_codicegravita_3 FROM interventi WHERE ID_INTERVENTO=2 AND STATO=1 AND CODICEGRAVITA = 3";
                            $result = $db->query($queryquattro);
                            $row = $result->fetch_assoc();
                            echo "ROSSI: " . $row['count_codicegravita_3'];
                            */?>
                        </div>
                    </div>
                </div>
                <div class="card-footer" style="text-align: center">
                    <p>
                        <?php
/*                        $querytot = "SELECT COUNT(*) AS count_total FROM interventi WHERE ID_INTERVENTO=2 AND STATO=1";
                        $result = $db->query($querytot);
                        $row = $result->fetch_assoc();
                        $count_total = $row['count_total'];
                        echo "TOTALE: " . $count_total;
                        */?>
                    </p>
                </div>
            </div>
        </div>

    </div>-->
</div>

</body>

<?php include('../config/include/footer.php'); ?>

</html>