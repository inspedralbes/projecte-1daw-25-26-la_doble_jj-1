<?php
date_default_timezone_set('Europe/Madrid');
require_once 'conexion.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id    = intval($_POST['id_incidencia']);
    $tec   = !empty($_POST['tecnico'])     ? intval($_POST['tecnico'])     : "NULL";
    $tip   = !empty($_POST['tipo'])        ? intval($_POST['tipo'])        : "NULL";
    $dep   = !empty($_POST['departamento'])? intval($_POST['departamento']): "NULL";
    $prio  = in_array($_POST['prioritat'], ['Alta','Media','Baja']) ? "'".$_POST['prioritat']."'" : "NULL";


    $conn->query("UPDATE incidencia SET tecnico=$tec, tipo=$tip, departamento=$dep, prioritat=$prio WHERE id_incidencia=$id");
    header("Location: administrador.php");
   
   exit;
}


