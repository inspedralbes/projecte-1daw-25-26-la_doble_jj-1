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
    if (total >= 20) {
        comptador.style.color = 'green';
    } else {
        comptador.style.color = 'red';
    }
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