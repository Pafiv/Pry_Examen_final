<?php
require_once '../conexion/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $telefono = $_POST['telefono'] ?? null;
    $fecha = $_POST['fecha_nacimiento'];

    try {
        $stmt = $pdo->prepare("UPDATE pacientes SET nombre=:nombre, correo=:correo, telefono=:telefono, fecha_nacimiento=:fecha WHERE id=:id");
        $stmt->execute([
            ':nombre' => $nombre,
            ':correo' => $correo,
            ':telefono' => $telefono,
            ':fecha' => $fecha,
            ':id' => $id
        ]);
        echo json_encode(['status'=>'ok','message'=>'Paciente actualizado correctamente']);
    } catch(PDOException $e) {
        echo json_encode(['status'=>'error','message'=>$e->getMessage()]);
    }
}
