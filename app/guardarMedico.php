<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        require_once '../conexion/db.php';

        // Validar que los datos estén presentes
        if (!isset($_POST['nombre']) || !isset($_POST['especialidad']) || !isset($_POST['tarifa_por_hora'])) {
            throw new Exception("Faltan datos requeridos: nombre, especialidad y tarifa por hora son obligatorios");
        }

        $nombre = trim($_POST['nombre']);
        $especialidad = trim($_POST['especialidad']);
        $tarifa = trim($_POST['tarifa_por_hora']);

        // Validaciones básicas
        if (empty($nombre)) {
            throw new Exception("El nombre es obligatorio");
        }

        if (empty($especialidad)) {
            throw new Exception("La especialidad es obligatoria");
        }

        if (empty($tarifa) || !is_numeric($tarifa) || $tarifa < 0) {
            throw new Exception("La tarifa por hora debe ser un número positivo");
        }

        // Verificar si el nombre y especialidad ya existen
        $sqlCheck = "SELECT id FROM medicos WHERE nombre = :nombre AND especialidad = :especialidad";
        $stmtCheck = $pdo->prepare($sqlCheck);
        $stmtCheck->bindParam(':nombre', $nombre);
        $stmtCheck->bindParam(':especialidad', $especialidad);
        $stmtCheck->execute();

        if ($stmtCheck->rowCount() > 0) {
            throw new Exception("Este médico con la misma especialidad ya está registrado");
        }

        // Insertar los datos en la base de datos
        $sql = "INSERT INTO medicos (nombre, especialidad, tarifa_por_hora) 
                VALUES (:nombre, :especialidad, :tarifa_por_hora)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':especialidad', $especialidad);
        $stmt->bindParam(':tarifa_por_hora', $tarifa);
        $stmt->execute();

        // Redirigir con éxito
        header("Location: medicos.php?success=Médico registrado correctamente");
        exit();

    } catch (Exception $e) {
        // En caso de error, redirigir con mensaje de error
        header("Location: medicos.php?error=" . urlencode($e->getMessage()));
        exit();
    }
} else {
    // Si no es POST, redirigir al formulario
    header("Location: medicos.php");
    exit();
}
?>
