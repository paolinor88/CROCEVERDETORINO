<tbody>
<?php

$select = $db->query("SELECT * FROM mezzi_tagliandi WHERE TIPOMANUTENZIONE=7 GROUP BY ID_MEZZO order by ID_MEZZO DESC");
while($ciclo = $select->fetch_array()){
    $diffKM= ($ciclo['KMATTUALI']-$ciclo['KMTAGLIANDO']);

    if($select->num_rows>0): ?>
        <tr>
            <td class="align-middle"><form><button type='button' id='<?=$ciclo['ID_MEZZO']?>' class='btn-link btn btn-sm notecomplete' style="font-size:16px" value='<?=$ciclo['ID_MEZZO']?>'><?=$ciclo['ID_MEZZO']?></button></form></td>
            <td class="align-middle"><form><button type='button' id='<?=$ciclo['ID_TAGLIANDO']?>' class='btn-link btn btn-sm note' style="font-size:16px" value='<?=$ciclo['ID_TAGLIANDO']?>'><?=$ciclo['DATATAGLIANDO']?></button></form></td>
            <td class="align-middle"><?=$ciclo['KMATTUALI']?></td>
            <td class="align-middle" <?if ($ciclo['KMATTUALI']>($ciclo['KMTAGLIANDO']+23000)){echo " style='color: red' ";}?>><?if ($ciclo['KMATTUALI']>($ciclo['KMTAGLIANDO']+23000)){echo "<i class=\"fas fa-exclamation-triangle\"></i>";}else{echo (23000-$diffKM);}?></td>
            <td class="align-middle"><?=$ciclo['SCADENZAREVISIONE']?></td>
        </tr>
    <? endif;
}?>
</tbody>