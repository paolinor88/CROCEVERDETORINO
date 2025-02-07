<?php
date_default_timezone_set('Europe/Rome'); // Imposta il fuso orario italiano
echo json_encode([
    'italianTime' => time() // Restituisce il timestamp UNIX dell'ora italiana
]);
?>
