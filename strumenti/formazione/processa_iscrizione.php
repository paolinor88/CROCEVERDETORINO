<?php
session_start();
include "../config/config.php";
global $db;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["success" => false, "message" => "Metodo non consentito"]);
    exit();
}

$id_edizione = $_POST['id_edizione'] ?? 0;
$codice_fiscale = trim($_POST['codice_fiscale'] ?? '');
$codice_matricola = trim($_POST['codice_matricola'] ?? '');
$tipo_iscrizione = $_POST['tipo_iscrizione'] ?? 'iscrizione';

if (empty($id_edizione) || empty($codice_fiscale) || empty($codice_matricola)) {
    echo json_encode(["success" => false, "message" => "Tutti i campi sono obbligatori"]);
    exit();
}

$stmt = $db->prepare("SELECT IDUtente, Nome, Cognome, Mail FROM rubrica WHERE CodFiscale = ? AND Codice = ?");
$stmt->bind_param("ss", $codice_fiscale, $codice_matricola);
$stmt->execute();
$result = $stmt->get_result();
$rubrica_data = $result->fetch_assoc();

if (!$rubrica_data) {
    echo json_encode(["success" => false, "message" => "Codice fiscale e matricola non corrispondono"]);
    exit();
}

$id_discente = $rubrica_data['IDUtente'];
$email = $rubrica_data['Mail'];
$nome = $rubrica_data['Nome'];
$cognome = $rubrica_data['Cognome'];

$stmt = $db->prepare("SELECT id_corso, DATE_FORMAT(data_inizio, '%d/%m/%Y') AS data_inizio FROM edizioni_corso WHERE id_edizione = ?");
$stmt->bind_param("i", $id_edizione);
$stmt->execute();
$result = $stmt->get_result();
$corso_data = $result->fetch_assoc();

$id_corso = $corso_data['id_corso'];
$data_inizio = $corso_data['data_inizio'];

if ($tipo_iscrizione === 'lista_attesa') {

    $stmt = $db->prepare("
        SELECT ac.id
        FROM autorizzazioni_corsi ac
        JOIN edizioni_corso ec ON ac.id_edizione = ec.id_edizione
        WHERE ac.discente_id = ? AND ec.id_corso = ?
    ");
    $stmt->bind_param("ii", $id_discente, $id_corso);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo json_encode(["success" => false, "message" => "Hai già un'iscrizione attiva per questo corso"]);
        exit();
    }

    $stmt = $db->prepare("SELECT id FROM lista_attesa WHERE id_corso = ? AND id_utente = ?");
    $stmt->bind_param("ii", $id_corso, $id_discente);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo json_encode(["success" => false, "message" => "Sei già in lista d'attesa per questo corso"]);
        exit();
    }

    $stmt = $db->prepare("INSERT INTO lista_attesa (id_corso, id_utente) VALUES (?, ?)");
    if (!$stmt) {
        echo json_encode(["success" => false, "message" => "Errore nella preparazione della query per la lista d’attesa"]);
        exit();
    }

    $stmt->bind_param("ii", $id_corso, $id_discente);
    if (!$stmt->execute()) {
        echo json_encode(["success" => false, "message" => "Errore durante l'inserimento nella lista d’attesa"]);
        exit();
    }

    echo json_encode(["success" => true, "message" => "Ti sei aggiunto alla lista d’attesa. Sarai contattato in caso di disponibilità di posti, anche su edizioni diverse da quella desiderata."]);
    exit();
}

if (!$corso_data) {
    echo json_encode(["success" => false, "message" => "Edizione non trovata"]);
    exit();
}

$stmt = $db->prepare("SELECT id FROM autorizzazioni_corsi WHERE discente_id = ? AND id_edizione = ?");
$stmt->bind_param("ii", $id_discente, $id_edizione);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo json_encode(["success" => false, "message" => "Sei già iscritto a questa edizione"]);
    exit();
}

$stmt = $db->prepare("SELECT id FROM lista_attesa WHERE id_corso = ? AND id_utente = ?");
$stmt->bind_param("ii", $id_corso, $id_discente);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo json_encode(["success" => false, "message" => "Non puoi iscriverti: sei già in lista d’attesa per questo corso"]);
    exit();
}

$stmt = $db->prepare("
    SELECT ac.id 
    FROM autorizzazioni_corsi ac
    JOIN edizioni_corso ec ON ac.id_edizione = ec.id_edizione
    WHERE ac.discente_id = ? AND ec.id_corso = ? AND ac.id_edizione <> ?
");
$stmt->bind_param("iii", $id_discente, $id_corso, $id_edizione);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo json_encode(["success" => false, "message" => "Sei già iscritto a un'altra edizione di questo corso"]);
    exit();
}

$stmt = $db->prepare("SELECT id, password_hash FROM discenti WHERE id = ?");
$stmt->bind_param("i", $id_discente);
$stmt->execute();
$result = $stmt->get_result();
$discente_data = $result->fetch_assoc();
$is_registered = !empty($discente_data);

if (!$is_registered) {
    $password = bin2hex(random_bytes(4));
    $stmt = $db->prepare("INSERT INTO discenti (id, nome, cognome, codice_fiscale, email, password_hash) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssss", $id_discente, $nome, $cognome, $codice_fiscale, $email, $password);
    $stmt->execute();

    if ($stmt->affected_rows <= 0) {
        echo json_encode(["success" => false, "message" => "Errore durante la creazione dell'account"]);
        exit();
    }
} else {
    $password = $discente_data['password_hash']; // Usa la password già esistente
}

$stmt = $db->prepare("INSERT INTO autorizzazioni_corsi (discente_id, id_edizione, id_corso) VALUES (?, ?, ?)");
if (!$stmt) {
    die("Errore SQL: " . $db->error);
}
$stmt->bind_param("iii", $id_discente, $id_edizione, $id_corso);
$stmt->execute();

if ($stmt->affected_rows <= 0) {
    echo json_encode(["success" => false, "message" => "Errore durante l'iscrizione alla edizione"]);
    exit();
}

$stmt = $db->prepare("UPDATE edizioni_corso SET posti_occupati = posti_occupati + 1 WHERE id_edizione = ?");
$stmt->bind_param("i", $id_edizione);
$stmt->execute();

if ($stmt->affected_rows <= 0) {
    echo json_encode(["success" => false, "message" => "Errore nell'aggiornamento dei posti disponibili"]);
    exit();
}

inviaEmailDiscente($email, $nome, $cognome, $codice_fiscale, $password, $id_edizione, $id_corso);

echo json_encode(["success" => true]);

function inviaEmailDiscente($email, $nome, $cognome, $codice_fiscale, $password, $id_edizione, $id_corso)
{
    global $db;

    $stmt = $db->prepare("
        SELECT c.titolo, e.data_inizio 
        FROM edizioni_corso e 
        JOIN corsi c ON e.id_corso = c.id_corso 
        WHERE e.id_edizione = ?
    ");

    if (!$stmt) {
        error_log("Errore SQL nella preparazione della query: " . $db->error);
        return false;
    }

    $stmt->bind_param("i", $id_edizione);
    if (!$stmt->execute()) {
        error_log("Errore SQL durante l'esecuzione: " . $stmt->error);
        return false;
    }

    $result = $stmt->get_result();
    if (!$result || $result->num_rows === 0) {
        error_log("Errore: Nessun risultato trovato per ID Edizione: $id_edizione");
        return false;
    }

    $corso = $result->fetch_assoc();
    $titolo_corso = $corso['titolo'] ?? "Corso Sconosciuto";
    $data_inizio = isset($corso['data_inizio']) ? date("d/m/Y", strtotime($corso['data_inizio'])) : "Data non disponibile";
    $redirectUrl = "https://croceverde.org/strumenti/formazione/login.php?redirect=" . urlencode("lezioni.php?id_corso=$id_corso");

    $to = $email;
    $subject = "Iscrizione $titolo_corso - Edizione $data_inizio";
    $boundary = md5(time());

    $headers = "From: Gestionale CVTO <gestioneutenti@croceverde.org>\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: multipart/alternative; boundary=\"$boundary\"\r\n";

    $emailBody = "--$boundary\r\n";
    $emailBody .= "Content-Type: text/plain; charset=UTF-8\r\n\r\n";
    $emailBody .= "Gentile $nome $cognome,\r\n\r\n";
    $emailBody .= "La tua iscrizione al corso \"$titolo_corso\" con inizio il $data_inizio è andata a buon fine.\r\n";
    $emailBody .= "Accedi al portale formazione al seguente link:\r\n";
    $emailBody .= "https://croceverde.org/strumenti/formazione/corsi.php\r\n\r\n";
    $emailBody .= "Credenziali di accesso:\r\n";
    $emailBody .= "- Codice Fiscale: $codice_fiscale\r\n";
    $emailBody .= "- Password: $password\r\n\r\n";
    $emailBody .= "Password generata automaticamente e valida solo per questo corso. Ricorda di conservarla!\r\n\r\n";
    $emailBody .= "Grazie,\r\nIl gruppo formazione di Croce Verde Torino\r\n\r\n";

    $emailBody .= "--$boundary\r\n";
    $emailBody .= "Content-Type: text/html; charset=UTF-8\r\n\r\n";
    $emailBody .= "<html><body style='font-family: Arial, sans-serif;'>";
    $emailBody .= "<div style='max-width: 600px; margin: auto; border: 1px solid #ddd; padding: 20px; border-radius: 10px;'>";
    $emailBody .= "<div style='text-align: center;'>";
    $emailBody .= "<img src='https://croceverde.org/strumenti/formazione/config/images/logo.png' alt='Croce Verde Torino' style='max-width: 150px;'><br>";
    $emailBody .= "<h2 style='color: #078f40;'>$titolo_corso</h2>";
    $emailBody .= "<p><strong>Edizione del $data_inizio</strong></p>";
    $emailBody .= "</div>";
    $emailBody .= "<p>Gentile <strong>$nome $cognome</strong>,</p>";
    $emailBody .= "<p>La tua iscrizione è andata a buon fine e puoi ora accedere al portale per seguire le lezioni</p>";
    $emailBody .= "<p style='text-align: center;'><a href='$redirectUrl' style='background-color: #078f40; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px;'>Accedi al corso</a></p>";
    //$emailBody .= "<p style='text-align: center;'><a href='https://croceverde.org/strumenti/formazione/corsi.php' style='background-color: #078f40; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px;'>Accedi</a></p>";
    $emailBody .= "<h3>Le tue credenziali di accesso:</h3>";
    $emailBody .= "<table style='width: 100%; border-collapse: collapse;'>";
    $emailBody .= "<tr><td style='border: 1px solid #ddd; padding: 8px;'><strong>Codice Fiscale:</strong></td><td style='border: 1px solid #ddd; padding: 8px;'>$codice_fiscale</td></tr>";
    $emailBody .= "<tr><td style='border: 1px solid #ddd; padding: 8px;'><strong>Password:</strong></td><td style='border: 1px solid #ddd; padding: 8px;'><strong>$password</strong></td></tr>";
    $emailBody .= "</table>";
    $emailBody .= "<p style='color: red;'><strong>Password generata automaticamente e valida solo per questo corso. Ricorda di conservarla!</strong></p>";
    $emailBody .= "<p>Grazie,<br><strong>Il gruppo formazione di Croce Verde Torino</strong></p>";
    $emailBody .= "</div>";
    $emailBody .= "</body></html>\r\n";

    $emailBody .= "--$boundary--";

    if (!mail($to, $subject, $emailBody, $headers)) {
        error_log("Errore nell'invio della email a: $to");
        return false;
    }
    return true;
}

?>