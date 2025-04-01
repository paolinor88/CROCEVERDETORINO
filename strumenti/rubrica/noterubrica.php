<?
header('Access-Control-Allow-Origin: *');

session_start();
/**
 *
 * @author     Paolo Randone
 * @author     <paolo.randone@croceverde.org>
 * @version    8.2
 * @note         Powered for Croce Verde Torino. All rights reserved
 *
 */

include "../config/config.php";

if (isset($_GET["id"])){
    $id = $_GET["id"];
    $select = $db->query("SELECT * FROM rubrica WHERE Codice='$id'");
    while($ciclo = $select->fetch_array()){

        if($select->num_rows>0): ?>
        <table class="table table-sm">
            <tr>
                <td colspan="2" style="text-align: center"><b><?=$ciclo['Codice']?> <?=$ciclo['Cognome']?> <?=$ciclo['Nome']?></b></td>
            </tr>
            <tr>
                <td>MILITE: </td>
                <td class="align-middle" style="text-align: center">
                    <?if ($ciclo['M']==2): ?> <i class="fa-solid fa-check" style="color: green"></i><? elseif ($ciclo['M']==""): ?> <i class="fa-solid fa-xmark" style="color: red"></i><? endif ?>
                </td>
            </tr>
            <tr>
                <td>MILITE AUTISTA: </td>
                <td class="align-middle" style="text-align: center">
                    <?if ($ciclo['MA']==4): ?><i class="fa-solid fa-check" style="color: green"></i><? elseif ($ciclo['MA']==""): ?> <i class="fa-solid fa-xmark" style="color: red"></i><? endif ?>
                </td>
            </tr>
            <tr>
                <td>MILITE AUTISTA URGENZE: </td>
                <td class="align-middle" style="text-align: center">
                    <?if ($ciclo['MAU']==5): ?><i class="fa-solid fa-check" style="color: green"></i><? elseif ($ciclo['MAU']==""): ?> <i class="fa-solid fa-xmark" style="color: red"></i><? endif ?>
                </td>
            </tr>
            <tr>
                <td>DAE: </td>
                <td class="align-middle" style="text-align: center">
                    <?if ($ciclo['DAE']==46): ?><i class="fa-solid fa-check" style="color: green"></i><? elseif ($ciclo['DAE']==""): ?> <i class="fa-solid fa-xmark" style="color: red"></i><? endif ?>
                </td>
            </tr>
            <tr>
                <td>SCADENZA DAE: </td>
                <td class="align-middle" style="text-align: center">
                    <?=($ciclo['ScadenzaDAE']);?>
                </td>
            </tr>
        </table>
        <? endif;
    }
}
