<?php
/**
 *
 * @author     Paolo Randone
 * @author     <paolo.randone@croceverde.org>
* @version    7.5
 * @note       Powered for Croce Verde Torino. All rights reserved
 *
 */
session_start();
include "../config/config.php";

if(isset($_POST["IDMEZZO"])){
    $numeroauto= $_POST['IDMEZZO'];
    $compilatore= $_POST['IDOPERATORE'];
    $var = $_POST['DATACHECK'];
    $data_check = date_create("$var");
    $start_event = date_format($data_check, "Y-m-d");
    $est= $_POST['ESTERNO'];
    $int= $_POST['INTERNO'];
    $san= $_POST['SANIFICAZIONE'];

    $insert = $db->query("INSERT INTO lavaggio_mezzi (title, user_id, start_event, stato, esterno, interno, neb) VALUES ('$numeroauto', '$compilatore', '$start_event', '1', '$est', '$int', '$san')");

}
