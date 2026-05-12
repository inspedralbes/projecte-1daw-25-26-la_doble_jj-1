<?php
date_default_timezone_set('Europe/Madrid');
require_once 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_incidencia'])) {
    $id   = intval($_POST['id_incidencia']);
    $tec  = !empty($_POST['tecnico'])      ? intval($_POST['tecnico'])      : "NULL";
    $tip  = !empty($_POST['tipo'])         ? intval($_POST['tipo'])         : "NULL";
    $dep  = !empty($_POST['departamento']) ? intval($_POST['departamento']) : "NULL";
    $prio = in_array($_POST['prioritat'], ['Alta','Media','Baja'])
            ? "'".$_POST['prioritat']."'" : "NULL";

    $conn->query("UPDATE incidencia SET tecnico=$tec, tipo=$tip, departamento=$dep, prioritat=$prio WHERE id_incidencia=$id");
    header("Location: administrador.php");
    exit;
}

$incidencies  = $conn->query("SELECT i.*, d.nom AS dep_nom, t.nom AS tec_nom, tp.nom AS tip_nom
                               FROM incidencia i
                               LEFT JOIN departamento d  ON i.departamento = d.id_departamento
                               LEFT JOIN tecnico t       ON i.tecnico      = t.id_tecnico
                               LEFT JOIN tipo tp         ON i.tipo         = tp.id_tipo
                               ORDER BY i.data DESC");
$tecnics      = $conn->query("SELECT * FROM tecnico      ORDER BY nom");
$tipus        = $conn->query("SELECT * FROM tipo         ORDER BY nom");
$departaments = $conn->query("SELECT * FROM departamento ORDER BY nom");
$conn->close();

include 'header.php';
?>

<main>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Administrador</h1>                 
    <div class="d-flex flex-row gap-3" style="min-width:180px">
        <a href="informe_departamentos.php" class="btn btn-outline-primary btn-sm">Informe departaments</a>
        <a href="informe_tecnicos.php" class="btn btn-outline-primary btn-sm">Informe tècnics</a>
        <a href="informe_tecnicos.php" class="btn btn-outline-primary btn-sm">Estadisticas de acceso</a>
    </div>
</div>

<div class="table-responsive">
<table class="table table-bordered table-hover align-middle">
    <thead class="table-primary">
        <tr>
            <th>ID</th><th>Títol</th><th>Descripció</th><th>Data</th><th>Estat</th>
            <th>Prioritat</th><th>Departament</th><th>Tècnic</th><th>Tipus</th><th></th>
        </tr>
    </thead>
    <tbody>
    <?php while ($i = $incidencies->fetch_assoc()):
        $tancada = !is_null($i['data_finalizacion']);
        if ($tancada)          $estat = 'Resolta';
        elseif ($i['tecnico']) $estat = 'En procés';
        else                   $estat = 'Pendent';
        if ($estat === 'Resolta')       $badge = 'success';
        elseif ($estat === 'En procés') $badge = 'warning';
        else                            $badge = 'secondary';
        $form_id = 'form-' . $i['id_incidencia'];
    ?>
    <form id="<?= $form_id ?>" method="POST" action="administrador.php">
        <input type="hidden" name="id_incidencia" value="<?= $i['id_incidencia'] ?>">
    </form>
    <tr>
        <td><?= $i['id_incidencia'] ?></td>
        <td><?= htmlspecialchars($i['titol'] ?? '—') ?></td>
        <td><?= htmlspecialchars($i['descripcion']) ?></td>
        <td><?= date('d/m/Y H:i', strtotime($i['data'])) ?></td>
        <td><span class="badge bg-<?= $badge ?>"><?= $estat ?></span></td>
        <td>
            <select name="prioritat" form="<?= $form_id ?>" class="form-select form-select-sm">
                <option value="">—</option>
                <?php foreach (['Alta','Media','Baja'] as $p): ?>
                    <option value="<?= $p ?>" <?= $i['prioritat'] === $p ? 'selected' : '' ?>><?= $p ?></option>
                <?php endforeach; ?>
            </select>
        </td>
        <td>
            <select name="departamento" form="<?= $form_id ?>" class="form-select form-select-sm">
                <option value="">—</option>
                <?php $departaments->data_seek(0); while ($d = $departaments->fetch_assoc()): ?>
                    <option value="<?= $d['id_departamento'] ?>" <?= $i['departamento'] == $d['id_departamento'] ? 'selected' : '' ?>><?= htmlspecialchars($d['nom']) ?></option>
                <?php endwhile; ?>
            </select>
        </td>
        <td>
            <select name="tecnico" form="<?= $form_id ?>" class="form-select form-select-sm">
                <option value="">—</option>
                <?php $tecnics->data_seek(0); while ($t = $tecnics->fetch_assoc()): ?>
                    <option value="<?= $t['id_tecnico'] ?>" <?= $i['tecnico'] == $t['id_tecnico'] ? 'selected' : '' ?>><?= htmlspecialchars($t['nom']) ?></option>
                <?php endwhile; ?>
            </select>
        </td>
        <td>
            <select name="tipo" form="<?= $form_id ?>" class="form-select form-select-sm">
                <option value="">—</option>
                <?php $tipus->data_seek(0); while ($tp = $tipus->fetch_assoc()): ?>
                    <option value="<?= $tp['id_tipo'] ?>" <?= $i['tipo'] == $tp['id_tipo'] ? 'selected' : '' ?>><?= htmlspecialchars($tp['nom']) ?></option>
                <?php endwhile; ?>
            </select>
        </td>
        <td><button type="submit" form="<?= $form_id ?>" class="btn btn-primary btn-sm">Desar</button></td>
    </tr>
    <?php endwhile; ?>
    </tbody>
</table>
</div>

<a href="index.php" class="btn btn-secondary mt-2">Tornar</a>

</main>

<?php include 'footer.php'; ?>