<?php
header('Access-Control-Allow-Origin: *');

include "../config/config.php";
if (isset($_GET["id"])) {
    $id = $_GET["id"];
    $modifica = $db->query("SELECT * FROM giacenza WHERE id='$id'")->fetch_array();
}

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
            $('#update').on('click', function () {
                var id = $("#id").val();
                var quantitaF = $("#quantitaF").val();
                $.ajax({
                    url: "script.php",
                    type: "POST",
                    data: {id:id, quantitaF:quantitaF},
                    success:function () {
                        swal({text:"Eseguito", icon: "success", button:false, timer:1000, closeOnClickOutside: false});
                        setTimeout(function () {
                                location.href='magazzino.php';
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
            <h6 class="card-title">Modifica quantità</h6>
            <input hidden name="id" id="id" value="<?=$id?>">
            <input type="button" class="btn btn-sm btn-link" onclick="decrementValue()" value="-" />
            <input type="text" style="text-align: center" class="form-control form-control-sm" name="quantitaF" id="quantitaF" value="<?=$modifica['quantita']?>" autofocus>
            <input type="button" class="btn btn-sm btn-link" onclick="incrementValue()" value="+" />
        </div>
        <div class="card-footer">
            <button type="button" name="update" id="update" class="btn btn-outline-success btn-sm" style="text-align: center">Aggiorna</button>
        </div>
    </div>
<!--</form>-->


