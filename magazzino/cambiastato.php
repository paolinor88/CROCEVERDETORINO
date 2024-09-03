<?php
header('Access-Control-Allow-Origin: *');

include "../config/config.php";
if (isset($_GET["id_richiesta"])) {
    $id = $_GET["id_richiesta"];
    $modifica = $db->query("SELECT * FROM richiesta_giacenza WHERE ID_RICHIESTA='$id'")->fetch_array();
}
$dictionary1 = array (
    1 => "Richiesto",
    2 => "Pronto",
    3 => "Consegnato",
);
?>
<head xmlns="">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <meta name="author" content="Paolo Randone">
    <title>MODAL</title>
    <? require "../config/include/header.html";?>
    <script type="text/javascript">
        function incrementValue()
        {
            var value = parseInt(document.getElementById('quantitaF').value, 10);
            value = isNaN(value) ? 0 : value;
            value++;
            document.getElementById('quantitaF').value = value;
        }
        function decrementValue()
        {
            var value = parseInt(document.getElementById('quantitaF').value, 10);
            value = isNaN(value) ? 0 : value;
            value--;
            document.getElementById('quantitaF').value = value;
        }
    </script>
    <script>
        $(document).ready(function () {
            $('#updatestato').on('click', function () {
                var id_richiesta = $("#id_richiesta").val();
                var statoF = $("#statoF").val();
                //alert(id_richiesta);
                $.ajax({
                    url: "script.php",
                    type: "POST",
                    data: {id_richiesta:id_richiesta, statoF:statoF},
                    success:function () {
                        Swal.fire({text:"Eseguito", icon: "success", button:false, timer:1000, closeOnClickOutside: false});
                        setTimeout(function () {
                                location.href='ordini.php';
                            },1001
                        )
                    }
                })
            })
        })
    </script>

</head>

<!--<form action="modal.php" method="post">-->
<div class="card text-center">
    <div class="card-body">
        <h6 class="card-title">Modifica stato</h6>
        <input hidden name="id_richiesta" id="id_richiesta" value="<?=$id?>">
        <select class="form-control form-control-sm" id="statoF" name="statoF">
            <?
            for($a=1;$a<4;$a++){
                ($a==$modifica['STATO'])? $sel="selected" : $sel="";
                echo "<option $sel value='$a'>".$dictionary1[$a]."</option>";
            }
            ?>
        </select>
    </div>
    <div class="card-footer">
        <button type="button" name="updatestato" id="updatestato" class="btn btn-outline-success btn-sm" style="text-align: center">Aggiorna</button>
    </div>
</div>
<!--</form>-->


