<?php
date_default_timezone_set('Europe/Madrid');
require_once 'conexion.php';


$missatge = "";
$error    = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   $id    = intval($_POST['id_incidencia']);
   $estat = $_POST['estat'];

   if ($estat === 'Resolta') {
       $data_actual = date('Y-m-d');
       $sql = "UPDATE incidencia SET data_finalizacion='$data_actual' WHERE id_incidencia=$id";
   } else {
       $sql = "UPDATE incidencia SET data_finalizacion=NULL WHERE id_incidencia=$id";
   }

   if ($conn->query($sql)) {
       $missatge = "Incidència #$id actualitzada.";
   } else {
       $error = "Error: " . $conn->error;
   }
}

$tecnics = [];
$res_tec = $conn->query("SELECT id_tecnico, nom FROM tecnico ORDER BY nom");
if ($res_tec) while ($r = $res_tec->fetch_assoc()) $tecnics[] = $r;

$id_tecnico = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id_tecnico === 0 && isset($_POST['id_tecnico_actual'])) {
   $id_tecnico = intval($_POST['id_tecnico_actual']);
}
