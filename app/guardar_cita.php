<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once '../conexion/db.php';

    $paciente_id = $_POST['paciente_id'];
    $medico_id = $_POST['medico_id'];
    $fecha = $_POST['fecha'];
    $hora_inicio = $_POST['hora_inicio'];
    $hora_fin = $_POST['hora_fin'];

    // Validar fecha y hora
    $ahora = date('Y-m-d H:i');
    if ("$fecha $hora_inicio" < $ahora) {
        header("Location: citas.php?error=" . urlencode("No se pueden crear citas en el pasado"));
        exit();
    }

    // Calcular duración en minutos
    $inicio = strtotime($hora_inicio);
    $fin = strtotime($hora_fin);
    $duracion = ($fin - $inicio) / 60;
    if ($duracion <= 0) {
        header("Location: citas.php?error=" . urlencode("La hora fin debe ser mayor a la hora inicio"));
        exit();
    }

    // Obtener tarifa del médico
    $stmt = $pdo->prepare("SELECT tarifa_por_hora FROM medicos WHERE id = ?");
    $stmt->execute([$medico_id]);
    $medico = $stmt->fetch(PDO::FETCH_ASSOC);
    $costo_total = ($duracion / 60) * $medico['tarifa_por_hora'];

    // Insertar cita
    $sql = "INSERT INTO citas (paciente_id, medico_id, fecha, hora_inicio, hora_fin, duracion, costo_total) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$paciente_id, $medico_id, $fecha, $hora_inicio, $hora_fin, $duracion, $costo_total]);

    header("Location: citas.php?success=" . urlencode("Cita registrada correctamente"));
}
?>
