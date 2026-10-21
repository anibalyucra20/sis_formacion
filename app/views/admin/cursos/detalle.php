<?php
require_once __DIR__ . '/../../layouts/header.php';
?>

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between pb-3">
            <div>
                <h4 class="mb-1 font-weight-bold" style="color: #1e293b;"><?= htmlspecialchars($curso['nombre']) ?></h4>
                <p class="text-muted font-size-13 mb-0">Código: <span class="badge badge-light border text-primary font-weight-bold"><?= htmlspecialchars($curso['codigo']) ?></span> • Modalidad: <strong><?= htmlspecialchars($curso['modalidad']) ?></strong> • Horas: <strong><?= $curso['horas_academicas'] ?> h</strong></p>
            </div>
            <div style="gap: 10px;" class="d-flex">
                <a href="<?= BASE_URL ?>/admin/cursos" class="btn btn-outline-secondary font-weight-bold" style="border-radius: 8px;">
                    <i class="fa-solid fa-arrow-left mr-1"></i> Volver a Cursos
                </a>
                <a href="<?= BASE_URL ?>/admin/certificados/nuevo?curso_id=<?= $curso['id'] ?>" class="btn btn-primary font-weight-bold" style="border-radius: 8px;">
                    <i class="fa-solid fa-award mr-1"></i> Emitir Certificado en este Curso
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Course Info Card -->
    <div class="col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-header bg-white py-3">
                <h5 class="card-title font-weight-bold mb-0" style="font-size: 15px;">
                    <i class="fa-solid fa-circle-info mr-2 text-primary"></i> Información del Programa
                </h5>
            </div>
            <div class="card-body">
                <p class="text-muted font-size-13 mb-3">
                    <?= nl2br(htmlspecialchars($curso['descripcion'] ?: 'Sin descripción detallada registrada.')) ?>
                </p>
                <ul class="list-group list-group-flush font-size-13">
                    <li class="list-group-item px-0 d-flex justify-content-between">
                        <span class="text-muted">Instructor / Ponente:</span>
                        <strong><?= htmlspecialchars($curso['instructor'] ?: 'No asignado') ?></strong>
                    </li>
                    <li class="list-group-item px-0 d-flex justify-content-between">
                        <span class="text-muted">Estado del Curso:</span>
                        <span class="badge badge-success px-2 py-1"><?= htmlspecialchars($curso['estado']) ?></span>
                    </li>
                    <li class="list-group-item px-0 d-flex justify-content-between">
                        <span class="text-muted">Fecha de Inicio:</span>
                        <strong><?= $curso['fecha_inicio'] ? date('d/m/Y', strtotime($curso['fecha_inicio'])) : 'Pendiente' ?></strong>
                    </li>
                    <li class="list-group-item px-0 d-flex justify-content-between">
                        <span class="text-muted">Fecha de Fin:</span>
                        <strong><?= $curso['fecha_fin'] ? date('d/m/Y', strtotime($curso['fecha_fin'])) : 'Pendiente' ?></strong>
                    </li>
                    <li class="list-group-item px-0 d-flex justify-content-between">
                        <span class="text-muted">Certificados Emitidos:</span>
                        <span class="badge badge-primary badge-pill font-size-12 px-2 py-1"><?= count($alumnos) ?></span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Participants List Card -->
    <div class="col-lg-8 mb-4">
        <div class="card h-100">
            <div class="card-header bg-white d-flex align-items-center justify-content-between py-3">
                <h5 class="card-title font-weight-bold mb-0" style="font-size: 15px;">
                    <i class="fa-solid fa-users mr-2 text-success"></i> Participantes Certificados (<?= count($alumnos) ?>)
                </h5>
                <a href="<?= BASE_URL ?>/admin/reportes/exportarCursoExcel?curso_id=<?= $curso['id'] ?>" class="btn btn-sm btn-outline-success font-weight-bold" data-no-loader="true">
                    <i class="fa-solid fa-file-excel mr-1"></i> Exportar a Excel
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>DNI</th>
                                <th>Apellidos y Nombres</th>
                                <th>Cód. Certificado</th>
                                <th>Nota</th>
                                <th>Condición</th>
                                <th>Fecha Emisión</th>
                                <th class="text-right">Certificado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($alumnos)): ?>
                                <?php foreach ($alumnos as $a): ?>
                                    <tr>
                                        <td class="font-weight-bold text-primary"><?= htmlspecialchars($a['dni']) ?></td>
                                        <td>
                                            <div class="font-weight-bold"><?= htmlspecialchars($a['participante']) ?></div>
                                            <small class="text-muted"><?= htmlspecialchars($a['email'] ?? '') ?></small>
                                        </td>
                                        <td>
                                            <span class="badge badge-light border text-dark font-weight-bold">
                                                <?= htmlspecialchars($a['codigo_certificado']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="font-weight-bold text-warning"><?= htmlspecialchars($a['nota'] ?: '-') ?></span>
                                        </td>
                                        <td>
                                            <span class="badge badge-success px-2 py-1 font-size-11"><?= htmlspecialchars($a['condicion']) ?></span>
                                        </td>
                                        <td class="font-size-12 text-muted">
                                            <?= date('d/m/Y', strtotime($a['fecha_emision'])) ?>
                                        </td>
                                        <td class="text-right">
                                            <a href="<?= BASE_URL ?>/consulta/pdf/<?= urlencode($a['codigo_certificado']) ?>" target="_blank" class="btn btn-sm btn-outline-danger py-1 px-2" title="Descargar Certificado PDF" data-no-loader="true">
                                                <i class="fa-solid fa-file-pdf"></i>
                                            </a>
                                            <a href="<?= BASE_URL ?>/verificar/<?= urlencode($a['codigo_certificado']) ?>" target="_blank" class="btn btn-sm btn-outline-primary py-1 px-2" title="Verificar" data-no-loader="true">
                                                <i class="fa-solid fa-shield-halved"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        No hay participantes vinculados a este curso aún.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/../../layouts/footer.php';
?>
