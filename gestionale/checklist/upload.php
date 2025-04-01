<?php
/**
 *
 * @author     Paolo Randone
 * @author     <paolo.randone@croceverde.org>
* @version    8.1
 * @note       Powered for Croce Verde Torino. All rights reserved
 *
 */
session_start();
//connessione DB
include "../config/config.php";
include "../config/include/destinatari.php";
//login
if (!isset($_SESSION["ID"])){
    header("Location: ../login.php");
}
//supervariabili
if (isset($_POST['IDMEZZO'])){
    $idoperatore = $_SESSION['ID'];
    $idmezzo = $_POST['IDMEZZO'];

    $select = $db->query("SELECT * FROM mezzi WHERE ID='$idmezzo' AND stato='1'")->fetch_array();
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
if(isset($_POST["submit"])){
    $id_mezzo = $_POST["id_mezzo"];
    $id_operatore = $_POST["id_operatore"];
    $cognome = $_SESSION['cognome'];
    $nome = $_SESSION['nome'];
    $emailmitt = $_SESSION["email"];
    $sezione= $_SESSION["sezione"];
    $squadra= $_SESSION["squadra"];

    $uploaded_on = $_POST["uploaded_on"];$var1=date_create("$var", timezone_open("Europe/Rome"));$datafoto=date_format($var1, "d/m/Y H:i");$datacheck=date_format($var1, "Y-m-d H:i:s");

    $descrizione = $_POST["descrizione"];

    $targetDir = "uploads/";

    $fileNames = array_filter($_FILES['files']['name']);
    if(!empty($fileNames)){
        foreach($_FILES['files']['name'] as $key=>$val){
            // File upload path
            $fileName = basename($_FILES['files']['name'][$key]);
            $targetFilePath = $targetDir . $fileName;

            // Check whether file type is valid
            $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
            // Upload file to server
            if(move_uploaded_file($_FILES["files"]["tmp_name"][$key], $targetFilePath)){
                $uploadedFile = $targetFilePath;
                // Image db insert sql
                $insertValuesSQL .= "('".$fileName."' , '".$datacheck."' , '".$id_mezzo."' , '".$id_operatore."' , '".$descrizione."'),";
                //$insertValuesSQL .= "('".$fileName."', NOW()),";
            }else{
                echo "<script type='text/javascript'>alert('Errore nel caricamento del file, prova ancora1');location.href = 'index.php';</script>";
            }
        }

        if(!empty($insertValuesSQL)){
            $insertValuesSQL = trim($insertValuesSQL, ',');
            // Insert image file name into database
            $insert = $db->query("INSERT INTO images (file_name, uploaded_on, id_mezzo, id_operatore, note) VALUES $insertValuesSQL");
            if($insert){
                echo "<script type='text/javascript'>alert('Il file è stato caricato con successo');location.href = 'index.php';</script>";
            }else{
                echo "<script type='text/javascript'>alert('Errore nel caricamento del file, prova ancora2');location.href = 'index.php';</script>";
            }
        }else{
            echo "<script type='text/javascript'>alert('Errore nel caricamento del file, prova ancora3');location.href = 'index.php';</script>";
        }

        if(($_POST['descrizione'])!=""){
            $aggiunginotacheck = $db->query("INSERT INTO checklist (IDMEZZO, IDOPERATORE, DATACHECK, NOTE) VALUES ('$id_mezzo', '$id_operatore', '$datacheck', '$descrizione')");
        }

        $toEmail = $comunicazioni.', '.$emailmitt;
        $from = $checklist;
        $fromName = 'Checklist CVTO';
        $emailSubject = 'Segnalazione danno auto '.$id_mezzo;

        $htmlContent = "
        <html lang='it'>
            <body>
                <p>Il giorno ".$datafoto.", [".$id_operatore."] ".$nome." ".$cognome." (".$dictionarySquadra[$squadra]." ".$dictionarySezione[$sezione].") ha inviato le seguenti foto riferite al mezzo in oggetto:</p>
                <p>**</p>
                <p>".$descrizione."</p>
                <p>**</p>
            </body>
        </html>";

        $headers = "From: $fromName"." <".$from.">";

        if(!empty($uploadedFile) && file_exists($uploadedFile)){

            // Boundary
            $semi_rand = md5(time());
            $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";

            // Headers for attachment
            $headers .= "\nMIME-Version: 1.0\n" . "Content-Type: multipart/mixed;\n" . " boundary=\"{$mime_boundary}\"";

            // Multipart boundary
            $message = "--{$mime_boundary}\n" . "Content-Type: text/html; charset=\"UTF-8\"\n" .
                "Content-Transfer-Encoding: 7bit\n\n" . $htmlContent . "\n\n";

            // Preparing attachment
            if(is_file($uploadedFile)){
                $message .= "--{$mime_boundary}\n";
                $fp =    @fopen($uploadedFile,"rb");
                $data =  @fread($fp,filesize($uploadedFile));
                @fclose($fp);
                $data = chunk_split(base64_encode($data));
                $message .= "Content-Type: application/octet-stream; name=\"".basename($uploadedFile)."\"\n" .
                    "Content-Description: ".basename($uploadedFile)."\n" .
                    "Content-Disposition: attachment;\n" . " filename=\"".basename($uploadedFile)."\"; size=".filesize($uploadedFile).";\n" .
                    "Content-Transfer-Encoding: base64\n\n" . $data . "\n\n";
            }

            $message .= "--{$mime_boundary}--";
            $returnpath = "-f" . $from;

            // Send email
            $mail = mail($toEmail, $emailSubject, $message, $headers, $returnpath);

        }else{
            // Set content-type header for sending HTML email
            $headers .= "\r\n". "MIME-Version: 1.0";
            $headers .= "\r\n". "Content-type:text/html;charset=UTF-8";

            // Send email
            $mail = mail($toEmail, $emailSubject, $htmlContent, $headers);
        }
    }
}

?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <meta name="author" content="Paolo Randone">
    <title>Segnala danno</title>

    <? require "../config/include/header.html";?>
    <script rel="stylesheet" src="https://cdn.jsdelivr.net/npm/bs-custom-file-input/dist/bs-custom-file-input.js"></script>

    <script>
        $(document).ready(function(){
            $('[data-toggle="popover"]').popover();
        });
    </script>
    <script>
        $(document).ready(function () {
            bsCustomFileInput.init()
        })
    </script>

</head>
<!-- NAVBAR -->
<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../index.php" style="color: #078f40">Home</a></li>
            <li class="breadcrumb-item"><a href="index.php" style="color: #078f40">Checklist</a></li>
            <li class="breadcrumb-item active" aria-current="page">Segnala danno</li>
        </ol>
    </nav>
</div>

<body>
<div class="container-fluid">
    <div class="jumbotron">
        <form action="upload.php" method="post" enctype="multipart/form-data">
            <div style="text-align: center;">
                <b>AUTO <?=$idmezzo?></b>
            </div>
            <hr>
            <?php
            $query = $db->query("SELECT * FROM images WHERE id_mezzo='$idmezzo' AND status!=3 AND status !=4 ORDER BY id DESC");

            if($query->num_rows > 0){ ?>
                <div class="accordion" id="accordionExample">
                    <div class="card">
                        <h2 class="mb-0">
                            <div class="alert alert-danger" role="alert">
                                <button class="btn btn-block text-left collapsed alert-link" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                    <h5 class="alert-heading" STYLE='text-align: center'><i class="fas fa-camera"></i> Vedi foto</h5>
                                </button>
                            </div>
                        </h2>
                        <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordionExample">
                            <div class="card-body">
                                <?
                                while($row = $query->fetch_assoc()){
                                    $imageURL = 'uploads/'.$row["file_name"];
                                    ?>
                                    <img src="<?php echo $imageURL; ?>" alt="" width="200" class="img-thumbnail" />
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
                <br>
                <?
            }else{ ?>
                <div class="alert alert-success" role="alert">
                    Nessuna immagine nel database
                </div>
            <?php } ?>

            <input hidden name="id_mezzo" value="<?=$idmezzo?>">
            <input hidden name="id_operatore" value="<?=$idoperatore?>">
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-camera"></i></span>
                </div>
                <div class="custom-file">
                    <input type="file" class="custom-file-input" name="files[]" aria-describedby="scegli..." multiple>
                    <label class="custom-file-label" for="file">Scegli file</label>
                </div>
            </div>
            <div class="form-group">
                <label for="descrizione"><b>Descrizione</b> <small class="text-muted"> (Facoltativa)</small></label>
                <textarea class="form-control" name="descrizione" id="descrizione" rows="10" maxlength="250"></textarea>
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
            <div style="text-align: center;">
                <button type="submit" name="submit" value="UPLOAD" class="btn btn-success"><i class="fas fa-check"></i></button>
            </div>
        </form>
</div>
</div>

</body>

<!-- FOOTER -->
<?php include('../config/include/footer.php'); ?>

</html>
