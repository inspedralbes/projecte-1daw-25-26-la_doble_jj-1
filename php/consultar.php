<?php
date_default_timezone_set('Europe/Madrid');
require_once 'conexion.php';


$buscar_id = isset($_GET['id']) && $_GET['id'] !== '' ? intval($_GET['id']) : null;
$where     = $buscar_id ? "WHERE i.id_incidencia = $buscar_id" : "";


$incidencies = [];
$result = $conn->query(
   "SELECT i.id_incidencia, i.titol, i.descripcion, i.data,
           i.tecnico, i.data_finalizacion, d.nom AS departament
    FROM incidencia i
    LEFT JOIN departamento d ON i.departamento = d.id_departamento
    $where
    ORDER BY i.data DESC"
);
