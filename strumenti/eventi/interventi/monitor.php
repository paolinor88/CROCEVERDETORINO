<?php
header('Access-Control-Allow-Origin: *');
session_start();
include "../config/config.php";

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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitor Real-Time</title>
    <?php require "../config/include/header.html"; ?>
    <link rel="stylesheet" href="../config/include/custom.css?v=<?= time() ?>">
</head>
<body>
<div class="container-fluid py-3">
    <div class="alert alert-secondary text-center">
        Questa pagina si ricarica automaticamente tra <span id="countdown">60</span> secondi.
    </div>
    <script>
        function startCountdown(seconds) {
            const el = document.getElementById("countdown");
            const interval = setInterval(() => {
                if (--seconds >= 0) {
                    el.innerText = seconds;
                } else {
                    clearInterval(interval);
                    location.reload();
                }
            }, 1000);
        }
        startCountdown(60);
    </script>

    <?php if (isset($_GET['message'])): ?>
        <div class="container">
            <div class="alert <?= $alert_class ?> text-center fw-bold" role="alert">
                <?= $alert_message ?>
            </div>
        </div>
        <script>setTimeout(() => { location.href = "list.php"; }, 2000);</script>
    <?php endif; ?>

    <div class="row g-4 pt-2">
        <?php
        $postazioni = ['PAPA 1', 'PAPA 2', 'PAPA 3'];
        $colori = [
            0 => ['label' => 'BIANCHI', 'colore' => '#ffffff'],
            1 => ['label' => 'VERDI',   'colore' => '#198754'],
            2 => ['label' => 'GIALLI',  'colore' => '#ffc107'],
            3 => ['label' => 'ROSSI',   'colore' => '#dc3545'],
        ];

        foreach ($postazioni as $postazione):
            ?>
            <div class="col-md-4">
                <div class="card card-cv h-100">
                    <div class="card-body">
                        <h3 class="card-title text-center"><?= $postazione ?></h3>
                        <div class="d-flex flex-column gap-2 mt-3">
                            <?php
                            foreach ($colori as $codice => $info):
                                $query = "SELECT COUNT(*) AS count_codicegravita FROM interventi WHERE POSTAZIONE='$postazione' AND STATO=1 AND CODICEGRAVITA = $codice";
                                $result = $db->query($query);
                                $row = $result->fetch_assoc();
                                ?>
                                <?php
                                    $style = $info['colore'] === '#ffffff'
                                        ? 'background-color: #ffffff; border: 1px solid #dee2e6;'
                                        : "background-color: {$info['colore']};";
                                ?>
                                <div class="d-flex justify-content-between align-items-center px-3 py-2 rounded" style="<?= $style ?>">
                                    <strong><?= $info['label'] ?></strong>
                                    <span><?= $row['count_codicegravita'] ?></span>
                                </div>

                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="card-footer text-center fw-bold">
                        <?php
                        $querytot = "SELECT COUNT(*) AS count_total FROM interventi WHERE POSTAZIONE='$postazione' AND STATO=1";
                        $result = $db->query($querytot);
                        $row = $result->fetch_assoc();
                        echo "TOTALE: " . $row['count_total'];
                        ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
</body>
</html>
