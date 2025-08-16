<?php
require_once '../conexion/db.php';

// Contar pacientes
$stmt = $pdo->query("SELECT COUNT(*) AS total FROM pacientes");
$totalPacientes = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Contar medicos
$stmt = $pdo->query("SELECT COUNT(*) AS total FROM medicos");
$totalMedicos = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Contar citas programadas
$stmt = $pdo->query("SELECT COUNT(*) AS total FROM citas WHERE estado='programada'");
$totalCitas = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Obtener próximas 5 citas
$stmt = $pdo->query("
    SELECT c.id, p.nombre AS paciente, m.nombre AS medico, c.fecha, c.hora_inicio, c.hora_fin, c.costo_total
    FROM citas c
    JOIN pacientes p ON c.paciente_id = p.id
    JOIN medicos m ON c.medico_id = m.id
    WHERE c.fecha >= CURDATE()
    ORDER BY c.fecha, c.hora_inicio
    LIMIT 5
");
$proximasCitas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard - Sistema Médico</title>
<link href="../public/lib/bootstrap-5.3.5-dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<div class="container py-5">
    <h1 class="mb-4">Dashboard del Sistema Médico</h1>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card text-white bg-primary mb-3">
                <div class="card-body">
                    <h5 class="card-title">Pacientes</h5>
                    <p class="card-text fs-3"><?= $totalPacientes ?></p>
                    <a href="listar.php" class="btn btn-light btn-sm">Ver lista</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-success mb-3">
                <div class="card-body">
                    <h5 class="card-title">Médicos</h5>
                    <p class="card-text fs-3"><?= $totalMedicos ?></p>
                    <a href="listarMedicos.php" class="btn btn-light btn-sm">Ver lista</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-warning mb-3">
                <div class="card-body">
                    <h5 class="card-title">Citas Programadas</h5>
                    <p class="card-text fs-3"><?= $totalCitas ?></p>
                    <a href="listar_citas.php" class="btn btn-light btn-sm">Ver lista</a>
                </div>
            </div>
        </div>
    </div>

    <h3>Próximas Citas</h3>
    <table class="table table-bordered mb-4">
        <thead>
            <tr>
                <th>Paciente</th>
                <th>Médico</th>
                <th>Fecha</th>
                <th>Hora Inicio</th>
                <th>Hora Fin</th>
                <th>Costo Total</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($proximasCitas as $cita): ?>
            <tr>
                <td><?= htmlspecialchars($cita['paciente']) ?></td>
                <td><?= htmlspecialchars($cita['medico']) ?></td>
                <td><?= $cita['fecha'] ?></td>
                <td><?= $cita['hora_inicio'] ?></td>
                <td><?= $cita['hora_fin'] ?></td>
                <td>$<?= number_format($cita['costo_total'],2) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h3>Gráfico de Citas por Médico</h3>
    <canvas id="citasChart" height="100"></canvas>
</div>

<script>
<?php
// Preparar datos para Chart.js
$stmt = $pdo->query("
    SELECT m.nombre, COUNT(c.id) AS total
    FROM medicos m
    LEFT JOIN citas c ON c.medico_id = m.id
    GROUP BY m.id
");
$datosChart = $stmt->fetchAll(PDO::FETCH_ASSOC);
$medicos = json_encode(array_column($datosChart, 'nombre'));
$totalCitas = json_encode(array_column($datosChart, 'total'));
?>
const ctx = document.getElementById('citasChart').getContext('2d');
const citasChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?= $medicos ?>,
        datasets: [{
            label: 'Número de Citas',
            data: <?= $totalCitas ?>,
            backgroundColor: 'rgba(54, 162, 235, 0.6)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive:true,
        scales: {
            y: {
                beginAtZero:true,
                precision:0
            }
        }
    }
});
</script>

<script src="../public/lib/bootstrap-5.3.5-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
