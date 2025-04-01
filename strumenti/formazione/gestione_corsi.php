<?php
session_start();
/**
 *
 * @author     Paolo Randone
 * @author     <paolo.randone@croceverde.org>
 * @version    8.2
 * @note         Powered for Croce Verde Torino. All rights reserved
 *
 */
include "../config/config.php";

if (!isset($_SESSION['Livello']) || $_SESSION['Livello'] != 28) {
    header("Location: index.php");
    exit();
}

$query_corsi = "
    SELECT c.id_corso, c.titolo, c.descrizione, c.note, c.accesso_libero,
           (SELECT COUNT(*) FROM edizioni_corso e WHERE e.id_corso = c.id_corso AND e.archiviata = 0) AS edizioni_attive
    FROM corsi c 
    ORDER BY c.titolo";
$result_corsi = $db->query($query_corsi);
$corsi = $result_corsi->fetch_all(MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestione Corsi</title>
    <?php require "config/include/header.html"; ?>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../config/include/custom.css?v=<?= time() ?>">
</head>

<body>
<?php include "config/include/navbar.php"; ?>

<!-- CONTENUTO -->
<div class="container-fluid px-2 mb-4">
    <div class="card card-cv">
        <h3 class="text mb-4">Gestione Corsi</h3>

        <div class="mb-3 text-end">
            <button class="btn btn-success" onclick="gestisciCorso()">+ Aggiungi Nuovo Corso</button>
        </div>

        <?php if (!empty($corsi)): ?>
            <ul class="list-group list-group-corsi">
                <?php foreach ($corsi as $corso): ?>
                    <li class="list-group-item corso-item">
                        <div class="corso-info">
                            <strong>
                                <?= htmlspecialchars($corso['titolo']); ?>
                                <?php if ($corso['edizioni_attive'] > 0): ?>
                                    <span class="badge bg-primary ms-2"><?= $corso['edizioni_attive']; ?> attive</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary ms-2">Nessuna attiva</span>
                                <?php endif; ?>
                            </strong>
                            <p><?= nl2br(htmlspecialchars($corso['descrizione'])); ?></p>
                            <p><em><?= nl2br(htmlspecialchars($corso['note'])); ?></em></p>
                        </div>

                        <div class="corso-actions">
                            <div class="form-check form-switch me-2">
                                <input class="form-check-input" type="checkbox"
                                       id="switch_<?= $corso['id_corso']; ?>"
                                    <?= ($corso['accesso_libero'] == 1) ? 'checked' : ''; ?>
                                       onchange="toggleAccessoLibero(<?= $corso['id_corso']; ?>, this)">
                                <label class="form-check-label"
                                       for="switch_<?= $corso['id_corso']; ?>"
                                       id="label_switch_<?= $corso['id_corso']; ?>">
                                    <?= ($corso['accesso_libero'] == 1) ? 'Accesso Libero' : 'Riservato'; ?>
                                </label>
                            </div>

                            <button class="btn btn-warning btn-sm" onclick="gestisciCorso(<?= $corso['id_corso']; ?>)">Modifica</button>
                            <button class="btn btn-danger btn-sm" onclick="confermaEliminazioneCorso(<?= $corso['id_corso']; ?>)">Elimina</button>
                            <button class="btn btn-info btn-sm" onclick="gestisciEdizioni(<?= $corso['id_corso']; ?>)">Gestisci Edizioni</button>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p class="text-center">Nessun corso disponibile.</p>
        <?php endif; ?>
    </div>
</div>

<script>
    function gestisciCorso(id_corso = null) {
        let url = id_corso ? `gestisci_corso.php?id_corso=${id_corso}` : "gestisci_corso.php";
        window.location.href = url;
    }

    function gestisciEdizioni(id_corso) {
        window.location.href = `gestisci_edizioni.php?id_corso=${id_corso}`;
    }

    function confermaEliminazioneCorso(id_corso) {
        Swal.fire({
            title: "Sei sicuro?",
            text: "Se elimini il corso, perderai tutte le edizioni associate!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Sì, elimina",
            cancelButtonText: "Annulla"
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`elimina_corso.php`, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: `id_corso=${id_corso}`
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire("Corso eliminato!", "", "success").then(() => location.reload());
                        } else {
                            Swal.fire("Errore", data.error || "Impossibile eliminare il corso", "error");
                        }
                    })
                    .catch(() => Swal.fire("Errore", "Si è verificato un errore imprevisto", "error"));
            }
        });
    }

    function toggleAccessoLibero(id_corso, checkbox) {
        let stato = checkbox.checked ? 1 : 0;

        fetch("toggle_accesso_libero.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `id_corso=${id_corso}&accesso_libero=${stato}`
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById(`label_switch_${id_corso}`).innerText = stato ? "Accesso Libero" : "Riservato";
                    Swal.fire({
                        title: "Successo",
                        text: `Accesso libero ${stato ? 'abilitato' : 'disabilitato'}.`,
                        icon: "success",
                        timer: 1500
                    });
                } else {
                    Swal.fire("Errore", data.error || "Errore durante l'aggiornamento.", "error");
                    checkbox.checked = !stato;
                }
            })
            .catch(() => {
                Swal.fire("Errore", "Si è verificato un errore imprevisto.", "error");
                checkbox.checked = !stato;
            });
    }
</script>

<?php include "config/include/footer.php"; ?>
</body>
</html>

