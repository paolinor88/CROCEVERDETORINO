<?php
/**
 *
 * @author     Paolo Randone
 * @author     <paolo.randone@croceverde.org>
 * @version    8.2
 * @note         Powered for Croce Verde Torino. All rights reserved
 *
 */
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include "../config/config.php";
global $db;

if (!isset($_GET['id_corso']) || !is_numeric($_GET['id_corso'])) {
    header("Location: corsi.php");
    exit();
}

$id_corso = intval($_GET['id_corso']);
$discente_id = $_SESSION['discente_id'] ?? null; // Può essere null per corsi liberi

$query_corso = "SELECT titolo, accesso_libero FROM corsi WHERE id_corso = ?";
$stmt = $db->prepare($query_corso);
$stmt->bind_param("i", $id_corso);
$stmt->execute();
$stmt->bind_result($titolo_corso, $accesso_libero);
$stmt->fetch();
$stmt->close();

if (!$titolo_corso) {
    header("Location: corsi.php");
    exit();
}

if (!$accesso_libero && !$discente_id) {
    header("Location: login.php?redirect=" . urlencode("lezioni.php?id_corso=$id_corso"));
    exit();
}

if (!$accesso_libero && $discente_id) {
    $query_autorizzazione = "SELECT 1 FROM autorizzazioni_corsi WHERE discente_id = ? AND id_corso = ?";
    $stmt = $db->prepare($query_autorizzazione);
    $stmt->bind_param("ii", $discente_id, $id_corso);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 0) {
        header("Location: corsi.php?errore=non_autorizzato");
        exit();
    }
}

$query_progresso = "
    SELECT COUNT(l.id_lezione) AS total_lezioni,
           SUM(COALESCE(p.completata, 0)) AS lezioni_completate,
           SUM(COALESCE(p.superato_test, 0)) AS test_superati
    FROM lezioni l
    LEFT JOIN progresso_lezioni p 
        ON l.id_lezione = p.id_lezione 
        AND p.discente_id = ?
        AND p.id_corso = ?
    WHERE l.id_corso = ?
";

$stmt = $db->prepare($query_progresso);
$discente_query_id = $discente_id ?? 0;
$stmt->bind_param("iii", $discente_query_id, $id_corso, $id_corso);
$stmt->execute();
$stmt->bind_result($total_lezioni, $lezioni_completate, $test_superati);
$stmt->fetch();
$stmt->close();

$query = "
    SELECT l.*, 
           COALESCE(p.completata, 0) AS completata, 
           COALESCE(p.superato_test, 0) AS superato_test 
    FROM lezioni l
    LEFT JOIN progresso_lezioni p 
        ON l.id_lezione = p.id_lezione 
        AND p.discente_id = ?
        AND p.id_corso = ?
    WHERE l.id_corso = ?
    ORDER BY l.ordine ASC
";

$stmt = $db->prepare($query);
$stmt->bind_param("iii", $discente_query_id, $id_corso, $id_corso);
$stmt->execute();
$result = $stmt->get_result();

$lezioni = [];
$lezione_precedente_completata = true; // La prima lezione è sempre disponibile

while ($row = $result->fetch_assoc()) {
    $row['sbloccata'] = $lezione_precedente_completata;
    $lezione_precedente_completata = ($row['completata'] == 1 && $row['superato_test'] == 1);
    $lezioni[] = $row;
}

$stmt->close();
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($titolo_corso); ?></title>
    <?php require "../config/include/header.html"; ?>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../config/include/custom.css?v=<?= time() ?>">

    <style>
        .sidebar {
            width: 300px;
            background-color: #00A25E;
            color: white;
            padding: 20px;
            height: 100vh;
            position: fixed;
            overflow-y: auto;
            border-radius: 0 20px 20px 0; /* Angoli arrotondati solo sul lato destro */
            box-shadow: 3px 0px 10px rgba(0, 0, 0, 0.2); /* Effetto ombra */
            transition: transform 0.3s ease-in-out;
        }

        .lezione-link {
            display: block;
            padding: 12px;
            margin: 8px 0;
            color: white;
            text-decoration: none;
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            transition: all 0.3s ease-in-out;
        }

        .lezione-link:hover {
            background-color: white;
            color: #00A25E;
            border: 1px solid #fff;
        }

        .main-content {
            margin-left: 320px;
            padding: 20px;
            flex: 1;
        }

        .video-container {
            width: 100%;
            text-align: center;
        }

        video {
            width: 80%;
            border-radius: 10px;
        }

        .locked {
            opacity: 0.5;
            pointer-events: none;
        }

        @media (max-width: 991px) {
            .sidebar {
                width: 260px;
            }
            .main-content {
                margin-left: 280px;
            }
        }

        @media (max-width: 767px) {
            .breadcrumb {
                display: none;
            }

            .sidebar {
                width: 250px;
                transform: translateX(-100%);
                position: fixed;
                left: 0;
                top: 0;
                bottom: 0;
                border-radius: 0;
                box-shadow: 5px 0px 15px rgba(0, 0, 0, 0.2);
                background-color: #00A25E;
                color: white;
                padding: 20px;
                z-index: 1040;
                transition: transform 0.3s ease-in-out;
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
                width: 100%;
            }

            .menu-toggle-mobile {
                background: none;
                border: none;
                color: white;
                font-size: 1.5rem;
                line-height: 1;
                cursor: pointer;
                padding: 0.25rem 0.5rem;
                z-index: 1050;
            }

            .sidebar .close-btn {
                display: block;
                text-align: right;
                margin-bottom: 10px;
            }

            .sidebar .close-btn button {
                background: none;
                border: none;
                color: white;
                font-size: 24px;
            }
        }

        .timeline-lock {
            position: absolute;
            bottom: 0;
            left: 0;
            height: 40px;
            width: 100%;
            z-index: 10;
            background: transparent;
            cursor: not-allowed;
        }

    </style>
    <script>
        function toggleSidebar() {
            document.querySelector(".sidebar").classList.toggle("active");
        }

        function verificaVisione(video, id_lezione, durata, id_corso) {
            let tempoVisionato = 0;
            let ultimoSalvataggio = 0;
            let lastAllowedTime = 0;
            let seekUnlocked = true;
            let skipManualeInCorso = false;
            let videoCompletato = false;
            let overlay = null;

            overlay = document.createElement("div");
            overlay.classList.add("timeline-lock");
            video.parentElement.style.position = "relative";
            video.parentElement.appendChild(overlay);

            fetch(`recupera_progresso.php?id_lezione=${id_lezione}&id_corso=${id_corso}`)
                .then(response => response.json())
                .then(data => {
                    if (data.completata == 1) {
                        video.dataset.completato = "true";
                        videoCompletato = true;

                        const overlay = document.getElementById(`overlay_${id_lezione}`);
                        if (overlay) overlay.remove();
                    }

                    if (data.tempo_visionato && data.tempo_visionato < durata) {
                        video.currentTime = data.tempo_visionato;
                        lastAllowedTime = data.tempo_visionato;
                    }

                    if (videoCompletato && overlay) {
                        overlay.remove();
                        overlay = null;
                    }

                    seekUnlocked = false;
                });

            video.addEventListener("seeking", () => {
                if (videoCompletato) return;

                if (!seekUnlocked && video.currentTime > lastAllowedTime + 1) {
                    skipManualeInCorso = true;
                    video.currentTime = lastAllowedTime;

                    setTimeout(() => {
                        skipManualeInCorso = false;
                    }, 1000);
                }
            });

            video.addEventListener("timeupdate", () => {
                if (!skipManualeInCorso && !videoCompletato) {
                    lastAllowedTime = Math.max(lastAllowedTime, video.currentTime);
                }
            });

            window.addEventListener("beforeunload", function () {
                if (!video.paused && !video.ended && !videoCompletato) {
                    let tempo = Math.floor(video.currentTime);
                    aggiornaProgresso(id_lezione, id_corso, tempo);
                }
            });

            video.addEventListener("ended", function () {
                if (!videoCompletato) {
                    videoCompletato = true;
                    video.dataset.completato = "true";
                    confermaVisione(id_lezione, id_corso);
                }
                const overlayFine = document.getElementById(`overlay_${id_lezione}`);
                if (overlayFine) overlayFine.remove();

            });

            video.addEventListener("loadedmetadata", function () {
                if (video.dataset.completato === "true") {
                    video.currentTime = 0;
                }
            });

            setInterval(() => {
                if (
                    !video.paused &&
                    !video.ended &&
                    !videoCompletato &&
                    !skipManualeInCorso
                ) {
                    tempoVisionato = Math.floor(video.currentTime);
                    if (tempoVisionato !== ultimoSalvataggio) {
                        aggiornaProgresso(id_lezione, id_corso, tempoVisionato);
                        ultimoSalvataggio = tempoVisionato;
                    }
                }
            }, 5000);
        }

        function aggiornaProgresso(id_lezione, id_corso, tempo_visionato) {
            fetch('salva_progresso.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    id_lezione: id_lezione,
                    id_corso: id_corso,
                    tempo_visionato: tempo_visionato
                })
            });
        }

        function confermaVisione(id_lezione, id_corso) {
            let video = document.querySelector(`#video_${id_lezione}`);

            if (video.dataset.completato === "true" && video.dataset.alertMostrato === "true") {
                return;
            }

            fetch('salva_progresso.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id_lezione: id_lezione, id_corso: id_corso, tempo_visionato: 9999 })
            }).then(response => response.json()).then(data => {
                if (data.success) {
                    Swal.fire({
                        title: "Lezione completata!",
                        text: "Ora puoi accedere al test.",
                        icon: "success",
                        allowOutsideClick: false,
                        showConfirmButton: true,
                        confirmButtonText: "OK"
                    }).then(() => {
                        video.dataset.completato = "true";
                        video.dataset.alertMostrato = "true";

                        let testButton = document.querySelector(`#test_button_${id_lezione}`);
                        if (testButton) {
                            testButton.removeAttribute("disabled");
                            testButton.innerHTML = 'Test <i class="fas fa-marker"></i>';
                        }
                    });
                }
            });
        }
    </script>
    <script>
        function generaCertificato(id_corso) {
            Swal.fire({
                title: "Generare il certificato?",
                text: "Riceverai il certificato via email e una copia verrà salvata automaticamente nella tua cartelle personale.",
                icon: "question",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Sì, genera",
                cancelButtonText: "Annulla"
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`genera_certificato.php?id_corso=${id_corso}`)
                        .then(response => response.json())
                        .then(data => {
                            Swal.fire("Successo!", "Il certificato è stato inviato via email.", "success");
                        });
                }
            });
        }
    </script>
    <script>
        function toggleSidebar() {
            document.querySelector(".sidebar").classList.toggle("active");
        }

        document.addEventListener("click", function(e) {
            const sidebar = document.querySelector(".sidebar");
            const toggle = document.querySelector(".menu-toggle-mobile");

            if (window.innerWidth < 768) {
                if (!sidebar.contains(e.target) && !toggle.contains(e.target)) {
                    sidebar.classList.remove("active");
                }
            }
        });
    </script>
</head>
<body>
<?php include "config/include/navbarlezioni.php"; ?>
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav class="sidebar col-md-3">
            <div class="d-flex justify-content-end d-md-none">
                <br>
            </div>
            <h4><?php echo htmlspecialchars($titolo_corso); ?></h4>
            <hr>
            <?php foreach ($lezioni as $lezione): ?>
                <a href="lezioni.php?id_corso=<?php echo $id_corso; ?>&id_lezione=<?php echo $lezione['id_lezione']; ?>" class="lezione-link">
                    <div class="d-flex justify-content-between align-items-center">
                        <span><?php echo htmlspecialchars($lezione['titolo']); ?></span>
                        <span>
                <?php if ($lezione['superato_test'] === 1): ?>
                    <i class='far fa-check-circle'></i>
                <?php endif; ?>
            </span>
                    </div>
                </a>
            <?php endforeach; ?>
            <?php
            $corso_completato = ($total_lezioni > 0 && $lezioni_completate == $total_lezioni && $test_superati == $total_lezioni);
            if ($corso_completato): ?>
                <br>
                <div class="alert alert-success text-center">
                    🎉 COMPLIMENTI! Hai superato il corso con successo!
                    <a href="#" onclick="generaCertificato(<?php echo $id_corso; ?>)" class="alert-link">CLICCA QUI</a> per ottenere il certificato.
                </div>
            <?php endif; ?>
            <br>
            <a href="corsi.php" class="btn btn-secondary">⬅ Torna ai Corsi</a>
        </nav>

        <main class="main-content col-md-9">

            <?php
            $lezione_corrente = isset($_GET['id_lezione']) ? $_GET['id_lezione'] : null;

            if (empty($lezioni)) {
                $lezione_selezionata = null; // Nessuna lezione disponibile
            } else {
                $lezione_corrente = $lezione_corrente ?? $lezioni[0]['id_lezione'];

                foreach ($lezioni as $lezione) {
                    if ($lezione['id_lezione'] == $lezione_corrente) {
                        $lezione_selezionata = $lezione;
                        break;
                    }
                }
            }


            if ($lezione_selezionata): ?>
                <div class="card shadow-sm <?php echo $lezione_selezionata['sbloccata'] ? '' : 'locked'; ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($lezione_selezionata['titolo']); ?></h5>
                        <div class="video-container" style="position: relative;">
                            <video id="video_<?= $lezione['id_lezione']; ?>"
                                   data-completato="<?= $lezione['completata'] == 1 ? 'true' : 'false' ?>"
                                   controls
                                   controlsList="nodownload noplaybackrate"
                                   onplay="verificaVisione(this, <?= $lezione['id_lezione']; ?>, <?= $lezione['durata']; ?>, <?= $id_corso; ?>)">
                                <source src="<?= htmlspecialchars($lezione['video_url']); ?>" type="video/mp4">
                                Il tuo browser non supporta il tag video.
                            </video>

                            <?php if ($lezione['completata'] != 1): ?>
                                <div class="timeline-lock" id="overlay_<?= $lezione['id_lezione']; ?>"></div>
                            <?php endif; ?>
                        </div>

                        <div class="d-flex justify-content-center">
                            <?php
                            $test_superato = $lezione_selezionata['superato_test'] == 1;
                            $completata = $lezione_selezionata['completata'] == 1;
                            $sbloccata = $lezione_selezionata['sbloccata'];

                            $test_btn_disabled = !$sbloccata || !$completata || $test_superato;
                            $test_btn_class = $test_superato ? 'btn btn-success' : 'btn btn-outline-primary';
                            $test_btn_text = $test_superato
                                ? 'Hai già completato con successo questo test!'
                                : ($sbloccata && $completata ? 'Test <i class="fas fa-marker"></i>' : 'Guarda il video per accedere al test');
                            ?>
                            <button id="test_button_<?php echo $lezione_selezionata['id_lezione']; ?>"
                                    class="<?php echo $test_btn_class; ?> mt-3 w-100"
                                <?php echo $test_btn_disabled ? 'disabled' : ''; ?>
                                <?php if (!$test_superato): ?>
                                    onclick="location.href='test.php?id=<?php echo $lezione_selezionata['id_lezione']; ?>&id_corso=<?php echo $id_corso; ?>'"
                                <?php endif; ?>>
                                <?php echo $test_btn_text; ?>
                            </button>
                        </div>
                    </div>
                </div>

            <?php else: ?>
                <div class="alert alert-warning text-center mt-4">
                    <h4>⚠ Nessuna lezione disponibile</h4>
                    <p>Al momento non ci sono lezioni disponibili per questo corso.</p>
                    <a href="corsi.php" class="btn btn-primary">⬅ Torna ai Corsi</a>
                </div>
            <?php endif; ?>

        </main>
    </div>
</div>
<script>
    document.querySelector(".menu-toggle").addEventListener("click", function () {
        document.querySelector(".sidebar").classList.toggle("active");
    });
</script>
</body>
</html>