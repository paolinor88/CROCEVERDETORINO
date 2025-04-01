<?php
/**
 *
 * @author     Paolo Randone
 * @author     <paolo.randone@croceverde.org>
 * @version    1.0
 * @note         Powered for Croce Verde Torino. All rights reserved
 *
 */
session_start();
include "../config/config.php";

if (!isset($_SESSION['Livello']) || $_SESSION['Livello'] != 28) {
    header("Location: index.php");
    exit();
}

$id_corso = $_GET['id_corso'] ?? null;
if (!$id_corso) {
    echo "Errore: ID corso mancante.";
    exit();
}

$stmt = $db->prepare("
    SELECT e.id_edizione, DATE_FORMAT(e.data_inizio, '%d/%m/%Y') AS data_inizio, 
           TIME_FORMAT(e.orario_inizio, '%H:%i') AS orario_inizio, e.archiviata, 
           (SELECT COUNT(*) FROM autorizzazioni_corsi WHERE id_edizione = e.id_edizione) AS num_discenti
    FROM edizioni_corso e 
    WHERE e.id_corso = ?
");
$stmt->bind_param("i", $id_corso);
$stmt->execute();
$result = $stmt->get_result();
$edizioni = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestione Edizioni</title>

    <?php require "config/include/header.html"; ?>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../config/include/custom.css?v=<?= time() ?>">
</head>
<body>

<?php include "config/include/navbar.php"; ?>

<!-- CONTENUTO -->
<div class="container-fluid px-2 mb-4">

    <div class="card card-cv">
        <h3 class="text mb-4">Gestione Edizioni</h3>
        <div class="mb-3 text-end">
            <button class="btn btn-success" onclick="gestisciEdizione(null, <?= $id_corso ?>)">+ Aggiungi Edizione</button>
        </div>

        <?php if (!empty($edizioni)): ?>
            <ul class="list-group list-group-edizioni">
                <?php foreach ($edizioni as $edizione): ?>
                    <li class="list-group-item edizione-item">
                        <div class="edizione-info">
                            <strong>Edizione del <?= $edizione['data_inizio'] ?> alle <?= $edizione['orario_inizio'] ?></strong>
                            <span id="badgeDiscenti_<?= $edizione['id_edizione'] ?>" class="badge bg-primary ms-2">
                    Iscritti: <?= $edizione['num_discenti'] ?>
                </span>
                        </div>

                        <div class="edizione-actions">
                            <button class="btn btn-warning btn-sm" onclick="gestisciEdizione(<?= $edizione['id_edizione'] ?>, <?= $id_corso ?>)">Modifica</button>
                            <button class="btn btn-danger btn-sm" onclick="confermaEliminazioneEdizione(<?= $edizione['id_edizione'] ?>)">Elimina</button>
                            <button id="btnArchivia_<?= $edizione['id_edizione'] ?>"
                                    class="btn btn-<?= ($edizione['archiviata']) ? 'success' : 'secondary' ?> btn-sm"
                                    onclick="archiviaEdizione(<?= $edizione['id_edizione'] ?>, <?= $id_corso ?>, <?= $edizione['archiviata'] ?>)">
                                <?= ($edizione['archiviata']) ? 'Riattiva' : 'Archivia' ?>
                            </button>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>

        <?php else: ?>
            <p class="text-center">Nessuna edizione trovata.</p>
        <?php endif; ?>

        <div class="mt-4 text-center">
            <a href="gestione_corsi.php" class="btn btn-secondary">⬅ Indietro</a>
        </div>
    </div>
</div>

<script>
    function gestisciEdizione(id_edizione, id_corso) {
        let url = id_edizione ? `gestisci_edizione.php?id_edizione=${id_edizione}&id_corso=${id_corso}` : `gestisci_edizione.php?id_corso=${id_corso}`;
        window.location.href = url;
    }

    function confermaEliminazioneEdizione(id_edizione) {
        Swal.fire({
            title: "Sei sicuro?",
            text: "Questa operazione eliminerà definitivamente l'edizione.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Sì, elimina",
            cancelButtonText: "Annulla"
        }).then((result) => {
            if (result.isConfirmed) {
                fetch("elimina_edizione.php", {
                    method: "POST",
                    body: new URLSearchParams({ id_edizione: id_edizione })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire("Eliminata!", "L'edizione è stata eliminata.", "success")
                                .then(() => location.reload());
                        } else {
                            Swal.fire("Errore", data.error || "Impossibile eliminare l'edizione.", "error");
                        }
                    })
                    .catch(error => {
                        console.error("Errore:", error);
                        Swal.fire("Errore", "Si è verificato un errore imprevisto.", "error");
                    });
            }
        });
    }

    function archiviaEdizione(id_edizione, id_corso, stato_attuale) {
        let azione = stato_attuale ? "riattivare" : "archiviare";
        let messaggio = stato_attuale
            ? "Riattivando questa edizione, i discenti potranno nuovamente iscriversi. Vuoi continuare?"
            : "Stai per disabilitare tutte le autorizzazioni dei discenti attualmente iscritti. Vuoi continuare?";

        Swal.fire({
            title: `Sei sicuro di voler ${azione}?`,
            text: messaggio,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Sì, continua",
            cancelButtonText: "Annulla"
        }).then((result) => {
            if (result.isConfirmed) {
                fetch("archivia_edizione.php", {
                    method: "POST",
                    body: new URLSearchParams({ id_edizione: id_edizione })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            let btn = document.getElementById(`btnArchivia_${id_edizione}`);
                            let badge = document.getElementById(`badgeDiscenti_${id_edizione}`);

                            if (btn) {
                                if (data.archiviata) {
                                    btn.classList.remove("btn-secondary");
                                    btn.classList.add("btn-success");
                                    btn.innerText = "Riattiva";
                                    btn.setAttribute("onclick", `archiviaEdizione(${id_edizione}, ${id_corso}, 1)`);

                                    if (badge) badge.innerText = "Iscritti: 0";
                                } else {
                                    btn.classList.remove("btn-success");
                                    btn.classList.add("btn-secondary");
                                    btn.innerText = "Archivia";
                                    btn.setAttribute("onclick", `archiviaEdizione(${id_edizione}, ${id_corso}, 0)`);

                                    fetch(`get_numero_discenti.php?id_edizione=${id_edizione}`)
                                        .then(response => response.json())
                                        .then(data => {
                                            if (data.success && badge) {
                                                badge.innerText = `Iscritti: ${data.num_discenti}`;
                                            }
                                        });
                                }
                            }

                            Swal.fire({
                                title: data.archiviata ? "Edizione archiviata!" : "Edizione riattivata!",
                                text: data.archiviata ? "Tutte le autorizzazioni dei discenti sono state revocate." : "L'edizione è ora attiva.",
                                icon: "success"
                            });
                        } else {
                            Swal.fire("Errore", data.error || "Errore durante l'aggiornamento.", "error");
                        }
                    })
                    .catch(error => {
                        console.error("Errore:", error);
                        Swal.fire("Errore", "Si è verificato un errore imprevisto.", "error");
                    });
            }
        });
    }
</script>
<?php include "config/include/footer.php"; ?>
</body>
</html>
