<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?= $pageTitle ?? 'Iniciar Sesión - Control de Certificados' ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" type="image/x-icon" href="https://iesphuanta.edu.pe/wp-content/uploads/2021/12/WhatsApp-Image-2025-09-17-at-12.55.48-PM-Photoroom.png">
    <style>
        :root {
            --primary: #1e3a8a;
            --primary-hover: #172554;
            --accent: #2563eb;
            --bg-gradient: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--bg-gradient);
            min-height: 100vh;
            color: #334155;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
        }

        .auth-card {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35);
            width: 100%;
            max-width: 440px;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .auth-header {
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
            padding: 32px 24px;
            text-align: center;
            color: #ffffff;
        }

        .auth-header .icon-wrap {
            width: 64px;
            height: 64px;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
            font-size: 28px;
            color: #ffffff;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
        }

        .auth-header h3 {
            font-size: 20px;
            font-weight: 700;
            margin: 0;
            letter-spacing: -0.02em;
        }

        .auth-header p {
            font-size: 13px;
            margin: 6px 0 0;
            opacity: 0.85;
        }

        .auth-body {
            padding: 32px 28px 24px;
        }

        .form-label {
            font-size: 13px;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 6px;
        }

        .form-control {
            height: 46px;
            border-radius: 8px;
            border: 1.5px solid #e2e8f0;
            font-size: 14px;
            padding: 10px 14px;
            transition: all 0.2s ease;
        }

        .form-control:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
        }

        .input-group-text {
            background: #ffffff;
            border: 1.5px solid #e2e8f0;
            border-left: none;
            border-radius: 0 8px 8px 0;
            cursor: pointer;
            color: #64748b;
        }

        .btn-submit {
            height: 48px;
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
            border: none;
            border-radius: 8px;
            color: #ffffff;
            font-weight: 600;
            font-size: 15px;
            letter-spacing: 0.01em;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
        }

        .btn-submit:hover {
            background: linear-gradient(135deg, #172554 0%, #1d4ed8 100%);
            box-shadow: 0 6px 16px rgba(37, 99, 235, 0.35);
            color: #ffffff;
            transform: translateY(-1px);
        }

        .auth-footer {
            padding: 16px 28px 24px;
            background: #f8fafc;
            border-top: 1px solid #f1f5f9;
            text-align: center;
            font-size: 13px;
        }

        .demo-badge {
            background: #f1f5f9;
            border-radius: 8px;
            padding: 10px 12px;
            font-size: 12px;
            color: #475569;
            margin-bottom: 20px;
            border: 1px dashed #cbd5e1;
        }
    </style>
</head>

<body>

    <div class="auth-card">
        <div class="auth-header">
            <img src="https://iesphuanta.edu.pe/wp-content/uploads/2021/12/WhatsApp-Image-2025-09-17-at-12.55.48-PM-Photoroom.png" alt="" width="40%">
            <h3>Control de Certificados</h3>
            <p>Panel Administrativo Institucional</p>
        </div>

        <div class="auth-body">
            <?php if (!empty($_SESSION['flash_success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert" style="font-size: 13px;">
                    <i class="fa-solid fa-circle-check mr-1"></i> <?= $_SESSION['flash_success'] ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <?php unset($_SESSION['flash_success']); ?>
            <?php endif; ?>

            <?php if (!empty($_SESSION['flash_error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert" style="font-size: 13px;">
                    <i class="fa-solid fa-triangle-exclamation mr-1"></i> <?= $_SESSION['flash_error'] ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <?php unset($_SESSION['flash_error']); ?>
            <?php endif; ?>

            <form action="<?= BASE_URL ?>/login/acceder" method="POST">
                <div class="form-group mb-3">
                    <label class="form-label" for="correo">Correo Electrónico</label>
                    <div class="input-group">
                        <input type="email" name="correo" id="correo" class="form-control" placeholder="admin@certificados.com" required autofocus value="admin@certificados.com">
                    </div>
                </div>

                <div class="form-group mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <label class="form-label" for="password">Contraseña</label>
                        <a href="<?= BASE_URL ?>/recuperar" style="font-size: 12px; color: var(--accent); font-weight: 500;">¿Olvidaste tu contraseña?</a>
                    </div>
                    <div class="input-group">
                        <input type="password" name="password" id="password" class="form-control" placeholder="••••••••" required value="admin123" style="border-right: none;">
                        <div class="input-group-append">
                            <span class="input-group-text" id="togglePassword">
                                <i class="fa-solid fa-eye" id="toggleIcon"></i>
                            </span>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-submit btn-block">
                    <i class="fa-solid fa-right-to-bracket mr-2"></i> Iniciar Sesión
                </button>
            </form>
        </div>

        <div class="auth-footer d-flex justify-content-between align-items-center">
            <a href="<?= BASE_URL ?>/consulta" class="text-decoration-none" style="color: #475569; font-weight: 500;">
                <i class="fa-solid fa-arrow-left mr-1"></i> Consulta de Certificados por DNI
            </a>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const icon = document.getElementById('toggleIcon');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    </script>
</body>

</html>