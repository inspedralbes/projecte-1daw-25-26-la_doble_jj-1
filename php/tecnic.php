<?php include 'header.php'; ?>
<?php
date_default_timezone_set('Europe/Madrid');
require_once 'conexion.php';

$missatge = "";
$error    = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_incidencia'])) {
    $id    = intval($_POST['id_incidencia']);
    $estat = $_POST['estat'];

    if ($estat === 'Resolta') {
        // Usem la data seleccionada pel tècnic, si no n'hi ha posem la d'avui
        $data_fi = !empty($_POST['data_finalizacion']) ? $conn->real_escape_string($_POST['data_finalizacion']) : date('Y-m-d');
        $sql = "UPDATE incidencia SET data_finalizacion='$data_fi' WHERE id_incidencia=$id";
    } else {
        $sql = "UPDATE incidencia SET data_finalizacion=NULL WHERE id_incidencia=$id";
    }

    if ($conn->query($sql)) $missatge = "Incidència #$id actualitzada.";
    else $error = "Error: " . $conn->error;
}

$tecnics = [];
$res_tec = $conn->query("SELECT id_tecnico, nom FROM tecnico ORDER BY nom");
if ($res_tec) while ($r = $res_tec->fetch_assoc()) $tecnics[] = $r;

$id_tecnico = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id_tecnico === 0 && isset($_POST['id_tecnico_actual'])) $id_tecnico = intval($_POST['id_tecnico_actual']);

$incidencies = [];
$nom_tecnic  = '';

if ($id_tecnico > 0) {
    foreach ($tecnics as $t) {
        if ($t['id_tecnico'] == $id_tecnico) { $nom_tecnic = $t['nom']; break; }
    }
    $result = $conn->query(
        "SELECT i.id_incidencia, i.titol, i.descripcion, i.data, i.prioritat,
                i.data_finalizacion, i.tecnico, d.nom AS departament
         FROM incidencia i
         LEFT JOIN departamento d ON i.departamento = d.id_departamento
         WHERE i.tecnico = $id_tecnico ORDER BY i.data DESC"
    );
    if ($result) while ($r = $result->fetch_assoc()) $incidencies[] = $r;
}

$conn->close();
?>

<main>

<h1 class="mb-4">Àrea del Tècnic</h1>

<?php if ($missatge): ?><div class="alert alert-success" role="alert"><?= htmlspecialchars($missatge) ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger" role="alert"><?= htmlspecialchars($error) ?></div><?php endif; ?>

<form method="GET" action="tecnic.php" class="d-flex gap-2 mb-4">
    <label for="id" class="form-label visually-hidden">Selecciona el teu nom</label>
    <select id="id" name="id" class="form-select w-auto" aria-label="Selecciona el teu nom">
        <option value="">Tria el teu nom</option>
        <?php foreach ($tecnics as $t): ?>
            <option value="<?= $t['id_tecnico'] ?>" <?= $id_tecnico == $t['id_tecnico'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($t['nom']) ?>
            </option>
        <?php endforeach; ?>
    </select>
    <button type="submit" class="btn btn-primary">Entrar</button>
</form>

<?php if ($id_tecnico > 0): ?>

<h2 class="mb-3"><?= htmlspecialchars($nom_tecnic) ?></h2>

<?php if (empty($incidencies)): ?>
    <div class="alert alert-info" role="alert">No tens incidències assignades.</div>
<?php else: ?>

<div class="table-responsive">
<table class="table table-bordered table-hover align-middle">
    <caption>Incidències assignades a <?= htmlspecialchars($nom_tecnic) ?></caption>
    <thead class="table-primary">
        <tr>
            <th scope="col">ID</th>
            <th scope="col">Títol</th>
            <th scope="col">Descripció</th>
            <th scope="col">Departament</th>
            <th scope="col">Prioritat</th>
            <th scope="col">Data</th>
            <th scope="col">Estat</th>
            <th scope="col">Canviar estat</th>
            <th scope="col">Actuació</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($incidencies as $inc):
        $tancada = !is_null($inc['data_finalizacion']);
        if ($tancada)                    $estat = 'Resolta';
        elseif (!empty($inc['tecnico'])) $estat = 'En procés';
        else                             $estat = 'Pendent';

        if ($estat === 'Resolta')       $badge = 'success';
        elseif ($estat === 'En procés') $badge = 'warning';
        else                            $badge = 'secondary';

        $p = $inc['prioritat'] ?? '';
        if ($p === 'Alta')      $pbadge = 'danger';
        elseif ($p === 'Media') $pbadge = 'warning';
        elseif ($p === 'Baja')  $pbadge = 'info';
        else                    $pbadge = '';
    ?>
    <tr>
        <td><?= $inc['id_incidencia'] ?></td>
        <td><?= htmlspecialchars($inc['titol'] ?? '—') ?></td>
        <td><?= htmlspecialchars($inc['descripcion']) ?></td>
        <td><?= htmlspecialchars($inc['departament'] ?? '—') ?></td>
        <td><?= $p ? "<span class='badge bg-$pbadge'>$p</span>" : '—' ?></td>
        <td><?= date('d/m/Y H:i', strtotime($inc['data'])) ?></td>
        <td><span class="badge bg-<?= $badge ?>"><?= $estat ?></span></td>
        <td>
            <form method="POST" action="tecnic.php?id=<?= $id_tecnico ?>">
                <input type="hidden" name="id_incidencia"     value="<?= $inc['id_incidencia'] ?>">
                <input type="hidden" name="id_tecnico_actual" value="<?= $id_tecnico ?>">
                <div class="d-flex flex-column gap-1">
                    <select name="estat" class="form-select form-select-sm" aria-label="Canviar estat de la incidència"
                            onchange="toggleData(this, '<?= $inc['id_incidencia'] ?>')">
                        <option value="Pendent"   <?= $estat === 'Pendent'   ? 'selected' : '' ?>>Pendent</option>
                        <option value="En procés" <?= $estat === 'En procés' ? 'selected' : '' ?>>En procés</option>
                        <option value="Resolta"   <?= $estat === 'Resolta'   ? 'selected' : '' ?>>Resolta</option>
                    </select>
                    <input type="date" name="data_finalizacion"
                           id="data-<?= $inc['id_incidencia'] ?>"
                           class="form-control form-control-sm"
                           aria-label="Data de resolució"
                           value="<?= $inc['data_finalizacion'] ?? date('Y-m-d') ?>"
                           style="display:<?= $estat === 'Resolta' ? 'block' : 'none' ?>">
                    <button type="submit" class="btn btn-primary btn-sm">Desar</button>
                </div>
            </form>
        </td>
        <td>
            <a href="actuacion.php?id=<?= $inc['id_incidencia'] ?>" class="btn btn-outline-secondary btn-sm">Afegir actuació</a>
        </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>

<?php endif; ?>
<?php endif; ?>

<a href="index.php" class="btn btn-secondary mt-2">Tornar</a>

</main>

<script>
function toggleData(select, id) {
    var input = document.getElementById('data-' + id);
    if (select.value === 'Resolta') {
        input.style.display = 'block';
    } else {
        input.style.display = 'none';
    }
}
</script>

<?php include 'footer.php'; ?>