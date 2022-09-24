<?
header('Access-Control-Allow-Origin: *');

session_start();
/**
 *
 * @author     Paolo Randone
 * @author     <mail@paolorandone.it>
 * @version    4.0
 * @note       Powered for Croce Verde Torino. All rights reserved
 *
 */

//parametri DB
include "../config/config.php";
require "../config/include/header.html";
echo"    <div class=\"table-responsive-sm\">
            <table class=\"table table-hover table-sm\" id=\"myTable\">";
if (isset($_GET["id"])){
    $id = $_GET["id"];
    $select = $db->query("SELECT * FROM mezzi_tagliandi WHERE ID_MEZZO='$id'");
    while($ciclo = $select->fetch_array()){
        if($select->num_rows>0): ?>
            <tr>
                <td><?=$ciclo['DATATAGLIANDO']?></td>
                <td><?=$ciclo['NOTE']?></td>
            </tr>
        <? endif;
    }
}
echo"    </div>
            </table>";