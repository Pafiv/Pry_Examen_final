<?php 
require_once '../conexion/db.php';

// Consultar médicos
$sql = "SELECT id, nombre, especialidad, tarifa_por_hora FROM medicos";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$medicos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Lista de Médicos</title>
<link rel="stylesheet" href="../public/lib/bootstrap-5.3.5-dist/css/bootstrap.min.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@magicbruno/swalstrap5@1.0.8/dist/css/swalstrap5_all.min.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
</head>
<body>
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Lista de Médicos</h1>
        <a href="medicos.php" class="btn btn-success">
            <i class="fas fa-plus"></i> Nuevo Médico
        </a>
    </div>

    <!-- Buscador -->
    <div class="mb-3">
        <input type="text" id="buscador" class="form-control" placeholder="Buscar por nombre o especialidad...">
    </div>

    <table class="table table-striped table-bordered" id="tabla_medicos">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Especialidad</th>
                <th>Tarifa por Hora</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($medicos as $m): ?>
                <tr>
                    <td><?= htmlspecialchars($m['nombre']) ?></td>
                    <td><?= htmlspecialchars($m['especialidad']) ?></td>
                    <td><?= htmlspecialchars($m['tarifa_por_hora']) ?></td>
                    <td>
                        <button type="button" class="btn btn-warning btn-sm btnEditar" 
                                data-id="<?= $m['id'] ?>" 
                                data-nombre="<?= htmlspecialchars($m['nombre']) ?>"
                                data-especialidad="<?= htmlspecialchars($m['especialidad']) ?>"
                                data-tarifa="<?= htmlspecialchars($m['tarifa_por_hora']) ?>">
                            Editar
                        </button>
                        <button type="button" class="btn btn-danger btn-sm btnEliminar" 
                                data-id="<?= $m['id'] ?>">
                            Eliminar
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <a href="../index.html" class="btn btn-primary">Inicio</a>
</div>

<!-- Modal para editar médico -->
<div class="modal" id="modalMedico" tabindex="-1" aria-labelledby="modalMedicoLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalMedicoLabel">Editar Médico</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      
      <form id="formEditarMedico">
        <div class="modal-body">
          <input type="hidden" id="medicoId" name="id">
          <div class="mb-3">
            <label for="nombre" class="form-label">Nombre</label>
            <input type="text" class="form-control" id="nombre" name="nombre" required>
          </div>
          <div class="mb-3">
            <label for="especialidad" class="form-label">Especialidad</label>
            <input type="text" class="form-control" id="especialidad" name="especialidad" required>
          </div>
          <div class="mb-3">
            <label for="tarifa_por_hora" class="form-label">Tarifa por Hora</label>
            <input type="number" step="0.01" class="form-control" id="tarifa_por_hora" name="tarifa_por_hora" required>
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
document.getElementById('buscador').addEventListener('input', function() {
    const filtro = this.value.toLowerCase();
    const filas = document.querySelectorAll('#tabla_medicos tbody tr');
    filas.forEach(fila => {
        const nombre = fila.cells[0].textContent.toLowerCase();
        const especialidad = fila.cells[1].textContent.toLowerCase();
        fila.style.display = nombre.includes(filtro) || especialidad.includes(filtro) ? '' : 'none';
    });
});

document.querySelectorAll('.btnEditar').forEach(boton => {
    boton.addEventListener('click', function () {
        document.getElementById('medicoId').value = this.dataset.id;
        document.getElementById('nombre').value = this.dataset.nombre;
        document.getElementById('especialidad').value = this.dataset.especialidad;
        document.getElementById('tarifa_por_hora').value = this.dataset.tarifa;

        new bootstrap.Modal(document.getElementById('modalMedico')).show();
    });
});

document.getElementById('formEditarMedico').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);

    fetch('editar_medico.php', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.status === 'ok') {
            Swal.fire('Éxito', data.message, 'success');
            const id = formData.get('id');
            const fila = [...document.querySelectorAll('.btnEditar')]
                .find(btn => btn.dataset.id === id).closest('tr');
            fila.cells[0].textContent = formData.get('nombre');
            fila.cells[1].textContent = formData.get('especialidad');
            fila.cells[2].textContent = formData.get('tarifa_por_hora');
            bootstrap.Modal.getInstance(document.getElementById('modalMedico')).hide();
        } else {
            Swal.fire('Error', data.message, 'error');
        }
    })
    .catch(err => {
        Swal.fire('Error', 'No se pudo conectar con el servidor', 'error');
        console.error(err);
    });
});

document.querySelectorAll('.btnEliminar').forEach(boton => {
    boton.addEventListener('click', function () {
        const id = this.dataset.id;
        Swal.fire({
            title: '¿Estás seguro?',
            text: "¡No podrás revertir esto!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then(result => {
            if (result.isConfirmed) {
                fetch('eliminar_medico.php', {
                    method: 'POST',
                    body: new URLSearchParams({ id })
                })
                .then(r => r.json())
                .then(data => {
                    if (data.status === 'ok') {
                        Swal.fire('Eliminado', data.message, 'success');
                        this.closest('tr').remove();
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                });
            }
        });
    });
});

// Accesibilidad modal
const modalEl = document.getElementById('modalMedico');
modalEl.addEventListener('shown.bs.modal', () => {
    modalEl.setAttribute('aria-hidden', 'false');
    modalEl.querySelector('#nombre').focus();
});
modalEl.addEventListener('hidden.bs.modal', () => {
    document.body.classList.remove('modal-open');
    document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
});
</script>
</body>
</html>
