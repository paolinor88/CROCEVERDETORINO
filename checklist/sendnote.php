<?php
/**
 *
 * @author     Paolo Randone
 * @author     <paolo.randone@croceverde.org>
* @version    7.4
 * @note       Powered for Croce Verde Torino. All rights reserved
 *
 */
session_start();
//parametri DB
include "../config/pdo.php";
include "../config/include/destinatari.php";
//set session var
$cognome = $_SESSION["cognome"];
$nome = $_SESSION["nome"];
$email = $_SESSION["email"];
$sezione= $_SESSION["sezione"];
$squadra= $_SESSION["squadra"];
//
$dictionary = array (
    1 => "MSB",
    2 => "MSA",
    3 => "FLOTTA 118",
);
//nicename sezioni
$dictionarySezione = array (
    1 => "TO",
    2 => "AL",
    3 => "BC",
    4 => "CI",
    5 => "SM",
    6 => "VE",
    7 => "DIP",
    8 => "SCN",
);
//nicename squadre
$dictionarySquadra = array (
    1 => "1",
    2 => "2",
    3 => "3",
    4 => "4",
    5 => "5",
    6 => "6",
    7 => "7",
    8 => "8",
    9 => "9",
    10 => "SAB",
    11 => "MON",
    12 => "DDS",
    13 => "Lunedì",
    14 => "Martedì",
    15 => "Mercoledì",
    16 => "Giovedì",
    17 => "Venerdì",
    18 => "DIU",
    19 => "GIO",
    20 => "GEN",
    21 => "Altro",
    22 => "TO",
    23 => "TO",
);
if(isset($_POST["solonote"])){

    $numeroauto = $_POST["IDMEZZO"];
    $segnalazione = $_POST["solonote"];
    $tipo = $_POST["tipo"];
    $datacheck = $_POST["DATACHECK"];$var1=date_create("$var", timezone_open("Europe/Rome"));$datatesto=date_format($var1, "d/m/Y H:i");
    $compilatore = $_POST["IDOPERATORE"];

    $query = "INSERT INTO checklist (IDMEZZO, IDOPERATORE, DATACHECK, NOTE) VALUES (:IDMEZZO, :IDOPERATORE, :DATACHECK, :NOTE)";

    $statement = $connect->prepare($query);
    $statement->execute(
        array(
            ':IDMEZZO'  => $_POST['IDMEZZO'],
            ':IDOPERATORE'  => $_POST['IDOPERATORE'],
            ':DATACHECK'  => $_POST['DATACHECK'],
            ':NOTE'  => $_POST['solonote'],
        )
    );
    $to= $comunicazioni;//.', '.$bechis;
    $nome_mittente="Checklist CVTO";
    $mail_mittente=$checklist;
    $headers = "From: " .  $nome_mittente . " <" .  $mail_mittente . ">\r\n";
    $headers .= "Bcc: ".$email."\r\n";
    $headers .= "Reply-To: " .  $email . "\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=iso-8859-1";
    $oggetto = "Segnalazione auto $numeroauto";
    $corpo = "
        <html lang='it'>
            <body>
                <p>Il giorno ".$datatesto.", [".$compilatore."] ".$nome." ".$cognome." (".$dictionarySquadra[$squadra]." ".$dictionarySezione[$sezione].") ha comunicato:</p>
                <p>**</p>
                <p>".$segnalazione."</p>
                <p>**</p>
            </body>
        </html>";
    mail($to, $oggetto, $corpo, $headers);

    //invio telegram
    $data = [
        'chat_id' => '@gestionaleCVTO',
        //'text' => $_POST['message']
        'text' => 'Il giorno '.$datatesto.', ['.$compilatore.'] '.$nome.' '.$cognome.' ha comunicato il seguente messaggio sul mezzo '.$numeroauto.': **'.$segnalazione.'**'
    ];
    $response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data) );
}