<?php
require_once '../conexion/db.php';

// Traer pacientes y médicos
$pacientes = $pdo->query("SELECT id, nombre FROM pacientes")->fetchAll(PDO::FETCH_ASSOC);
$medicos = $pdo->query("SELECT id, nombre, tarifa_por_hora FROM medicos")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Citas</title>
    <link rel="stylesheet" href="../public/lib/bootstrap-5.3.5-dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container py-5">
    <h1 class="mb-4">Registrar Nueva Cita</h1>
    <form action="guardar_cita.php" method="POST" id="formCita">
        <div class="mb-3">
            <label>Paciente </label>
            <select name="paciente_id" class="form-control" required>
                <option value="">Selecciona un paciente</option>
                <?php foreach($pacientes as $p): ?>
                    <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label>Médico </label>
            <select name="medico_id" class="form-control" required>
                <option value="">Selecciona un médico</option>
                <?php foreach($medicos as $m): ?>
                    <option value="<?= $m['id'] ?>" data-tarifa="<?= $m['tarifa_por_hora'] ?>">
                        <?= htmlspecialchars($m['nombre']) ?> (<?= $m['tarifa_por_hora'] ?>$/h)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label>Fecha </label>
            <input type="date" name="fecha" class="form-control" min="2025-08-16" required>
        </div>
        <div class="mb-3">
            <label>Hora inicio </label>
            <input type="time" name="hora_inicio" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Hora fin *</label>
            <input type="time" name="hora_fin" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Registrar Cita</button>
        <a href="./listar_citas.php" class="btn btn-info">
        <i class="fas fa-arrow-left"></i> Ver a la Lista</a>
                     <a href="../index.html" class="btn btn-secondary ">
                                <i class="fas fa-home"></i> Volver al Inicio
                            </a>
        
    </form>
</div>

<script>
document.getElementById('formCita').addEventListener('submit', function(e){
    const fecha = this.fecha.value;
    const hoy = new Date().toISOString().split('T')[0];
    if(fecha < hoy){
        e.preventDefault();
        alert("No se puede seleccionar una fecha pasada.");
    }
});
</script>
<script src="../public/lib/bootstrap-5.3.5-dist/js/bootstrap.bundle.min.js"></script>
    
</script>
</body>
</html>