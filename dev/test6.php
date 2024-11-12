<?php
// Connessione al database
include "../config/pdo.php";
$connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if (isset($_POST["submit"])) {
    $anno = $_POST["anno"];
    $squadrauno = $_POST["squadrauno"];
    elimina_turni_storici_anno($connect, $anno);
}

// Definisci le squadre
$squadre = array(1, 2, 3, 4, 5, 6, 7, 8, 9);


// Funzione per eliminare i turni storici di un anno specifico
function elimina_turni_storici_anno($connect, $anno) {
    $stmt = $connect->prepare("DELETE FROM turni_storici WHERE anno = :anno");
    $stmt->execute(['anno' => $anno]);
}

// Funzione per ottenere lo storico turni per una squadra e un tipo di turno
function get_turni_storici($connect, $squadra, $tipo_turno) {
    $stmt = $connect->prepare("SELECT COUNT(*) FROM turni_storici WHERE squadra = :squadra AND tipo_turno = :tipo_turno");
    $stmt->execute(['squadra' => $squadra, 'tipo_turno' => $tipo_turno]);
    return $stmt->fetchColumn();
}

// Funzione per assegnare turno diurno senza sovrapposizione di turni notturni e rispettando lo storico
function assegna_turno_diurno($squadre, $turni_diurni, $turni_notturni, $data, $connect) {
    $squadra_min_turni = null;
    $min_turni = PHP_INT_MAX;

    foreach ($squadre as $squadra) {
        $storico_turni_festivi = get_turni_storici($connect, $squadra, 'festivo');
        $num_turni = count_turni_diurni($turni_diurni, $squadra);

        if (($num_turni + $storico_turni_festivi) < $min_turni &&
            verifica_riposo_diurno($turni_diurni, $turni_notturni, $squadra, $data)) {
            $squadra_min_turni = $squadra;
            $min_turni = $num_turni + $storico_turni_festivi;
        }
    }

    $turni_diurni[$data] = $squadra_min_turni;

    // Salva il turno assegnato nel database
    $stmt = $connect->prepare("INSERT INTO turni_storici (anno, data, squadra, tipo_turno) VALUES (:anno, :data, :squadra, 'festivo')");
    $stmt->execute(['anno' => $_POST["anno"], 'data' => date('Y-m-d', strtotime($data)), 'squadra' => $squadra_min_turni]);

    return $turni_diurni;
}

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

// Assegna i turni festivi e notturni tenendo conto dello storico e della sovrapposizione
foreach ($giorni_anno as $giorno) {
    $data = date('d-m-Y', $giorno);
    $patrono = strtotime('24-06-'.$anno);
    $patronox = date('d-m-Y', $patrono);

    if (date('N', $giorno) == 7 || in_array($data, festivita_anno())) {
        $turni_diurni = assegna_turno_diurno($squadre, $turni_diurni, $turni_notturni, $data, $connect);
    }
    // Gestione speciale per il patrono
    if (date('N', $patrono) == 2 && in_array($data, festivita_anno())) {
        $turni_diurni[$patronox] = "SAB";
    } elseif (date('N', $patrono) == 3 && in_array($data, festivita_anno())) {
        $turni_diurni[$patronox] = "SAB";
    } elseif (date('N', $giorno) == 6 && in_array($data, festivita_anno())) {
        $turni_diurni[$data] = "SAB";
    }
}

// Funzione per restituire tutte le festività di un anno
function festivita_anno() {
    $anno = $_POST["anno"];
    $pasqua = easter_date($anno);
    $pasquetta = strtotime("+1 day", $pasqua);

    $festivita = array(
        '01-01-'.$anno, '06-01-'.$anno, date('d-m-Y', $pasquetta),
        '25-04-'.$anno, '01-05-'.$anno, '02-06-'.$anno,
        '24-06-'.$anno, '15-08-'.$anno, '01-11-'.$anno,
        '08-12-'.$anno, '25-12-'.$anno, '26-12-'.$anno,
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
