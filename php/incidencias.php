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

<style>
    .volver {
        padding: 0.5rem; 
        text-align: center;
        text-decoration: none; border: 1px solid #535757; border-radius: 6px;
        color: #1a1a1a; 
        background : #2780e3;
    }
</style>

<main>

<h1>Registrar Nova Incidència</h1>

<?php if ($missatge): ?>
    <p><?= $missatge ?>  Guarda aquest número per consultar l'estat.</p>
<?php endif; ?>

<?php if ($error): ?>
    <p><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form method="POST" action="incidencias.php" onsubmit="return validar()">
    <p>
        <label for="titol">Títol</label><br>
        <input type="text" id="titol" name="titol" maxlength="150"
               value="<?= (isset($_POST['titol']) && $error) ? htmlspecialchars($_POST['titol']) : '' ?>">
    </p>
    <p>
        <label for="departamento">Departament</label><br>
        <select id="departamento" name="departamento">
            <option value="">Selecciona</option>
            <?php foreach ($departaments as $d): ?>
                <option value="<?= $d['id_departamento'] ?>"
                    <?= (isset($_POST['departamento']) && $_POST['departamento'] == $d['id_departamento'] && $error) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($d['nom']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </p>
    <p>
        <label for="descripcion">Descripció</label><br>
        <textarea id="descripcion" name="descripcion" rows="5" cols="40"><?= (isset($_POST['descripcion']) && $error) ? htmlspecialchars($_POST['descripcion']) : '' ?></textarea>
    </p>
    <p id="error-js" style="color:red; display:none"></p>
    <button type="submit" class="btn btn-primary">Buscar</button>
</form>

<div>
    <a href="index.php" class="btn btn-secondary mt-2">Tornar</a>
</div>

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