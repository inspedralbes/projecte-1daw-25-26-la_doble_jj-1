<?php require_once 'logger.php'; ?>

<?php include 'header.php'; ?>

<?php
date_default_timezone_set('Europe/Madrid');
require_once 'conexion.php';

$missatge = "";
$error    = "";

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id === 0) { header("Location: index.php"); exit; }

$inc = $conn->query("SELECT id_incidencia, titol FROM incidencia WHERE id_incidencia = $id")->fetch_assoc();
if (!$inc) { echo "<p>Incidència no trobada.</p>"; exit; }

$actuacions = [];
$res_act = $conn->query("SELECT descripcion, data, tiempo, visible FROM actuacio WHERE incidencia = $id ORDER BY data ASC");
if ($res_act) while ($a = $res_act->fetch_assoc()) $actuacions[] = $a;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $descripcion = trim($conn->real_escape_string($_POST['descripcion']));
    $tiempo      = intval($_POST['tiempo']);
    $visible     = isset($_POST['visible']) ? 1 : 0;

    if (empty($descripcion) || $tiempo <= 0) {
        $error = "La descripció i el temps són obligatoris.";
    } else {
        $sql = "INSERT INTO actuacio (descripcion, data, tiempo, incidencia, visible)
                VALUES ('$descripcion', NOW(), $tiempo, $id, $visible)";

        if ($conn->query($sql)) {
            $missatge = "Actuació registrada correctament.";
        } else {
            $error = "Error: " . $conn->error;
        }
    }
}

$conn->close();
?>

<main>

<h1>Afegir Actuació</h1>
<p>Incidència #<?= $inc['id_incidencia'] ?> — <?= htmlspecialchars($inc['titol'] ?? '—') ?></p>

<?php if ($missatge): ?><div class="alert alert-success" role="alert"><?= htmlspecialchars($missatge) ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger" role="alert"><?= htmlspecialchars($error) ?></div><?php endif; ?>


<?php if (!empty($actuacions)): ?>
<h2 class="mb-3">Actuacions realitzades</h2>
<div class="table-responsive mb-4">
<table class="table table-bordered table-hover align-middle">
    <caption>Historial d'actuacions de la incidència #<?= $id ?></caption>
    <thead class="table-primary">
        <tr>
            <th scope="col">Data</th>
            <th scope="col">Descripció</th>
            <th scope="col">Temps (min)</th>
            <th scope="col">Visible</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($actuacions as $a): ?>
    <tr>
        <td><?= date('d/m/Y H:i', strtotime($a['data'])) ?></td>
        <td><?= htmlspecialchars($a['descripcion']) ?></td>
        <td><?= $a['tiempo'] ?></td>
        <td><?= $a['visible'] ? 'Sí' : 'No' ?></td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>
<?php else: ?>
<p class="text-muted mb-4">Encara no hi ha actuacions per aquesta incidència.</p>
<?php endif; ?>


<h2 class="mb-3">Nova actuació</h2>
<form method="POST" action="actuacion.php?id=<?= $id ?>" onsubmit="return validar()" novalidate>

    <div class="mb-3">
        <label for="descripcion" class="form-label">Descripció *</label>
        <textarea id="descripcion" name="descripcion" class="form-control" rows="5"
                  aria-required="true" aria-describedby="comptador"><?= (isset($_POST['descripcion']) && $error) ? htmlspecialchars($_POST['descripcion']) : '' ?></textarea>
        <small id="comptador" class="form-text text-muted">0 caràcters (mínim 20)</small>
    </div>

    <div class="mb-3">
        <label for="tiempo" class="form-label">Temps (minuts) *</label>
        <input type="number" id="tiempo" name="tiempo" class="form-control" min="1"
               aria-required="true"
               value="<?= (isset($_POST['tiempo']) && $error) ? intval($_POST['tiempo']) : '' ?>">
    </div>

    <div class="mb-3 form-check">
        <input type="checkbox" id="visible" name="visible" class="form-check-input"
               <?= isset($_POST['visible']) ? 'checked' : '' ?>>
        <label for="visible" class="form-check-label">Visible per l'usuari</label>
    </div>

    <div id="error-js" class="alert alert-danger" role="alert" style="display:none"></div>

    <button type="submit" class="btn btn-primary">Guardar</button>
</form>

<a href="index.php" class="btn btn-secondary mt-2">Tornar</a>

</main>

<script>
document.getElementById('descripcion').addEventListener('input', function() {
    var total = this.value.length;
    var comptador = document.getElementById('comptador');
    comptador.innerText = total + ' caràcters (mínim 20)';
    comptador.style.color = total >= 20 ? 'green' : 'red';
});

function validar() {
    var descripcion = document.getElementById('descripcion').value;
    var tiempo = document.getElementById('tiempo').value;
    var error = document.getElementById('error-js');

    if (descripcion.length < 20) {
        error.innerText = 'La descripció ha de tenir almenys 20 caràcters.';
        error.style.display = 'block';
        return false;
    }
    if (tiempo == '' || tiempo <= 0) {
        error.innerText = 'Has d\'introduir el temps en minuts.';
        error.style.display = 'block';
        return false;
    }
    return true;
}
</script>

<?php include 'footer.php'; ?>