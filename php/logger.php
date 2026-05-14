<?php
require_once __DIR__ . '/conexion_mongo.php';

// Inserim un document de log per cada petició HTTP
$logsCol->insertOne([
    'url'        => $_SERVER['REQUEST_URI']     ?? '/',
    'metode'     => $_SERVER['REQUEST_METHOD']  ?? 'GET',
    'ip'         => $_SERVER['REMOTE_ADDR']     ?? 'unknown',
    'navegador'  => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
    'timestamp'  => new MongoDB\BSON\UTCDateTime(),
    'usuari'     => null, // null perquè no tenim autenticació
]);
