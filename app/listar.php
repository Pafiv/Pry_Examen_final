<?php 
require_once '../conexion/db.php';

// Consultar pacientes
$sql = "SELECT id, nombre, correo, telefono, fecha_nacimiento 
        FROM pacientes";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$pacientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Lista de Pacientes</title>
    <link rel="stylesheet" href="../public/lib/bootstrap-5.3.5-dist/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@magicbruno/swalstrap5@1.0.8/dist/css/swalstrap5_all.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
</head>
<body>
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Lista de Pacientes</h1>
        <a href="pacientes.php" class="btn btn-success">
            <i class="fas fa-plus"></i> Nuevo Paciente
        </a>
    </div>

    <!-- Buscador -->
    <div class="mb-3">
        <input type="text" id="buscador" class="form-control" placeholder="Buscar por nombre o correo...">
    </div>

    <table class="table table-striped table-bordered" id="tabla_pacientes">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Correo</th>
                <th>Teléfono</th>
                <th>Fecha de Nacimiento</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($pacientes as $p): ?>
                <tr>
                    <td><?= htmlspecialchars($p['nombre']) ?></td>
                    <td><?= htmlspecialchars($p['correo']) ?></td>
                    <td><?= htmlspecialchars($p['telefono']) ?></td>
                    <td><?= htmlspecialchars($p['fecha_nacimiento']) ?></td>
                    <td>
                        <button type="button" class="btn btn-warning btn-sm btnEditar" 
                                data-id="<?= $p['id'] ?>" 
                                data-nombre="<?= htmlspecialchars($p['nombre']) ?>"
                                data-correo="<?= htmlspecialchars($p['correo']) ?>"
                                data-telefono="<?= htmlspecialchars($p['telefono']) ?>"
                                data-fecha="<?= htmlspecialchars($p['fecha_nacimiento']) ?>">
                            Editar
                        </button>
                        <button type="button" class="btn btn-danger btn-sm btnEliminar" 
                                data-id="<?= $p['id'] ?>">
                            Eliminar
                        </button>
                    </td>

                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <a href="../index.html" class="btn btn-primary">Inicio</a>
</div>

<!-- Modal para editar paciente -->
<div class="modal " id="modalPaciente" tabindex="-1" aria-labelledby="modalPacienteLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalPacienteLabel">Editar Paciente</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      
      <form id="formEditarPaciente">
        <div class="modal-body">
          <input type="hidden" id="pacienteId" name="id">
          <div class="mb-3">
            <label for="nombre" class="form-label">Nombre</label>
            <input type="text" class="form-control" id="nombre" name="nombre" required>
          </div>
          <div class="mb-3">
            <label for="correo" class="form-label">Correo</label>
            <input type="email" class="form-control" id="correo" name="correo" required>
          </div>
          <div class="mb-3">
            <label for="telefono" class="form-label">Teléfono</label>
            <input type="text" class="form-control" id="telefono" name="telefono">
          </div>
          <div class="mb-3">
            <label for="fecha_nacimiento" class="form-label">Fecha Nacimiento</label>
            <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento" required>
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
    // Buscador: filtra por nombre (col 0) o correo (col 1)
    document.getElementById('buscador').addEventListener('input', function() {
        const filtro = this.value.toLowerCase();
        const filas = document.querySelectorAll('#tabla_pacientes tbody tr');
        filas.forEach(fila => {
            const nombre = fila.cells[0].textContent.toLowerCase();
            const correo = fila.cells[1].textContent.toLowerCase();
            if (nombre.includes(filtro) || correo.includes(filtro)) {
                fila.style.display = '';
            } else {
                fila.style.display = 'none';
            }
        });
    });

    // Cargar datos al modal
    document.querySelectorAll('.btnEditar').forEach(boton => {
        boton.addEventListener('click', function () {
            document.getElementById('pacienteId').value = this.dataset.id;
            document.getElementById('nombre').value = this.dataset.nombre;
            document.getElementById('correo').value = this.dataset.correo;
            document.getElementById('telefono').value = this.dataset.telefono;
            document.getElementById('fecha_nacimiento').value = this.dataset.fecha;

            const modal = new bootstrap.Modal(document.getElementById('modalPaciente'));
            modal.show();
        });
    });

    // Guardar cambios
    document.getElementById('formEditarPaciente').addEventListener('submit', function (e) {
        e.preventDefault();

        const formData = new FormData(this);

        fetch('editar_paciente.php', {
            method: 'POST',
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            if (data.status === 'ok') {
                Swal.fire('Éxito', data.message, 'success');

                // Actualizar fila
                const id = formData.get('id');
                const fila = [...document.querySelectorAll('.btnEditar')]
                    .find(btn => btn.dataset.id === id)
                    .closest('tr');

                fila.cells[0].textContent = formData.get('nombre');
                fila.cells[1].textContent = formData.get('correo');
                fila.cells[2].textContent = formData.get('telefono');
                fila.cells[3].textContent = formData.get('fecha_nacimiento');

                bootstrap.Modal.getInstance(document.getElementById('modalPaciente')).hide();
            } else {
                Swal.fire('Error', data.message, 'error');
            }
        })
        .catch(err => {
            Swal.fire('Error', 'No se pudo conectar con el servidor', 'error');
            console.error(err);
        });
    });

    // Eliminar paciente
    // Cargar datos al modal para editar
document.querySelectorAll('.btnEditar').forEach(boton => {
    boton.addEventListener('click', function () {
        document.getElementById('pacienteId').value = this.dataset.id;
        document.getElementById('nombre').value = this.dataset.nombre;
        document.getElementById('correo').value = this.dataset.correo;
        document.getElementById('telefono').value = this.dataset.telefono;
        document.getElementById('fecha_nacimiento').value = this.dataset.fecha;

        const modal = new bootstrap.Modal(document.getElementById('modalPaciente'));
        modal.show();
    });
});

// Guardar cambios
document.getElementById('formEditarPaciente').addEventListener('submit', function (e) {
    e.preventDefault();
    const formData = new FormData(this);

    fetch('editar_paciente.php', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.status === 'ok') {
            Swal.fire('Éxito', data.message, 'success');
            const id = formData.get('id');
            const fila = [...document.querySelectorAll('.btnEditar')]
                .find(btn => btn.dataset.id === id)
                .closest('tr');
            fila.cells[0].textContent = formData.get('nombre');
            fila.cells[1].textContent = formData.get('correo');
            fila.cells[2].textContent = formData.get('telefono');
            fila.cells[3].textContent = formData.get('fecha_nacimiento');
            bootstrap.Modal.getInstance(document.getElementById('modalPaciente')).hide();
        } else {
            Swal.fire('Error', data.message, 'error');
        }
    })
    .catch(err => {
        Swal.fire('Error', 'No se pudo conectar con el servidor', 'error');
        console.error(err);
    });
});

// Eliminar paciente
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
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('eliminar_paciente.php', {
                    method: 'POST',
                    body: new URLSearchParams({ id: id })
                })
                .then(r => r.json())
                .then(data => {
                    if (data.status === 'ok') {
                        Swal.fire('Eliminado', data.message, 'success');
                        // Quitar fila de la tabla
                        this.closest('tr').remove();
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                })
                .catch(err => {
                    Swal.fire('Error', 'No se pudo conectar con el servidor', 'error');
                    console.error(err);
                });
            }
        });
    });
});

// Fix accesibilidad y foco en modalPaciente
const modalEl = document.getElementById('modalPaciente');

// Cuando el modal termina de abrirse
modalEl.addEventListener('shown.bs.modal', () => {
    // Asegura accesibilidad
    modalEl.setAttribute('aria-hidden', 'false');

    // Foco automático en el campo "Nombre"
    const inputNombre = modalEl.querySelector('#nombre');
    if (inputNombre) {
        inputNombre.focus();
    }
});

// Cuando el modal termina de cerrarse
modalEl.addEventListener('hidden.bs.modal', () => {
    // Limpieza de clases y backdrop si algo queda pegado
    document.body.classList.remove('modal-open');
    document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
});


</script>

</body>
</html>
