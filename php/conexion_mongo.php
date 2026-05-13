<?php
require_once __DIR__ . '/vendor/autoload.php';

$mongoUri = getenv('MONGODB_URI') ?: 'mongodb://usuari:paraula_de_pas@mongodb:27017/logs?authSource=admin';

$mongoClient = new MongoDB\Client($mongoUri);
$mongoDB     = $mongoClient->logs;
$logsCol     = $mongoDB->accesslogs;

