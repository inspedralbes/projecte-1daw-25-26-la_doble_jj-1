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

$incidencies  = $conn->query("SELECT i.*, d.nom AS dep_nom, t.nom AS tec_nom, tp.nom AS tip_nom FROM incidencia i LEFT JOIN departamento d ON i.departamento=d.id_departamento LEFT JOIN tecnico t ON i.tecnico=t.id_tecnico LEFT JOIN tipo tp ON i.tipo=tp.id_tipo ORDER BY i.data DESC");
$tecnics      = $conn->query("SELECT * FROM tecnico ORDER BY nom");
$tipus        = $conn->query("SELECT * FROM tipo ORDER BY nom");
$departaments = $conn->query("SELECT * FROM departamento ORDER BY nom");
$conn->close(); 


?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Administrador</title>
</head>
<body>


<h1>Administrador</h1>


<table border="1" cellpadding="5">
    <tr>
        <th>ID</th><th>Títol</th><th>Descripció</th><th>Data</th><th>Estat</th>
        <th>Prioritat</th><th>Departament</th><th>Tècnic</th><th>Tipus</th><th></th>
    </tr>
    <?php while ($i = $incidencies->fetch_assoc()):
        $tancada = !is_null($i['data_finalizacion']);
        if ($tancada)              $estat = 'Resolta';
        elseif ($i['tecnico'])     $estat = 'En procés';
        else                       $estat = 'Pendent';
    ?>
    <tr>
        
    <form method="POST">
        <input type="hidden" name="id_incidencia" value="<?= $i['id_incidencia'] ?>">
        <td><?= $i['id_incidencia'] ?></td>
        <td><?= htmlspecialchars($i['titol'] ?? '—') ?></td>
        <td><?= htmlspecialchars($i['descripcion']) ?></td>
        <td><?= date('d/m/Y H:i', strtotime($i['data'])) ?></td>
        <td><?= $estat ?></td>
        <td>


