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
if ($result) while ($r = $result->fetch_assoc()) $incidencies[] = $r;

$conn->close();
?>
<!DOCTYPE html>
<html lang="ca">
<head>
   <meta charset="UTF-8">
   <title>Consultar Incidències</title>
</head>
<body>

<h1>Consultar Incidències</h1>

<form method="GET" action="consultar.php">
   <label for="id">Número d'incidència:</label>
   <input type="number" id="id" name="id" min="1" value="<?= htmlspecialchars($_GET['id'] ?? '') ?>">
   <button type="submit">Buscar</button>
   <?php if ($buscar_id): ?>
       <a href="consultar.php">Veure totes</a>
   <?php endif; ?>
</form>

<?php if ($buscar_id && empty($incidencies)): ?>
   <p>No s'ha trobat cap incidència amb l'ID #<?= $buscar_id ?>.</p>
<?php elseif (empty($incidencies)): ?>
   <p>No hi ha incidències registrades.</p>
<?php else: ?>
