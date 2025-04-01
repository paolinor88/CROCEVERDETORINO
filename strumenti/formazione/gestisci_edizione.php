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

$id_edizione = isset($_GET['id_edizione']) ? intval($_GET['id_edizione']) : 0;
$id_corso = isset($_GET['id_corso']) ? intval($_GET['id_corso']) : 0;

if ($id_corso == 0) {
    echo "<p class='text-danger'>Errore: Parametri mancanti.</p>";
    exit();
}

$edizione = null;
$iscritti = 0;

if ($id_edizione > 0) {
    $stmt = $db->prepare("SELECT data_inizio, TIME_FORMAT(orario_inizio, '%H:%i') AS orario_inizio, posti_disponibili FROM edizioni_corso WHERE id_edizione = ?");
    $stmt->bind_param("i", $id_edizione);
    $stmt->execute();
    $result = $stmt->get_result();
    $edizione = $result->fetch_assoc();

    if (!$edizione) {
        echo "<p class='text-danger'>Errore: Edizione non trovata.</p>";
        exit();
    }

    $stmt = $db->prepare("SELECT COUNT(*) AS iscritti FROM autorizzazioni_corsi WHERE id_edizione = ?");
    $stmt->bind_param("i", $id_edizione);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $iscritti = $row['iscritti'];
}

?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo ($id_edizione > 0) ? "Modifica Edizione" : "Nuova Edizione"; ?></title>
    <?php require "config/include/header.html"; ?>
    <link rel="stylesheet" href="../config/include/custom.css?v=<?= time() ?>">
    <style>

        .d-flex.flex-wrap.gap-2 > .btn {
            margin-bottom: 0;
        }

        @media (max-width: 576px) {
            .d-flex.flex-wrap.gap-2 > .btn {
                flex: 1 1 100%;
            }
        }

    </style>
</head>
<body>
<?php include "config/include/navbar.php"; ?>
<div class="container mb-5">
    <div class="card card-cv mx-auto" style="max-width: 600px;">
        <h3 class="text-center mb-4"><?php echo ($id_edizione > 0) ? "Modifica Edizione" : "Nuova Edizione"; ?></h3>

        <form id="modificaEdizioneForm">
            <input type="hidden" name="id_edizione" value="<?php echo $id_edizione; ?>">
            <input type="hidden" name="id_corso" value="<?php echo $id_corso; ?>">

            <div class="mb-3">
                <label for="data_inizio" class="form-label">Data Inizio:</label>
                <input type="date" class="form-control" id="data_inizio" name="data_inizio"
                       value="<?php echo $edizione['data_inizio'] ?? ''; ?>" required>
            </div>

            <div class="mb-3">
                <label for="orario_inizio" class="form-label">Orario Inizio:</label>
                <input type="time" class="form-control" id="orario_inizio" name="orario_inizio"
                       value="<?php echo $edizione['orario_inizio'] ?? ''; ?>" required>
            </div>

            <div class="mb-3">
                <label for="posti_disponibili" class="form-label">Posti Disponibili:</label>
                <input type="number" class="form-control" id="posti_disponibili" name="posti_disponibili"
                       value="<?php echo $edizione['posti_disponibili'] ?? ''; ?>"
                       min="<?php echo ($id_edizione > 0) ? $iscritti : 1; ?>" required>
                <?php if ($id_edizione > 0): ?>
                    <small class="text-muted">Iscritti attuali: <?php echo $iscritti; ?></small>
                <?php endif; ?>
            </div>

            <div class="d-flex flex-wrap gap-2 mt-4">
                <button type="submit" class="btn btn-primary">
                    <?= ($id_edizione > 0) ? "Salva Modifiche" : "Crea Edizione"; ?>
                </button>
                <a href="gestisci_edizioni.php?id_corso=<?= $id_corso; ?>" class="btn btn-secondary">
                    ⬅ Indietro
                </a>
            </div>

        </form>
    </div>
</div>

<script>
    document.getElementById("modificaEdizioneForm").addEventListener("submit", function(event) {
        event.preventDefault();

        let formData = new FormData(this);

        fetch("salva_edizione.php", {
            method: "POST",
            body: formData
        }).then(response => response.json()).then(data => {
            if (data.success) {
                Swal.fire("Successo!", "<?php echo ($id_edizione > 0) ? "Modifiche salvate" : "Edizione creata"; ?> correttamente.", "success")
                    .then(() => window.location.href = "gestisci_edizioni.php?id_corso=<?php echo $id_corso; ?>");
            } else {
                Swal.fire("Errore", data.message || "Errore durante il salvataggio.", "error");
            }
        }).catch(error => {
            console.error("Errore:", error);
            Swal.fire("Errore", "Si è verificato un errore imprevisto.", "error");
        });
    });

    document.getElementById("eliminaEdizione")?.addEventListener("click", function() {
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
                let idEdizione = "<?php echo $id_edizione; ?>";

                if (!idEdizione || idEdizione <= 0) {
                    Swal.fire("Errore", "ID edizione non valido.", "error");
                    return;
                }

                let formData = new FormData();
                formData.append("id_edizione", idEdizione);

                fetch("elimina_edizione.php", {
                    method: "POST",
                    body: formData
                }).then(response => response.json()).then(data => {

                    if (data.success) {
                        Swal.fire("Eliminata!", "L'edizione è stata eliminata.", "success")
                            .then(() => window.location.href = "gestisci_edizioni.php?id_corso=<?php echo $id_corso; ?>");
                    } else {
                        Swal.fire("Errore", data.error || "Errore durante l'eliminazione.", "error");
                    }
                }).catch(error => {
                    console.error("Errore:", error);
                    Swal.fire("Errore", "Si è verificato un errore imprevisto.", "error");
                });
            }
        });
    });
</script>

<?php include "config/include/footer.php"; ?>
</body>
</html>
