<?php
require_once __DIR__ . '/../layouts/header.php';
?>

<!-- Page Header -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between pb-3">
            <div>
                <h4 class="mb-1 font-weight-bold" style="color: #1e293b;">Panel de Control</h4>
                <p class="text-muted font-size-13 mb-0">Resumen ejecutivo y estado del sistema de certificaciones</p>
            </div>
            <div class="d-flex" style="gap: 10px;">
                <a href="<?= BASE_URL ?>/consulta" target="_blank" class="btn btn-outline-primary btn-sm font-weight-bold" style="border-radius: 8px;">
                    <i class="fa-solid fa-arrow-up-right-from-square mr-1"></i> Portal de Consulta DNI
                </a>
                <a href="<?= BASE_URL ?>/admin/certificados/nuevo" class="btn btn-primary btn-sm font-weight-bold" style="border-radius: 8px;">
                    <i class="fa-solid fa-plus mr-1"></i> Emitir Certificado
                </a>
            </div>
        </div>
    </div>
</div>

<!-- KPI Cards -->
<div class="row">
    <!-- Cursos -->
    <div class="col-md-6 col-xl-3 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="text-muted font-size-12 font-weight-bold text-uppercase">Cursos y Talleres</span>
                    <div style="width: 44px; height: 44px; border-radius: 10px; background: #eff6ff; color: #2563eb; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                        <i class="fa-solid fa-book-bookmark"></i>
                    </div>
                </div>
                <h2 class="font-weight-bold mb-1" style="color: #0f172a;"><?= number_format($totalCursos) ?></h2>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted font-size-12">Capacitaciones creadas</span>
                    <a href="<?= BASE_URL ?>/admin/cursos" class="font-weight-bold font-size-12 text-primary">Gestionar &rarr;</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Personas -->
    <div class="col-md-6 col-xl-3 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="text-muted font-size-12 font-weight-bold text-uppercase">Personas Registradas</span>
                    <div style="width: 44px; height: 44px; border-radius: 10px; background: #ecfdf5; color: #10b981; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                        <i class="fa-solid fa-users"></i>
                    </div>
                </div>
                <h2 class="font-weight-bold mb-1" style="color: #0f172a;"><?= number_format($totalPersonas) ?></h2>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted font-size-12">Participantes y alumnos</span>
                    <a href="<?= BASE_URL ?>/admin/personas" class="font-weight-bold font-size-12 text-success">Ver lista &rarr;</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Certificados Emitidos -->
    <div class="col-md-6 col-xl-3 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="text-muted font-size-12 font-weight-bold text-uppercase">Certificados Emitidos</span>
                    <div style="width: 44px; height: 44px; border-radius: 10px; background: #fef3c7; color: #d97706; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                        <i class="fa-solid fa-award"></i>
                    </div>
                </div>
                <h2 class="font-weight-bold mb-1" style="color: #0f172a;"><?= number_format($totalCertificados) ?></h2>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted font-size-12">Acreditaciones activas</span>
                    <a href="<?= BASE_URL ?>/admin/certificados" class="font-weight-bold font-size-12 text-warning">Ver emisión &rarr;</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Horas Lectivas -->
    <div class="col-md-6 col-xl-3 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="text-muted font-size-12 font-weight-bold text-uppercase">Horas Acreditadas</span>
                    <div style="width: 44px; height: 44px; border-radius: 10px; background: #f3e8ff; color: #9333ea; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                        <i class="fa-regular fa-clock"></i>
                    </div>
                </div>
                <h2 class="font-weight-bold mb-1" style="color: #0f172a;"><?= number_format($totalHoras) ?> h</h2>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted font-size-12">Horas lectivas totales</span>
                    <a href="<?= BASE_URL ?>/admin/reportes" class="font-weight-bold font-size-12 text-purple" style="color: #9333ea;">Reportes &rarr;</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Section: Quick Actions & Top Courses -->
<div class="row">
    <!-- Últimos Certificados Emitidos -->
    <!--<div class="col-lg-8 mb-4">
        <div class="card h-100">
            <div class="card-header bg-white d-flex align-items-center justify-content-between py-3">
                <h5 class="card-title font-weight-bold mb-0" style="font-size: 15px;">
                    <i class="fa-solid fa-clock-rotate-left mr-2 text-primary"></i> Últimos Certificados Emitidos
                </h5>
                <a href="<?= BASE_URL ?>/admin/certificados" class="btn btn-sm btn-outline-secondary font-weight-bold" style="border-radius: 6px;">
                    Ver Todos
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>#</th>
                                <th>Participante</th>
                                <th>Curso</th>
                                <th>Horas</th>
                                <th>Emisión</th>
                                <th class="text-right">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($ultimosCertificados)): ?>
                                <?php foreach ($ultimosCertificados as $uc): ?>
                                    <tr>
                                        <td>
                                            <span class="badge badge-light border text-dark font-weight-bold font-size-12">
                                                #<?= $uc['id'] ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="font-weight-bold"><?= htmlspecialchars($uc['participante']) ?></div>
                                            <small class="text-muted">DNI: <?= htmlspecialchars($uc['dni']) ?></small>
                                        </td>
                                        <td>
                                            <div class="text-truncate" style="max-width: 220px;" title="<?= htmlspecialchars($uc['curso_nombre']) ?>">
                                                <?= htmlspecialchars($uc['curso_nombre']) ?>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="font-weight-bold text-primary font-size-12"><?= $uc['horas'] ?> h</span>
                                        </td>
                                        <td class="font-size-12 text-muted">
                                            <?= date('d/m/Y', strtotime($uc['fecha_emision'])) ?>
                                        </td>
                                        <td class="text-right">
                                            <a href="<?= BASE_URL ?>/admin/certificados/editar/<?= $uc['id'] ?>" class="btn btn-sm btn-outline-primary py-1 px-2" title="Editar Certificado">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        No hay certificados emitidos todavía.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>-->

    <!-- Accesos Rápidos y Top Cursos -->
    <div class="col-lg-4 mb-4">
        <!-- Accesos directos -->
        <!--<div class="card mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="card-title font-weight-bold mb-0" style="font-size: 15px;">
                    <i class="fa-solid fa-bolt mr-2 text-warning"></i> Operaciones Rápidas
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid" style="display: flex; flex-direction: column; gap: 10px;">
                    <a href="<?= BASE_URL ?>/admin/certificados/nuevo" class="btn btn-primary d-flex align-items-center justify-content-between p-2 font-weight-bold" style="border-radius: 8px;">
                        <span><i class="fa-solid fa-award mr-2"></i> Emitir Certificado Individual</span>
                        <i class="fa-solid fa-chevron-right font-size-12"></i>
                    </a>
                    <a href="<?= BASE_URL ?>/admin/certificados/cargaMasiva" class="btn btn-outline-success d-flex align-items-center justify-content-between p-2 font-weight-bold" style="border-radius: 8px;">
                        <span><i class="fa-solid fa-file-excel mr-2"></i> Carga Masiva Certificados Excel</span>
                        <i class="fa-solid fa-chevron-right font-size-12"></i>
                    </a>
                    <a href="<?= BASE_URL ?>/admin/personas/cargaMasiva" class="btn btn-outline-info d-flex align-items-center justify-content-between p-2 font-weight-bold" style="border-radius: 8px;">
                        <span><i class="fa-solid fa-users-gear mr-2"></i> Carga Masiva Personas Excel</span>
                        <i class="fa-solid fa-chevron-right font-size-12"></i>
                    </a>
                    <a href="<?= BASE_URL ?>/admin/reportes" class="btn btn-outline-secondary d-flex align-items-center justify-content-between p-2 font-weight-bold" style="border-radius: 8px;">
                        <span><i class="fa-solid fa-file-chart-column mr-2"></i> Reportes Generales (Excel / PDF)</span>
                        <i class="fa-solid fa-chevron-right font-size-12"></i>
                    </a>
                </div>
            </div>
        </div>
        -->

        <!-- Top Cursos con más alumnos certificados -->
        <!--<div class="card">
            <div class="card-header bg-white py-3">
                <h5 class="card-title font-weight-bold mb-0" style="font-size: 15px;">
                    <i class="fa-solid fa-fire mr-2 text-danger"></i> Cursos más Solicitados
                </h5>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    <?php if (!empty($topCursos)): ?>
                        <?php foreach ($topCursos as $tc): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                                <div class="text-truncate mr-2 font-size-13 font-weight-bold" style="max-width: 200px;">
                                    <?= htmlspecialchars($tc['nombre']) ?>
                                </div>
                                <span class="badge badge-primary badge-pill font-size-12 px-2 py-1">
                                    <?= $tc['total'] ?> certificados
                                </span>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li class="list-group-item text-muted text-center py-3">No hay datos suficientes</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
        -->
    </div>
</div>

<?php
require_once __DIR__ . '/../layouts/footer.php';
?>