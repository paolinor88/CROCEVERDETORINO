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

if (!isset($_SESSION['discente_id'])) {
    header("Location: login.php");
    exit();
}

$discente_id = $_SESSION['discente_id'];
$id_lezione = $_GET['id'] ?? 0;

$query = "SELECT id_corso FROM lezioni WHERE id_lezione = ?";
$stmt = $db->prepare($query);
$stmt->bind_param("i", $id_lezione);
$stmt->execute();
$stmt->bind_result($id_corso);
$stmt->fetch();
$stmt->close();

if (!$id_corso) {
    header("Location: corsi.php");
    exit();
}

$query = "SELECT id, domanda, risposta1, risposta2, risposta3, risposta4, risposta_corretta FROM test_domande WHERE id_lezione = ?";
$stmt = $db->prepare($query);
$stmt->bind_param("i", $id_lezione);
$stmt->execute();
$result = $stmt->get_result();

$domande = [];
$numero_domanda = 1;

while ($row = $result->fetch_assoc()) {
    $risposte = [
        ["id" => 1, "testo" => $row["risposta1"]],
        ["id" => 2, "testo" => $row["risposta2"]],
        ["id" => 3, "testo" => $row["risposta3"]],
        ["id" => 4, "testo" => $row["risposta4"]]
    ];

    shuffle($risposte);

    $domande[] = [
        "id" => $row["id"],
        "numero" => $numero_domanda++,
        "domanda" => $row["domanda"],
        "risposte" => $risposte,
        "risposta_corretta" => $row["risposta_corretta"]
    ];
}

if (empty($domande)) {
    header("Location: lezioni.php?id_corso=" . $id_corso);
    exit();
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Lezione</title>
    <?php require "../config/include/header.html"; ?>
    <link rel="stylesheet" href="../config/include/custom.css?v=<?= time() ?>">
    <style>
        body { background-color: #f8f9fa; }
        .test-container { max-width: 700px; margin: 50px auto; padding: 20px; background: white; border-radius: 10px; box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1); }
        .btn-primary { background-color: #007bff; border: none; width: 100%; }
        .btn-primary:hover { background-color: #0056b3; }
        .error {
            border: 2px solid red;
            background-color: #ffdddd;
            padding: 5px;
            border-radius: 5px;
        }

        .correct {
            border: 2px solid green;
            background-color: #d4edda;
            padding: 5px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
<div class="test-container">
    <h2 class="text-center">Test Lezione <?php echo $id_lezione; ?></h2>
    <form id="testForm">
        <input type="hidden" name="id_lezione" value="<?php echo $id_lezione; ?>">
        <input type="hidden" name="id_corso" value="<?php echo $id_corso; ?>">
        <input type="hidden" name="id_edizione" value="<?= $id_edizione ?>">

        <?php foreach ($domande as $domanda): ?>
            <div class="mb-3">
                <p><strong><?php echo $domanda['numero'] . ". " . htmlspecialchars($domanda['domanda']); ?></strong></p>
                <div id="risposte_<?php echo $domanda['id']; ?>">
                    <?php foreach ($domanda['risposte'] as $risposta): ?>
                        <label class="d-block">
                            <input type="radio" name="risposta_<?php echo $domanda['id']; ?>" value="<?php echo $risposta['id']; ?>">
                            <?php echo htmlspecialchars($risposta['testo']); ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
        <button type="submit" class="btn btn-primary mt-3">Invia Risposte</button>
    </form>
</div>

<script>
    document.getElementById("testForm").addEventListener("submit", function(event) {
        event.preventDefault();

        let form = this;
        let formData = new FormData(form);
        let allAnswered = true;

        document.querySelectorAll("[id^='risposte_']").forEach(function(div) {
            let inputs = div.querySelectorAll("input[type='radio']");
            let checked = Array.from(inputs).some(input => input.checked);

            div.querySelectorAll("label").forEach(label => {
                label.classList.remove("error", "correct");
            });

            if (!checked) {
                div.classList.add("error");
                allAnswered = false;
            } else {
                div.classList.remove("error");
            }
        });

        if (!allAnswered) {
            Swal.fire({
                title: "Attenzione!",
                text: "Devi rispondere a tutte le domande prima di inviare il test.",
                icon: "warning",
                confirmButtonText: "OK"
            });
            return;
        }

        fetch("salva_test.php", {
            method: "POST",
            body: formData
        }).then(response => response.json()).then(data => {
            let punteggio = data.score + "/" + data.total;
            let submitButton = document.querySelector("button[type='submit']");

            if (data.success) {
                Swal.fire({
                    title: "Test superato!",
                    html: "Hai ottenuto un punteggio di <strong>" + punteggio + "</strong>.",
                    icon: "success",
                    confirmButtonText: "OK"
                }).then(() => {
                    data.error_questions.forEach(q => {
                        let risposteDiv = document.getElementById("risposte_" + q.id);
                        let inputs = risposteDiv.querySelectorAll("input[type='radio']");

                        inputs.forEach(input => {
                            let label = input.parentElement;
                            if (parseInt(input.value) === q.corretta) {
                                label.classList.add("correct");
                            } else if (input.checked) {
                                label.classList.add("error");
                            }
                        });
                    });

                    submitButton.textContent = "Vai alla Lezione Successiva";
                    submitButton.classList.remove("btn-primary");
                    submitButton.classList.add("btn-success");
                    submitButton.onclick = function() {
                        window.location.href = "lezioni.php?id_corso=" + formData.get("id_corso") + "&id_lezione=" + data.next_lezione;
                    };
                });
            } else {
                Swal.fire({
                    title: "Test non superato",
                    html: "Hai ottenuto un punteggio di <strong>" + punteggio + "</strong>.<br><br>Alcune risposte sono errate.<br>Controlla e riprova.",
                    icon: "error",
                    confirmButtonText: "OK"
                });

                data.error_questions.forEach(q => {
                    let risposteDiv = document.getElementById("risposte_" + q.id);
                    let inputs = risposteDiv.querySelectorAll("input[type='radio']");

                    inputs.forEach(input => {
                        let label = input.parentElement;
                        if (input.checked) {
                            label.classList.add("error");
                        }
                    });
                });
            }
        }).catch(error => console.error("Errore:", error));
    });

    document.querySelectorAll("input[type='radio']").forEach(input => {
        input.addEventListener("change", function() {
            let labels = this.closest("#risposte_" + this.name.split("_")[1]).querySelectorAll("label");
            labels.forEach(label => label.classList.remove("error", "correct"));
        });
    });
</script>
</body>
</html>