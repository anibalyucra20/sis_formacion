<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?= $pageTitle ?? 'Nueva Contraseña' ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" type="image/x-icon" href="https://iesphuanta.edu.pe/wp-content/uploads/2021/12/WhatsApp-Image-2025-09-17-at-12.55.48-PM-Photoroom.png">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
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
        }

        .auth-header {
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
            padding: 32px 24px;
            text-align: center;
            color: #ffffff;
        }

        .auth-header .icon-wrap {
            width: 60px;
            height: 60px;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 14px;
            font-size: 26px;
            color: #ffffff;
        }

        .auth-header h3 {
            font-size: 20px;
            font-weight: 700;
            margin: 0;
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
        }

        .btn-submit {
            height: 48px;
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
            border: none;
            border-radius: 8px;
            color: #ffffff;
            font-weight: 600;
            font-size: 15px;
            transition: all 0.2s ease;
        }

        .btn-submit:hover {
            opacity: 0.95;
            color: #ffffff;
        }

        .auth-footer {
            padding: 16px 28px 24px;
            background: #f8fafc;
            border-top: 1px solid #f1f5f9;
            text-align: center;
            font-size: 13px;
        }
    </style>
</head>

<body>

    <div class="auth-card">
        <div class="auth-header">
            <img src="https://iesphuanta.edu.pe/wp-content/uploads/2021/12/WhatsApp-Image-2025-09-17-at-12.55.48-PM-Photoroom.png" alt="" width="40%">
            <h3>Nueva Contraseña</h3>
            <p>Defina una contraseña segura para su cuenta</p>
        </div>

        <div class="auth-body">
            <?php if (!empty($_SESSION['flash_error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert" style="font-size: 13px;">
                    <i class="fa-solid fa-triangle-exclamation mr-1"></i> <?= $_SESSION['flash_error'] ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <?php unset($_SESSION['flash_error']); ?>
            <?php endif; ?>

            <form action="<?= BASE_URL ?>/guardarNuevaPassword" method="POST">
                <input type="hidden" name="token" value="<?= htmlspecialchars($token ?? '') ?>">
                <input type="hidden" name="email" value="<?= htmlspecialchars($email ?? '') ?>">

                <div class="form-group mb-3">
                    <label class="form-label" for="password">Nueva Contraseña</label>
                    <input type="password" name="password" id="password" class="form-control" placeholder="Mínimo 6 caracteres" required autofocus>
                </div>

                <div class="form-group mb-4">
                    <label class="form-label" for="password_confirm">Confirmar Nueva Contraseña</label>
                    <input type="password" name="password_confirm" id="password_confirm" class="form-control" placeholder="Repita la contraseña" required>
                </div>

                <button type="submit" class="btn btn-submit btn-block">
                    <i class="fa-solid fa-check mr-2"></i> Guardar Nueva Contraseña
                </button>
            </form>
        </div>

        <div class="auth-footer">
            <a href="<?= BASE_URL ?>/login" class="text-decoration-none font-weight-bold" style="color: #2563eb;">
                <i class="fa-solid fa-arrow-left mr-1"></i> Cancelar y Volver
            </a>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>