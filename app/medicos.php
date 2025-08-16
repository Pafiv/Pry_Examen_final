<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../public/lib/bootstrap-5.3.5-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@magicbruno/swalstrap5@1.0.8/dist/css/swalstrap5_all.min.css">
    <title>Registrar Médico</title>
</head>
<body>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h1 class="text-center mb-4">Registrar Nuevo Médico</h1>
            
            <div class="card">
                <div class="card-body">
                    <form action="guardarMedico.php" method="POST" id="formCrearMedico">
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre completo *</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required 
                                   value="<?php echo isset($_GET['nombre']) ? htmlspecialchars($_GET['nombre']) : ''; ?>">
                        </div>
                        <div class="mb-3">
                            <label for="especialidad" class="form-label">Especialidad *</label>
                            <input type="text" class="form-control" id="especialidad" name="especialidad" required
                                   value="<?php echo isset($_GET['especialidad']) ? htmlspecialchars($_GET['especialidad']) : ''; ?>">
                        </div>
                        <div class="mb-3">
                            <label for="tarifa_por_hora" class="form-label">Tarifa por hora (USD) *</label>
                            <input type="number" step="0.01" class="form-control" id="tarifa_por_hora" name="tarifa_por_hora" required
                                   value="<?php echo isset($_GET['tarifa_por_hora']) ? htmlspecialchars($_GET['tarifa_por_hora']) : ''; ?>">
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> 
                                Guardar Médico
                            </button>
                            <a href="listarMedicos.php" class="btn btn-info">
                                <i class="fas fa-arrow-left"></i> Ver Lista de Médicos
                            </a>
                            <a href="index.html" class="btn btn-secondary">
                                <i class="fas fa-home"></i> Volver al Inicio
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="../public/lib/bootstrap-5.3.5-dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@magicbruno/swalstrap5@1.0.8/dist/js/swalstrap5_all.min.js"></script>
<script>
    // Validación adicional del formulario
    document.getElementById('formCrearMedico').addEventListener('submit', function(event) {
        const nombre = document.getElementById('nombre').value.trim();
        const especialidad = document.getElementById('especialidad').value.trim();
        const tarifa = document.getElementById('tarifa_por_hora').value.trim();
        
        if (nombre.length < 3) {
            event.preventDefault();
            Swal.fire({
                title: 'Error de Validación',
                text: 'El nombre debe tener al menos 3 caracteres',
                icon: 'warning',
                confirmButtonText: 'Entendido'
            });
            return false;
        }

        if (especialidad.length < 3) {
            event.preventDefault();
            Swal.fire({
                title: 'Error de Validación',
                text: 'La especialidad debe tener al menos 3 caracteres',
                icon: 'warning',
                confirmButtonText: 'Entendido'
            });
            return false;
        }

        if (isNaN(tarifa) || tarifa <= 0) {
            event.preventDefault();
            Swal.fire({
                title: 'Error de Validación',
                text: 'La tarifa debe ser un número mayor a 0',
                icon: 'warning',
                confirmButtonText: 'Entendido'
            });
            return false;
        }
        
        // Mostrar indicador de envío
        const submitBtn = event.target.querySelector('button[type="submit"]');
        submitBtn.textContent = 'Guardando...';
        submitBtn.disabled = true;
    });

    // Mensajes de éxito o error desde la URL
    window.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const error = urlParams.get('error');
        const success = urlParams.get('success');
        
        if (error) {
            Swal.fire({
                title: 'Error',
                text: error,
                icon: 'error',
                confirmButtonText: 'Entendido'
            }).then(() => {
                window.history.replaceState({}, document.title, window.location.pathname);
            });
        }

        if (success) {
            Swal.fire({
                title: 'Éxito',
                text: success,
                icon: 'success',
                confirmButtonText: 'Perfecto'
            }).then(() => {
                window.history.replaceState({}, document.title, window.location.pathname);
            });
        }
    });
</script>
</body>
</html>
