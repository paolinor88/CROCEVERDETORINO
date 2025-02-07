<?php
include "../config/pdo.php";
$connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

function isFestivo($data, $anno) {
    $festivi = [
        "$anno-01-01", "$anno-01-06", "$anno-04-25", "$anno-05-01", "$anno-06-02", "$anno-08-15",
        "$anno-11-01", "$anno-12-08", "$anno-12-25", "$anno-12-26"
    ];
    $pasqua = date('Y-m-d', easter_date($anno));
    $festivi[] = $pasqua;
    $festivi[] = date('Y-m-d', strtotime("+1 day", strtotime($pasqua))); // Pasquetta

    return in_array($data, $festivi) || date('w', strtotime($data)) == 0; // Domeniche
}

function getStorico($connect, $anno, $squadra) {
    $query = "SELECT * FROM turni_storici_bk WHERE squadra = :squadra AND YEAR(data) >= :startYear";
    try {
        $stmt = $connect->prepare($query);
        $stmt->execute([':squadra' => $squadra, ':startYear' => $anno - 2]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Errore nella query SQL: " . $e->getMessage());
    }
}

function generaTurni($anno, $squadraInizio, $connect) {
    $results = [];
    $patrono = "$anno-06-24";
    $squadraNotte = $squadraInizio;
    $storicoFestivi = [];
    for ($i = 1; $i <= 9; $i++) {
        $storicoFestivi[$i] = getStorico($connect, $anno, $i);
    }

    // Copertura giorni
    $dateStart = "$anno-01-01";
    $dateEnd = "$anno-12-31";
    $currentDate = $dateStart;
    while (strtotime($currentDate) <= strtotime($dateEnd)) {
        $isFestivo = isFestivo($currentDate, $anno);
        $isSabato = date('w', strtotime($currentDate)) == 6;

        if ($isFestivo) {
            if ($currentDate == $patrono && ($isSabato || date('w', strtotime($patrono)) == 2 || date('w', strtotime($patrono)) == 3)) {
                $results[] = ['data' => $currentDate, 'squadra' => 10, 'tipo_turno' => 'Patrono'];
            }
            if (!$isSabato) {
                $assigned = false;
                foreach (range(1, 9) as $squadra) {
                    $storico = $storicoFestivi[$squadra];
                    $assegnatoRecentemente = false;
                    foreach ($storico as $record) {
                        if (abs(strtotime($record['data']) - strtotime($currentDate)) < 2 * 86400) {
                            $assegnatoRecentemente = true;
                            break;
                        }
                    }
                    if (!$assegnatoRecentemente) {
                        $results[] = ['data' => $currentDate, 'squadra' => $squadra, 'tipo_turno' => 'Diurno Festivo'];
                        $storicoFestivi[$squadra][] = ['data' => $currentDate, 'squadra' => $squadra];
                        $assigned = true;
                        break;
                    }
                }

                if (!$assigned) {
                    $results[] = ['data' => $currentDate, 'squadra' => array_rand(range(1, 9)), 'tipo_turno' => 'Diurno Festivo'];
                }
            }
        }

        $results[] = ['data' => $currentDate, 'squadra' => $squadraNotte, 'tipo_turno' => 'Notturno'];
        $squadraNotte = $squadraNotte % 9 + 1;

        $currentDate = date('Y-m-d', strtotime('+1 day', strtotime($currentDate)));
    }

    return $results;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $anno = $_POST['anno'];
    $squadraInizio = $_POST['squadraInizio'];

    $turni = generaTurni($anno, $squadraInizio, $connect);

    foreach ($turni as $turno) {
        $query = "INSERT INTO turni_storici_bk (data, squadra, tipo_turno) VALUES (:data, :squadra, :tipo_turno)";
        try {
            $stmt = $connect->prepare($query);
            $stmt->execute([':data' => $turno['data'], ':squadra' => $turno['squadra'], ':tipo_turno' => $turno['tipo_turno']]);
        } catch (PDOException $e) {
            die("Errore nell'inserimento dei dati: " . $e->getMessage());
        }
    }

    echo "<table border='1'>";
    echo "<tr><th>Data</th><th>Squadra</th><th>Tipo Turno</th></tr>";
    foreach ($turni as $turno) {
        echo "<tr><td>{$turno['data']}</td><td>{$turno['squadra']}</td><td>{$turno['tipo_turno']}</td></tr>";
    }
    echo "</table>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Genera Turni</title>
</head>
<body>
<form method="POST">
    <label for="anno">Anno:</label>
    <input type="number" name="anno" required>
    <label for="squadraInizio">Squadra Inizio:</label>
    <input type="number" name="squadraInizio" min="1" max="9" required>
    <button type="submit">Genera Turni</button>
</form>
</body>
</html>
