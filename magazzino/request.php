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
//test super user
if (($_SESSION["ID"])!='D9999'){
    header("Location: ../error.php");
}

if( isset($_POST['form_item_id_list']) ) {
    $array_item = explode( ',' , $_POST['form_item_id_list'] );
    foreach( $array_item as $id_item ) {
        //echo $id_item . ' - ';
        if( isset($_POST['form_qt_' . $id_item]) and ($_POST['form_qt_' . $id_item] > 0) ) {

            $quantita = $_POST['form_qt_' . $id_item];

            while ($elenco = mysqli_fetch_array($quantita));
            {
                echo $id_item .' - '. $quantita . ' - ';
            }

            /*
            //PARAMETRI MAIL ->
            //$destinatario='direzione@croceverde.org, mgaletto@libero.it';
            $destinatario='paolo.randone@yahoo.it';
            $nome_mittente="Gestionale CVTO";
            $mail_mittente="gestioneutenti@croceverde.org";
            $headers = "From: " .  $nome_mittente . " <" .  $mail_mittente . ">\r\n";
            //$headers .= "Bcc: paolo.randone@yahoo.it\r\n";
            //$headers .= "Reply-To: " .  $mail_mittente . "\r\n";
            $headers .= "X-Mailer: PHP/" . phpversion();
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-type: text/html; charset=iso-8859-1";

            $oggetto = 'TEST RICHIESTA MATERIALE';
            $replace = array(
                '{{id}}',
                '{{cognome}}',
                '{{nome}}',
            );
            $with = array(
                $id,
                $cognome,
                $nome,
            );

            $corpo = file_get_contents('../config/template/request_item.html');
            $corpo = str_replace ($replace, $with, $corpo);

            mail($destinatario, $oggetto, $corpo, $headers);
            // <- fine parametri mail
            */
        }
    }
    //echo $item_nome;
    //var_dump($quantita);
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
<!--    <form action="request.php" method="post">-->
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
                        <form method="post" action="request.php">
                            <?php
                            $sql = "SELECT DISTINCT id, nome, tipo FROM giacenza WHERE categoria='1' order by nome, tipo";
                            $ret = mysqli_query( $db, $sql );

                            $html_form_item = '';
                            $html_hidd_id_item = '';

                            while ($row = mysqli_fetch_assoc($ret))
                            {
                                $html_hidd_id_item .= ( $html_hidd_id_item == '' ) ? $row['id'] : ',' . $row['id'];
                                $html_form_item .=
                                    "
                                <div class='form-group row'>
                                    <div class='col-sm-1'>
                                        <input type='text' class='form-control form-control-sm' name='form_qt_{$row['id']}' value='0' />
                                    </div>
                                    <label class='col-sm-11 col-form-label'>{$row['nome']} {$row['tipo']}</label>
                                </div>
                                ";
                                //$html_form_item .= "\r\n <label>{$row['nome']} {$row['tipo']}</label><br />quantita': <input type='text' name='form_qt_{$row['id']}' value='0' /><br /><br /> \r\n";
                            }
                            ?>

                            <input type="hidden" name="form_item_id_list" value="<?= $html_hidd_id_item; ?>" />

                            <?= $html_form_item; ?>
                            <button type="submit" id="submit" name="submit" class="btn btn-success"><i class="fas fa-check"></i></button>

                        </form>



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
                            $sql = "SELECT DISTINCT nome, tipo FROM giacenza WHERE categoria='4' order by nome, tipo";
                            $ret = mysqli_query( $db, $sql );

                            $html_form_item = '';
                            $html_hidd_id_item = '';

                            while ($row = mysqli_fetch_assoc($ret))
                            {
                                $html_hidd_id_item .= ( $html_hidd_id_item == '' ) ? $row['id'] : ',' . $row['id'];
                                $html_form_item .=
                                    "
                                <div class='form-group row'>
                                    <div class='col-sm-1'>
                                        <input type='text' class='form-control form-control-sm' name='form_qt_{$row['id']}' value='0' />
                                    </div>
                                    <label class='col-sm-11 col-form-label'>{$row['nome']} {$row['tipo']}</label>
                                </div>
                                ";
                                //$html_form_item .= "\r\n <label>{$row['nome']} {$row['tipo']}</label><br />quantita': <input type='text' name='form_qt_{$row['id']}' value='0' /><br /><br /> \r\n";
                            }
                            ?>

                            <input type="hidden" name="form_item_id_list" value="<?= $html_hidd_id_item; ?>" />

                            <?= $html_form_item; ?>

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
<!--            <button type="submit" id="submit" name="submit" class="btn btn-success"><i class="fas fa-check"></i></button>-->
        </center>
<!--    </form>-->
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

