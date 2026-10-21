<?php
require_once __DIR__ . '/../../layouts/header.php';
?>

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between pb-3">
            <div>
                <h4 class="mb-1 font-weight-bold" style="color: #1e293b;">Mi Perfil</h4>
                <p class="text-muted font-size-13 mb-0">Gestión de datos personales y seguridad de su cuenta</p>
            </div>
            <div>
                <a href="<?= BASE_URL ?>/admin" class="btn btn-outline-secondary font-weight-bold" style="border-radius: 8px;">
                    <i class="fa-solid fa-arrow-left mr-1"></i> Ir al Dashboard
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-white py-3">
                <h5 class="card-title font-weight-bold mb-0" style="font-size: 15px;">
                    <i class="fa-solid fa-user-gear mr-2 text-primary"></i> Configuración de Cuenta
                </h5>
            </div>
            <div class="card-body p-4">
                <form action="<?= BASE_URL ?>/admin/usuarios/guardarPerfil" method="POST">
                    <h6 class="text-uppercase text-muted font-weight-bold font-size-12 mb-3">Información Personal</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold font-size-13">Nombres <span class="text-danger">*</span></label>
                            <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($usuario['nombre']) ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold font-size-13">Apellidos <span class="text-danger">*</span></label>
                            <input type="text" name="apellidos" class="form-control" value="<?= htmlspecialchars($usuario['apellidos']) ?>" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold font-size-13">Correo Electrónico <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($usuario['email']) ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold font-size-13">Teléfono</label>
                            <input type="text" name="telefono" class="form-control" value="<?= htmlspecialchars($usuario['telefono'] ?? '') ?>">
                        </div>
                    </div>

                    <hr class="my-4">

                    <h6 class="text-uppercase text-muted font-weight-bold font-size-12 mb-2">Cambiar Contraseña</h6>
                    <p class="text-muted font-size-12 mb-3">Complete esta sección únicamente si desea cambiar su clave de acceso.</p>

                    <div class="form-group mb-3">
                        <label class="form-label font-weight-bold font-size-13">Contraseña Actual</label>
                        <input type="password" name="password_actual" class="form-control" placeholder="Ingrese su clave actual si va a cambiarla">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold font-size-13">Nueva Contraseña</label>
                            <input type="password" name="password_nueva" class="form-control" placeholder="Mínimo 6 caracteres">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold font-size-13">Confirmar Nueva Contraseña</label>
                            <input type="password" name="password_confirm" class="form-control" placeholder="Repita la nueva contraseña">
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-primary font-weight-bold px-4">
                            <i class="fa-solid fa-save mr-1"></i> Guardar Cambios en Mi Perfil
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/../../layouts/footer.php';
?>
