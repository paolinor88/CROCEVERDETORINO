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
$corso = ['titolo' => '', 'descrizione' => '', 'note' => ''];

if ($id_corso) {
    $stmt = $db->prepare("SELECT titolo, descrizione, note FROM corsi WHERE id_corso = ?");
    $stmt->bind_param("i", $id_corso);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $corso = $result->fetch_assoc();
    } else {
        echo "Errore: Corso non trovato.";
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $id_corso ? "Modifica Corso" : "Nuovo Corso" ?></title>

    <?php require "config/include/header.html"; ?>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../config/include/custom.css?v=<?= time() ?>">
    <style>
        label {
            font-weight: 600;
            margin-top: 1rem;
        }
    </style>
</head>

<body>

<?php include "config/include/navbar.php"; ?>

<!-- CONTENUTO -->
<div class="container mb-5">
    <div class="card card-cv mx-auto" style="max-width: 600px;">
        <h3 class="text-center mb-4"><?= $id_corso ? "Modifica Corso" : "Nuovo Corso" ?></h3>
        <form id="corsoForm">
            <input type="hidden" name="id_corso" value="<?= $id_corso ?>">

            <label for="titolo">Titolo</label>
            <input type="text" name="titolo" class="form-control" value="<?= htmlspecialchars($corso['titolo']) ?>" required>

            <label for="descrizione">Descrizione</label>
            <textarea name="descrizione" class="form-control" rows="4" required><?= htmlspecialchars($corso['descrizione']) ?></textarea>

            <label for="note">Note</label>
            <textarea name="note" class="form-control" rows="3"><?= htmlspecialchars($corso['note']) ?></textarea>

            <div class="d-flex justify-content-between mt-4">
                <button type="submit" class="btn btn-success">
                    <?= $id_corso ? "Salva Modifiche" : "Crea Corso" ?>
                </button>
                <a href="gestione_corsi.php" class="btn btn-secondary">⬅ Indietro</a>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById("corsoForm").addEventListener("submit", function(event) {
        event.preventDefault();

        let formData = new FormData(this);

        fetch("salva_corso.php", {
            method: "POST",
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire("Successo!", "Il corso è stato salvato correttamente.", "success")
                        .then(() => window.location.href = "gestione_corsi.php");
                } else {
                    Swal.fire("Errore!", data.error || "Errore nel salvataggio.", "error");
                }
            })
            .catch(() => {
                Swal.fire("Errore!", "Errore nella richiesta.", "error");
            });
    });
</script>

<?php include "config/include/footer.php"; ?>
</body>
</html>
