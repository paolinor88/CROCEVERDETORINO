<?
header('Access-Control-Allow-Origin: *');

session_start();
/**
 *
 * @author     Paolo Randone
 * @author     <paolo.randone@croceverde.org>
* @version    7.2
 * @note       Powered for Croce Verde Torino. All rights reserved
 *
 */

//parametri DB
include "../config/config.php";

if (isset($_GET["id"])){
    $id = $_GET["id"];
    $select = $db->query("SELECT * FROM mezzi_tagliandi WHERE ID_TAGLIANDO='$id'");
while($ciclo = $select->fetch_array()){
    $diffKM= ($ciclo['KMATTUALI']-$ciclo['KMTAGLIANDO']);

    if($select->num_rows>0): ?>
        <tr>
            <td><?=$ciclo['NOTE']?></td>
        </tr>
    <? endif;
}
}
