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

<style>
    .volver {
        padding: 0.5rem; 
        text-align: center;
        text-decoration: none; border: 1px solid #535757; border-radius: 6px;
        color: #1a1a1a; 
        background : #04eff7;
    }
</style>

<main>

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

<table border="1" cellpadding="5" cellspacing="0">
    <thead>
        <tr>
            <th>ID</th><th>Títol</th><th>Descripció</th><th>Departament</th>
            <th>Data</th><th>Estat</th><th>Actuacions</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($incidencies as $inc):
        $tancada = !is_null($inc['data_finalizacion']);
        if ($tancada)                    $estat = 'Resolta';
        elseif (!empty($inc['tecnico'])) $estat = 'En procés';
        else                             $estat = 'Pendent';
    ?>
    <tr>
        <td><?= $inc['id_incidencia'] ?></td>
        <td><?= htmlspecialchars($inc['titol'] ?? '—') ?></td>
        <td><?= htmlspecialchars($inc['descripcion']) ?></td>
        <td><?= htmlspecialchars($inc['departament'] ?? '—') ?></td>
        <td><?= date('d/m/Y H:i', strtotime($inc['data'])) ?></td>
        <td><?= $estat ?></td>
        <td>
            <?php if (!empty($actuacions[$inc['id_incidencia']])): ?>
                <?php foreach ($actuacions[$inc['id_incidencia']] as $a): ?>
                    <p style="margin:2px 0">
                        <small><?= date('d/m/Y H:i', strtotime($a['data'])) ?> (<?= $a['tiempo'] ?> min) — <?= htmlspecialchars($a['descripcion']) ?></small>
                    </p>
                <?php endforeach; ?>
            <?php else: ?>
                —
            <?php endif; ?>
        </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<?php endif; ?>

    <div>
        <p><a href="index.php" class="volver">Tornar</a></p>
    </div>

</main>

<?php include 'footer.php'; ?>