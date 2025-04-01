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

// Funzione per eliminare i turni storici di un anno specifico
function elimina_turni_storici_anno($connect, $anno) {
    $stmt = $connect->prepare("DELETE FROM turni_storici WHERE anno = :anno");
    $stmt->execute(['anno' => $anno]);
}

// Funzione per ottenere lo storico turni per una squadra e una festività specifica
function get_storico_festivita($connect, $squadra, $data) {
    try {
        $stmt = $connect->prepare("SELECT COUNT(*) FROM turni_storici WHERE squadra = :squadra AND data = :data");
        $stmt->execute(['squadra' => $squadra, 'data' => date('Y-m-d', strtotime($data))]);
        return $stmt->fetchColumn();
    } catch (Exception $e) {
        die("Errore durante la lettura dello storico delle festività: " . $e->getMessage());
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

// Funzione per assegnare turno diurno evitando ripetizioni e rispettando i pesi
function assegna_turno_diurno($squadre, $turni_diurni, $turni_notturni, $data, $connect, $is_festivo = false) {
    $squadra_min_turni = null;
    $min_turni = PHP_INT_MAX;

    foreach ($squadre as $squadra) {
        $storico_turni = get_turni_storici($connect, $squadra);
        $num_turni = count_turni_diurni($turni_diurni, $squadra);
        $storico_festivita = get_storico_festivita($connect, $squadra, $data);

// Evita di assegnare la stessa festività alla stessa squadra in anni successivi
        if ($storico_festivita > 0) {
            continue;
        }

// Aggiungi un peso maggiore per le festività nazionali rispetto alle domeniche
        $peso_festivo = $is_festivo ? 2 : 1;
        $turni_ponderati = ($num_turni + $storico_turni) * $peso_festivo;

        if ($turni_ponderati < $min_turni && verifica_riposo_diurno($turni_diurni, $turni_notturni, $squadra, $data)) {
            $squadra_min_turni = $squadra;
            $min_turni = $turni_ponderati;
        }
    }

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

// Gestione speciale per il patrono e festività
            if (in_array($data, festivita_anno()) || date('N', strtotime($data)) == 7) {
                if (date('N', strtotime($data)) == 6) {
                    $turni_diurni[$data] = $squadra_sab;
                    try {
                        $stmt = $connect->prepare("INSERT INTO turni_storici (anno, data, squadra, giorno_settimana) VALUES (:anno, :data, :squadra, :giorno_settimana)");
                        setlocale(LC_TIME, 'it_IT.UTF-8');
                        $giorno_settimana = strtoupper(strftime('%a', strtotime($data)));
                        $stmt->execute(['anno' => $anno, 'data' => date('Y-m-d', strtotime($data)), 'squadra' => $squadra_sab, 'giorno_settimana' => $giorno_settimana]);
                    } catch (Exception $e) {
                        die("Errore durante l'inserimento dei turni SAB: " . $e->getMessage());
                    }
                } elseif ($data == $patronox && in_array(date('N', $patrono), [2, 3, 6])) {
                    $turni_diurni[$data] = $squadra_sab;
                    try {
                        $stmt = $connect->prepare("INSERT INTO turni_storici (anno, data, squadra, giorno_settimana) VALUES (:anno, :data, :squadra, :giorno_settimana)");
                        setlocale(LC_TIME, 'it_IT.UTF-8');
                        $giorno_settimana = strtoupper(strftime('%a', strtotime($data)));
                        $stmt->execute(['anno' => $anno, 'data' => date('Y-m-d', strtotime($data)), 'squadra' => $squadra_sab, 'giorno_settimana' => $giorno_settimana]);
                    } catch (Exception $e) {
                        die("Errore durante l'inserimento dei turni SAB patrono: " . $e->getMessage());
                    }
                } else {
                    $is_festivo = in_array($data, festivita_anno());
                    $turni_diurni = assegna_turno_diurno($squadre, $turni_diurni, $turni_notturni, $data, $connect, $is_festivo);
                }
            }
        }
    } catch (Exception $e) {
        die("Errore durante l'elaborazione: " . $e->getMessage());
    }
}
?>