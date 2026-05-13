<?php include 'header.php'; ?>
<?php
require_once 'conexion_mongo.php';

$filtre_data   = $_GET['data']   ?? '';
$filtre_pagina = $_GET['pagina'] ?? '';

$match = [];
if ($filtre_data) {
    $inici = new MongoDB\BSON\UTCDateTime(strtotime($filtre_data . ' 00:00:00') * 1000);
    $fi    = new MongoDB\BSON\UTCDateTime(strtotime($filtre_data . ' 23:59:59') * 1000);
    $match['timestamp'] = ['$gte' => $inici, '$lte' => $fi];
}
if ($filtre_pagina) {
    $match['url'] = ['$regex' => $filtre_pagina, '$options' => 'i'];
}

$matchStage = empty($match) ? [] : [['$match' => $match]];

$total = $logsCol->countDocuments($match ?: []);

$pagines = $logsCol->aggregate(array_merge($matchStage, [
    ['$group'  => ['_id' => '$url', 'visites' => ['$sum' => 1]]],
    ['$sort'   => ['visites' => -1]],
    ['$limit'  => 5],
]))->toArray();

$perDia = $logsCol->aggregate(array_merge($matchStage, [
    ['$group' => [
        '_id'     => ['$dateToString' => ['format' => '%Y-%m-%d', 'date' => '$timestamp']],
        'visites' => ['$sum' => 1],
    ]],
    ['$sort'  => ['_id' => 1]],
    ['$limit' => 7],
]))->toArray();

$dies    = array_map(fn($r) => $r['_id'],     $perDia);
$visites = array_map(fn($r) => $r['visites'], $perDia);

$ultims = $logsCol->find(
    $match ?: [],
    ['sort' => ['timestamp' => -1], 'limit' => 10]
)->toArray();
?>

<main>

<h1 class="mb-4">Estadístiques d'Accés</h1>

<form method="GET" action="estadisticas_acesso.php" class="d-flex gap-2 mb-4 flex-wrap">
    <div>
        <label for="data" class="form-label">Data</label>
        <input type="date" id="data" name="data" class="form-control"
               value="<?= htmlspecialchars($filtre_data) ?>">
    </div>
    <div>
        <label for="pagina" class="form-label">Pàgina</label>
        <input type="text" id="pagina" name="pagina" class="form-control"
               placeholder="Ex: administrador" value="<?= htmlspecialchars($filtre_pagina) ?>">
    </div>
    <div class="d-flex align-items-end gap-2">
        <button type="submit" class="btn btn-primary">Filtrar</button>
        <a href="estadisticas_acesso.php" class="btn btn-outline-secondary">Netejar</a>
    </div>
</form>

<p class="fs-5 mb-4"><strong>Total d'accessos:</strong> <?= $total ?></p>


<div style="max-width:600px; margin-bottom:2rem">
    <canvas id="graficDies"></canvas>
</div>


<h2 class="mb-3">Pàgines més visitades</h2>
<div class="table-responsive mb-4">
<table class="table table-bordered table-hover align-middle">
    <caption>Top 5 pàgines més visitades</caption>
    <thead class="table-primary">
        <tr>
            <th scope="col">URL</th>
            <th scope="col">Visites</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($pagines as $p): ?>
    <tr>
        <td><?= htmlspecialchars($p['_id']) ?></td>
        <td><?= $p['visites'] ?></td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>

<h2 class="mb-3">Últims 10 accessos</h2>
<div class="table-responsive">
<table class="table table-bordered table-hover align-middle">
    <caption>Registre dels últims accessos</caption>
    <thead class="table-primary">
        <tr>
            <th scope="col">URL</th>
            <th scope="col">Mètode</th>
            <th scope="col">IP</th>
            <th scope="col">Navegador</th>
            <th scope="col">Hora</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($ultims as $log): ?>
    <tr>
        <td><?= htmlspecialchars($log['url']) ?></td>
        <td><?= htmlspecialchars($log['metode']) ?></td>
        <td><?= htmlspecialchars($log['ip']) ?></td>
        <td style="max-width:200px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap">
            <?= htmlspecialchars($log['navegador']) ?>
        </td>
        <td><?= date('d/m/Y H:i:s', $log['timestamp']->toDateTime()->getTimestamp()) ?></td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>

<a href="administrador.php" class="btn btn-secondary mt-2">Tornar</a>

</main>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
const ctx = document.getElementById('graficDies').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: <?= json_encode($dies) ?>,
        datasets: [{
            label: 'Visites per dia',
            data: <?= json_encode($visites) ?>,
            borderColor: 'rgba(75, 192, 192, 1)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            borderWidth: 2,
            fill: true,
            tension: 0.3,
            pointRadius: 4
        }]
    },
    options: {
        responsive: true,
        scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
    }
});
</script>

<?php include 'footer.php'; ?>