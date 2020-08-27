<?php
/**
 *
 * @author     Paolo Randone
 * @author     <mail@paolorandone.it>
 * @version    1.0
 * @note       Powered for Croce Verde Torino. All rights reserved
 *
 */
session_start();
//parametri DB
include "../config/config.php";
//accesso consentito a logistica, segreteria e ADMIN
if (($_SESSION["ID"])!='D9999'){
    header("Location: ../error.php");
}

?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <meta name="author" content="Paolo Randone">
    <title>Richiesta materiale</title>

    <? require "../config/include/header.html";?>

</head>
<body>
<!-- NAVBAR -->
<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../index.php" style="color: #078f40">Home</a></li>
            <li class="breadcrumb-item"><a href="index.php" style="color: #078f40">Magazzino</a></li>
            <li class="breadcrumb-item active" aria-current="page">Richiesta materiale</li>
        </ol>
    </nav>
    <form action="request.php" method="post">
        <div class="accordion" id="accordionExample">
            <div class="card">
                <div class="card-header" id="headingOne">
                    <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                            Materiale di consumo
                        </button>
                    </h2>
                </div>
                <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordionExample">
                    <div class="card-body">
                        <?php

                        $select = $db->query("SELECT DISTINCT nome, tipo FROM giacenza WHERE categoria='1' order by nome, tipo");

                        while($ciclo = $select->fetch_array()){

                            echo "
                                    <div class=\"form-group row\">
                                        <div class=\"col-sm-1\" >
                                           <input type=\"text\" class=\"form-control form-control-sm oggetto\" name=\"".$ciclo['nome']." ".$ciclo['tipo']."\" value=''>
                                        </div>
                                        <label class=\"col-sm-11 col-form-label\" for=\"".$ciclo['nome']." ".$ciclo['tipo']."\">".$ciclo['nome']." ".$ciclo['tipo']."</label>
                                    </div>
                                    ";

                        }

                        ?>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header" id="headingTwo">
                    <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                            Vestiario
                        </button>
                    </h2>
                </div>
                <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionExample">
                    <div class="card-body">
                        <?php

                        $select = $db->query("SELECT DISTINCT nome, tipo FROM giacenza WHERE categoria='4' order by nome, tipo");

                        while($ciclo = $select->fetch_array()){

                            echo "
                                    <div class=\"form-group row\">
                                        <div class=\"col-sm-1\">
                                           <input type=\"text\" class=\"form-control form-control-sm\" id=\"".$ciclo['nome']." ".$ciclo['tipo']."\">
                                        </div>
                                        <label class=\"col-sm-11 col-form-label\" for=\"".$ciclo['nome']." ".$ciclo['tipo']."\">".$ciclo['nome']." ".$ciclo['tipo']."</label>
                                    </div>
                                    ";

                        }

                        ?>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header" id="headingThree">
                    <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                            Altro
                        </button>
                    </h2>
                </div>
                <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordionExample">
                    <div class="card-body">
                        <div class="form-group">
                            <textarea class="form-control" name="note" id="note" rows="10" maxlength="250"></textarea>
                            <span id="conteggio" style="font-size: small; color: grey"></span>
                            <script type="text/javascript">
                                // avvio il controllo all'evento keyup
                                $('textarea#note').keyup(function() {
                                    // definisco il limite massimo di caratteri
                                    var limite = 250;
                                    var quanti = $(this).val().length;
                                    // mostro il conteggio in real-time
                                    $('span#conteggio').html(quanti + ' / ' + limite);
                                    // quando raggiungo il limite
                                    if(quanti >= limite) {
                                        // mostro un avviso
                                        $('span#conteggio').html('<strong>Non puoi inserire più di ' + limite + ' caratteri!</strong>');
                                        // taglio il contenuto per il numero massimo di caratteri ammessi
                                        var $contenuto = $(this).val().substr(0,limite);
                                        $('textarea#note').val($contenuto);
                                    }
                                });
                            </script>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <br>
        <center>
            <button type="submit" id="inviarichiesta" name="inviarichiesta" class="btn btn-success"><i class="fas fa-check"></i></button>
        </center>
    </form>
</div>
<br>
<?
/*//ottieni items
if(isset($_POST["inviarichiesta"])&&(($_POST[$ciclo['nome'].' '.$ciclo['tipo']])!="")){
    echo "OK";
}else{
    echo "ERRORE";
}*/?>

</body>
<?php include('../config/include/footer.php'); ?>
</html>

