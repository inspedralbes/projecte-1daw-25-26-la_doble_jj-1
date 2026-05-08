<?php include 'header.php'; ?>
<?php
require_once 'conexion.php';

$result = $conn->query(
    "SELECT t.nom AS tecnic,
            COUNT(DISTINCT i.id_incidencia) AS total,
            SUM(CASE WHEN i.data_finalizacion IS NOT NULL THEN 1 ELSE 0 END) AS resoltes,
            COALESCE(SUM(a.tiempo), 0) AS temps_total
     FROM tecnico t
     LEFT JOIN incidencia i ON i.tecnico    = t.id_tecnico
     LEFT JOIN actuacio a   ON a.incidencia = i.id_incidencia
     GROUP BY t.id_tecnico, t.nom
     ORDER BY resoltes DESC"
);

$actuacions = $conn->query(
    "SELECT t.nom AS tecnic, a.descripcion, a.data, a.tiempo
     FROM actuacio a
     JOIN incidencia i ON a.incidencia = i.id_incidencia
     JOIN tecnico t    ON i.tecnico    = t.id_tecnico
     ORDER BY t.nom, a.data DESC"
);

$acts_per_tecnic = [];
while ($a = $actuacions->fetch_assoc()) {
    $acts_per_tecnic[$a['tecnic']][] = $a;
}

$conn->close();
?>

<main>

<h1>Informe de Tècnics</h1>

<table border="1" cellpadding="5" cellspacing="0">
    <thead>
        <tr>
            <th>Tècnic</th>
            <th>Total incidències</th>
            <th>Resoltes</th>
            <th>Temps total (min)</th>
            <th>Actuacions</th>
        </tr>
    </thead>
    <tbody>
    <?php while ($r = $result->fetch_assoc()): ?>
    <tr>
        <td><?= htmlspecialchars($r['tecnic']) ?></td>
        <td><?= $r['total'] ?></td>
        <td><?= $r['resoltes'] ?></td>
        <td><?= $r['temps_total'] ?></td>
        <td>
            <?php if (!empty($acts_per_tecnic[$r['tecnic']])): ?>
                <?php foreach ($acts_per_tecnic[$r['tecnic']] as $a): ?>
                    <p style="margin:2px 0">
                        <small><?= date('d/m/Y', strtotime($a['data'])) ?> || <?= htmlspecialchars($a['descripcion']) ?></small>
                    </p>
                <?php endforeach; ?>
            <?php else: ?>
                —
            <?php endif; ?>
        </td>
    </tr>
    <?php endwhile; ?>
    </tbody>
</table>

<p><a href="administrador.php">Tornar</a></p>

</main>

</body>
</html>