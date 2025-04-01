<?php
date_default_timezone_set('Europe/Rome'); // Imposta il fuso orario italiano
echo json_encode([
    'serverTime' => date('d-m-Y H:i:s') // Restituisci l'ora nel formato corretto
]);