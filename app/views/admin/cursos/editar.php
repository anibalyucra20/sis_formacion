<?php
require_once __DIR__ . '/../../layouts/header.php';
?>

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between pb-3">
            <div>
                <h4 class="mb-1 font-weight-bold" style="color: #1e293b;">Editar Curso</h4>
                <p class="text-muted font-size-13 mb-0">Actualización de información del curso: <strong><?= htmlspecialchars($curso['codigo']) ?></strong></p>
            </div>
            <div>
                <a href="<?= BASE_URL ?>/admin/cursos" class="btn btn-outline-info font-weight-bold" style="border-radius: 8px;">
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
                    <i class="fa-solid fa-pen-to-square mr-2 text-primary"></i> Modificar Datos del Curso
                </h5>
            </div>
            <div class="card-body p-4">
                <form action="<?= BASE_URL ?>/admin/cursos/guardar" method="POST">
                    <input type="hidden" name="id" value="<?= $curso['id'] ?>">

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label font-weight-bold font-size-13">Código del Curso <span class="text-danger">*</span></label>
                            <input type="text" name="codigo" class="form-control" value="<?= htmlspecialchars($curso['codigo']) ?>" required style="text-transform: uppercase;">
                        </div>
                        <div class="col-md-8 mb-3">
                            <label class="form-label font-weight-bold font-size-13">Nombre Completo del Curso <span class="text-danger">*</span></label>
                            <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($curso['nombre']) ?>" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label font-weight-bold font-size-13">Horas Académicas <span class="text-danger">*</span></label>
                            <input type="number" name="horas_academicas" class="form-control" value="<?= $curso['horas_academicas'] ?>" min="1" max="1000" required>
                        </div>
                        <div class="col-md-5 mb-3">
                            <label class="form-label font-weight-bold font-size-13">Instructor / Ponente</label>
                            <input type="text" name="instructor" class="form-control" value="<?= htmlspecialchars($curso['instructor'] ?? '') ?>">
                        </div>
                        <div class="col-md-2 mb-3">
                            <label class="form-label font-weight-bold font-size-13">Modalidad</label>
                            <select name="modalidad" class="form-control">
                                <option value="Virtual" <?= $curso['modalidad'] === 'Virtual' ? 'selected' : '' ?>>Virtual</option>
                                <option value="Presencial" <?= $curso['modalidad'] === 'Presencial' ? 'selected' : '' ?>>Presencial</option>
                                <option value="Semipresencial" <?= $curso['modalidad'] === 'Semipresencial' ? 'selected' : '' ?>>Semipresencial</option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label class="form-label font-weight-bold font-size-13">Estado</label>
                            <select name="estado" class="form-control">
                                <option value="Activo" <?= $curso['estado'] === 'Activo' ? 'selected' : '' ?>>Activo</option>
                                <option value="En Curso" <?= $curso['estado'] === 'En Curso' ? 'selected' : '' ?>>En Curso</option>
                                <option value="Finalizado" <?= $curso['estado'] === 'Finalizado' ? 'selected' : '' ?>>Finalizado</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold font-size-13">Fecha de Inicio</label>
                            <input type="date" name="fecha_inicio" class="form-control" value="<?= $curso['fecha_inicio'] ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold font-size-13">Fecha de Finalización</label>
                            <input type="date" name="fecha_fin" class="form-control" value="<?= $curso['fecha_fin'] ?>">
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <label class="form-label font-weight-bold font-size-13">Descripción</label>
                        <textarea name="descripcion" rows="4" class="form-control"><?= htmlspecialchars($curso['descripcion'] ?? '') ?></textarea>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted font-size-12">Total certificados emitidos para este curso: <strong><?= $curso['total_certificados'] ?></strong></span>
                        <div style="gap: 10px;" class="d-flex">
                            <a href="<?= BASE_URL ?>/admin/cursos" class="btn btn-outline-danger font-weight-bold px-4">Cancelar</a>
                            <button type="submit" class="btn btn-primary font-weight-bold px-4">
                                <i class="fa-solid fa-save mr-1"></i> Actualizar Curso
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