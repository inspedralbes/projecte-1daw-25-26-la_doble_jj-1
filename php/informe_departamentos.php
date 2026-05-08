<?php include 'header.php'; ?>
<?php
require_once 'conexion.php';

$result = $conn->query(
    "SELECT d.nom AS departament,
            COUNT(i.id_incidencia) AS total,
            SUM(CASE WHEN i.data_finalizacion IS NOT NULL THEN 1 ELSE 0 END) AS resoltes,
            COALESCE(SUM(a.tiempo), 0) AS temps_total
     FROM departamento d
     LEFT JOIN incidencia i  ON i.departamento = d.id_departamento
     LEFT JOIN actuacio a    ON a.incidencia   = i.id_incidencia
     GROUP BY d.id_departamento, d.nom
     ORDER BY total DESC"
);

$conn->close();
?>

<main>

<h1>Informe per Departaments</h1>

<table border="1" cellpadding="5" cellspacing="0">
    <thead>
        <tr>
            <th>Departament</th>
            <th>Total incidències</th>
            <th>Resoltes</th>
            <th>Temps total (min)</th>
        </tr>
    </thead>
    <tbody>
    <?php while ($r = $result->fetch_assoc()): ?>
    <tr>
        <td><?= htmlspecialchars($r['departament']) ?></td>
        <td><?= $r['total'] ?></td>
        <td><?= $r['resoltes'] ?></td>
        <td><?= $r['temps_total'] ?></td>
    </tr>
    <?php endwhile; ?>
    </tbody>
</table>

<p><a href="administrador.php">Tornar</a></p>

</main>

</body>
</html>