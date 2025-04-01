<?php
session_start();
include "../config/config.php";
global $db;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $codice_fiscale = strtoupper(trim($_POST['codice_fiscale']));
    $password = $_POST['password'];
    $redirect_url = $_POST['redirect'] ?? 'corsi.php';

    $query = "SELECT id, nome FROM discenti WHERE codice_fiscale = ? AND password_hash = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("ss", $codice_fiscale, $password);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($discente_id, $nome);
        $stmt->fetch();
        $_SESSION['discente_id'] = $discente_id;
        $_SESSION['nome'] = $nome;

        header("Location: " . $redirect_url);
        exit();
    } else {
        $errore = "Credenziali non valide!";
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accesso Area Corsi</title>
    <?php require "config/include/header.html"; ?>
    <link rel="stylesheet" href="../config/include/custom.css?v=<?= time() ?>">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Open Sans', sans-serif;
        }

        .login-wrapper {
            max-width: 400px;
            margin: 50px auto;
        }

        .card-login {
            border-radius: 1rem;
            padding: 2rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            background-color: white;
        }

        .btn-login {
            width: 100%;
            padding: 0.75rem;
            font-weight: 600;
            font-size: 1rem;
        }

        .text-uppercase {
            text-transform: uppercase !important;
        }

        .logo-login {
            max-width: 240px;
        }
    </style>
</head>
<body>

<div class="container login-wrapper">
    <div class="text-center mb-4">
        <img src="../config/images/logo.png" alt="Logo" class="img-fluid logo-login">
    </div>

    <div class="card card-cv card-login">
        <h3 class="text-center mb-4">Accesso ai Corsi</h3>
        <form method="POST">
            <input type="hidden" name="redirect" value="<?= htmlspecialchars($_GET['redirect'] ?? 'corsi.php'); ?>">

            <div class="mb-3">
                <label class="form-label">Codice Fiscale</label>
                <input type="text" name="codice_fiscale" class="form-control text-uppercase" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <?php if (isset($errore)): ?>
                <div class="alert alert-danger text-center"><?= $errore; ?></div>
            <?php endif; ?>

            <button type="submit" class="btn btn-outline-cv btn-login">Accedi</button>
        </form>
    </div>
</div>

</body>
</html>
