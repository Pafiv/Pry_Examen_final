<?php
require_once '../conexion/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recibir datos
    $id = $_POST['id'] ?? '';
    $nombre = trim($_POST['nombre'] ?? '');
    $especialidad = trim($_POST['especialidad'] ?? '');
    $tarifa = trim($_POST['tarifa_por_hora'] ?? '');

    // Validación básica
    if (!$id || !$nombre || !$especialidad || !$tarifa) {
        echo json_encode(['status' => 'error', 'message' => 'Todos los campos son obligatorios']);
        exit;
    }

    if (!is_numeric($tarifa) || $tarifa < 0) {
        echo json_encode(['status' => 'error', 'message' => 'La tarifa debe ser un número positivo']);
        exit;
    }

    try {
        $sql = "UPDATE medicos SET nombre = :nombre, especialidad = :especialidad, tarifa_por_hora = :tarifa WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':especialidad', $especialidad);
        $stmt->bindParam(':tarifa', $tarifa);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        echo json_encode(['status' => 'ok', 'message' => 'Médico actualizado correctamente']);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error al actualizar: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Método no permitido']);
}
?>