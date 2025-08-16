<?php
require_once '../conexion/db.php';

// Traer citas con JOIN a pacientes y médicos
$sql = "SELECT c.id, c.paciente_id, c.medico_id, c.fecha, c.hora_inicio, c.hora_fin, c.duracion, c.costo_total,
               p.nombre AS paciente, m.nombre AS medico, m.tarifa_por_hora
        FROM citas c
        JOIN pacientes p ON c.paciente_id = p.id
        JOIN medicos m ON c.medico_id = m.id
        ORDER BY c.fecha, c.hora_inicio";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$citas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Listado de Citas</title>
    <link rel="stylesheet" href="../public/lib/bootstrap-5.3.5-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@magicbruno/swalstrap5@1.0.8/dist/css/swalstrap5_all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
</head>
<body>
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Listado de Citas</h1>
        <a href="citas.php" class="btn btn-success">
            <i class="fas fa-plus"></i> Nueva Cita
        </a>
    </div>

    <input type="text" id="buscador" class="form-control mb-3" placeholder="Buscar por paciente o médico...">

    <table class="table table-striped table-bordered" id="tabla_citas">
        <thead>
            <tr>
                <th>Paciente</th>
                <th>Médico</th>
                <th>Fecha</th>
                <th>Hora Inicio</th>
                <th>Hora Fin</th>
                <th>Duración (min)</th>
                <th>Costo Total ($)</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($citas as $c): ?>
            <tr>
                <td><?= htmlspecialchars($c['paciente']) ?></td>
                <td><?= htmlspecialchars($c['medico']) ?></td>
                <td><?= $c['fecha'] ?></td>
                <td><?= $c['hora_inicio'] ?></td>
                <td><?= $c['hora_fin'] ?></td>
                <td><?= $c['duracion'] ?></td>
                <td><?= number_format($c['costo_total'],2) ?></td>
                <td>
                    <button class="btn btn-warning btn-sm btnEditar" 
                        data-id="<?= $c['id'] ?>"
                        data-paciente="<?= $c['paciente_id'] ?>"
                        data-medico="<?= $c['medico_id'] ?>"
                        data-fecha="<?= $c['fecha'] ?>"
                        data-inicio="<?= $c['hora_inicio'] ?>"
                        data-fin="<?= $c['hora_fin'] ?>"
                        data-tarifa="<?= $c['tarifa_por_hora'] ?>">
                        Editar
                    </button>
                    <button class="btn btn-danger btn-sm btnEliminar" data-id="<?= $c['id'] ?>">Eliminar</button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <a href="../index.html" class="btn btn-primary">Inicio</a>
</div>

<!-- Modal para editar cita -->
<div class="modal" id="modalCita" tabindex="-1" aria-labelledby="modalCitaLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalCitaLabel">Editar Cita</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <form id="formEditarCita">
        <div class="modal-body">
            <input type="hidden" id="citaId" name="id">
            <div class="mb-3">
                <label>Paciente</label>
                <select id="paciente" name="paciente_id" class="form-control" required>
                    <?php
                    $pacientes = $pdo->query("SELECT id, nombre FROM pacientes")->fetchAll(PDO::FETCH_ASSOC);
                    foreach($pacientes as $p){
                        echo "<option value='{$p['id']}'>{$p['nombre']}</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="mb-3">
                <label>Médico</label>
                <select id="medico" name="medico_id" class="form-control" required>
                    <?php
                    $medicos = $pdo->query("SELECT id, nombre, tarifa_por_hora FROM medicos")->fetchAll(PDO::FETCH_ASSOC);
                    foreach($medicos as $m){
                        echo "<option value='{$m['id']}' data-tarifa='{$m['tarifa_por_hora']}'>{$m['nombre']}</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="mb-3">
                <label>Fecha</label>
                <input type="date" id="fecha" name="fecha" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Hora Inicio</label>
                <input type="time" id="hora_inicio" name="hora_inicio" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Hora Fin</label>
                <input type="time" id="hora_fin" name="hora_fin" class="form-control" required>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="../public/lib/bootstrap-5.3.5-dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Buscador
document.getElementById('buscador').addEventListener('input', function(){
    const filtro = this.value.toLowerCase();
    document.querySelectorAll('#tabla_citas tbody tr').forEach(fila=>{
        const paciente = fila.cells[0].textContent.toLowerCase();
        const medico = fila.cells[1].textContent.toLowerCase();
        fila.style.display = (paciente.includes(filtro) || medico.includes(filtro)) ? '' : 'none';
    });
});

// Abrir modal para editar
document.querySelectorAll('.btnEditar').forEach(btn=>{
    btn.addEventListener('click', function(){
        document.getElementById('citaId').value = this.dataset.id;
        document.getElementById('paciente').value = this.dataset.paciente;
        document.getElementById('medico').value = this.dataset.medico;
        document.getElementById('fecha').value = this.dataset.fecha;
        document.getElementById('hora_inicio').value = this.dataset.inicio;
        document.getElementById('hora_fin').value = this.dataset.fin;

        const modal = new bootstrap.Modal(document.getElementById('modalCita'));
        modal.show();
    });
});

// Guardar cambios
document.getElementById('formEditarCita').addEventListener('submit', function(e){
    e.preventDefault();
    const formData = new FormData(this);
    fetch('editar_cita_backend.php', {
        method:'POST',
        body: formData
    })
    .then(r=>r.json())
    .then(data=>{
        if(data.status==='ok'){
            Swal.fire('Éxito', data.message, 'success').then(()=>location.reload());
        }else{
            Swal.fire('Error', data.message, 'error');
        }
    }).catch(err=>{
        Swal.fire('Error','No se pudo conectar con el servidor','error');
        console.error(err);
    });
});

// Eliminar cita
document.querySelectorAll('.btnEliminar').forEach(btn=>{
    btn.addEventListener('click', function(){
        const id = this.dataset.id;
        Swal.fire({
            title:'¿Estás seguro?',
            text:"¡No podrás revertir esto!",
            icon:'warning',
            showCancelButton:true,
            confirmButtonColor:'#d33',
            cancelButtonColor:'#3085d6',
            confirmButtonText:'Sí, eliminar',
            cancelButtonText:'Cancelar'
        }).then(result=>{
            if(result.isConfirmed){
                fetch('eliminar_cita_backend.php',{
                    method:'POST',
                    body:new URLSearchParams({id:id})
                }).then(r=>r.json()).then(data=>{
                    if(data.status==='ok'){
                        Swal.fire('Eliminado', data.message,'success').then(()=>location.reload());
                    }else{
                        Swal.fire('Error', data.message,'error');
                    }
                }).catch(err=>{
                    Swal.fire('Error','No se pudo conectar con el servidor','error');
                    console.error(err);
                });
            }
        });
    });
});
</script>
</body>
</html>
