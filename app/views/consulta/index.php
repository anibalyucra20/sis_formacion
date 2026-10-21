<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?= $pageTitle ?? 'Consulta de Certificados de Cursos' ?></title>
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap 4 & FontAwesome 6 -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" type="image/x-icon" href="<?= BASE_URL ?>/img/favicon.ico">
    <style>
        :root {
            --primary: #1e3a8a;
            --primary-dark: #0f172a;
            --accent: #2563eb;
            --accent-light: #dbeafe;
            --gold: #d97706;
            --surface: #ffffff;
            --bg-page: #f8fafc;
            --text-dark: #0f172a;
            --text-muted: #64748b;
            --border: #e2e8f0;
        }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-page);
            color: var(--text-dark);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        /* Navbar */
        .portal-nav {
            background: #ffffff;
            border-bottom: 1px solid var(--border);
            padding: 14px 0;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        }
        .portal-brand {
            font-weight: 700;
            font-size: 18px;
            color: var(--primary);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .portal-brand:hover {
            color: var(--accent);
            text-decoration: none;
        }
        .brand-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-size: 18px;
        }
        /* Hero Section */
        .hero-section {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 60%, #1e3a8a 100%);
            color: #ffffff;
            padding: 56px 0 64px;
            position: relative;
            overflow: hidden;
        }
        .hero-section::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(37,99,235,0.15) 0%, rgba(0,0,0,0) 70%);
            pointer-events: none;
        }
        .hero-title {
            font-size: 34px;
            font-weight: 800;
            letter-spacing: -0.02em;
            margin-bottom: 12px;
        }
        .hero-subtitle {
            font-size: 16px;
            color: #94a3b8;
            max-width: 620px;
            margin: 0 auto 32px;
            line-height: 1.6;
        }
        /* Search Box */
        .search-container {
            max-width: 600px;
            margin: 0 auto;
        }
        .search-box-card {
            background: #ffffff;
            padding: 8px;
            border-radius: 16px;
            box-shadow: 0 20px 40px -15px rgba(0,0,0,0.4);
            display: flex;
            gap: 8px;
        }
        .search-input-wrap {
            position: relative;
            flex-grow: 1;
            display: flex;
            align-items: center;
        }
        .search-icon {
            position: absolute;
            left: 18px;
            color: #94a3b8;
            font-size: 18px;
        }
        .search-input {
            width: 100%;
            height: 52px;
            border: none;
            outline: none;
            padding-left: 48px;
            padding-right: 16px;
            font-size: 16px;
            font-weight: 600;
            color: #1e293b;
            border-radius: 10px;
        }
        .search-input::placeholder {
            color: #94a3b8;
            font-weight: 400;
        }
        .btn-search {
            height: 52px;
            padding: 0 28px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 15px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
            border: none;
            color: #ffffff;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s ease;
        }
        .btn-search:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(37,99,235,0.35);
            color: #ffffff;
        }
        /* Results Section */
        .results-section {
            padding: 48px 0;
            flex-grow: 1;
        }
        .participant-card {
            background: #ffffff;
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 24px 28px;
            margin-bottom: 32px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.03);
        }
        .stat-badge {
            background: var(--bg-page);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 12px 20px;
            text-align: center;
        }
        .stat-value {
            font-size: 24px;
            font-weight: 800;
            color: var(--primary);
            line-height: 1;
        }
        .stat-label {
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-muted);
            margin-top: 4px;
        }
        /* Certificate Table / Cards */
        .cert-card {
            background: #ffffff;
            border: 1px solid var(--border);
            border-radius: 14px;
            transition: all 0.2s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.02);
        }
        .cert-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 24px -10px rgba(0,0,0,0.1);
            border-color: #cbd5e1;
        }
        .cert-header {
            padding: 20px 20px 14px;
            border-bottom: 1px solid #f1f5f9;
        }
        .cert-body {
            padding: 16px 20px;
            flex-grow: 1;
        }
        .cert-footer {
            padding: 14px 20px 18px;
            background: #f8fafc;
            border-top: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }
        .cert-code-tag {
            font-family: monospace;
            background: #f1f5f9;
            color: #334155;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 700;
        }
        .btn-pdf {
            background: #ffffff;
            border: 1.5px solid #cbd5e1;
            color: #1e293b;
            font-weight: 600;
            font-size: 13px;
            padding: 8px 14px;
            border-radius: 8px;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn-pdf:hover {
            border-color: #dc2626;
            color: #dc2626;
            background: #fef2f2;
            text-decoration: none;
        }
        .btn-verify {
            background: var(--primary);
            color: #ffffff;
            font-weight: 600;
            font-size: 13px;
            padding: 8px 14px;
            border-radius: 8px;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn-verify:hover {
            background: var(--accent);
            color: #ffffff;
            text-decoration: none;
        }
        /* Footer */
        .portal-footer {
            background: #ffffff;
            border-top: 1px solid var(--border);
            padding: 24px 0;
            color: var(--text-muted);
            font-size: 13px;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="portal-nav">
    <div class="container d-flex align-items-center justify-content-between">
        <a href="<?= BASE_URL ?>/consulta" class="portal-brand">
            <div class="brand-icon">
                <i class="fa-solid fa-graduation-cap"></i>
            </div>
            <div>
                <span class="d-block" style="line-height: 1.1; font-weight: 800; font-size: 17px;"><?= htmlspecialchars($configuracion['nombre_sistema'] ?? 'Control de Certificados') ?></span>
                <small class="text-muted font-weight-normal font-size-11"><?= htmlspecialchars($configuracion['institucion'] ?? 'Verificación de Acreditaciones') ?></small>
            </div>
        </a>
        <div class="d-flex align-items-center" style="gap: 12px;">
            <a href="<?= BASE_URL ?>/login" class="btn btn-outline-primary btn-sm font-weight-bold px-3 py-2" style="border-radius: 8px;">
                <i class="fa-solid fa-lock mr-1"></i> Acceso Administrativo
            </a>
        </div>
    </div>
</nav>

<!-- Hero Section -->
<header class="hero-section text-center">
    <div class="container position-relative">
        <div class="badge badge-primary px-3 py-2 mb-3" style="background: rgba(37,99,235,0.25); border: 1px solid rgba(255,255,255,0.1); border-radius: 30px; font-weight: 600; font-size: 12px;">
            <i class="fa-solid fa-shield-check mr-1 text-warning"></i> Portal Oficial de Validación de Certificados
        </div>
        <h1 class="hero-title">Consulta de Certificados y Capacitaciones</h1>
        <p class="hero-subtitle">
            Ingrese el número de DNI del participante para consultar el historial completo de cursos realizados, horas académicas acreditadas y descargar certificados oficiales.
        </p>

        <!-- Search Box -->
        <div class="search-container">
            <form action="<?= BASE_URL ?>/consulta" method="GET" class="search-box-card">
                <div class="search-input-wrap">
                    <i class="fa-solid fa-id-card search-icon"></i>
                    <input type="text" name="dni" class="search-input" placeholder="Ingrese N° de DNI (ej: 71234567)" value="<?= htmlspecialchars($dni ?? '') ?>" maxlength="15" required autofocus>
                </div>
                <button type="submit" class="btn-search">
                    <i class="fa-solid fa-magnifying-glass"></i> Consultar
                </button>
            </form>
            <div class="mt-3 text-white-50" style="font-size: 13px;">
                <span>DNIs de demostración para probar:</span>
                <a href="<?= BASE_URL ?>/consulta?dni=71234567" class="text-warning font-weight-bold ml-1 mr-2">71234567</a> |
                <a href="<?= BASE_URL ?>/consulta?dni=45892134" class="text-warning font-weight-bold ml-1 mr-2">45892134</a> |
                <a href="<?= BASE_URL ?>/consulta?dni=74561230" class="text-warning font-weight-bold ml-1">74561230</a>
            </div>
        </div>
    </div>
</header>

<!-- Main Results Area -->
<main class="results-section">
    <div class="container">
        <?php if ($buscado && $resultado): ?>
            <!-- Participant Summary Card -->
            <div class="participant-card">
                <div class="row align-items-center">
                    <div class="col-md-7 mb-3 mb-md-0">
                        <div class="d-flex align-items-center" style="gap: 16px;">
                            <div style="width: 56px; height: 56px; border-radius: 50%; background: #eff6ff; color: var(--accent); display: flex; align-items: center; justify-content: center; font-size: 24px; font-weight: 700;">
                                <?= strtoupper(substr($resultado['persona']['nombres'], 0, 1)) ?>
                            </div>
                            <div>
                                <span class="badge badge-success px-2 py-1 mb-1 font-size-11" style="font-weight: 700;">
                                    <i class="fa-solid fa-check-circle mr-1"></i> Participante Registrado
                                </span>
                                <h3 class="mb-0 font-weight-bold" style="font-size: 22px; color: var(--text-dark);">
                                    <?= htmlspecialchars($resultado['persona']['nombres'] . ' ' . $resultado['persona']['apellidos']) ?>
                                </h3>
                                <p class="text-muted mb-0 font-size-13">
                                    <i class="fa-solid fa-id-card mr-1"></i> DNI: <strong><?= htmlspecialchars($resultado['persona']['dni']) ?></strong>
                                    <?php if (!empty($resultado['persona']['email'])): ?>
                                        <span class="mx-2">•</span> <i class="fa-solid fa-envelope mr-1"></i> <?= htmlspecialchars($resultado['persona']['email']) ?>
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="d-flex justify-content-md-end" style="gap: 16px;">
                            <div class="stat-badge flex-fill flex-md-grow-0" style="min-width: 140px;">
                                <div class="stat-value"><?= $resultado['total_cursos'] ?></div>
                                <div class="stat-label">Cursos Aprobados</div>
                            </div>
                            <div class="stat-badge flex-fill flex-md-grow-0" style="min-width: 140px;">
                                <div class="stat-value text-primary"><?= $resultado['total_horas'] ?> h</div>
                                <div class="stat-label">Horas Acreditadas</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex align-items-center justify-content-between mb-4">
                <h4 class="font-weight-bold mb-0" style="font-size: 18px; color: #1e293b;">
                    <i class="fa-solid fa-certificate text-primary mr-2"></i> Certificados Registrados (<?= count($resultado['certificados']) ?>)
                </h4>
                <span class="text-muted font-size-13">Registros oficiales validados por el sistema</span>
            </div>

            <!-- Certificates Grid -->
            <div class="row">
                <?php foreach ($resultado['certificados'] as $cert): ?>
                    <div class="col-lg-6 mb-4">
                        <div class="cert-card">
                            <div class="cert-header d-flex justify-content-between align-items-start">
                                <div>
                                    <span class="badge badge-light border text-muted font-size-11 px-2 py-1 mb-2 font-weight-bold">
                                        <?= htmlspecialchars($cert['curso_codigo']) ?>
                                    </span>
                                    <h5 class="font-weight-bold mb-1" style="font-size: 16px; color: var(--text-dark); line-height: 1.4;">
                                        <?= htmlspecialchars($cert['curso_nombre']) ?>
                                    </h5>
                                    <small class="text-muted d-block">
                                        <i class="fa-solid fa-chalkboard-user mr-1"></i> Instructor: <?= htmlspecialchars($cert['curso_instructor'] ?: 'Docente Asignado') ?>
                                    </small>
                                </div>
                                <span class="badge badge-primary px-2 py-1 font-size-12 font-weight-bold" style="background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe;">
                                    <?= htmlspecialchars($cert['condicion']) ?>
                                </span>
                            </div>

                            <div class="cert-body">
                                <div class="row font-size-13 text-secondary mb-2">
                                    <div class="col-6 mb-2">
                                        <span class="text-muted d-block font-size-11">Horas Académicas:</span>
                                        <strong><i class="fa-regular fa-clock mr-1 text-primary"></i> <?= $cert['horas'] ?> horas</strong>
                                    </div>
                                    <div class="col-6 mb-2">
                                        <span class="text-muted d-block font-size-11">Modalidad:</span>
                                        <strong><i class="fa-solid fa-laptop-code mr-1 text-primary"></i> <?= htmlspecialchars($cert['curso_modalidad']) ?></strong>
                                    </div>
                                    <div class="col-6">
                                        <span class="text-muted d-block font-size-11">Fecha de Emisión:</span>
                                        <strong><i class="fa-regular fa-calendar-check mr-1 text-primary"></i> <?= date('d/m/Y', strtotime($cert['fecha_emision'])) ?></strong>
                                    </div>
                                    <div class="col-6">
                                        <span class="text-muted d-block font-size-11">Calificación:</span>
                                        <strong><i class="fa-solid fa-star mr-1 text-warning"></i> <?= htmlspecialchars($cert['nota'] ?: 'Aprobado') ?></strong>
                                    </div>
                                </div>

                                <div class="mt-2 pt-2 border-top d-flex align-items-center justify-content-between">
                                    <span class="font-size-12 text-muted">Cód. Registro:</span>
                                    <span class="cert-code-tag"><?= htmlspecialchars($cert['codigo_certificado']) ?></span>
                                </div>
                            </div>

                            <div class="cert-footer">
                                <a href="<?= BASE_URL ?>/verificar/<?= urlencode($cert['codigo_certificado']) ?>" class="btn-verify">
                                    <i class="fa-solid fa-shield-halved"></i> Validar
                                </a>
                                <a href="<?= BASE_URL ?>/consulta/pdf/<?= urlencode($cert['codigo_certificado']) ?>" target="_blank" class="btn-pdf">
                                    <i class="fa-solid fa-file-pdf text-danger"></i> Descargar PDF
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

        <?php elseif ($buscado && !$resultado): ?>
            <!-- Not Found State -->
            <div class="text-center py-5">
                <div style="width: 80px; height: 80px; border-radius: 50%; background: #fef2f2; color: #ef4444; display: inline-flex; align-items: center; justify-content: center; font-size: 36px; margin-bottom: 20px;">
                    <i class="fa-solid fa-user-xmark"></i>
                </div>
                <h3 class="font-weight-bold" style="color: #1e293b;">No se encontraron registros</h3>
                <p class="text-muted" style="max-width: 480px; margin: 0 auto 24px; font-size: 15px;">
                    El DNI <strong><?= htmlspecialchars($dni) ?></strong> no cuenta con cursos o certificados emitidos en el sistema. Verifique que los dígitos ingresados sean correctos.
                </p>
                <a href="<?= BASE_URL ?>/consulta" class="btn btn-outline-primary px-4 py-2 font-weight-bold" style="border-radius: 8px;">
                    <i class="fa-solid fa-rotate-left mr-1"></i> Realizar otra búsqueda
                </a>
            </div>

        <?php else: ?>
            <!-- Default Welcome State -->
            <div class="text-center py-5">
                <div class="row justify-content-center">
                    <div class="col-md-4 mb-4">
                        <div class="p-4 bg-white rounded-lg border h-100 shadow-sm" style="border-radius: 14px;">
                            <div class="text-primary mb-3 font-size-28"><i class="fa-solid fa-magnifying-glass-arrow-right"></i></div>
                            <h5 class="font-weight-bold" style="font-size: 16px;">Consulta Inmediata</h5>
                            <p class="text-muted font-size-13 mb-0">Escriba su número de DNI para acceder instantáneamente a su historial de cursos realizados.</p>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="p-4 bg-white rounded-lg border h-100 shadow-sm" style="border-radius: 14px;">
                            <div class="text-success mb-3 font-size-28"><i class="fa-solid fa-file-pdf"></i></div>
                            <h5 class="font-weight-bold" style="font-size: 16px;">Certificados Digitales</h5>
                            <p class="text-muted font-size-13 mb-0">Descargue el certificado oficial en formato PDF con registro correlativo y firmas institucionales.</p>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="p-4 bg-white rounded-lg border h-100 shadow-sm" style="border-radius: 14px;">
                            <div class="text-warning mb-3 font-size-28"><i class="fa-solid fa-shield-halved"></i></div>
                            <h5 class="font-weight-bold" style="font-size: 16px;">Verificación Pública</h5>
                            <p class="text-muted font-size-13 mb-0">Cualquier empleador o entidad puede comprobar la veracidad de la constancia mediante su código único.</p>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</main>

<!-- Footer -->
<footer class="portal-footer">
    <div class="container d-flex flex-column flex-md-row align-items-center justify-content-between text-center text-md-left">
        <div>
            <strong><?= htmlspecialchars($configuracion['institucion'] ?? 'Centro de Formación Continua') ?></strong>
            <div class="font-size-12 text-muted"><?= htmlspecialchars($configuracion['direccion'] ?? 'Av. De la Cultura 750, Lima - Perú') ?> | <?= htmlspecialchars($configuracion['telefono'] ?? '+51 987 654 321') ?></div>
        </div>
        <div class="mt-2 mt-md-0 font-size-12">
            <span>© <?= date('Y') ?> Control de Certificados. Sistema Oficial.</span>
        </div>
    </div>
</footer>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
