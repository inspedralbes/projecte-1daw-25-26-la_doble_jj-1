<?php
require_once __DIR__ . '/vendor/autoload.php';

$mongoUri = 'mongodb+srv://a25juaosomej_db_user:F1DM9zS1WOWLEwii@cluster0.0wthoxp.mongodb.net';

$mongoClient = new MongoDB\Client($mongoUri);
$mongoDB     = $mongoClient->logs;
$logsCol     = $mongoDB->accesslogs;

