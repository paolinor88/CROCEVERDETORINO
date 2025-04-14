<?php
session_start();
include "../config/config.php";

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["success" => false, "error" => "Metodo non consentito"]);
    exit();
}

$id_discente = intval($_POST['id_discente'] ?? 0);
$id_corso = intval($_POST['id_corso'] ?? 0);
$id_edizione = intval($_POST['id_edizione'] ?? 0);

if (!$id_discente || !$id_corso || !$id_edizione) {
    echo json_encode(["success" => false, "error" => "Parametri mancanti"]);
    exit();
}

error_log("Assegnazione discente: ID_DISCENTE=$id_discente, ID_CORSO=$id_corso, ID_EDIZIONE=$id_edizione");

$stmt = $db->prepare("SELECT c.titolo, e.data_inizio FROM edizioni_corso e JOIN corsi c ON e.id_corso = c.id_corso WHERE e.id_edizione = ?");
$stmt->bind_param("i", $id_edizione);
$stmt->execute();
$result = $stmt->get_result();
$corso_data = $result->fetch_assoc();

if (!$corso_data) {
    echo json_encode(["success" => false, "error" => "Edizione non trovata"]);
    exit();
}

$titolo_corso = $corso_data['titolo'];
$data_inizio = date("d/m/Y", strtotime($corso_data['data_inizio']));

$stmt = $db->prepare("SELECT id, email, password_hash FROM discenti WHERE id = ?");
$stmt->bind_param("i", $id_discente);
$stmt->execute();
$result = $stmt->get_result();
$discente = $result->fetch_assoc();
$is_registered = $discente ? true : false;

if (!$is_registered) {
    $stmt = $db->prepare("SELECT Nome, Cognome, CodFiscale, Mail FROM rubrica WHERE IDUtente = ?");
    $stmt->bind_param("i", $id_discente);
    $stmt->execute();
    $result = $stmt->get_result();
    $rubrica_data = $result->fetch_assoc();

    if (!$rubrica_data) {
        echo json_encode(["success" => false, "error" => "Discente non trovato nella rubrica"]);
        exit();
    }

    $password = bin2hex(random_bytes(4)); // 8 caratteri casuali

    $stmt = $db->prepare("INSERT INTO discenti (id, nome, cognome, codice_fiscale, email, password_hash) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssss", $id_discente, $rubrica_data['Nome'], $rubrica_data['Cognome'], $rubrica_data['CodFiscale'], $rubrica_data['Mail'], $password);
    $stmt->execute();

    if ($stmt->affected_rows <= 0) {
        echo json_encode(["success" => false, "error" => "Errore nella creazione del discente"]);
        exit();
    }

    $email = $rubrica_data['Mail'];
} else {
    $password = $discente['password_hash'];
    $email = $discente['email'];
}

$stmt = $db->prepare("SELECT id FROM autorizzazioni_corsi WHERE discente_id = ? AND id_edizione = ?");
$stmt->bind_param("ii", $id_discente, $id_edizione);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo json_encode(["success" => false, "error" => "Il discente è già iscritto a questa edizione"]);
    exit();
}

$stmt = $db->prepare("INSERT INTO autorizzazioni_corsi (discente_id, id_edizione, id_corso) VALUES (?, ?, ?)");
$stmt->bind_param("iii", $id_discente, $id_edizione, $id_corso);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    $stmt = $db->prepare("UPDATE edizioni_corso SET posti_occupati = posti_occupati + 1 WHERE id_edizione = ?");
    $stmt->bind_param("i", $id_edizione);
    $stmt->execute();

    $stmt = $db->prepare("DELETE FROM lista_attesa WHERE id_corso = ? AND id_utente = ?");
    $stmt->bind_param("ii", $id_corso, $id_discente);
    $stmt->execute();

    inviaEmailDiscente($email, $rubrica_data['Nome'] ?? $discente['nome'], $rubrica_data['Cognome'] ?? $discente['cognome'], $rubrica_data['CodFiscale'] ?? '', $password, $titolo_corso, $data_inizio);

    echo json_encode(["success" => true, "message" => "Discente assegnato con successo"]);
} else {
    error_log("Errore MySQL: " . $db->error);
    echo json_encode(["success" => false, "error" => "Errore nell'assegnazione al corso"]);
}

function inviaEmailDiscente($email, $nome, $cognome, $codice_fiscale, $password, $titolo_corso) {
    $to = $email;
    $subject = "Iscrizione al corso: $titolo_corso";
    $boundary = md5(time());

    $headers = "From: Gestionale CVTO <gestioneutenti@croceverde.org>\r\n";
    $headers .= "Bcc: paolo.randone@croceverde.org\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: multipart/alternative; boundary=\"$boundary\"\r\n";

    $emailBody = "--$boundary\r\n";
    $emailBody .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $emailBody .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
    $emailBody .= "Gentile $nome $cognome,\r\n\r\n";
    $emailBody .= "Sei stato iscritto al corso: $titolo_corso.\r\n";
    $emailBody .= "Accedi al portale formazione al seguente link:\r\n";
    $emailBody .= "https://croceverde.org/strumenti/formazione/corsi.php\r\n\r\n";
    $emailBody .= "Credenziali di accesso:\r\n";
    $emailBody .= "- Codice Fiscale: $codice_fiscale\r\n";
    $emailBody .= "- Password: $password\r\n\r\n";
    $emailBody .= "Password generata automaticamente e valida solo per questo corso. Ricorda di conservare questa mail!\r\n";
    $emailBody .= "Grazie,\r\nIl gruppo formazione di Croce Verde Torino\r\n\r\n";

    $emailBody .= "--$boundary\r\n";
    $emailBody .= "Content-Type: text/html; charset=UTF-8\r\n";
    $emailBody .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
    $emailBody .= "<html><body style='font-family: Arial, sans-serif;'>";
    $emailBody .= "<div style='max-width: 600px; margin: auto; border: 1px solid #ddd; padding: 20px; border-radius: 10px;'>";
    $emailBody .= "<div style='text-align: center;'>";
    $emailBody .= "<img src='https://croceverde.org/strumenti/formazione/config/images/logo.png' alt='Croce Verde Torino' style='max-width: 150px;'><br>";
    $emailBody .= "<h2 style='color: #078f40;'>Iscrizione al corso: $titolo_corso</h2>";
    $emailBody .= "</div>";
    $emailBody .= "<p>Gentile <strong>$nome $cognome</strong>,</p>";
    $emailBody .= "<p>Sei stato iscritto con successo al corso <strong>$titolo_corso</strong>.</p>";
    $emailBody .= "<p>Accedi al portale formazione al seguente link:</p>";
    $emailBody .= "<p style='text-align: center;'><a href='https://croceverde.org/strumenti/formazione/corsi.php' style='background-color: #078f40; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px;'>Accedi al Portale</a></p>";
    $emailBody .= "<h3>Le tue credenziali di accesso:</h3>";
    $emailBody .= "<table style='width: 100%; border-collapse: collapse;'>";
    $emailBody .= "<tr><td style='border: 1px solid #ddd; padding: 8px;'><strong>Codice Fiscale:</strong></td><td style='border: 1px solid #ddd; padding: 8px;'>$codice_fiscale</td></tr>";
    $emailBody .= "<tr><td style='border: 1px solid #ddd; padding: 8px;'><strong>Password:</strong></td><td style='border: 1px solid #ddd; padding: 8px;'><strong>$password</strong></td></tr>";
    $emailBody .= "</table>";
    $emailBody .= "<p style='color: red;'><strong>Password generata automaticamente e valida solo per questo corso. Ricorda di conservare questa mail!</strong></p>";
    $emailBody .= "<p>Grazie,<br><strong>Il gruppo formazione di Croce Verde Torino</strong></p>";
    $emailBody .= "</div>";
    $emailBody .= "</body></html>\r\n";

    $emailBody .= "--$boundary--";

    if (!mail($to, $subject, $emailBody, $headers)) {
        error_log("Errore nell'invio della email a: $to", 0);
        die("Errore nell'invio della email. Contatta l'amministratore.");
    }
}
?>