<?php
session_start();
header('Content-Type: application/json');
date_default_timezone_set("UTC");

$italianTimezone = new DateTimeZone("Europe/Rome");
$validStart = new DateTime("-1 year today", $italianTimezone);
$validEnd = new DateTime("+7 days today", $italianTimezone);

echo json_encode([
    'validStart' => $validStart->format('Y-m-d'),
    'validEnd' => $validEnd->format('Y-m-d')
]);

