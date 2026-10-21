<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Crear Cuenta - Tienda Virtual</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="icon" type="image/x-icon" href="<?= BASE_URL ?>/img/favicon.ico">
    <style>
        body {
            background-color: #f4f7f6;
            min-height: 100vh;
        }
        .registro-container {
            min-height: 100vh;
            padding: 40px 15px;
        }
        .card {
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
            border: none;
        }
        .logo {
            width: 140px;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <div class="container d-flex align-items-center justify-content-center registro-container">
        <div class="col-md-8 col-lg-6">
            <div class="card p-4">
                <div class="text-center mb-4">
                    <a href="<?= BASE_URL ?>/tienda">
                        <img src="<?= BASE_URL ?>/img/logo_completo.png" alt="Logo" class="logo">
                    </a>
                    <h4 class="mb-1 text-primary font-weight-bold">Crear Cuenta de Cliente</h4>
                    <p class="text-muted">Complete sus datos para comprar y hacer seguimiento a sus pedidos</p>
                </div>

                <?php if (!empty($_SESSION['flash_error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= $_SESSION['flash_error'] ?>
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                    </div>
                    <?php unset($_SESSION['flash_error']); ?>
                <?php endif; ?>

                <form action="<?= BASE_URL ?>/login/registrar" method="post" autocomplete="off">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label class="font-weight-bold">Nombres <span class="text-danger">*</span></label>
                            <input type="text" name="nombre" class="form-control" required placeholder="Ej: Carlos">
                        </div>
                        <div class="form-group col-md-6">
                            <label class="font-weight-bold">Apellidos <span class="text-danger">*</span></label>
                            <input type="text" name="apellidos" class="form-control" required placeholder="Ej: Mendoza">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Correo Electrónico <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" required placeholder="ejemplo@correo.com">
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Teléfono / WhatsApp</label>
                        <input type="text" name="telefono" class="form-control" placeholder="Ej: 987654321">
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label class="font-weight-bold">Contraseña <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control" required minlength="6" placeholder="Mínimo 6 caracteres">
                        </div>
                        <div class="form-group col-md-6">
                            <label class="font-weight-bold">Confirmar Contraseña <span class="text-danger">*</span></label>
                            <input type="password" name="password_confirm" class="form-control" required minlength="6" placeholder="Repita la contraseña">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block btn-lg font-weight-bold shadow-sm mt-3">
                        Registrarme y Comenzar a Comprar
                    </button>
                </form>

                <div class="mt-4 text-center border-top pt-3">
                    <p class="mb-1 text-muted">¿Ya tienes una cuenta?</p>
                    <a href="<?= BASE_URL ?>/login" class="font-weight-bold text-primary">Inicia Sesión aquí</a>
                    <div class="mt-2">
                        <a href="<?= BASE_URL ?>/tienda" class="text-muted font-size-12"><i class="fas fa-arrow-left mr-1"></i> Volver a la Tienda</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
