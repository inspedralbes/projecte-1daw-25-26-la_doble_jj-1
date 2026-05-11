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

<?php if ($missatge): ?><p><?= htmlspecialchars($missatge) ?></p><?php endif; ?>
<?php if ($error): ?><p><?= htmlspecialchars($error) ?></p><?php endif; ?>

<form method="POST" action="actuacion.php?id=<?= $id ?>" onsubmit="return validar()">
    <p>
        <label for="descripcion">Descripció</label><br>
        <textarea id="descripcion" name="descripcion" rows="5" cols="40"><?= (isset($_POST['descripcion']) && $error) ? htmlspecialchars($_POST['descripcion']) : '' ?></textarea>
        <br><small id="contador" style="color:gray">0 caràcters (mínim 20)</small>
    </p>
    <p>
        <label for="tiempo">Temps (minuts)</label><br>
        <input type="number" id="tiempo" name="tiempo" min="1"
               value="<?= (isset($_POST['tiempo']) && $error) ? intval($_POST['tiempo']) : '' ?>">
    </p>
    <p>
        <input type="checkbox" id="visible" name="visible" <?= isset($_POST['visible']) ? 'checked' : '' ?>>
        <label for="visible">Visible per l'usuari</label>
    </p>
    <p id="error-js" style="color:red; display:none"></p>
    <button type="submit" class="btn btn-primary">Guardar</button>
</form>

<a href="index.php" class="btn btn-secondary mt-2">Tornar</a>

</main>

<script>
document.getElementById('descripcion').addEventListener('input', function() {
    var total = this.value.length;
    var contador = document.getElementById('contador');
    contador.innerText = total + ' caràcters (mínim 20)';
    if (total >= 20) {
        contador.style.color = 'green';
    } else {
        contador.style.color = 'red';
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