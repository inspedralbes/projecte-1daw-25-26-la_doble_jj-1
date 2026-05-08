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

<form method="POST" action="actuacion.php?id=<?= $id ?>">

    <p>
        <label for="descripcion">Descripció</label><br>
        <textarea id="descripcion" name="descripcion" rows="5" cols="40"><?= (isset($_POST['descripcion']) && $error) ? htmlspecialchars($_POST['descripcion']) : '' ?></textarea>
    </p>

    <p>
        <label for="tiempo">Temps (minuts)</label><br>
        <input type="number" id="tiempo" name="tiempo" min="1"
               value="<?= (isset($_POST['tiempo']) && $error) ? intval($_POST['tiempo']) : '' ?>">
    </p>

    <p>
        <input type="checkbox" id="visible" name="visible" <?= (isset($_POST['visible'])) ? 'checked' : '' ?>>
        <label for="visible">Visible per l'usuari</label>
    </p>

    <p><button type="submit">Guardar</button></p>

</form>

<p><a href="index.php">Tornar</a></p>

</main>

</body>
</html>