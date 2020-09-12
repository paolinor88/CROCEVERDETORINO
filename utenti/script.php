<?php
/*
 * File di script
 * Non modificare i parametri
 */
session_start();
//connessione DB
include "../config/pdo.php";
//aggiungi operatore
if(isset($_POST["ID"])){
    $query = "INSERT INTO utenti (ID, cognome, nome, email, cf, password, telefono, ciclico, livello, stato, sezione, squadra) VALUES (:ID, :cognome, :nome, :email, cf, :password, :telefono, :ciclico, :livello, :stato, :sezione, :squadra)";

    $statement = $connect->prepare($query);
    $statement->execute(
        array(
            ':ID'  => $_POST['ID'],
            ':cognome' => $_POST['cognome'],
            ':nome' => $_POST['nome'],
            ':email' => $_POST['email'],
            ':cf' => $_POST['cf'],
            ':password' => $_POST['password'],
            ':telefono' => $_POST['telefono'],
            ':ciclico' => NULL,
            ':livello' => $_POST['livello'],
            ':stato' => 1,
            ':sezione' => $_POST['sezione'],
            ':squadra' => $_POST['squadra'],
        )
    );

    if ($_POST["ID"]){
        $matricola = $_POST['ID'];
        $email = $_POST['email'];
        $nome = $_POST['nome'];
        $cognome = $_POST['cognome'];
        $password = $_POST['password'];
        $to= $email;
        $subject="Attivazione utenza";
        $nome_mittente="Gestionale CVTO";
        $mail_mittente="gestioneutenti@croceverde.org";
        $headers = "From: " .  $nome_mittente . " <" .  $mail_mittente . ">\r\n";
        $headers .= "Bcc: ".$mail_mittente."\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion();
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=iso-8859-1";

        $replace = array(
            '{{id}}',
            '{{password}}',
            '{{cognome}}',
            '{{nome}}',
        );
        $with = array(
            $id,
            $password,
            $cognome,
            $nome,
        );

        $corpo = file_get_contents('../config/template/active.html');
        $corpo = str_replace ($replace, $with, $corpo);

        mail($to, $subject, $corpo, $headers);

    }
}