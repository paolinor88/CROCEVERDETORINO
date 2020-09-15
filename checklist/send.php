<?php
/**
 *
 * @author     Paolo Randone
 * @author     <mail@paolorandone.it>
 * @version    1.3
 * @note       Powered for Croce Verde Torino. All rights reserved
 *
 */
session_start();
//parametri DB
include "../config/pdo.php";
//set session var
$cognome = $_SESSION["cognome"];
$nome = $_SESSION["nome"];
$email = $_SESSION["email"];
//
$dictionary = array (
    1 => "MSB",
    2 => "MSA",
    3 => "FLOTTA 118",
);
if(isset($_POST["IDMEZZO"])){

    //PARAMETRI MAIL ->
    //$destinatario='direzione@croceverde.org, mgaletto@libero.it';
    $to=$email;
    $nome_mittente="Gestionale CVTO";
    $mail_mittente="gestioneutenti@croceverde.org";
    $headers = "From: " .  $nome_mittente . " <" .  $mail_mittente . ">\r\n";
    $headers .= "Bcc: ".$to."\r\n";
    //$headers .= "Reply-To: " .  $mail_mittente . "\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=iso-8859-1";
    // <- fine parametri mail

    $numeroauto = $_POST["IDMEZZO"];
    $segnalazione = $_POST["note"];
    $tipo = $_POST["tipo"];
    $datatesto = $_POST["DATACHECK"];
    $compilatore = $_POST["IDOPERATORE"];

    $query = "INSERT INTO checklist (IDMEZZO, IDOPERATORE, DATACHECK, LAVAGGIO, SCADENZE, NOTE) VALUES (:IDMEZZO, :IDOPERATORE, :DATACHECK, :LAVAGGIO, :SCADENZE, :NOTE)";

    $statement = $connect->prepare($query);
    $statement->execute(
        array(
            ':IDMEZZO'  => $_POST['IDMEZZO'],
            ':IDOPERATORE'  => $_POST['IDOPERATORE'],
            ':DATACHECK'  => $_POST['DATACHECK'],
            ':LAVAGGIO'  => $_POST['LAVAGGIO'],
            ':SCADENZE'  => $_POST['SCADENZE'],
            ':NOTE'  => $_POST['note'],
        )
    );

    $subject="Checklist auto $numeroauto";
    $replace = array(
        '{{numeroauto}}',
        '{{compilatore}}',
        '{{cognome}}',
        '{{nome}}',
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
        '{{amputazioni}}',
        '{{ragno}}',
        '{{trauma}}',
        '{{cinghie}}',
        '{{estintorepost}}',
        '{{coltrino}}',
        '{{coperta}}',
        '{{traslatore}}',
        '{{estintoreant}}',
        '{{faro}}',
        '{{scasso}}',
        '{{bloccocv}}',
        '{{schede118}}',
        '{{fuoriservizio}}',
        '{{antifiamma}}',
        '{{panseptil}}',
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
    );
    $with = array(
        $numeroauto,
        $compilatore,
        $cognome,
        $nome,
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
        $amputazioni=$_POST["amputazioni"],
        $ragno=$_POST["ragno"],
        $trauma=$_POST["trauma"],
        $cinghie=$_POST["cinghie"],
        $estintorepost=$_POST["estintorepost"],
        $coltrino=$_POST["coltrino"],
        $coperta=$_POST["coperta"],
        $traslatore=$_POST["traslatore"],
        $estintoreant=$_POST["estintoreant"],
        $faro=$_POST["faro"],
        $scasso=$_POST["scasso"],
        $bloccocv=$_POST["bloccocv"],
        $schede118=$_POST["schede118"],
        $fuoriservizio=$_POST["fuoriservizio"],
        $antifiamma=$_POST["antifiamma"],
        $panseptil=$_POST["panseptil"],
        $luci=$_POST["luci"],
        $blu=$_POST["blu"],
        $sirene=$_POST["sirene"],
        $gasolio=$_POST["gasolio"],
        $telepass=$_POST["telepass"],
        $doc=$_POST["doc"],
        $cartaagip=$_POST["cartaagip"],
        $lavaggioesterno=$_POST["lavaggioesterno"],
        $lavaggiointerno=$_POST["lavaggiointerno"],
        $disinfezione=$_POST["disinfezione"],
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
    );
    if($tipo==2){   // MSA
        $corpo = file_get_contents('../config/template/msa.html');
        $corpo = str_replace ($replace, $with, $corpo);
        mail($to, $subject, $corpo, $headers);
    }elseif ($tipo==1){ // MSB
        $corpo = file_get_contents('../config/template/msb.html');
        $corpo = str_replace ($replace, $with, $corpo);
        mail($to, $subject, $corpo, $headers);
    }elseif ($tipo==3){ // 118
        $corpo = file_get_contents('../config/template/118.html');
        $corpo = str_replace ($replace, $with, $corpo);
        mail($to, $subject, $corpo, $headers);
    }
};