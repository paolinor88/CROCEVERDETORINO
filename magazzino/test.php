<?php
include "../config/config.php";

if( isset($_POST['form_item_id_list']) ) {
    $array_item = explode( ',' , $_POST['form_item_id_list'] );
    foreach( $array_item as $id_item ) {
        //echo $id_item . ' - ';
        if( isset($_POST['form_qt_' . $id_item]) and ($_POST['form_qt_' . $id_item] > 0) ) {

            $quantita = $_POST['form_qt_' . $id_item];
            //$elenco = mysqli_fetch_array($quantita);
            /*
            while (mysqli_fetch_array($quantita));
            {
                $sql_item = $id_item . ' ';
                $sql_quantita = $quantita . ' ';
                //echo $sql_quantita;
                //echo $id_item .' - '. $quantita . ' - ';
            }
            */
            $prova = $db->query("SELECT nome, tipo FROM giacenza WHERE id='$id_item'")->fetch_array();
            echo "<div class='container-fluid'>
                    <div class='table-responsive'>
                        <table class='table table-sm table-hover'>
                            <tbody>
                                <tr>
                                    <td>".$prova['nome'].' '.$prova['tipo']."</td>
                                    <td>".$quantita."</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                  </div>  
                  
            ";

            //echo $prova['nome'].' '.$prova['tipo'].': '.$sql_quantita.';<br>';


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
    //var_dump($quantita);
    //var_dump($quantita);
}
