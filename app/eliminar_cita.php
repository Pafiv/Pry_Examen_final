<?php
if($_SERVER['REQUEST_METHOD']==='POST'){
    try{
        require_once '../conexion/db.php';

        $id = $_POST['id'] ?? null;
        if(!$id){
            throw new Exception("ID de la cita no proporcionado");
        }

        $stmt = $pdo->prepare("DELETE FROM citas WHERE id=:id");
        $stmt->execute([':id'=>$id]);

        echo json_encode(['status'=>'ok','message'=>'Cita eliminada correctamente']);
    }catch(Exception $e){
        echo json_encode(['status'=>'error','message'=>$e->getMessage()]);
    }
}else{
    header("HTTP/1.1 405 Method Not Allowed");
    exit();
}
?>
