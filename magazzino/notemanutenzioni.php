<?
header('Access-Control-Allow-Origin: *');

session_start();
/**
 *
 * @author     Paolo Randone
 * @author     <mail@paolorandone.it>
 * @version    5.0
 * @note       Powered for Croce Verde Torino. All rights reserved
 *
 */

//parametri DB
include "../config/config.php";
require "../config/include/header.html";
echo"    <div class=\"table-responsive-sm\">
            <table class=\"table table-hover table-sm\" id=\"myTable\">
                <tr>
                    <td>Data</td>
                    <td>KM</td>
                    <td>Note</td>
                </tr>
            ";
if (isset($_GET["id"])){
    $id = $_GET["id"];
    $select = $db->query("SELECT * FROM mezzi_tagliandi WHERE ID_MEZZO='$id' AND TIPOMANUTENZIONE !=5");
    while($ciclo = $select->fetch_array()){
        if($select->num_rows>0): ?>
            <tr>
                <td><?=$ciclo['DATATAGLIANDO']?></td>
                <td><?=$ciclo['KMTAGLIANDO']?></td>
                <td><?=$ciclo['NOTE']?></td>
            </tr>
        <? endif;
    }
}
echo"    </div>
            </table>";