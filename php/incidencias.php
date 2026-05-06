<?php
date_default_timezone_set('Europe/Madrid');
require_once 'conexion.php';

$error    = "";
$missatge = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $titol        = trim($conn->real_escape_string($_POST['titol']));
    $descripcion  = trim($conn->real_escape_string($_POST['descripcion']));
    $departamento = intval($_POST['departamento']);

    if (empty($titol) || empty($descripcion) || $departamento === 0) {
        $error = "Tots els camps són obligatoris.";
    } else {
        $sql = "INSERT INTO incidencia (titol, descripcion, data, departamento)
                VALUES ('$titol', '$descripcion', NOW(), $departamento)";


        if ($conn->query($sql)) {
            $missatge = "Incidència registrada. ID: " . $conn->insert_id;
        } else {
            $error = "Error: " . $conn->error;
        }
    }
}
