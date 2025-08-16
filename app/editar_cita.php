<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        require_once '../conexion/db.php';

        // Recibir datos
        $id = $_POST['id'];
        $paciente_id = $_POST['paciente_id'];
        $medico_id = $_POST['medico_id'];
        $fecha = $_POST['fecha'];
        $hora_inicio = $_POST['hora_inicio'];
        $hora_fin = $_POST['hora_fin'];

        // Validaciones
        if(empty($id) || empty($paciente_id) || empty($medico_id) || empty($fecha) || empty($hora_inicio) || empty($hora_fin)){
            throw new Exception("Todos los campos son obligatorios");
        }

        $fechaHoraInicio = new DateTime("$fecha $hora_inicio");
        $fechaHoraFin = new DateTime("$fecha $hora_fin");
        $ahora = new DateTime();

        if($fechaHoraInicio < $ahora){
            throw new Exception("No se puede programar una cita en el pasado");
        }

        if($fechaHoraFin <= $fechaHoraInicio){
            throw new Exception("La hora de fin debe ser mayor a la hora de inicio");
        }

        // Calcular duración en minutos
        $duracion = ($fechaHoraFin->getTimestamp() - $fechaHoraInicio->getTimestamp()) / 60;

        // Obtener tarifa del médico
        $stmtMed = $pdo->prepare("SELECT tarifa_por_hora FROM medicos WHERE id = :id");
        $stmtMed->execute([':id'=>$medico_id]);
        $medico = $stmtMed->fetch(PDO::FETCH_ASSOC);
        if(!$medico){
            throw new Exception("Médico no encontrado");
        }

        $costo_total = ($duracion / 60) * $medico['tarifa_por_hora'];

        // Actualizar la cita
        $sql = "UPDATE citas SET paciente_id=:paciente_id, medico_id=:medico_id, fecha=:fecha,
                hora_inicio=:hora_inicio, hora_fin=:hora_fin, duracion=:duracion, costo_total=:costo_total
                WHERE id=:id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':paciente_id'=>$paciente_id,
            ':medico_id'=>$medico_id,
            ':fecha'=>$fecha,
            ':hora_inicio'=>$hora_inicio,
            ':hora_fin'=>$hora_fin,
            ':duracion'=>$duracion,
            ':costo_total'=>$costo_total,
            ':id'=>$id
        ]);

        echo json_encode(['status'=>'ok', 'message'=>'Cita actualizada correctamente']);
    } catch(Exception $e){
        echo json_encode(['status'=>'error', 'message'=>$e->getMessage()]);
    }
} else {
    header("HTTP/1.1 405 Method Not Allowed");
    exit();
}
?>
