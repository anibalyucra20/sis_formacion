<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?= $pageTitle ?? 'Verificación de Certificado' ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" type="image/x-icon" href="https://iesphuanta.edu.pe/wp-content/uploads/2021/12/WhatsApp-Image-2025-09-17-at-12.55.48-PM-Photoroom.png">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
            color: #0f172a;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .portal-nav {
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            padding: 14px 0;
        }

        .cert-verify-card {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.06);
            border: 1px solid #e2e8f0;
            overflow: hidden;
            max-width: 820px;
            margin: 40px auto;
        }

        .verify-banner {
            background: linear-gradient(135deg, #16a34a 0%, #15803d 100%);
            color: #ffffff;
            padding: 30px;
            text-align: center;
        }

        .verify-banner.invalid {
            background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
        }

        .verify-shield {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(8px);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            margin-bottom: 14px;
        }

        .info-row {
            padding: 12px 0;
            border-bottom: 1px solid #f1f5f9;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            color: #64748b;
            font-size: 13px;
            font-weight: 500;
        }

        .info-value {
            color: #1e293b;
            font-size: 15px;
            font-weight: 700;
        }
    </style>
</head>

<body>

    <nav class="portal-nav">
        <div class="container d-flex align-items-center justify-content-between">
            <a href="<?= BASE_URL ?>/consulta" class="font-weight-bold text-primary text-decoration-none d-flex align-items-center" style="gap: 8px;">
                <img src="https://iesphuanta.edu.pe/wp-content/uploads/2021/12/WhatsApp-Image-2025-09-17-at-12.55.48-PM-Photoroom.png" alt="" width="100" height="50">
                <span><?= htmlspecialchars($configuracion['nombre_sistema'] ?? 'Control de Certificados') ?></span>
            </a>
            <a href="<?= BASE_URL ?>/consulta" class="btn btn-outline-secondary btn-sm font-weight-bold" style="border-radius: 8px;">
                <i class="fa-solid fa-magnifying-glass mr-1"></i> Nueva Consulta por DNI
            </a>
        </div>
    </nav>

    <div class="container flex-grow-1">
        <?php if ($certificado): ?>
            <div class="cert-verify-card">
                <div class="verify-banner">
                    <div class="verify-shield">
                        <img src="https://iesphuanta.edu.pe/wp-content/uploads/2021/12/WhatsApp-Image-2025-09-17-at-12.55.48-PM-Photoroom.png" alt="" width="100%">
                    </div>
                    <h3 class="font-weight-bold mb-1" style="font-size: 24px;">DOCUMENTO OFICIAL VÁLIDO</h3>
                    <p class="mb-0 opacity-90 font-size-14">Este certificado se encuentra debidamente registrado en el sistema institucional</p>
                    <div class="mt-2 font-weight-bold font-size-15" style="letter-spacing: 1px;">
                        REGISTRO: #<?= htmlspecialchars($certificado['id'] ?? '') ?>
                    </div>
                </div>

                <div class="p-4 p-md-5">
                    <h5 class="text-uppercase text-muted font-weight-bold font-size-12 mb-3" style="letter-spacing: 1px;">
                        Detalles del Participante y Curso
                    </h5>

                    <div class="info-row row">
                        <div class="col-sm-4 info-label">Participante Acreditado:</div>
                        <div class="col-sm-8 info-value text-primary font-size-17">
                            <?= htmlspecialchars($certificado['participante']) ?>
                        </div>
                    </div>

                    <div class="info-row row">
                        <div class="col-sm-4 info-label">Documento de Identidad (DNI):</div>
                        <div class="col-sm-8 info-value">
                            <?= htmlspecialchars($certificado['dni']) ?>
                        </div>
                    </div>

                    <div class="info-row row">
                        <div class="col-sm-4 info-label">Nombre del Curso / Capacitación:</div>
                        <div class="col-sm-8 info-value">
                            <?= htmlspecialchars($certificado['curso_nombre']) ?> (<?= htmlspecialchars($certificado['curso_codigo']) ?>)
                        </div>
                    </div>

                    <div class="info-row row">
                        <div class="col-sm-4 info-label">Condición / Estado:</div>
                        <div class="col-sm-8 info-value">
                            <span class="badge badge-success px-3 py-1 font-size-13"><?= htmlspecialchars($certificado['condicion']) ?></span>
                            <?php if (!empty($certificado['nota'])): ?>
                                <span class="ml-2 text-muted font-weight-normal">(Calificación: <strong><?= htmlspecialchars($certificado['nota']) ?></strong>)</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="info-row row">
                        <div class="col-sm-4 info-label">Horas Académicas:</div>
                        <div class="col-sm-8 info-value">
                            <?= $certificado['horas'] ?> horas lectivas certificadas
                        </div>
                    </div>

                    <div class="info-row row">
                        <div class="col-sm-4 info-label">Modalidad:</div>
                        <div class="col-sm-8 info-value">
                            <?= htmlspecialchars($certificado['curso_modalidad']) ?>
                        </div>
                    </div>

                    <div class="info-row row">
                        <div class="col-sm-4 info-label">Instructor / Ponente:</div>
                        <div class="col-sm-8 info-value">
                            <?= htmlspecialchars($certificado['curso_instructor'] ?: 'No especificado') ?>
                        </div>
                    </div>

                    <div class="info-row row">
                        <div class="col-sm-4 info-label">Fecha de Emisión:</div>
                        <div class="col-sm-8 info-value">
                            <?= date('d/m/Y', strtotime($certificado['fecha_emision'])) ?>
                        </div>
                    </div>

                    <div class="info-row row">
                        <div class="col-sm-4 info-label">Institución Emisora:</div>
                        <div class="col-sm-8 info-value">
                            <?= htmlspecialchars($configuracion['institucion'] ?? 'Centro de Formación Continua') ?>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-top d-flex flex-column flex-sm-row justify-content-between align-items-center" style="gap: 12px;">
                        <a href="<?= BASE_URL ?>/consulta?dni=<?= urlencode($certificado['dni']) ?>" class="btn btn-outline-secondary font-weight-bold" style="border-radius: 8px;">
                            <i class="fa-solid fa-list-check mr-1"></i> Ver todos los cursos de <?= htmlspecialchars($certificado['nombres']) ?>
                        </a>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="cert-verify-card">
                <div class="verify-banner invalid">
                    <div class="verify-shield">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                    <h3 class="font-weight-bold mb-1" style="font-size: 24px;">CERTIFICADO NO ENCONTRADO</h3>
                    <p class="mb-0 opacity-90 font-size-14">El código consultado no corresponde a ningún documento emitido en nuestros registros oficiales.</p>
                </div>
                <div class="p-5 text-center">
                    <p class="text-muted font-size-15">
                        El código <strong><?= htmlspecialchars($codigo) ?></strong> no existe o ha sido cancelado.
                    </p>
                    <a href="<?= BASE_URL ?>/consulta" class="btn btn-primary font-weight-bold px-4 py-2" style="border-radius: 8px;">
                        <i class="fa-solid fa-magnifying-glass mr-1"></i> Ir al Buscador por DNI
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <footer class="text-center py-4 text-muted font-size-12 border-top bg-white mt-auto">
        <?= htmlspecialchars($configuracion['institucion'] ?? 'Centro de Formación Continua') ?> — Sistema de Verificación Oficial de Certificados
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>