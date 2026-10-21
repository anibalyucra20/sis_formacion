<?php
require_once __DIR__ . '/../../layouts/header.php';
?>

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between pb-3">
            <div>
                <h4 class="mb-1 font-weight-bold" style="color: #1e293b;">Editar Usuario</h4>
                <p class="text-muted font-size-13 mb-0">Actualización de usuario: <strong><?= htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellidos']) ?></strong></p>
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
                    <i class="fa-solid fa-pen-to-square mr-2 text-primary"></i> Modificar Datos de Acceso
                </h5>
            </div>
            <div class="card-body p-4">
                <form action="<?= BASE_URL ?>/admin/usuarios/guardar" method="POST">
                    <input type="hidden" name="id" value="<?= $usuario['id'] ?>">

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
                            <label class="form-label font-weight-bold font-size-13">Correo Electrónico (Login) <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($usuario['email']) ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold font-size-13">Teléfono / Celular</label>
                            <input type="text" name="telefono" class="form-control" value="<?= htmlspecialchars($usuario['telefono'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold font-size-13">Nueva Contraseña (Opcional)</label>
                            <input type="password" name="password" class="form-control" placeholder="Dejar en blanco para no cambiarla">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold font-size-13">Estado</label>
                            <select name="activo" class="form-control">
                                <option value="1" <?= $usuario['activo'] == 1 ? 'selected' : '' ?>>Activo (Habilitado)</option>
                                <option value="0" <?= $usuario['activo'] == 0 ? 'selected' : '' ?>>Inactivo (Deshabilitado)</option>
                            </select>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <a href="<?= BASE_URL ?>/admin/usuarios/enviarReset/<?= $usuario['id'] ?>" class="btn btn-outline-warning btn-sm font-weight-bold">
                            <i class="fa-solid fa-key mr-1"></i> Enviar Enlace de Restablecimiento por Correo
                        </a>
                        <div style="gap: 10px;" class="d-flex">
                            <a href="<?= BASE_URL ?>/admin/usuarios" class="btn btn-outline-secondary font-weight-bold px-4">Cancelar</a>
                            <button type="submit" class="btn btn-primary font-weight-bold px-4">
                                <i class="fa-solid fa-save mr-1"></i> Guardar Cambios
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/../../layouts/footer.php';
?>
