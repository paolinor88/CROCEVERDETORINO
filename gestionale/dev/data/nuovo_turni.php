<?php
// Connessione al database
$conn = new mysqli("localhost", "urhqx7h4kxv84", "Gestional€", "massi369_gestionale");
if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}

// Caricamento dati storici nella tabella
function caricaStorico($conn, $filename, $anno)
{
    $file = fopen($filename, "r");
    if ($file) {
        while (($line = fgets($file)) !== false) {
            $parts = preg_split('/\s+/', trim($line));
            if (count($parts) < 4) {
                continue; // Salta righe malformate
            }

            $numero_consecutivo = intval($parts[0]);
            $giorno_settimana = $parts[1];
            $data = DateTime::createFromFormat('d/m', $parts[2])->format('Y-m-d');
            $data = $anno . '-' . substr($data, 5); // Aggiungi l'anno
            $squadra = intval($parts[3]);

            $sql = "INSERT INTO turni_storici (anno, giorno_settimana, data, squadra) 
                    VALUES ('$anno', '$giorno_settimana', '$data', '$squadra')";
            $conn->query($sql);
        }
        fclose($file);
    } else {
        echo "Errore nell'apertura del file $filename.";
    }
}

// Caricamento dei turni storici
caricaStorico($conn, "TURNI.15", 2015);
caricaStorico($conn, "TURNI.16", 2016);
caricaStorico($conn, "TURNI.17", 2017);
caricaStorico($conn, "TURNI.18", 2018);
caricaStorico($conn, "TURNI.19", 2019);
caricaStorico($conn, "TURNI.20", 2020);
caricaStorico($conn, "TURNI.21", 2021);
caricaStorico($conn, "TURNI.22", 2022);
caricaStorico($conn, "TURNI.23", 2023);
caricaStorico($conn, "TURNI.24", 2024);

// Funzione per calcolare i turni diurni
function calcolaTurni($conn, $anno, $pasqua)
{
    $festivita = [
        "$anno-01-01", // Capodanno
        "$anno-01-06", // Epifania
        "$anno-04-25", // Liberazione
        "$anno-05-01", // Festa del Lavoro
        "$anno-06-02", // Festa della Repubblica
        "$anno-08-15", // Ferragosto
        "$anno-11-01", // Ognissanti
        "$anno-12-08", // Immacolata
        "$anno-12-25", // Natale
        "$anno-12-26"  // Santo Stefano
    ];

    $festivita[] = $pasqua; // Aggiungi la Pasqua
    $patrono = "$anno-06-24";

    // Caricamento storico per evitare duplicazioni
    $storico = [];
    $result = $conn->query("SELECT data, squadra FROM turni_storici");
    while ($row = $result->fetch_assoc()) {
        $storico[$row['data']] = $row['squadra'];
    }

    // Calcolo delle squadre
    $squadre = range(1, 9);
    $assegnazioni = [];

    foreach ($festivita as $data) {
        if ($data == $patrono) {
            $giorno_settimana = date('w', strtotime($data));
            if (in_array($giorno_settimana, [2, 3, 6])) {
                $assegnazioni[$data] = 0; // Squadra speciale
                continue;
            }
        }

        // Assegna una squadra diversa dallo storico recente
        $squadra_assegnata = null;
        foreach ($squadre as $squadra) {
            if (!in_array($squadra, $storico)) {
                $squadra_assegnata = $squadra;
                break;
            }
        }

        // Selezione casuale se tutte sono già usate
        if (!$squadra_assegnata) {
            $squadra_assegnata = $squadre[array_rand($squadre)];
        }

        $assegnazioni[$data] = $squadra_assegnata;
    }

    // Inserimento nel database
    foreach ($assegnazioni as $data => $squadra) {
        $giorno_settimana = date('l', strtotime($data));
        $sql = "INSERT INTO turni_storici (anno, giorno_settimana, data, squadra) 
                VALUES ('$anno', '$giorno_settimana', '$data', '$squadra')";
        $conn->query($sql);
    }

    echo "Turni calcolati per l'anno $anno:<br>";
    echo "<pre>";
    print_r($assegnazioni);
    echo "</pre>";
}

// Modulo HTML per richiedere anno e Pasqua
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $anno = $_POST['anno'];
    $pasqua = $_POST['pasqua'];

    if ($anno && $pasqua) {
        calcolaTurni($conn, $anno, $pasqua);
    } else {
        echo "Inserisci tutti i dati richiesti.";
    }
} else {
    echo "<form method='POST'>
        <label for='anno'>Anno:</label>
        <input type='number' name='anno' id='anno' required><br>
        <label for='pasqua'>Data di Pasqua (YYYY-MM-DD):</label>
        <input type='date' name='pasqua' id='pasqua' required><br>
        <button type='submit'>Calcola Turni</button>
    </form>";
}

// Chiusura connessione
$conn->close();
?>