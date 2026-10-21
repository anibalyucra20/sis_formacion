<?php
require_once __DIR__ . '/../../layouts/header.php';
?>

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between pb-3">
            <div>
                <h4 class="mb-1 font-weight-bold" style="color: #1e293b;">Nuevo Curso o Capacitación</h4>
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
                    <i class="fa-solid fa-graduation-cap mr-2 text-primary"></i> Datos del Curso
                </h5>
            </div>
            <div class="card-body p-4">
                <form action="<?= BASE_URL ?>/admin/cursos/guardar" method="POST">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label font-weight-bold font-size-13">Código del Curso <span class="text-danger">*</span></label>
                            <input type="text" name="codigo" class="form-control" placeholder="Ej: CUR-2026-006" required style="text-transform: uppercase;">
                            <small class="form-text text-muted">Identificador único del curso.</small>
                        </div>
                        <div class="col-md-8 mb-3">
                            <label class="form-label font-weight-bold font-size-13">Nombre Completo del Curso / Taller <span class="text-danger">*</span></label>
                            <input type="text" name="nombre" class="form-control" placeholder="Ej: Especialización en Ciencia de Datos" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label font-weight-bold font-size-13">Horas Académicas <span class="text-danger">*</span></label>
                            <input type="number" name="horas_academicas" class="form-control" value="40" min="1" max="1000" required>
                        </div>
                        <div class="col-md-5 mb-3">
                            <label class="form-label font-weight-bold font-size-13">Instructor / Ponente</label>
                            <input type="text" name="instructor" class="form-control" placeholder="Ej: Dr. Martín Vizcarra">
                        </div>
                        <div class="col-md-2 mb-3">
                            <label class="form-label font-weight-bold font-size-13">Modalidad</label>
                            <select name="modalidad" class="form-control">
                                <option value="Virtual" selected>Virtual</option>
                                <option value="Presencial">Presencial</option>
                                <option value="Semipresencial">Semipresencial</option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label class="form-label font-weight-bold font-size-13">Estado</label>
                            <select name="estado" class="form-control">
                                <option value="Activo" selected>Activo</option>
                                <option value="En Curso">En Curso</option>
                                <option value="Finalizado">Finalizado</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold font-size-13">Fecha de Inicio</label>
                            <input type="date" name="fecha_inicio" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold font-size-13">Fecha de Finalización</label>
                            <input type="date" name="fecha_fin" class="form-control">
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <label class="form-label font-weight-bold font-size-13">Descripción y Objetivos Académicos</label>
                        <textarea name="descripcion" rows="4" class="form-control" placeholder="Breve descripción del temario o contenidos acreditados..."></textarea>
                    </div>

                    <div class="d-flex justify-content-end" style="gap: 10px;">
                        <a href="<?= BASE_URL ?>/admin/cursos" class="btn btn-outline-danger font-weight-bold px-4">Cancelar</a>
                        <button type="submit" class="btn btn-primary font-weight-bold px-4">
                            <i class="fa-solid fa-save mr-1"></i> Guardar Curso
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