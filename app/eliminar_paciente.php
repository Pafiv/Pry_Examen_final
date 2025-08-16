<?php
require_once '../conexion/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];

    try {
        $stmt = $pdo->prepare("DELETE FROM pacientes WHERE id=:id");
        $stmt->execute([':id'=>$id]);
        echo json_encode(['status'=>'ok','message'=>'Paciente eliminado correctamente']);
    } catch(PDOException $e) {
        echo json_encode(['status'=>'error','message'=>$e->getMessage()]);
    }
}
?>