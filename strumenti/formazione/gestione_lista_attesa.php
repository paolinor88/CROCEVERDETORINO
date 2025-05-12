<?php
session_start();
include "../config/config.php";
include "config/include/dictionary.php";

if (!isset($_SESSION['Livello']) || $_SESSION['Livello'] != 28) {
    header("Location: index.php");
    exit();
}

// Corsi disponibili
$query_corsi = "SELECT id_corso, titolo FROM corsi ORDER BY titolo";
$result_corsi = $db->query($query_corsi);
$corsi = $result_corsi->fetch_all(MYSQLI_ASSOC);

$id_corso = isset($_GET['id_corso']) ? intval($_GET['id_corso']) : null;
$lista_attesa = [];

if ($id_corso) {
    $stmt = $db->prepare("
        SELECT la.id, la.id_utente, r.Nome, r.Cognome, r.CodFiscale, r.Mail, r.IDFiliale, r.IDSquadra
        FROM lista_attesa la
        JOIN rubrica r ON la.id_utente = r.IDUtente
        WHERE la.id_corso = ?
        ORDER BY la.data_iscrizione ASC
    ");
    $stmt->bind_param("i", $id_corso);
    $stmt->execute();
    $result = $stmt->get_result();
    $lista_attesa = $result->fetch_all(MYSQLI_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista d'attesa</title>
    <?php require "../config/include/header.html"; ?>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../config/include/custom.css?v=<?= time() ?>">
</head>
<body>
<?php include "config/include/navbar.php"; ?>

<div class="container-fluid px-2 mb-4">
    <div class="card card-cv">
        <h3 class="text mb-4">Gestione Lista d’attesa</h3>
        <?php if ($id_corso && !empty($lista_attesa)): ?>
            <div class="mb-3 text-end">
                <a href="esporta_lista_attesa.php?id_corso=<?= $id_corso ?>" class="btn btn-outline-cv" target="_blank">
                    <i class="fas fa-file-excel"></i> Esporta Excel
                </a>
            </div>
        <?php endif; ?>

        <form method="GET" action="gestione_lista_attesa.php" class="mb-4">
            <label for="id_corso">Seleziona un corso:</label>
            <select name="id_corso" id="id_corso" class="form-control" onchange="this.form.submit()">
                <option value="">-- Seleziona un corso --</option>
                <?php foreach ($corsi as $corso): ?>
                    <option value="<?= $corso['id_corso']; ?>" <?= ($id_corso == $corso['id_corso']) ? 'selected' : ''; ?>>
                        <?= htmlspecialchars($corso['titolo']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>

        <?php if ($id_corso): ?>
            <?php if (!empty($lista_attesa)): ?>
                <ul class="list-group">
                    <?php foreach ($lista_attesa as $utente): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-start flex-wrap">
                            <div class="me-3">
                                <strong><?= htmlspecialchars($utente['Nome'] . ' ' . $utente['Cognome']); ?></strong><br>
                                <small>Codice Fiscale: <?= htmlspecialchars($utente['CodFiscale']); ?></small><br>
                                <small>Email: <?= htmlspecialchars($utente['Mail']); ?></small><br>
                                <small>Sezione: <?= htmlspecialchars($dictionaryFiliale[$utente['IDFiliale']] ?? 'N/A'); ?></small> |
                                <small>Squadra: <?= htmlspecialchars($dictionarySquadra[$utente['IDSquadra']] ?? 'N/A'); ?></small>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <button class="btn btn-success btn-sm"
                                        onclick="selezionaEdizione(<?= $utente['id_utente']; ?>, '<?= addslashes($utente['Nome']); ?>', '<?= addslashes($utente['Cognome']); ?>', '<?= $utente['CodFiscale']; ?>', <?= $id_corso; ?>)">
                                    Assegna a edizione
                                </button>
                                <button class="btn btn-danger btn-sm"
                                        onclick="rimuoviAttesa(<?= $utente['id']; ?>)">
                                    Rimuovi
                                </button>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p class="text-center">Nessun utente in lista d’attesa per questo corso.</p>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<script>
    function selezionaEdizione(id_discente, nome, cognome, codice_fiscale, id_corso) {
        fetch(`get_edizioni.php?id_corso=${id_corso}`)
            .then(response => response.json())
            .then(edizioni => {
                if (!Array.isArray(edizioni) || edizioni.length === 0) {
                    Swal.fire("Errore", "Nessuna edizione disponibile per questo corso.", "error");
                    return;
                }

                let options = edizioni.map(ed => `<option value="${ed.id_edizione}">${ed.data_inizio}</option>`).join('');

                Swal.fire({
                    title: "Seleziona l'edizione",
                    html: `<select id="selezione_edizione" class="swal2-input">${options}</select>`,
                    showCancelButton: true,
                    confirmButtonText: "Assegna",
                    cancelButtonText: "Annulla",
                    preConfirm: () => {
                        let id_edizione = document.getElementById("selezione_edizione").value;
                        if (!id_edizione) {
                            Swal.showValidationMessage("Seleziona un'edizione valida!");
                            return false;
                        }
                        return { id_edizione: id_edizione };
                    }
                }).then(result => {
                    if (result.isConfirmed) {
                        confermaAssegnazione(id_discente, nome, cognome, codice_fiscale, id_corso, result.value.id_edizione);
                    }
                });
            })
            .catch(error => {
                console.error("Errore nel recupero edizioni:", error);
                Swal.fire("Errore", "Impossibile recuperare le edizioni.", "error");
            });
    }

    function confermaAssegnazione(id_discente, nome, cognome, codice_fiscale, id_corso, id_edizione) {
        Swal.fire({
            title: "Conferma Assegnazione",
            text: `${nome} ${cognome} verrà assegnato all'edizione selezionata.`,
            icon: "question",
            showCancelButton: true,
            confirmButtonText: "Sì, assegna",
            cancelButtonText: "Annulla"
        }).then((result) => {
            if (result.isConfirmed) {
                fetch("assegna_discente.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: `id_discente=${encodeURIComponent(id_discente)}&id_corso=${encodeURIComponent(id_corso)}&id_edizione=${encodeURIComponent(id_edizione)}`
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire("Assegnato!", `${nome} ${cognome} è stato iscritto.`, "success")
                                .then(() => location.reload());
                        } else {
                            Swal.fire("Errore", data.message || "Errore durante l'assegnazione.", "error");
                        }
                    })
                    .catch(error => {
                        console.error("Errore durante l'assegnazione:", error);
                        Swal.fire("Errore", "Errore imprevisto.", "error");
                    });
            }
        });
    }

    function rimuoviAttesa(id_attesa) {
        Swal.fire({
            title: "Conferma rimozione",
            text: "Vuoi davvero rimuovere questo utente dalla lista d'attesa?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Sì, rimuovi",
            cancelButtonText: "Annulla"
        }).then(result => {
            if (result.isConfirmed) {
                fetch(`rimuovi_attesa.php?id=${id_attesa}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire("Rimosso!", "Utente rimosso dalla lista.", "success")
                                .then(() => location.reload());
                        } else {
                            Swal.fire("Errore", data.message || "Errore durante la rimozione.", "error");
                        }
                    })
                    .catch(error => {
                        console.error("Errore nella rimozione:", error);
                        Swal.fire("Errore", "Errore imprevisto.", "error");
                    });
            }
        });
    }
</script>
<?php include "../config/include/footer.php"; ?>
</body>
</html>
