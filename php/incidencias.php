<?php require_once 'logger.php'; ?>

<?php include 'header.php'; ?>

<?php
date_default_timezone_set('Europe/Madrid');
require_once 'conexion.php';

$error    = "";
$missatge = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titol        = trim($conn->real_escape_string($_POST['titol']));
    $descripcion  = trim($conn->real_escape_string($_POST['descripcion']));
    $departamento = intval($_POST['departamento']);

    if (empty($titol) || empty($descripcion) || $departamento === 0) {
        $error = "Tots els camps són obligatoris.";
    } else {
        $sql = "INSERT INTO incidencia (titol, descripcion, data, departamento)
                VALUES ('$titol', '$descripcion', NOW(), $departamento)";

        if ($conn->query($sql)) {
            $missatge = "Incidència registrada. ID: " . $conn->insert_id;
        } else {
            $error = "Error: " . $conn->error;
        }
    }
}

$departaments = [];
$res = $conn->query("SELECT id_departamento, nom FROM departamento ORDER BY nom");
if ($res) while ($r = $res->fetch_assoc()) $departaments[] = $r;

$conn->close();
?>

<main>

<h1>Registrar Nova Incidència</h1>

<?php if ($missatge): ?>
    <div class="alert alert-success" role="alert"><?= $missatge ?> — Guarda aquest número per consultar l'estat.</div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="alert alert-danger" role="alert"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<form method="POST" action="incidencias.php" onsubmit="return validar()" novalidate>

    <div class="mb-3">
        <label for="titol" class="form-label">Títol *</label>
        <input type="text" id="titol" name="titol" class="form-control" maxlength="150"
               value="<?= (isset($_POST['titol']) && $error) ? htmlspecialchars($_POST['titol']) : '' ?>"
               aria-required="true">
    </div>

    <div class="mb-3">
        <label for="departamento" class="form-label">Departament *</label>
        <select id="departamento" name="departamento" class="form-select" aria-required="true">
            <option value="">Selecciona un departament</option>
            <?php foreach ($departaments as $d): ?>
                <option value="<?= $d['id_departamento'] ?>"
                    <?= (isset($_POST['departamento']) && $_POST['departamento'] == $d['id_departamento'] && $error) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($d['nom']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="mb-3">
        <label for="descripcion" class="form-label">Descripció *</label>
        <textarea id="descripcion" name="descripcion" class="form-control" rows="5" aria-required="true"><?= (isset($_POST['descripcion']) && $error) ? htmlspecialchars($_POST['descripcion']) : '' ?></textarea>
    </div>

    <div id="error-js" class="alert alert-danger" role="alert" style="display:none"></div>

    <button type="submit" class="btn btn-primary">Enviar</button>
</form>

<a href="index.php" class="btn btn-secondary mt-2">Tornar</a>

</main>

<script>
function validar() {
    var titol = document.getElementById('titol').value;
    var departamento = document.getElementById('departamento').value;
    var descripcion = document.getElementById('descripcion').value;
    var error = document.getElementById('error-js');

    if (titol == '') {
        error.innerText = 'El títol no pot estar buit.';
        error.style.display = 'block';
        return false;
    }
    if (departamento == '') {
        error.innerText = 'Has de seleccionar un departament.';
        error.style.display = 'block';
        return false;
    }
    if (descripcion == '') {
        error.innerText = 'La descripció no pot estar buida.';
        error.style.display = 'block';
        return false;
    }
    return true;
}
</script>

<?php include 'footer.php'; ?>