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
include "../config/config.php";
global $db;

$query = "
    SELECT e.id_edizione, c.titolo, e.data_inizio, e.posti_disponibili, 
           (e.posti_disponibili - e.posti_occupati) AS posti_liberi
    FROM edizioni_corso e
    JOIN corsi c ON e.id_corso = c.id_corso
    WHERE e.archiviata != 1
    ORDER BY e.data_inizio ASC
";

$result = $db->query($query);
$edizioni = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iscrizione ai Corsi</title>
    <?php require "config/include/header.html"; ?>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../config/include/custom.css?v=<?= time() ?>">

    <style>
        .card-edizione h5 {
            font-weight: 600;
            color: #00A25E;
        }

        .card-edizione .info {
            font-size: 0.95rem;
            color: #444;
        }

        .card-edizione .btn {
            margin-top: 1rem;
        }
        @media (max-width: 576px) {
            .card-edizione {
                font-size: 0.95rem;
            }

            .card-edizione .btn {
                font-size: 0.9rem;
            }
        }
        .text-uppercase {
            text-transform: uppercase !important;
        }
    </style>
</head>
<body>

<!-- CONTENUTO -->
<div class="container my-5">
    <div class="text-center mb-4">
        <img src="../config/images/logo.png" class="img-fluid" style="max-width: 280px;" alt="LOGOCVTOESTESO">
    </div>

    <h3 class="text-center mb-3" style="color: #00A25E;">Formazione a distanza</h3>
    <p class="text-center mb-4 text-muted">Seleziona uno dei corsi disponibili e completa la registrazione per partecipare.</p>

    <div class="card card-cv mx-auto" style="max-width: 580px;">
        <?php if (empty($edizioni)): ?>
            <p class="text-center my-4">Nessuna edizione disponibile al momento.</p>
        <?php else: ?>
            <div class="d-grid gap-3">
                <?php foreach ($edizioni as $edizione): ?>
                    <div class="card border-0 shadow-sm card-edizione">
                        <div class="card-body text-center">
                            <h5><?= htmlspecialchars($edizione['titolo']); ?></h5>
                            <p class="info mb-1">
                                <i class="far fa-calendar-alt me-1"></i>
                                Data: <strong><?= date("d/m/Y", strtotime($edizione['data_inizio'])); ?></strong>
                            </p>
                            <p class="info">
                                <i class="fas fa-user-friends me-1"></i>
                                Posti disponibili: <strong><?= $edizione['posti_liberi']; ?></strong>
                            </p>

                            <button class="btn btn-outline-cv btn-iscriviti <?= ($edizione['posti_liberi'] <= 0) ? 'disabled' : ''; ?>"
                                    data-edizione="<?= $edizione['id_edizione']; ?>"
                                <?= ($edizione['posti_liberi'] <= 0) ? 'disabled' : ''; ?>>
                                <?= ($edizione['posti_liberi'] <= 0) ? "Posti esauriti" : "Iscriviti"; ?>
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    document.querySelectorAll(".btn-iscriviti:not(.disabled)").forEach(button => {
        button.addEventListener("click", function () {
            let idEdizione = this.getAttribute("data-edizione");

            Swal.fire({
                title: "Iscrizione al Corso",
                html: `
                    <form id="formIscrizione">
                        <div class="mb-3 text-start">
                            <label for="codice_fiscale" class="form-label">Codice Fiscale</label>
                            <input type="text" class="form-control text-uppercase" id="codice_fiscale" name="codice_fiscale" required>
                        </div>
                        <div class="mb-3 text-start">
                            <label for="codice_matricola" class="form-label">Matricola</label>
                            <input type="text" class="form-control text-uppercase" id="codice_matricola" name="codice_matricola" required>
                        </div>
                    </form>
                `,
                showCancelButton: true,
                confirmButtonText: "Iscriviti",
                cancelButtonText: "Annulla",
                customClass: {
                    confirmButton: 'swal2-confirm btn btn-success',
                    cancelButton: 'swal2-cancel btn btn-secondary'
                },
                buttonsStyling: false,
                focusConfirm: false,
                preConfirm: () => {
                    const codiceFiscale = document.getElementById("codice_fiscale").value.trim().toUpperCase();
                    const codiceMatricola = document.getElementById("codice_matricola").value.trim().toUpperCase();

                    if (!codiceFiscale || !codiceMatricola) {
                        Swal.showValidationMessage("Compila tutti i campi!");
                        return false;
                    }

                    return {
                        id_edizione: idEdizione,
                        codice_fiscale: codiceFiscale,
                        codice_matricola: codiceMatricola
                    };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const formData = new FormData();
                    formData.append("id_edizione", result.value.id_edizione);
                    formData.append("codice_fiscale", result.value.codice_fiscale);
                    formData.append("codice_matricola", result.value.codice_matricola);

                    fetch("processa_iscrizione.php", {
                        method: "POST",
                        body: formData
                    }).then(response => response.json()).then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: "Iscrizione completata!",
                                html: `
                                    <img src="../config/images/logo.png" alt="logo" class="img-fluid mb-3" style="max-width: 180px;">
                                    <p class="mb-0">Hai ricevuto un'email con le credenziali di accesso.</p>
                                    <p class="text-muted small mt-2">Grazie per esserti iscritto!</p>
                                `,
                                icon: "success",
                                confirmButtonText: "OK",
                                customClass: {
                                    confirmButton: 'btn btn-success'
                                },
                                buttonsStyling: false
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: "Errore!",
                                text: data.message,
                                icon: "error",
                                confirmButtonText: "OK",
                                customClass: {
                                    confirmButton: 'btn btn-danger'
                                },
                                buttonsStyling: false
                            });
                        }
                    }).catch(error => {
                        Swal.fire("Errore", "Errore imprevisto durante l'iscrizione.", "error");
                        console.error("Errore:", error);
                    });
                }
            });
        });
    });
</script>
</body>
</html>
