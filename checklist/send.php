<?php
/**
 *
 * @author     Paolo Randone
 * @author     <paolo.randone@croceverde.org>
* @version    7.3
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
if(isset($_POST["IDMEZZO"])){

    //TODO modificare destinatario
    $to= $checklist;//.', '.$bechis;
    $nome_mittente="Checklist CVTO";
    $mail_mittente=$checklist;
    $headers = "From: " .  $nome_mittente . " <" .  $mail_mittente . ">\r\n";
    $headers .= "Bcc: ".$email."\r\n";
    //$headers .= "Reply-To: " .  $email . "\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=iso-8859-1";
    // <- fine parametri mail

    $numeroauto = $_POST["IDMEZZO"];
    $segnalazione = $_POST["note"];
    $tipo = $_POST["tipo"];
    $datacheck = $_POST["DATACHECK"];$var1=date_create("$var", timezone_open("Europe/Rome"));$datatesto=date_format($var1, "d/m/Y H:i");;

    $compilatore = $_POST["IDOPERATORE"];

    $query = "INSERT INTO checklist (IDMEZZO, IDOPERATORE, DATACHECK, ESTERNO, INTERNO, NEB, SCADENZE, OLIO, NOTE) VALUES (:IDMEZZO, :IDOPERATORE, :DATACHECK, :ESTERNO, :INTERNO, :SANIFICAZIONE, :SCADENZE, :OLIO, :NOTE)";

    $statement = $connect->prepare($query);
    $statement->execute(
        array(
            ':IDMEZZO'  => $_POST['IDMEZZO'],
            ':IDOPERATORE'  => $_POST['IDOPERATORE'],
            ':DATACHECK'  => $_POST['DATACHECK'],
            ':ESTERNO'  => $_POST['ESTERNO'],
            ':INTERNO'  => $_POST['INTERNO'],
            ':SANIFICAZIONE'  => $_POST['SANIFICAZIONE'],
            ':SCADENZE'  => $_POST['SCADENZE'],
            ':OLIO'  => $_POST['OLIO'],
            ':NOTE'  => $_POST['note'],
        )
    );

    $subject="Checklist auto $numeroauto";
    $replace = array(
        '{{numeroauto}}',
        '{{compilatore}}',
        '{{cognome}}',
        '{{nome}}',
        '{{squadra}}',
        '{{sezione}}',
        '{{datatesto}}',
        '{{tipo}}',
        '{{segnalazione}}',
        //AMBULANZA
        '{{spinale}}',
        '{{scoop}}',
        '{{collari}}',
        '{{elettrodi}}',
        '{{gel}}',
        '{{ecg}}',
        '{{sixlead}}',
        '{{fourlead}}',
        '{{saturimetro}}',
        '{{pacing}}',
        '{{circuitoventilatore}}',
        '{{maschere}}',
        '{{piastre}}',
        '{{LP}}',
        '{{cavoLP}}',
        '{{batterieLP}}',
        '{{aspiratore}}',
        '{{ventilatore}}',
        '{{cavovent12}}',
        '{{cavovent220}}',
        '{{pompa}}',
        '{{cavopompa12}}',
        '{{cavopompa220}}',
        '{{bombolefisse}}',
        '{{taglienti}}',
        '{{DAE}}',
        '{{lenzuola}}',
        '{{cpap}}',
        '{{pedimate}}',
        '{{guanti}}',
        '{{sedia}}',
        '{{KED}}',
        '{{steccobende}}',
        '{{bomboleport}}',
        '{{caschi}}',
        '{{padella}}',
        '{{carta}}',
        '{{ragno}}',
        '{{trauma}}',
        '{{cinghie}}',
        '{{estintorepost}}',
        '{{coltrino}}',
        '{{traslatore}}',
        '{{estintoreant}}',
        '{{faro}}',
        '{{scasso}}',
        '{{bloccocv}}',
        '{{schede118}}',
        '{{fuoriservizio}}',
        '{{antifiamma}}',
        '{{panseptil}}',
        '{{oliocheck}}',
        '{{luci}}',
        '{{blu}}',
        '{{sirene}}',
        '{{gasolio}}',
        '{{telepass}}',
        '{{doc}}',
        '{{cartaagip}}',
        '{{lavaggioesterno}}',
        '{{lavaggiointerno}}',
        '{{disinfezione}}',
        '{{battesedia}}',
        //BORSA
        '{{scadenzeborsa}}',
        '{{ambuped}}',
        '{{reservoirped}}',
        '{{filtroped}}',
        '{{maschereped}}',
        '{{guedelped}}',
        '{{ossped}}',
        '{{ambuadulti}}',
        '{{reservoiradulti}}',
        '{{filtroadulti}}',
        '{{maschereadulti}}',
        '{{guedeladulti}}',
        '{{ossadulti}}',
        '{{fisio}}',
        '{{h2o2}}',
        '{{betadine}}',
        '{{cerotti}}',
        '{{benda}}',
        '{{garze}}',
        '{{ghiaccio}}',
        '{{arterioso}}',
        '{{venoso}}',
        '{{rasoio}}',
        '{{sfigmo}}',
        '{{fonendo}}',
        '{{saturimetrob}}',
        '{{termometro}}',
        '{{sondini}}',
        '{{maschereborsa}}',
        '{{robin}}',
        '{{guantisterili}}',
        '{{telini}}',
        '{{metalline}}',
        '{{spazzatura}}',
        '{{pappagallo}}',
        '{{dpi}}',
        '{{chirurgiche}}',
        '{{monossido}}',
        '{{tablet}}',
    );
    $with = array(
        $numeroauto,
        $compilatore,
        $cognome,
        $nome,
        $dictionarySquadra[$squadra],
        $dictionarySezione[$sezione],
        $datatesto,
        $dictionary[$tipo],
        $segnalazione,
        // AMBULANZA
        $spinale=$_POST["spinale"],
        $scoop=$_POST["scoop"],
        $collari=$_POST["collari"],
        $elettrodi=$_POST["elettrodi"],
        $gel=$_POST["gel"],
        $ecg=$_POST["ecg"],
        $sixlead=$_POST["sixlead"],
        $fourlead=$_POST["fourlead"],
        $saturimetro=$_POST["saturimetro"],
        $pacing=$_POST["pacing"],
        $circuitoventilatore=$_POST["circuitoventilatore"],
        $maschere=$_POST["maschere"],
        $piastre=$_POST["piastre"],
        $LP=$_POST["LP"],
        $cavoLP=$_POST["cavoLP"],
        $batterieLP=$_POST["batterieLP"],
        $aspiratore=$_POST["aspiratore"],
        $ventilatore=$_POST["ventilatore"],
        $cavovent12=$_POST["cavovent12"],
        $cavovent220=$_POST["cavovent220"],
        $pompa=$_POST["pompa"],
        $cavopompa12=$_POST["cavopompa12"],
        $cavopompa220=$_POST["cavopompa220"],
        $bombolefisse=$_POST["bombolefisse"],
        $taglienti=$_POST["taglienti"],
        $DAE=$_POST["DAE"],
        $lenzuola=$_POST["lenzuola"],
        $cpap=$_POST["cpap"],
        $pedimate=$_POST["pedimate"],
        $guanti=$_POST["guanti"],
        $sedia=$_POST["sedia"],
        $KED=$_POST["KED"],
        $steccobende=$_POST["steccobende"],
        $bomboleport=$_POST["bomboleport"],
        $caschi=$_POST["caschi"],
        $padella=$_POST["padella"],
        $carta=$_POST["carta"],
        $ragno=$_POST["ragno"],
        $trauma=$_POST["trauma"],
        $cinghie=$_POST["cinghie"],
        $estintorepost=$_POST["estintorepost"],
        $coltrino=$_POST["coltrino"],
        $traslatore=$_POST["traslatore"],
        $estintoreant=$_POST["estintoreant"],
        $faro=$_POST["faro"],
        $scasso=$_POST["scasso"],
        $bloccocv=$_POST["bloccocv"],
        $schede118=$_POST["schede118"],
        $fuoriservizio=$_POST["fuoriservizio"],
        $antifiamma=$_POST["antifiamma"],
        $panseptil=$_POST["panseptil"],
        $olio=$_POST["oliocheck"],
        $luci=$_POST["luci"],
        $blu=$_POST["blu"],
        $sirene=$_POST["sirene"],
        $gasolio=$_POST["gasolio"],
        $telepass=$_POST["telepass"],
        $doc=$_POST["doc"],
        $cartaagip=$_POST["cartaagip"],
        $lavaggioesterno=$_POST["lavaggioesternotext"],
        $lavaggiointerno=$_POST["lavaggiointernotext"],
        $disinfezione=$_POST["disinfezionetext"],
        $battesedia=$_POST["battesedia"],
        //BORSA
        $scadenzeborsa=$_POST["scadenzeborsa"],
        $ambuped=$_POST["ambuped"],
        $reservoirped=$_POST["reservoirped"],
        $filtroped=$_POST["filtroped"],
        $maschereped=$_POST["maschereped"],
        $guedelped=$_POST["guedelped"],
        $ossped=$_POST["ossped"],
        $ambuadulti=$_POST["ambuadulti"],
        $reservoiradulti=$_POST["reservoiradulti"],
        $filtroadulti=$_POST["filtroadulti"],
        $maschereadulti=$_POST["maschereadulti"],
        $guedeladulti=$_POST["guedeladulti"],
        $ossadulti=$_POST["ossadulti"],
        $fisio=$_POST["fisio"],
        $h2o2=$_POST["h2o2"],
        $betadine=$_POST["betadine"],
        $cerotti=$_POST["cerotti"],
        $benda=$_POST["benda"],
        $garze=$_POST["garze"],
        $ghiaccio=$_POST["ghiaccio"],
        $arterioso=$_POST["arterioso"],
        $venoso=$_POST["venoso"],
        $rasoio=$_POST["rasoio"],
        $sfigmo=$_POST["sfigmo"],
        $fonendo=$_POST["fonendo"],
        $saturimetrob=$_POST["saturimetrob"],
        $termometro=$_POST["termometro"],
        $sondini=$_POST["sondini"],
        $maschereborsa=$_POST["maschereborsa"],
        $robin=$_POST["robin"],
        $guantisterili=$_POST["guantisterili"],
        $telini=$_POST["telini"],
        $metalline=$_POST["metalline"],
        $spazzatura=$_POST["spazzatura"],
        $pappagallo=$_POST["pappagallo"],
        $dpi=$_POST["dpi"],
        $chirurgiche=$_POST["chirurgiche"],
        $monossido=$_POST["monossido"],
        $tablet=$_POST["tablet"],
    );
    if($tipo==2){   // MSA
        $corpo = file_get_contents('../config/template/msa.html');
        $corpo = str_replace ($replace, $with, $corpo);
        mail($to, $subject, $corpo, $headers);
        //invio telegram
        $data = [
            'chat_id' => '@gestionaleCVTO',
            //'text' => $_POST['message']
            'text' => 'Auto '.$numeroauto.': eseguita checklist da ['.$compilatore.'] '.$nome.' '.$cognome.''
        ];
        $response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data) );
    }elseif ($tipo==1){ // MSB
        $corpo = file_get_contents('../config/template/msb.html');
        $corpo = str_replace ($replace, $with, $corpo);
        mail($to, $subject, $corpo, $headers);
        //invio telegram
        $data = [
            'chat_id' => '@gestionaleCVTO',
            //'text' => $_POST['message']
            'text' => 'Auto '.$numeroauto.': eseguita checklist da ['.$compilatore.'] '.$nome.' '.$cognome.''
        ];
        $response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data) );
    }elseif ($tipo==3){ // 118
        $corpo = file_get_contents('../config/template/118.html');
        $corpo = str_replace ($replace, $with, $corpo);
        mail($to, $subject, $corpo, $headers);
        //invio telegram
        $data = [
            'chat_id' => '@gestionaleCVTO',
            //'text' => $_POST['message']
            'text' => 'Auto '.$numeroauto.': eseguita checklist da ['.$compilatore.'] '.$nome.' '.$cognome.''
        ];
        $response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data) );
    }
    if (($_POST["rabbocco"])!=0){
        $olioq = $_POST["rabbocco"];
        $oliok = $_POST["kilometriolio"];
        //TODO modificare destinatario
        $to= $comunicazioni;//.', '.$bechis;
        $nome_mittente="Gestionale CVTO";
        $mail_mittente=$gestionale;
        $headers = "From: " .  $nome_mittente . " <" .  $mail_mittente . ">\r\n";
        //$headers .= "Bcc: ".$email."\r\n";
        //$headers .= "Reply-To: " .  $mail_mittente . "\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion();
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=iso-8859-1";
        $oggetto = "Rabbocco olio motore auto $numeroauto";
        $corpo = "
        <html lang='it'>
            <body>
                <p>Si segnala che in data ".$datatesto." sono stati aggiunti ".$olioq." Kg di olio motore all'auto in oggetto (KM percorsi: ".$oliok.")</p>
                <p>".$compilatore." ".$nome." ".$cognome." (".$dictionarySquadra[$squadra]." ".$dictionarySezione[$sezione].")</p>
            </body>
        </html>";
        mail($to, $oggetto, $corpo, $headers);

        //invio telegram
        $data = [
            'chat_id' => '@gestionaleCVTO',
            //'text' => $_POST['message']
            'text' => 'Il giorno '.$datatesto.', ['.$compilatore.'] '.$nome.' '.$cognome.' ha aggiunto '.$olioq.'Kg di olio motore sul mezzo '.$numeroauto.' (KM percorsi: '.$oliok.')'
        ];
        $response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data) );
    }
    if (($_POST["note"])!=""){
        $inoltronote = $_POST["note"];
        //TODO modificare destinatario
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
                <p>".$inoltronote."</p>
                <p>**</p>
            </body>
        </html>";
        mail($to, $oggetto, $corpo, $headers);

        //invio telegram
        $data = [
            'chat_id' => '@gestionaleCVTO',
            //'text' => $_POST['message']
            'title' => 'Comunicazione auto '.$numeroauto.'',
            'text' => 'Il giorno '.$datatesto.', ['.$compilatore.'] '.$nome.' '.$cognome.' ha comunicato il seguente messaggio sul mezzo '.$numeroauto.': **'.$inoltronote.'**'
        ];
        $response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data) );
    }

    if ((($_POST["ESTERNO"])!=0) OR (($_POST["INTERNO"])!=0) OR (($_POST["SANIFICAZIONE"])!=0)){
        include "../config/config.php";
        $var = $_POST['DATACHECK'];
        $data_check = date_create("$var");
        $start_event = date_format($data_check, "Y-m-d");
        $est = $_POST["ESTERNO"];
        $int = $_POST["INTERNO"];
        $san = $_POST["SANIFICAZIONE"];

        $insert = $db->query("INSERT INTO lavaggio_mezzi (title, user_id, start_event, stato, esterno, interno, neb) VALUES ('$numeroauto', '$compilatore', '$start_event', '1', '$est', '$int', '$san')");

    }

}