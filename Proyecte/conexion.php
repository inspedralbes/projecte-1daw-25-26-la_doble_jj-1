<?php
// Connexió a la base de dades
$host     = "localhost";
$user     = "a25juaosomej_doble_JJ";
$password = "InsPedralbes2025";
$database = "a25juaosomej_doble_JJ";

$conn = mysqli_connect($host, $user, $password, $database);

if (!$conn) {
    die("Error de connexió: " . mysqli_connect_error());
}