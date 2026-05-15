<?php require_once 'logger.php'; ?>

<?php include 'header.php'; ?>

<?php
require_once 'conexion.php';

$result = $conn->query(
    "SELECT d.nom AS departament,
            COUNT(i.id_incidencia) AS total,
            SUM(CASE WHEN i.data_finalizacion IS NOT NULL THEN 1 ELSE 0 END) AS resoltes,
            (
                SELECT COALESCE(SUM(a.tiempo), 0)
                FROM actuacio a
                JOIN incidencia i2 ON a.incidencia = i2.id_incidencia
                WHERE i2.departamento = d.id_departamento
            ) AS temps_total
     FROM departamento d
     LEFT JOIN incidencia i ON i.departamento = d.id_departamento
     GROUP BY d.id_departamento, d.nom
     ORDER BY total DESC"
);

$conn->close();
?>

<main>

<h1 class="mb-4">Informe per Departaments</h1>

<div class="table-responsive">
<table class="table table-bordered table-hover align-middle">
    <caption>Resum d'incidències i temps per departament</caption>
    <thead class="table-primary">
        <tr>
            <th scope="col">Departament</th>
            <th scope="col">Total incidències</th>
            <th scope="col">Resoltes</th>
            <th scope="col">Temps total (min)</th>
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
</div>

<a href="administrador.php" class="btn btn-secondary mt-2">Tornar</a>

</main>

<?php include 'footer.php'; ?>