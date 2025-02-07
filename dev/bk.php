<?php

// Connessione al database
include "../config/pdo.php";
$connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$anno = null;
$squadrauno = null;
if (isset($_POST["submit"])) {
    $anno = $_POST["anno"];
    $squadrauno = $_POST["squadrauno"];

    try {
        elimina_turni_storici_anno($connect, $anno);
    } catch (Exception $e) {
        die("Errore durante l'eliminazione dei turni storici: " . $e->getMessage());
    }
}

// Definisci le squadre
$squadre = array(1, 2, 3, 4, 5, 6, 7, 8, 9);
$squadra_sab = 0; // Squadra speciale per i sabati
$squadra_fittizia = 99; // Squadra fittizia per vincoli troppo stringenti

// Funzione per eliminare i turni storici di un anno specifico
function elimina_turni_storici_anno($connect, $anno) {
    $stmt = $connect->prepare("DELETE FROM turni_storici WHERE anno = :anno");
    $stmt->execute(['anno' => $anno]);
}

// Funzione per ottenere lo storico di una festività aggregata (es. Pasqua e Pasquetta)
function get_storico_festivita_aggregata($connect, $squadra, $date_group) {
    try {
        $placeholders = implode(",", array_fill(0, count($date_group), "?"));
        $query = "SELECT COUNT(*) FROM turni_storici WHERE squadra = ? AND data IN ($placeholders)";
        $stmt = $connect->prepare($query);
        $params = array_merge([$squadra], array_map(fn($date) => date('Y-m-d', strtotime($date)), $date_group));
        $stmt->execute($params);
        return $stmt->fetchColumn();
    } catch (Exception $e) {
        die("Errore durante la lettura dello storico delle festività aggregate: " . $e->getMessage());
    }
}

// Funzione per ottenere lo storico turni totali per una squadra
function get_turni_storici($connect, $squadra) {
    try {
        $stmt = $connect->prepare("SELECT COUNT(*) FROM turni_storici WHERE squadra = :squadra");
        $stmt->execute(['squadra' => $squadra]);
        return $stmt->fetchColumn();
    } catch (Exception $e) {
        die("Errore durante la lettura dello storico turni: " . $e->getMessage());
    }
}

// Funzione per verificare la distanza minima tra turni diurni di 30 giorni
function verifica_distanza_turni_diurni($turni_diurni, $squadra, $data_diurno) {
    $data_diurno_timestamp = strtotime($data_diurno);

    foreach ($turni_diurni as $data => $num_squadra) {
        if ($num_squadra == $squadra) {
            $data_turno_timestamp = strtotime($data);
            $distanza = abs($data_diurno_timestamp - $data_turno_timestamp) / 86400; // Differenza in giorni
            if ($distanza <= 30) {
                return false; // Turno troppo vicino
            }
        }
    }

    return true; // Turno assegnabile
}

// Funzione per assegnare turno diurno con tutte le regole
function assegna_turno_diurno($squadre, $turni_diurni, $turni_notturni, $data_group, $connect, $is_festivo = false) {
    global $squadra_fittizia;
    $squadra_min_turni = null;
    $min_turni = PHP_INT_MAX;
    $distanza_annuale = 4;

    foreach ($squadre as $squadra) {
        $storico_turni = get_turni_storici($connect, $squadra);
        $num_turni = count_turni_diurni($turni_diurni, $squadra);
        $storico_festivita_aggregata = get_storico_festivita_aggregata($connect, $squadra, $data_group);

        // Evita di assegnare la stessa festività aggregata alla stessa squadra entro 4 anni
        if ($storico_festivita_aggregata > 0 && $distanza_annuale > 3) {
            $distanza_annuale = 3; // Abbassa il vincolo se necessario
            continue;
        }

        // Verifica la distanza di 30 giorni tra turni diurni
        if (!verifica_distanza_turni_diurni($turni_diurni, $squadra, $data_group[0])) {
            continue;
        }

        // Aggiungi un peso maggiore per le festività nazionali rispetto alle domeniche
        $peso_festivo = $is_festivo ? 2 : 1;
        $turni_ponderati = ($num_turni + $storico_turni) * $peso_festivo;

        if ($turni_ponderati < $min_turni && verifica_riposo_diurno($turni_diurni, $turni_notturni, $squadra, $data_group[0])) {
            $squadra_min_turni = $squadra;
            $min_turni = $turni_ponderati;
        }
    }

    // Se nessuna squadra è assegnabile, assegna alla squadra fittizia
    if ($squadra_min_turni === null) {
        $squadra_min_turni = $squadra_fittizia;
    }

    foreach ($data_group as $data) {
        $turni_diurni[$data] = $squadra_min_turni;

        try {
            // Salva il turno assegnato nel database
            $stmt = $connect->prepare("INSERT INTO turni_storici (anno, data, squadra, giorno_settimana) VALUES (:anno, :data, :squadra, :giorno_settimana)");
            setlocale(LC_TIME, 'it_IT.UTF-8');
            $giorno_settimana = strtoupper(strftime('%a', strtotime($data)));
            $stmt->execute(['anno' => $_POST["anno"], 'data' => date('Y-m-d', strtotime($data)), 'squadra' => $squadra_min_turni, 'giorno_settimana' => $giorno_settimana]);
        } catch (Exception $e) {
            die("Errore durante l'inserimento dei turni: " . $e->getMessage());
        }
    }

    return $turni_diurni;
}

// Funzione per restituire tutte le festività di un anno
function festivita_anno() {
    if (!isset($_POST["anno"])) {
        return [];
    }

    $anno = $_POST["anno"];
    $pasqua = easter_date($anno);
    $pasquetta = strtotime("+1 day", $pasqua);

    $festivita = array(
        '01-01-' . $anno, '06-01-' . $anno, date('d-m-Y', $pasquetta),
        '25-04-' . $anno, '01-05-' . $anno, '02-06-' . $anno,
        '24-06-' . $anno, '15-08-' . $anno, '01-11-' . $anno,
        '08-12-' . $anno, '25-12-' . $anno, '26-12-' . $anno
    );

    return array_values($festivita);
}

// Funzione per contare i turni diurni assegnati a una squadra
function count_turni_diurni($turni_diurni, $squadra) {
    $count = 0;
    foreach ($turni_diurni as $data => $num_squadra) {
        if ($num_squadra == $squadra) {
            $count++;
        }
    }
    return $count;
}

// Verifica se una squadra ha il riposo richiesto e non è assegnata a un turno notturno nei giorni vicini
function verifica_riposo_diurno($turni_diurni, $turni_notturni, $squadra, $data_diurno) {
    $riposo_minimo = strtotime('-2 days', strtotime($data_diurno));
    $riposo_massimo = strtotime('+2 days', strtotime($data_diurno));

    foreach ($turni_notturni as $data => $num_squadra) {
        if ($num_squadra == $squadra && strtotime($data) >= $riposo_minimo && strtotime($data) <= $riposo_massimo) {
            return false;
        }
    }

    foreach ($turni_diurni as $data => $num_squadra) {
        if ($num_squadra == $squadra && strtotime($data) >= $riposo_minimo && strtotime($data) <= $riposo_massimo && $data != $data_diurno) {
            return false;
        }
    }

    return true;
}

if ($anno) {
    try {
        // Crea un array di tutti i giorni dell'anno
        $giorni_anno = range(strtotime($anno . '-01-01'), strtotime($anno . '-12-31'), 86400);

        // Inizializza gli array dei turni notturni e festivi
        $turni_notturni = array();
        $turni_diurni = array();

        // ciclo turni notturni
        $numero_squadra = $squadrauno;
        foreach ($giorni_anno as $giorno) {
            $data = date('d-m-Y', $giorno);
            $turni_notturni[$data] = $numero_squadra;
            $numero_squadra++;
            if ($numero_squadra > 9) {
                $numero_squadra = 1;
            }
        }

        // Assegna i turni festivi e notturni tenendo conto dello storico e delle regole
        foreach ($giorni_anno as $giorno) {
            $data = date('d-m-Y', $giorno);
            $patrono = strtotime('24-06-' . $anno);
            $patronox = date('d-m-Y', $patrono);

            if (in_array($data, festivita_anno())) {
                $is_festivo = true;

                // Festività aggregate (es. Natale e Santo Stefano)
                if ($data == '25-12-' . $anno || $data == '26-12-' . $anno) {
                    $data_group = ['25-12-' . $anno, '26-12-' . $anno];
                    $turni_diurni = assegna_turno_diurno($squadre, $turni_diurni, $turni_notturni, $data_group, $connect, $is_festivo);
                } elseif ($data == date('d-m-Y', easter_date($anno)) || $data == date('d-m-Y', strtotime('+1 day', easter_date($anno)))) {
                    $data_group = [date('d-m-Y', easter_date($anno)), date('d-m-Y', strtotime('+1 day', easter_date($anno)))];
                    $turni_diurni = assegna_turno_diurno($squadre, $turni_diurni, $turni_notturni, $data_group, $connect, $is_festivo);
                } else {
                    $turni_diurni = assegna_turno_diurno($squadre, $turni_diurni, $turni_notturni, [$data], $connect, $is_festivo);
                }
            } elseif (date('N', strtotime($data)) == 7) { // Domeniche normali
                $turni_diurni = assegna_turno_diurno($squadre, $turni_diurni, $turni_notturni, [$data], $connect, false);
            } elseif ($data == $patronox && in_array(date('N', $patrono), [2, 3, 6])) { // Patrono
                $turni_diurni[$data] = $squadra_sab;
                try {
                    $stmt = $connect->prepare("INSERT INTO turni_storici (anno, data, squadra, giorno_settimana) VALUES (:anno, :data, :squadra, :giorno_settimana)");
                    setlocale(LC_TIME, 'it_IT.UTF-8');
                    $giorno_settimana = strtoupper(strftime('%a', strtotime($data)));
                    $stmt->execute(['anno' => $anno, 'data' => date('Y-m-d', strtotime($data)), 'squadra' => $squadra_sab, 'giorno_settimana' => $giorno_settimana]);
                } catch (Exception $e) {
                    die("Errore durante l'inserimento dei turni del patrono: " . $e->getMessage());
                }
            }
        }
    } catch (Exception $e) {
        die("Errore durante l'elaborazione: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <meta name="author" content="Paolo Randone">
    <title>Guardie</title>
    <style>
        table, th, td { border: 1px solid black; }
        table.center { margin-left: auto; margin-right: auto; }
        td.festivi { background-color: #ffc107; }
    </style>
</head>
<body style="font-family: Arial,serif">
<form action="creaguardie.php" method="post">
    <table class="center">
        <tr><th>ANNO</th><th><input type="text" name="anno" required></th></tr>
        <tr><th>SQ. INIZIALE</th><th><input type="text" name="squadrauno" required></th></tr>
    </table>
    <br>
    <div style="text-align: center"><input type="submit" name="submit"></div>
</form>
<hr>
<?php
if (isset($anno)) {
    setlocale(LC_TIME, 'it_IT.utf8');
    echo '<h3>ANNO ' . $anno . '</h3>';
    echo '<p><b>Totale turni festivi per squadra:</b></p>';
    echo '<ul>';
    foreach ($squadre as $squadra) {
        echo '<li>SQ. ' . $squadra . ': ' . count_turni_diurni($turni_diurni, $squadra) . '</li>';
    }
    echo '</ul>';
    echo '<p><b>San Giovanni Patrono: ' . strftime("%A", strtotime($patronox)) . ' ' . $patronox . ' (SQ. ' . $turni_diurni[$patronox] . ')</b></p>';
    echo '<table><thead><tr><th>Data</th><th>Notturno</th><th>Festivo</th></tr></thead><tbody>';
    foreach ($giorni_anno as $giorno) {
        $data = date('d-m-Y', $giorno);
        echo '<tr><td>' . $data . ' - ' . strftime("%a", strtotime($data)) . '</td>';
        echo '<td style="text-align: center">SQ. ' . $turni_notturni[$data] . '</td>';
        if (date('N', $giorno) == 7 || in_array($data, festivita_anno())) {
            echo '<td style="text-align: center" class="festivi">SQ. ' . $turni_diurni[$data] . '</td>';
        } else {
            echo '<td></td>';
        }
        echo '</tr>';
    }
    echo '</tbody></table>';
}
?>
</body>
</html>
