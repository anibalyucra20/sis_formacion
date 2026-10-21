<?php
require_once __DIR__ . '/../../layouts/header.php';
?>

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between pb-3">
            <div>
                <h4 class="mb-1 font-weight-bold" style="color: #1e293b;">Editar Persona</h4>
                <p class="text-muted font-size-13 mb-0">Actualización de datos personales: <strong><?= htmlspecialchars($persona['nombres'] . ' ' . $persona['apellidos']) ?></strong></p>
            </div>
            <div>
                <a href="<?= BASE_URL ?>/admin/personas" class="btn btn-outline-info font-weight-bold" style="border-radius: 8px;">
                    <i class="fa-solid fa-arrow-left mr-1"></i> Volver a la Lista
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header bg-white py-3">
                <h5 class="card-title font-weight-bold mb-0" style="font-size: 15px;">
                    <i class="fa-solid fa-user-pen mr-2 text-primary"></i> Modificar Datos
                </h5>
            </div>
            <div class="card-body p-4">
                <form action="<?= BASE_URL ?>/admin/personas/guardar" method="POST">
                    <input type="hidden" name="id" value="<?= $persona['id'] ?>">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold font-size-13">Documento de Identidad (DNI) <span class="text-danger">*</span></label>
                            <input type="text" name="dni" class="form-control" value="<?= htmlspecialchars($persona['dni']) ?>" required maxlength="15">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold font-size-13">Teléfono / WhatsApp</label>
                            <input type="text" name="telefono" class="form-control" value="<?= htmlspecialchars($persona['telefono'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold font-size-13">Nombres <span class="text-danger">*</span></label>
                            <input type="text" name="nombres" class="form-control" value="<?= htmlspecialchars($persona['nombres']) ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold font-size-13">Apellidos <span class="text-danger">*</span></label>
                            <input type="text" name="apellidos" class="form-control" value="<?= htmlspecialchars($persona['apellidos']) ?>" required>
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <label class="form-label font-weight-bold font-size-13">Correo Electrónico</label>
                        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($persona['email'] ?? '') ?>">
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <a href="<?= BASE_URL ?>/consulta?dni=<?= urlencode($persona['dni']) ?>" target="_blank" class="btn btn-sm btn-outline-info font-weight-bold">
                            <i class="fa-solid fa-graduation-cap mr-1"></i> Ver Certificados (<?= $persona['total_cursos'] ?>)
                        </a>
                        <div style="gap: 10px;" class="d-flex">
                            <a href="<?= BASE_URL ?>/admin/personas" class="btn btn-outline-danger font-weight-bold px-4">Cancelar</a>
                            <button type="submit" class="btn btn-primary font-weight-bold px-4">
                                <i class="fa-solid fa-save mr-1"></i> Actualizar Persona
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