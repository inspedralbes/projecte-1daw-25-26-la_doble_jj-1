<?php include 'header.php'; ?>
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

$actuacions = [];
if (!empty($incidencies)) {
    $ids = implode(',', array_column($incidencies, 'id_incidencia'));
    $res_act = $conn->query(
        "SELECT incidencia, descripcion, data, tiempo
         FROM actuacio
         WHERE incidencia IN ($ids) AND visible = 1
         ORDER BY data ASC"
    );
    while ($a = $res_act->fetch_assoc()) {
        $actuacions[$a['incidencia']][] = $a;
    }
}

$conn->close();
?>

<main>

<h1 class="mb-4">Consultar Incidències</h1>

<form method="GET" action="consultar.php" class="d-flex gap-2 mb-4">
    <input type="number" name="id" min="1" class="form-control w-auto"
           placeholder="Número d'incidència" value="<?= htmlspecialchars($_GET['id'] ?? '') ?>">
    <button type="submit" class="btn btn-primary">Buscar</button>
    <?php if ($buscar_id): ?>
        <a href="consultar.php" class="btn btn-outline-secondary">Veure totes</a>
    <?php endif; ?>
</form>

<?php if ($buscar_id && empty($incidencies)): ?>
    <div class="alert alert-warning">No s'ha trobat cap incidència amb l'ID #<?= $buscar_id ?>.</div>
<?php elseif (empty($incidencies)): ?>
    <div class="alert alert-info">No hi ha incidències registrades.</div>
<?php else: ?>

<div class="table-responsive">
<table class="table table-bordered table-hover align-middle">
    <thead class="table-primary">
        <tr>
            <th>ID</th>
            <th>Títol</th>
            <th>Descripció</th>
            <th>Departament</th>
            <th>Data</th>
            <th>Estat</th>
            <th>Actuacions</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($incidencies as $inc):
        $tancada = !is_null($inc['data_finalizacion']);
        if ($tancada)                    $estat = 'Resolta';
        elseif (!empty($inc['tecnico'])) $estat = 'En procés';
        else                             $estat = 'Pendent';

        if ($estat === 'Resolta')   $badge = 'success';
        elseif ($estat === 'En procés') $badge = 'warning';
        else                        $badge = 'secondary';
    ?>
    <tr>
        <td><?= $inc['id_incidencia'] ?></td>
        <td><?= htmlspecialchars($inc['titol'] ?? '—') ?></td>
        <td><?= htmlspecialchars($inc['descripcion']) ?></td>
        <td><?= htmlspecialchars($inc['departament'] ?? '—') ?></td>
        <td><?= date('d/m/Y H:i', strtotime($inc['data'])) ?></td>
        <td><span class="badge bg-<?= $badge ?>"><?= $estat ?></span></td>
        <td>
            <?php if (!empty($actuacions[$inc['id_incidencia']])): ?>
                <?php foreach ($actuacions[$inc['id_incidencia']] as $a): ?>
                    <small class="d-block text-muted">
                        <?= date('d/m/Y H:i', strtotime($a['data'])) ?> (<?= $a['tiempo'] ?> min) — <?= htmlspecialchars($a['descripcion']) ?>
                    </small>
                <?php endforeach; ?>
            <?php else: ?>
                <span class="text-muted">—</span>
            <?php endif; ?>
        </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>

<?php endif; ?>

<a href="index.php" class="btn btn-secondary mt-2">Tornar</a>

</main>

<?php include 'footer.php'; ?>