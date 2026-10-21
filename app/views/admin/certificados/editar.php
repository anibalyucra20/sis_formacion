<?php
require_once __DIR__ . '/../../layouts/header.php';
?>

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between pb-3">
            <div>
                <h4 class="mb-1 font-weight-bold" style="color: #1e293b;">Editar Certificado</h4>
                <p class="text-muted font-size-13 mb-0">Certificado código: <strong><?= htmlspecialchars($certificado['codigo_certificado']) ?></strong></p>
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
                    <i class="fa-solid fa-pen-to-square mr-2 text-primary"></i> Modificar Datos de la Certificación
                </h5>
            </div>
            <div class="card-body p-4">
                <form action="<?= BASE_URL ?>/admin/certificados/guardar" method="POST">
                    <input type="hidden" name="id" value="<?= $certificado['id'] ?>">

                    <div class="row">
                        <!-- Selector de Curso -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold font-size-13">Curso / Capacitación <span class="text-danger">*</span></label>
                            <select name="curso_id" id="curso_id" class="form-control" required>
                                <?php foreach ($cursos as $c): ?>
                                    <option value="<?= $c['id'] ?>" <?= ($certificado['curso_id'] == $c['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($c['codigo'] . ' - ' . $c['nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Selector de Persona -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold font-size-13">Persona / Alumno <span class="text-danger">*</span></label>
                            <select name="persona_id" id="persona_id" class="form-control" required>
                                <?php foreach ($personas as $p): ?>
                                    <option value="<?= $p['id'] ?>" <?= ($certificado['persona_id'] == $p['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($p['dni'] . ' - ' . $p['apellidos'] . ', ' . $p['nombres']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Código de Certificado -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold font-size-13">Código de Certificado <span class="text-danger">*</span></label>
                            <input type="text" name="codigo_certificado" class="form-control font-weight-bold" value="<?= htmlspecialchars($certificado['codigo_certificado']) ?>" required style="text-transform: uppercase;">
                        </div>

                        <!-- Horas Lectivas -->
                        <div class="col-md-3 mb-3">
                            <label class="form-label font-weight-bold font-size-13">Horas Acreditadas <span class="text-danger">*</span></label>
                            <input type="number" name="horas" class="form-control" value="<?= $certificado['horas'] ?>" min="1" required>
                        </div>

                        <!-- Calificación -->
                        <div class="col-md-3 mb-3">
                            <label class="form-label font-weight-bold font-size-13">Calificación / Nota</label>
                            <input type="text" name="nota" class="form-control" value="<?= htmlspecialchars($certificado['nota'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="row">
                        <!-- Condición -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold font-size-13">Condición del Participante <span class="text-danger">*</span></label>
                            <select name="condicion" class="form-control" required>
                                <option value="Aprobado" <?= $certificado['condicion'] === 'Aprobado' ? 'selected' : '' ?>>Aprobado</option>
                                <option value="Participante" <?= $certificado['condicion'] === 'Participante' ? 'selected' : '' ?>>Participante</option>
                                <option value="Ponente" <?= $certificado['condicion'] === 'Ponente' ? 'selected' : '' ?>>Ponente / Expositor</option>
                                <option value="Organizador" <?= $certificado['condicion'] === 'Organizador' ? 'selected' : '' ?>>Organizador</option>
                            </select>
                        </div>

                        <!-- Fecha de Emisión -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold font-size-13">Fecha de Emisión <span class="text-danger">*</span></label>
                            <input type="date" name="fecha_emision" class="form-control" value="<?= $certificado['fecha_emision'] ?>" required>
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <label class="form-label font-weight-bold font-size-13">Observaciones</label>
                        <textarea name="observaciones" rows="3" class="form-control"><?= htmlspecialchars($certificado['observaciones'] ?? '') ?></textarea>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <div style="gap: 8px;" class="d-flex">
                            <a href="<?= BASE_URL ?>/consulta/pdf/<?= urlencode($certificado['codigo_certificado']) ?>" target="_blank" class="btn btn-outline-info btn-sm font-weight-bold">
                                <i class="fa-solid fa-file-pdf mr-1"></i> Ver PDF Oficial
                            </a>
                            <a href="<?= BASE_URL ?>/verificar/<?= urlencode($certificado['codigo_certificado']) ?>" target="_blank" class="btn btn-outline-success btn-sm font-weight-bold">
                                <i class="fa-solid fa-shield-halved mr-1"></i> Ver Verificación
                            </a>
                        </div>
                        <div style="gap: 10px;" class="d-flex">
                            <a href="<?= BASE_URL ?>/admin/certificados" class="btn btn-outline-danger font-weight-bold px-4">Cancelar</a>
                            <button type="submit" class="btn btn-primary font-weight-bold px-4">
                                <i class="fa-solid fa-save mr-1"></i> Actualizar Certificado
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