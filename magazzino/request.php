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
//test
if (!isset($_SESSION["ID"])){
    header("Location: ../error.php");
}
//nicename sezioni
$dictionarySezione = array (
    1 => "Torino",
    2 => "Alpignano",
    3 => "Borgaro/Caselle",
    4 => "Ciriè",
    5 => "San Mauro",
    6 => "Venaria",
    7 => "",
);
//nicename sezioni
$dictionarySquadra = array (
    1 => "Prima",
    2 => "Seconda",
    3 => "Terza",
    4 => "Quarta",
    5 => "Quinta",
    6 => "Sesta",
    7 => "Settima",
    8 => "Ottava",
    9 => "Nona",
    10 => "Sabato",
    11 => "Montagna",
    12 => "Direzione",
    13 => "Lunedì",
    14 => "Martedì",
    15 => "Mercoledì",
    16 => "Giovedì",
    17 => "Venerdì",
    18 => "Diurno",
    19 => "Giovani",
    20 => "Servizi Generali",
    21 => "Altro",
    22 => "",
);
//input item
if( isset($_POST['form_item_id_list']) ) {
    $array_item = explode( ',' , $_POST['form_item_id_list'] );
    foreach( $array_item as $id_item ) {
        if( isset($_POST['form_qt_' . $id_item]) and ($_POST['form_qt_' . $id_item] > 0) ) {
            $quantita = $_POST['form_qt_' . $id_item];
            $prova = $db->query("SELECT nome, tipo FROM giacenza WHERE id='$id_item'")->fetch_array();
            $tabella .= $prova['nome'].' '.$prova['tipo'].': '.$quantita.'<br>';
        }
    }
    //PARAMETRI MAIL ->
    //$to='paolo.randone@yahoo.it';
    $to='massimilianobechis@gmail.com';
    //$destinatario=$_SESSION['email'];
    $nome_mittente="Gestionale CVTO";
    $mail_mittente="gestioneutenti@croceverde.org";
    $headers = "From: " .  $nome_mittente . " <" .  $mail_mittente . ">\r\n";
    $headers .= "Bcc: ".$mail_mittente."\r\n";
    $headers .= "Reply-To: " .  $_SESSION['email'] . "\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=iso-8859-1";
    $data = date("d-m-Y");
    $replace = array(
        '{{tabella}}',
        '{{ID}}',
        '{{cognome}}',
        '{{nome}}',
        '{{data}}',
        '{{sezione}}',
        '{{squadra}}',
        '{{note}}',
    );
    $with = array(
        $tabella,
        $_SESSION['ID'],
        $_SESSION['cognome'],
        $_SESSION['nome'],
        $data,
        $dictionarySezione[$_SESSION['sezione']],
        $dictionarySquadra[$_SESSION['squadra']],
        $_POST['note'],
    );
    $message = file_get_contents('../config/template/request_item.html');
    $corpo = str_replace ($replace, $with, $message);

    $subject = 'RICHIESTA MATERIALE';

    mail($to, $subject, $corpo, $headers);
    echo '<script type="text/javascript">
        alert("La richiesta è stata inviata correttamente");
        location.reload();
        </script>';

    // <- fine parametri mail
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
                            $sql_1 = "SELECT id, nome, tipo, SUM(quantita) AS quantita 
                                      FROM giacenza 
                                      WHERE categoria='1' 
                                      AND quantita >'0' 
                                      GROUP BY nome, tipo 
                                      ORDER BY nome, tipo";
                            $ret_1 = mysqli_query( $db, $sql_1 );

                            $html_form_item_1 = '';
                            $html_hidd_id_item = '';

                            while ($row = mysqli_fetch_assoc($ret_1))
                            {
                                if (($html_hidd_id_item == '')) {
                                    $html_hidd_id_item .= $row['id'];
                                } else {
                                    $html_hidd_id_item .= (',' . $row['id']);
                                }
                                $html_form_item_1 .=
                                    "
                                <div class='form-group row'>
                                    <div class='col-sm-2'>
                                        <input type='number' class='form-control form-control-sm' name='form_qt_{$row['id']}' value='0' max='{$row['quantita']}' required/>
                                    </div>
                                    <label class='col-sm-10 col-form-label'>{$row['nome']} {$row['tipo']} <small class='text-muted'>(Disponibile: {$row['quantita']})</small></label>
                                </div>
                                
                                ";
                            }
                            ?>

                            <?= $html_form_item_1; ?>
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
                            $sql_4 = "SELECT id, nome, tipo, SUM(quantita) AS quantita 
                                      FROM giacenza 
                                      WHERE categoria='4' 
                                      AND quantita >'0' 
                                      GROUP BY nome, tipo 
                                      ORDER BY nome, tipo";
                            $ret_4 = mysqli_query( $db, $sql_4 );

                            $html_form_item_4 = '';
                            $html_hidd_id_item .= '';

                            while ($row = mysqli_fetch_assoc($ret_4))
                            {
                                if (($html_hidd_id_item == '')) {
                                    $html_hidd_id_item .= $row['id'];
                                } else {
                                    $html_hidd_id_item .= (',' . $row['id']);
                                }
                                $html_form_item_4 .=
                                    "
                                <div class='form-group row'>
                                    <div class='col-sm-2'>
                                        <input type='number' class='form-control form-control-sm' name='form_qt_{$row['id']}' value='0' max='{$row['quantita']}' required />
                                    </div>
                                    <label class='col-sm-10 col-form-label'>{$row['nome']} {$row['tipo']} <small class='text-muted'>(Disponibile: {$row['quantita']})</small></label>
                                </div>
                                ";
                            }
                            ?>

                            <?= $html_form_item_4;?>

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
            <input type="hidden" name="form_item_id_list" value="<?= $html_hidd_id_item;?>" />

            <button type="submit" id="submit" name="submit" class="btn btn-success"><i class="fas fa-check"></i></button>
        </center>
    </form>
</div>
<br>
</body>
<?php include('../config/include/footer.php'); ?>
</html>

