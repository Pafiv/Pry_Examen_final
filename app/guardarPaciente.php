<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        require_once '../conexion/db.php';

        // Validar que los datos estén presentes
        if (!isset($_POST['nombre']) || !isset($_POST['correo']) || !isset($_POST['fecha_nacimiento'])) {
            throw new Exception("Faltan datos requeridos: nombre, correo y fecha de nacimiento son obligatorios");
        }

        $nombre = trim($_POST['nombre']);
        $correo = trim($_POST['correo']);
        $telefono = !empty($_POST['telefono']) ? trim($_POST['telefono']) : null;
        $fecha_nacimiento = $_POST['fecha_nacimiento']; // formato YYYY-MM-DD

        // Validaciones básicas
        if (empty($nombre)) {
            throw new Exception("El nombre es obligatorio");
        }
        
        if (empty($correo)) {
            throw new Exception("El correo es obligatorio");
        }
        
        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("El correo no tiene un formato válido");
        }

        if (empty($fecha_nacimiento)) {
            throw new Exception("La fecha de nacimiento es obligatoria");
        }

        // Verificar si el correo ya existe
        $sqlCheck = "SELECT id FROM pacientes WHERE correo = :correo";
        $stmtCheck = $pdo->prepare($sqlCheck);
        $stmtCheck->bindParam(':correo', $correo);
        $stmtCheck->execute();
        
        if ($stmtCheck->rowCount() > 0) {
            throw new Exception("El correo ya está registrado");
        }

        // Insertar los datos en la base de datos
        $sql = "INSERT INTO pacientes (nombre, correo, telefono, fecha_nacimiento) 
                VALUES (:nombre, :correo, :telefono, :fecha_nacimiento)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':correo', $correo);
        $stmt->bindParam(':telefono', $telefono);
        $stmt->bindParam(':fecha_nacimiento', $fecha_nacimiento);
        $stmt->execute();

        // Redirigir con éxito
        header("Location: pacientes.php?success=Paciente registrado correctamente");
        exit();

    } catch (Exception $e) {
        // En caso de error, redirigir con mensaje de error
        header("Location: pacientes.php?error=" . urlencode($e->getMessage()));
        exit();
    }
} else {
    // Si no es POST, redirigir al formulario
    header("Location: pacientes.php");
    exit();
}
?>
