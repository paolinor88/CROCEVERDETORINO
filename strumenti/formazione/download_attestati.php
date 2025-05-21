<?php
session_start();
require_once "../config/config.php";
global $db;

if (!isset($_SESSION['discente_id'])) {
    header("Location: login.php?redirect=download_attestati.php?" . http_build_query($_GET));
    exit();
}

$IDCorso = isset($_GET['IDCorso']) ? intval($_GET['IDCorso']) : 0;
$discente_id = $_SESSION['discente_id'];

// Recupero codice fiscale
$query = "SELECT codice_fiscale FROM discenti WHERE id = ?";
$stmt = $db->prepare($query);
$stmt->bind_param("i", $discente_id);
$stmt->execute();
$stmt->bind_result($codice_fiscale);
$stmt->fetch();
$stmt->close();

$codice_fiscale = strtoupper($codice_fiscale);
$filename = "{$codice_fiscale}_IDCORSO{$IDCorso}.pdf";
$filepath = __DIR__ . "/certificati/" . $filename;
$download_url = "scarica.php?IDCorso=" . $IDCorso;

$file_presente = file_exists($filepath);
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Download certificato</title>
    <meta http-equiv="refresh" content="<?= $file_presente ? '6;url=https://www.croceverde.org' : '' ?>">
    <?php require "../config/include/header.html"; ?>
    <link rel="stylesheet" href="../config/include/custom.css?v=<?= time() ?>">
</head>
<body>
<div class="container mt-5">
    <div class="card card-cv p-4 text-center">
        <?php if ($file_presente): ?>
            <h3 class="text-success mb-3">✅ Il tuo certificato è stato scaricato</h3>
            <p>Grazie per aver partecipato al corso.</p>
            <p>Verrai automaticamente reindirizzato alla <strong><a href="https://www.croceverde.org">home page</a></strong> tra pochi secondi.</p>
            <p class="mt-4">
                Se il download non è partito, <a class="btn btn-outline-cv" href="<?= $download_url ?>">clicca qui per scaricare manualmente</a>
            </p>
        <?php else: ?>
            <h3 class="text-danger mb-3">❌ Certificato non trovato</h3>
            <p>Il file richiesto non è stato trovato sul server.</p>
            <p>Se pensi che ci sia un errore, <a href="mailto: formazione@croceverde.org">contattaci</a> </p>
            <a href="https://www.croceverde.org" class="btn btn-outline-cv mt-3">Torna alla home</a>
        <?php endif; ?>
    </div>
</div>

<?php if ($file_presente): ?>
    <script>
        window.onload = function () {
            const link = document.createElement("a");
            link.href = "<?= $download_url ?>";
            link.download = "<?= $filename ?>";
            document.body.appendChild(link);
            link.click();
        };
    </script>
<?php endif; ?>
</body>
</html>
