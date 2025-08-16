<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../public/lib/bootstrap-5.3.5-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@magicbruno/swalstrap5@1.0.8/dist/css/swalstrap5_all.min.css">
    <title>Registrar Paciente</title>
</head>
<body>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h1 class="text-center mb-4">Registrar Nuevo Paciente</h1>
            
            <div class="card">
                <div class="card-body">
                    <form action="guardarPaciente.php" method="POST" id="formCrearUsuario">
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre completo *</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required 
                                   value="<?php echo isset($_GET['nombre']) ? htmlspecialchars($_GET['nombre']) : ''; ?>">
                        </div>
                        <div class="mb-3">
                            <label for="correo" class="form-label">Correo electrónico *</label>
                            <input type="email" class="form-control" id="correo" name="correo" required
                                   value="<?php echo isset($_GET['correo']) ? htmlspecialchars($_GET['correo']) : ''; ?>">
                        </div>
                        <div class="mb-3">
                            <label for="telefono" class="form-label">Teléfono</label>
                            <input type="tel" class="form-control" id="telefono" name="telefono" 
                                   value="<?php echo isset($_GET['telefono']) ? htmlspecialchars($_GET['telefono']) : ''; ?>">
                            <div class="form-text">Campo opcional</div>
                        </div>
                        <div class="mb-3">
                            <label for="fecha_nacimiento" class="form-label">Fecha de nacimiento *</label>
                            <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento" required
                                   value="<?php echo isset($_GET['fecha_nacimiento']) ? htmlspecialchars($_GET['fecha_nacimiento']) : ''; ?>">
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> 
                                Guardar Paciente
                            </button>
                            <a href="listar.php" class="btn btn-info">
                                <i class="fas fa-arrow-left"></i> Ver a la Lista
                            </a>
                            <a href="index.html" class="btn btn-secondary ">
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
    // Mostrar mensajes con SweetAlert si existen en la URL
    window.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const error = urlParams.get('error');
        
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
    });

    // Validación adicional del formulario
    document.getElementById('formCrearUsuario').addEventListener('submit', function(event) {
        const nombre = document.getElementById('nombre').value.trim();
        const correo = document.getElementById('correo').value.trim();
        
        if (nombre.length < 2) {
            event.preventDefault();
            Swal.fire({
                title: 'Error de Validación',
                text: 'El nombre debe tener al menos 2 caracteres',
                icon: 'warning',
                confirmButtonText: 'Entendido'
            });
            return false;
        }
        
        if (!correo.includes('@')) {
            event.preventDefault();
            Swal.fire({
                title: 'Error de Validación',
                text: 'Por favor ingresa un correo válido',
                icon: 'warning',
                confirmButtonText: 'Entendido'
            });
            return false;
        }
        
        // Mostrar indicador de envío
        const submitBtn = event.target.querySelector('button[type="submit"]');
        submitBtn.textContent = 'Creando...';
        submitBtn.disabled = true;
    });
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
