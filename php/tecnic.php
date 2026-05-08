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
        $data_actual = date('Y-m-d');
        $sql = "UPDATE incidencia SET data_finalizacion='$data_actual' WHERE id_incidencia=$id";
    } else {
        $sql = "UPDATE incidencia SET data_finalizacion=NULL WHERE id_incidencia=$id";
    }

    if ($conn->query($sql)) {
        $missatge = "Incidència #$id actualitzada.";
    } else {
        $error = "Error: " . $conn->error;
    }
}

$tecnics = [];
$res_tec = $conn->query("SELECT id_tecnico, nom FROM tecnico ORDER BY nom");
if ($res_tec) while ($r = $res_tec->fetch_assoc()) $tecnics[] = $r;

$id_tecnico = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id_tecnico === 0 && isset($_POST['id_tecnico_actual'])) {
    $id_tecnico = intval($_POST['id_tecnico_actual']);
}

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
         WHERE i.tecnico = $id_tecnico
         ORDER BY i.data DESC"
    );
    if ($result) while ($r = $result->fetch_assoc()) $incidencies[] = $r;
}

$conn->close();
?>

<main>

<h1>Àrea del Tècnic</h1>

<?php if ($missatge): ?><p><?= htmlspecialchars($missatge) ?></p><?php endif; ?>
<?php if ($error): ?><p><?= htmlspecialchars($error) ?></p><?php endif; ?>

<form method="GET" action="tecnic.php">
    <label for="id">Selecciona el teu nom:</label>
    <select id="id" name="id">
        <option value="">Tria</option>
        <?php foreach ($tecnics as $t): ?>
            <option value="<?= $t['id_tecnico'] ?>" <?= $id_tecnico == $t['id_tecnico'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($t['nom']) ?>
            </option>
        <?php endforeach; ?>
    </select>
    <button type="submit">Entrar</button>
</form>

<?php if ($id_tecnico > 0): ?>

<h2><?= htmlspecialchars($nom_tecnic) ?></h2>

<?php if (empty($incidencies)): ?>
    <p>No tens incidències assignades</p>
<?php else: ?>

<table border="2" cellpadding="9" cellspacing="10">
    <thead>
        <tr>
            <th>ID</th><th>Títol</th><th>Descripció</th><th>Departament</th>
            <th>Prioritat</th><th>Data</th><th>Estat actual</th><th>Canviar estat</th><th>Actuació</th>
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
        <td><?= htmlspecialchars($inc['prioritat'] ?? '—') ?></td>
        <td><?= date('d/m/Y H:i', strtotime($inc['data'])) ?></td>
        <td><?= $estat ?></td>
        <td>
            <form method="POST" action="tecnic.php?id=<?= $id_tecnico ?>">
                <input type="hidden" name="id_incidencia"     value="<?= $inc['id_incidencia'] ?>">
                <input type="hidden" name="id_tecnico_actual" value="<?= $id_tecnico ?>">
                <select name="estat">
                    <option value="Pendent"   <?= $estat === 'Pendent'   ? 'selected' : '' ?>>Pendent</option>
                    <option value="En procés" <?= $estat === 'En procés' ? 'selected' : '' ?>>En procés</option>
                    <option value="Resolta"   <?= $estat === 'Resolta'   ? 'selected' : '' ?>>Resolta</option>
                </select>
                <button type="submit">Guardar</button>
            </form>
        </td>
        <td>
            <a href="actuacion.php?id=<?= $inc['id_incidencia'] ?>"><button>Afegir actuació</button></a>
        </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<?php endif; ?>
<?php endif; ?>

<p><a href="index.php">Tornar</a></p>

</main>

<?php include 'footer.php'; ?>