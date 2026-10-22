<?php
require_once __DIR__ . '/../../layouts/header.php';
?>

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between pb-3">
            <div>
                <h4 class="mb-1 font-weight-bold" style="color: #1e293b;">Emitir Certificado / Vincular Alumno</h4>
                <p class="text-muted font-size-13 mb-0">Vinculación individual de participante a un curso con registro correlativo</p>
            </div>
            <div>
                <a href="<?= BASE_URL ?>/admin/certificados" class="btn btn-outline-info font-weight-bold" style="border-radius: 8px;">
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
                    <i class="fa-solid fa-award mr-2 text-primary"></i> Datos de la Certificación
                </h5>
            </div>
            <div class="card-body p-4">
                <form action="<?= BASE_URL ?>/admin/certificados/guardar" method="POST">
                    <div class="row">
                        <!-- Selector de Curso -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold font-size-13">Curso / Capacitación <span class="text-danger">*</span></label>
                            <select name="curso_id" id="curso_id" class="form-control" required>
                                <option value="">-- Seleccione un curso --</option>
                                <?php foreach ($cursos as $c): ?>
                                    <option value="<?= $c['id'] ?>" data-horas="<?= $c['horas_academicas'] ?>" <?= ($cursoSeleccionado == $c['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($c['codigo'] . ' - ' . $c['nombre']) ?> (<?= $c['horas_academicas'] ?>h)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Selector de Persona -->
                        <div class="col-md-6 mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label font-weight-bold font-size-13 mb-0">Persona / Alumno <span class="text-danger">*</span></label>
                                <a href="<?= BASE_URL ?>/admin/personas/nuevo" target="_blank" class="font-size-12 text-primary font-weight-bold">+ Registrar nueva</a>
                            </div>
                            <select name="persona_id" id="persona_id" class="form-control" required>
                                <option value="">-- Seleccione a la persona (DNI - Nombres) --</option>
                                <?php foreach ($personas as $p): ?>
                                    <option value="<?= $p['id'] ?>" <?= ($personaSeleccionada == $p['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($p['dni'] . ' - ' . $p['apellidos'] . ', ' . $p['nombres']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Calificación -->
                        <div class="col-md-4 mb-3">
                            <label class="form-label font-weight-bold font-size-13">Calificación / Nota</label>
                            <input type="text" name="nota" class="form-control" value="18" placeholder="Ej: 18 o Aprobado">
                        </div>

                        <!-- Condición -->
                        <div class="col-md-4 mb-3">
                            <label class="form-label font-weight-bold font-size-13">Condición del Participante <span class="text-danger">*</span></label>
                            <select name="condicion" class="form-control" required>
                                <option value="Aprobado" selected>Aprobado</option>
                                <option value="Participante">Participante</option>
                                <option value="Ponente">Ponente / Expositor</option>
                                <option value="Organizador">Organizador</option>
                            </select>
                        </div>

                        <!-- Fecha de Emisión -->
                        <div class="col-md-4 mb-3">
                            <label class="form-label font-weight-bold font-size-13">Fecha de Emisión <span class="text-danger">*</span></label>
                            <input type="date" name="fecha_emision" class="form-control" value="<?= date('Y-m-d') ?>" required>
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <label class="form-label font-weight-bold font-size-13">Observaciones / Mención de Honor</label>
                        <textarea name="observaciones" rows="3" class="form-control" placeholder="Aprobó con honores, proyecto final destacado... (opcional)"></textarea>
                    </div>

                    <div class="d-flex justify-content-end" style="gap: 10px;">
                        <a href="<?= BASE_URL ?>/admin/certificados" class="btn btn-outline-danger font-weight-bold px-4">Cancelar</a>
                        <button type="submit" class="btn btn-primary font-weight-bold px-4">
                            <i class="fa-solid fa-award mr-1"></i> Emitir Certificado
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