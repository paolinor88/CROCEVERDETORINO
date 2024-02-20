<?php
/**
 *
 * @author     Paolo Randone
 * @author     <paolo.randone@croceverde.org>
* @version    7.2
 * @note       Powered for Croce Verde Torino. All rights reserved
 *
 */
session_start();
//parametri DB
include "../config/config.php";
include "../config/include/destinatari.php";

//test
if (!isset($_SESSION["ID"])){
    header("Location: ../error.php");
}
//nicename sezioni
$dictionarySezione = array (
    1 => "TO",
    2 => "AL",
    3 => "BC",
    4 => "CI",
    5 => "SM",
    6 => "VE",
    7 => "DIP",
    8 => "SCN",
);
//nicename squadre
$dictionarySquadra = array (
    1 => "1",
    2 => "2",
    3 => "3",
    4 => "4",
    5 => "5",
    6 => "6",
    7 => "7",
    8 => "8",
    9 => "9",
    10 => "SAB",
    11 => "MON",
    12 => "DDS",
    13 => "Lunedì",
    14 => "Martedì",
    15 => "Mercoledì",
    16 => "Giovedì",
    17 => "Venerdì",
    18 => "DIU",
    19 => "GIO",
    20 => "GEN",
    21 => "Altro",
    22 => "TO",
    23 => "TO",
);
//input item
if( isset($_POST['form_item_id_list']) ) {
    $array_item = explode( ',' , $_POST['form_item_id_list'] );
    foreach( $array_item as $id_item ) {
        if( isset($_POST['form_qt_' . $id_item]) and ($_POST['form_qt_' . $id_item] > 0) ) {
            $quantita = $_POST['form_qt_' . $id_item];
            $prova = $db->query("SELECT nome, tipo FROM giacenza WHERE id='$id_item'")->fetch_array();
            $tabella .= $prova['nome']." ".$prova['tipo'].":  ".$quantita."<br>";
            $sessionID = $_SESSION['ID'];
            $oggi= date("Y-m-d");
            $note = $_POST['note'];
            $insert = $db -> query("INSERT INTO richiesta_giacenza (ID_UTENTE, ID_ITEM, QUANTITA, STATO, DATA, NOTE) VALUES ('$sessionID', '$id_item', '$quantita', 1, '$oggi', '$note')");

        }
    }

    //PARAMETRI MAIL ->
    //TODO modificare mail
    $to=$bechis.', '.$_SESSION['email'];
    $nome_mittente="Gestionale CVTO";
    $mail_mittente=$gestionale;
    $headers = "From: " .  $nome_mittente . " <" .  $mail_mittente . ">\r\n";
    $headers .= "Bcc: ".$randone."\r\n";
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

    //invio telegram
    $compilatore= $_SESSION['ID'];
    $nome= $_SESSION['nome'];
    $cognome= $_SESSION['cognome'];

    $data = [
        'chat_id' => '@gestionaleCVTO',
        //'text' => $_POST['message']
        'text' => "Richiesta materiale effettuata da [$compilatore] $nome $cognome:\n**$note**\n$tabella"
    ];
    $response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data) );

    // <- fine parametri mail

    echo '<script type="text/javascript">
        alert("La richiesta è stata inviata correttamente");
            location.href=\'index.php\';
        </script>';
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
            <li class="breadcrumb-item"><a href="index.php" style="color: #078f40">Autoparco</a></li>
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
                                      WHERE categoria='5' 
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
                                      /*WHERE nome= 'POLO TECNICA*/
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
        <div style="text-align: center;">
            <input type="hidden" name="form_item_id_list" value="<?= $html_hidd_id_item;?>" />

            <button type="submit" id="submit" name="submit" class="btn btn-outline-success btn-sm"><i class="fas fa-check"></i></button>
            <? if (($_SESSION['livello']=='4') OR ($_SESSION['livello']=='6')){
                echo "<a role=\"button\" class=\"btn btn-outline-info btn-sm\" href=\"ordini.php\"><i class=\"fas fa-search\"></i></a>";
            }
            ?>

        </div>
    </form>
</div>
<br>
</body>
<?php include('../config/include/footer.php'); ?>
</html>

