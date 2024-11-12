<?php
header('Access-Control-Allow-Origin: *');
session_start();
include "../config/pdo.php";
include "../config/config.php";
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <meta name="author" content="Paolo Randone">
    <title>HOME</title>
    <base href="/gestionale/magazzino/">
    <?php require "../config/include/header.html"; ?>

    <script>
        $(document).ready(function(){
            $('[data-toggle="popover"]').popover();
        });
    </script>
    <style>
        .card-link {
            display: block;
            color: inherit;
            text-decoration: none;
            width: 100%;
        }
        .card {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            border-radius: 10px;
            transition: transform 0.2s, box-shadow 0.2s;
            margin-bottom: 20px;
            width: 100%; /* Occupa tutta la larghezza del contenitore */
        }
        .card-container {
            max-width: 400px; /* Larghezza fissa per entrambe le card */
            width: 100%;
            margin: 0 auto; /* Centra il contenitore */
        }
        .card:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 12px rgba(7, 143, 64, 0.5);
        }
        .card-title {
            text-align: center;
            color: #078f40;
            font-weight: bold;
        }
        .container-fluid {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }
    </style>
</head>
<body>
<div class="container-fluid">

        <img class="img-fluid" src="../config/images/logo.png" alt="logoCVTO"/>
<br>
    <div class="card-container">
        <a href="/depositaossigeno" class="card-link">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">DEPOSITO BOMBOLA VUOTA</h4>
                </div>
            </div>
        </a>
        <br>
        <a href="/prelevaossigeno" class="card-link">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">PRELIEVO BOMBOLA PIENA</h4>
                </div>
            </div>
        </a>
    </div>
</div>
</body>
</html>
