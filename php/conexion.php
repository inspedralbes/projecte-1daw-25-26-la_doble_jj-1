<?php
// Connexió a la base de dades
$host     = "db";
$user     = "usuari";
$password = "paraula_de_pas";
$database = "incidencies";

$conn = mysqli_connect($host, $user, $password, $database);

if (!$conn) {
    die("Error de connexió: " . mysqli_connect_error());
}