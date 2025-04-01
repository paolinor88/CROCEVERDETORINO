<?php
$domanda_id = $domanda_modifica['id'] ?? '';
$domanda_testo = $domanda_modifica['domanda'] ?? '';
$risposte = [
    1 => $domanda_modifica['risposta1'] ?? '',
    2 => $domanda_modifica['risposta2'] ?? '',
    3 => $domanda_modifica['risposta3'] ?? '',
    4 => $domanda_modifica['risposta4'] ?? ''
];
$risposta_corretta = $domanda_modifica['risposta_corretta'] ?? '';
?>

<div class="card mt-4">
    <div class="card-header">
        <h4><?php echo $domanda_id ? "Modifica Domanda" : "Aggiungi Domanda"; ?></h4>
    </div>
    <div class="card-body">
        <form method="post" action="salva_domande.php">
            <input type="hidden" name="id_corso" value="<?php echo $id_corso; ?>">
            <input type="hidden" name="id_lezione" value="<?php echo $id_lezione; ?>">
            <?php if ($domanda_id): ?>
                <input type="hidden" name="id" value="<?php echo $domanda_id; ?>">
            <?php endif; ?>

            <div class="mb-3">
                <label for="domanda" class="form-label">Domanda:</label>
                <input type="text" name="domanda" id="domanda" class="form-control" value="<?php echo htmlspecialchars($domanda_testo); ?>" required>
            </div>

            <label class="form-label">Risposte:</label>
            <?php for ($i = 1; $i <= 4; $i++): ?>
                <div class="mb-2">
                    <input type="text" name="risposta<?php echo $i; ?>" class="form-control" value="<?php echo htmlspecialchars($risposte[$i]); ?>" required>
                </div>
            <?php endfor; ?>

            <div class="mb-3">
                <label for="risposta_corretta" class="form-label">Risposta Corretta:</label>
                <select name="risposta_corretta" id="risposta_corretta" class="form-control" required>
                    <?php for ($i = 1; $i <= 4; $i++): ?>
                        <option value="<?php echo $i; ?>" <?php echo ($risposta_corretta == $i) ? 'selected' : ''; ?>>
                            Risposta <?php echo $i; ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>

            <div class="d-flex justify-content-between">
                <button type="submit" class="btn btn-success"><?php echo $domanda_id ? "Modifica" : "Salva"; ?> Domanda</button>
                <a href="gestione_domande.php?id_corso=<?php echo $id_corso; ?>&id_lezione=<?php echo $id_lezione; ?>" class="btn btn-secondary">Annulla</a>
            </div>
        </form>
    </div>
</div>