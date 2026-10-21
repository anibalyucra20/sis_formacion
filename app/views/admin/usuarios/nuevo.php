<?php
require_once __DIR__ . '/../../layouts/header.php';
?>

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between pb-3">
            <div>
                <h4 class="mb-1 font-weight-bold" style="color: #1e293b;">Nuevo Usuario Administrador</h4>
                <p class="text-muted font-size-13 mb-0">Creación de credenciales de acceso al sistema</p>
            </div>
            <div>
                <a href="<?= BASE_URL ?>/admin/usuarios" class="btn btn-outline-secondary font-weight-bold" style="border-radius: 8px;">
                    <i class="fa-solid fa-arrow-left mr-1"></i> Volver a la Lista
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
                    <i class="fa-solid fa-shield-halved mr-2 text-primary"></i> Datos del Nuevo Administrador
                </h5>
            </div>
            <div class="card-body p-4">
                <form action="<?= BASE_URL ?>/admin/usuarios/guardar" method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold font-size-13">Nombres <span class="text-danger">*</span></label>
                            <input type="text" name="nombre" class="form-control" placeholder="Ej: Roberto" required autofocus>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold font-size-13">Apellidos <span class="text-danger">*</span></label>
                            <input type="text" name="apellidos" class="form-control" placeholder="Ej: Mendoza Quispe" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold font-size-13">Correo Electrónico (Login) <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" placeholder="usuario@certificados.com" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold font-size-13">Teléfono / Celular</label>
                            <input type="text" name="telefono" class="form-control" placeholder="Ej: 987654321">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold font-size-13">Contraseña Inicial <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control" placeholder="Mínimo 6 caracteres" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold font-size-13">Estado</label>
                            <select name="activo" class="form-control">
                                <option value="1" selected>Activo (Habilitado)</option>
                                <option value="0">Inactivo (Deshabilitado)</option>
                            </select>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end" style="gap: 10px;">
                        <a href="<?= BASE_URL ?>/admin/usuarios" class="btn btn-outline-secondary font-weight-bold px-4">Cancelar</a>
                        <button type="submit" class="btn btn-primary font-weight-bold px-4">
                            <i class="fa-solid fa-save mr-1"></i> Crear Usuario
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