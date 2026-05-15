<?php require_once 'logger.php'; ?>

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

<h1 class="mb-4">Informe de Tècnics</h1>

<div class="table-responsive">
<table class="table table-bordered table-hover align-middle">
    <caption>Resum d'incidències resoltes i temps per tècnic</caption>
    <thead class="table-primary">
        <tr>
            <th scope="col">Tècnic</th>
            <th scope="col">Total incidències</th>
            <th scope="col">Resoltes</th>
            <th scope="col">Temps total (min)</th>
            <th scope="col">Actuacions</th>
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
                    <small class="d-block text-muted">
                        <?= date('d/m/Y', strtotime($a['data'])) ?> || <?= htmlspecialchars($a['descripcion']) ?>
                    </small>
                <?php endforeach; ?>
            <?php else: ?>
                <span class="text-muted">—</span>
            <?php endif; ?>
        </td>
    </tr>
    <?php endwhile; ?>
    </tbody>
</table>
</div>

<a href="administrador.php" class="btn btn-secondary mt-2">Tornar</a>

</main>

<?php include 'footer.php'; ?>