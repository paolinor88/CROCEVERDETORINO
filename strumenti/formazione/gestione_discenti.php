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
include "config/include/dictionary.php";

if (!isset($_SESSION['Livello']) || $_SESSION['Livello'] != 28 ) {
    header("Location: index.php");
    exit();
}

$query_corsi = "SELECT id_corso, titolo FROM corsi ORDER BY titolo";
$result_corsi = $db->query($query_corsi);
$corsi = $result_corsi->fetch_all(MYSQLI_ASSOC);

$id_corso = isset($_GET['id_corso']) ? intval($_GET['id_corso']) : null;
$id_edizione = isset($_GET['id_edizione']) ? intval($_GET['id_edizione']) : null;
$discenti_assegnati = [];
$edizioni = [];

if ($id_corso) {
    $stmt = $db->prepare("SELECT id_edizione, DATE_FORMAT(data_inizio, '%d/%m/%Y') as data_inizio FROM edizioni_corso WHERE id_corso = ? ORDER BY data_inizio ASC");
    $stmt->bind_param("i", $id_corso);
    $stmt->execute();
    $result_edizioni = $stmt->get_result();
    $edizioni = $result_edizioni->fetch_all(MYSQLI_ASSOC);

    $query_discenti = "
        SELECT d.id, d.nome, d.cognome, d.codice_fiscale, 
               r.IDFiliale, r.IDSquadra,
               COUNT(pl.id_lezione) AS lezioni_seguite,
               SUM(COALESCE(pl.completata, 0)) AS lezioni_completate,
               SUM(COALESCE(pl.superato_test, 0)) AS test_superati,
               (SELECT COUNT(*) FROM lezioni WHERE id_corso = ?) AS totale_lezioni,
               e.data_inizio, e.id_edizione
        FROM discenti d
        JOIN autorizzazioni_corsi a ON d.id = a.discente_id
        JOIN edizioni_corso e ON a.id_edizione = e.id_edizione
        LEFT JOIN rubrica r ON d.codice_fiscale = r.CodFiscale
        LEFT JOIN progresso_lezioni pl ON d.id = pl.discente_id AND pl.id_corso = ?
        WHERE e.id_corso = ?
    ";

    if ($id_edizione) {
        $query_discenti .= " AND e.id_edizione = ? ";
    }

    $query_discenti .= " GROUP BY d.id, e.data_inizio, e.id_edizione ";

    $stmt = $db->prepare($query_discenti);
    if ($id_edizione) {
        $stmt->bind_param("iiii", $id_corso, $id_corso, $id_corso, $id_edizione);
    } else {
        $stmt->bind_param("iii", $id_corso, $id_corso, $id_corso);
    }

    $stmt->execute();
    $result_discenti = $stmt->get_result();
    $discenti_assegnati = $result_discenti->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestione Discenti</title>
    <?php require "config/include/header.html"; ?>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../config/include/custom.css?v=<?= time() ?>">
    <style>
        .list-group-item small {
            display: inline-block;
        }

        .badge {
            margin-left: 10px;
        }
    </style>
</head>
<body>
<?php include "config/include/navbar.php"; ?>
<!-- CONTENUTO -->
<div class="container-fluid px-2 mb-4">
    <div class="card card-cv">
        <h3 class="text mb-4">Gestione Discenti</h3>

        <!-- FORM FILTRI -->
        <form method="GET" action="gestione_discenti.php" class="mb-4">
            <label for="id_corso">Seleziona un corso:</label>
            <select name="id_corso" id="id_corso" class="form-control" onchange="this.form.submit()">
                <option value="">-- Seleziona un corso --</option>
                <?php foreach ($corsi as $corso): ?>
                    <option value="<?= $corso['id_corso']; ?>" <?= ($id_corso == $corso['id_corso']) ? 'selected' : ''; ?>>
                        <?= htmlspecialchars($corso['titolo']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <?php if ($id_corso && !empty($edizioni)): ?>
                <label for="id_edizione" class="mt-3">Filtra per edizione:</label>
                <select name="id_edizione" id="id_edizione" class="form-control" onchange="this.form.submit()">
                    <option value="">-- Tutte le edizioni --</option>
                    <?php foreach ($edizioni as $edizione): ?>
                        <option value="<?= $edizione['id_edizione']; ?>" <?= ($id_edizione == $edizione['id_edizione']) ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($edizione['data_inizio']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            <?php endif; ?>
        </form>

        <?php if ($id_corso): ?>
            <!-- RICERCA DISCENTE -->
            <div class="mb-4">
                <label for="ricerca_discente">Cerca discente (Nome/Cognome/Codice Fiscale):</label>
                <input type="text" id="ricerca_discente" class="form-control" placeholder="Inserisci un nome o codice fiscale">
                <input type="hidden" id="id_corso_hidden" value="<?= $id_corso; ?>">
                <div id="risultati_ricerca" class="mt-2"></div>
            </div>

            <!-- LISTA DISCENTI -->
            <h4>Assegnati</h4>
            <?php if (!empty($discenti_assegnati)): ?>
                <ul class="list-group mb-3">
                    <?php foreach ($discenti_assegnati as $discente): ?>
                        <?php
                        $badge = '<span class="badge bg-secondary">Non Iniziato</span>';
                        if ($discente['lezioni_completate'] == $discente['totale_lezioni'] && $discente['test_superati'] == $discente['totale_lezioni']) {
                            $badge = '<span class="badge bg-success">Completato</span>';
                        } elseif ($discente['lezioni_seguite'] > 0) {
                            $badge = '<span class="badge bg-warning text-dark">In Corso</span>';
                        }
                        ?>
                        <li class="list-group-item d-flex justify-content-between align-items-start flex-wrap">
                            <div class="me-3">
                                <strong><?= htmlspecialchars($discente['nome'] . " " . $discente['cognome']); ?></strong><br>
                                <small>Codice Fiscale: <?= htmlspecialchars($discente['codice_fiscale']); ?></small><br>
                                <small>Sezione: <?= htmlspecialchars($dictionaryFiliale[$discente['IDFiliale']] ?? 'N/A'); ?></small> |
                                <small>Squadra: <?= htmlspecialchars($dictionarySquadra[$discente['IDSquadra']] ?? 'N/A'); ?></small><br>
                                <small>Edizione: <strong><?= date("d/m/Y", strtotime($discente['data_inizio'])); ?></strong></small>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <?= $badge; ?>
                                <button class="btn btn-danger btn-sm"
                                        onclick="confermaRimozione(<?= $discente['id']; ?>, <?= $discente['id_edizione']; ?>, <?= $id_corso; ?>)">
                                    Rimuovi
                                </button>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p class="text-center">Nessun discente assegnato a questo corso.</p>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<script>
    document.getElementById("ricerca_discente").addEventListener("input", function () {
        let query = this.value.trim();
        let id_corso = document.getElementById("id_corso_hidden").value;

        if (query.length >= 3) {
            console.log("Ricerca discenti: ", query, "ID_CORSO:", id_corso);

            fetch(`cerca_discente.php?query=${encodeURIComponent(query)}&id_corso=${id_corso}`)
                .then(response => response.text())
                .then(data => {
                    document.getElementById("risultati_ricerca").innerHTML = data;
                })
                .catch(error => {
                    console.error("Errore ricerca discenti:", error);
                    document.getElementById("risultati_ricerca").innerHTML = '<p class="text-danger">Errore nel caricamento dei dati.</p>';
                });
        } else {
            document.getElementById("risultati_ricerca").innerHTML = "";
        }
    });

    function confermaRimozione(id_discente, id_edizione, id_corso) {
        Swal.fire({
            title: "Sei sicuro?",
            text: "Questa operazione è irreversibile!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Sì, rimuovi!",
            cancelButtonText: "Annulla"
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`rimuovi_discente.php?id_discente=${id_discente}&id_edizione=${id_edizione}&id_corso=${id_corso}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: "Discente Rimosso!",
                                text: "Il discente è stato rimosso dall'edizione del corso.",
                                icon: "success"
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: "Errore",
                                text: data.message || "Errore durante la rimozione.",
                                icon: "error"
                            });
                        }
                    })
                    .catch(error => {
                        console.error("Errore nella rimozione:", error);
                        Swal.fire("Errore", "Errore imprevisto durante la rimozione.", "error");
                    });
            }
        });
    }
    function selezionaEdizione(id_discente, nome, cognome, codice_fiscale, id_corso) {
        console.log("Chiamata get_edizioni.php con id_corso:", id_corso);

        fetch(`get_edizioni.php?id_corso=${id_corso}`)
            .then(response => response.json())
            .then(edizioni => {
                //console.log("Edizioni ricevute:", edizioni);

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
                        //console.log("ID Edizione selezionato:", id_edizione); // Debugging
                        if (!id_edizione || id_edizione === "undefined") {
                            Swal.showValidationMessage("Seleziona un'edizione valida!");
                            return false;
                        }
                        return { id_edizione: id_edizione };
                    }
                }).then(result => {
                    if (result.isConfirmed) {
                        //console.log("Edizione confermata:", result.value.id_edizione);
                        confermaAssegnazione(id_discente, nome, cognome, codice_fiscale, id_corso, result.value.id_edizione);
                    }
                });
            })
            .catch(error => {
                console.error("Errore nel recupero delle edizioni:", error);
                Swal.fire("Errore", "Impossibile recuperare le edizioni.", "error");
            });
    }

    function confermaAssegnazione(id_discente, nome, cognome, codice_fiscale, id_corso, id_edizione) {
        Swal.fire({
            title: "Conferma Assegnazione",
            text: `${nome} ${cognome} verrà assegnato all'edizione selezionata.`,
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Sì, assegna",
            cancelButtonText: "Annulla"
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`assegna_discente.php`, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: `id_discente=${encodeURIComponent(id_discente)}&id_corso=${encodeURIComponent(id_corso)}&id_edizione=${encodeURIComponent(id_edizione)}`
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire("Fatto!", `${nome} ${cognome} è stato assegnato all'edizione del corso.`, "success")
                                .then(() => location.reload());
                        } else {
                            Swal.fire("Errore", data.error || "Errore durante l'assegnazione.", "error");
                        }
                    })
                    .catch(error => {
                        console.error("Errore assegnazione:", error);
                        Swal.fire("Errore", "Errore durante l'assegnazione.", "error");
                    });
            }
        });
    }
</script>

<?php include "config/include/footer.php"; ?>
</body>
</html>