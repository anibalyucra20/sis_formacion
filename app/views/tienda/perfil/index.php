<?php require __DIR__ . '/../../layouts/header.php'; ?>

<div class="container-fluid py-3">

    <!-- BREADCRUMB -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb bg-transparent p-0">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/tienda">Inicio</a></li>
            <li class="breadcrumb-item active" aria-current="page">Mi Cuenta y Perfil</li>
        </ol>
    </nav>

    <!-- MENSAJES FLASH -->
    <?php if (!empty($_SESSION['flash_success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="mdi mdi-check-circle mr-1"></i> <?= htmlspecialchars($_SESSION['flash_success']); unset($_SESSION['flash_success']); ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>
    <?php if (!empty($_SESSION['flash_error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="mdi mdi-alert-circle mr-1"></i> <?= htmlspecialchars($_SESSION['flash_error']); unset($_SESSION['flash_error']); ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <div class="row">
        <!-- BARRA LATERAL DEL PERFIL -->
        <div class="col-lg-3 col-md-4 mb-4">
            <div class="card border-0 shadow-sm text-center p-3 mb-3">
                <div class="avatar-lg mx-auto mb-3 bg-soft-primary rounded-circle d-flex align-items-center justify-content-center text-primary font-size-32">
                    <i class="mdi mdi-account"></i>
                </div>
                <h5 class="font-weight-bold text-dark mb-1"><?= htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellidos']) ?></h5>
                <p class="text-muted font-size-13 mb-3"><?= htmlspecialchars($usuario['email']) ?></p>

                <div class="list-group text-left border-0 font-size-14">
                    <a href="<?= BASE_URL ?>/tienda/perfil" class="list-group-item list-group-item-action active font-weight-bold">
                        <i class="mdi mdi-account-circle-outline mr-2"></i> Mis Datos y Direcciones
                    </a>
                    <a href="<?= BASE_URL ?>/tienda/perfil/pedidos" class="list-group-item list-group-item-action">
                        <i class="mdi mdi-package-variant-closed mr-2"></i> Mis Compras y Pedidos
                    </a>
                    <a href="<?= BASE_URL ?>/tienda/carrito" class="list-group-item list-group-item-action">
                        <i class="mdi mdi-cart-outline mr-2"></i> Ver Mi Carrito
                    </a>
                    <a href="<?= BASE_URL ?>/logout" class="list-group-item list-group-item-action text-danger">
                        <i class="mdi mdi-logout mr-2"></i> Cerrar Sesión
                    </a>
                </div>
            </div>
        </div>

        <!-- CONTENIDO PRINCIPAL: DATOS Y DIRECCIONES -->
        <div class="col-lg-9 col-md-8">

            <!-- 1. GESTIÓN DE DATOS PERSONALES -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="card-title font-weight-bold mb-0">
                        <i class="mdi mdi-card-account-details-outline text-primary mr-2"></i> Información Personal
                    </h5>
                </div>
                <div class="card-body">
                    <form action="<?= BASE_URL ?>/tienda/perfil/guardarPerfil" method="POST">
                        <div class="form-row">
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold">Nombres <span class="text-danger">*</span></label>
                                <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($usuario['nombre']) ?>" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold">Apellidos <span class="text-danger">*</span></label>
                                <input type="text" name="apellidos" class="form-control" value="<?= htmlspecialchars($usuario['apellidos']) ?>" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold">Correo Electrónico <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($usuario['email']) ?>" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold">Teléfono / Celular</label>
                                <input type="text" name="telefono" class="form-control" value="<?= htmlspecialchars($usuario['telefono'] ?? '') ?>" placeholder="Ej: 987654321">
                            </div>
                        </div>

                        <hr class="my-3">
                        <h6 class="font-weight-bold text-dark mb-2">Cambiar Contraseña (opcional)</h6>
                        <small class="text-muted d-block mb-3">Deja estos campos en blanco si no deseas cambiar tu contraseña actual.</small>

                        <div class="form-row">
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold">Nueva Contraseña</label>
                                <input type="password" name="password" class="form-control" placeholder="Mínimo 6 caracteres">
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold">Confirmar Nueva Contraseña</label>
                                <input type="password" name="password_confirm" class="form-control" placeholder="Repite la nueva contraseña">
                            </div>
                        </div>

                        <div class="text-right mt-2">
                            <button type="submit" class="btn btn-primary font-weight-bold px-4">
                                <i class="mdi mdi-content-save mr-1"></i> Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- 2. GESTIÓN DE DIRECCIONES -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <h5 class="card-title font-weight-bold mb-0">
                        <i class="mdi mdi-map-marker-multiple-outline text-primary mr-2"></i> Mis Direcciones de Entrega
                    </h5>
                    <button type="button" class="btn btn-sm btn-primary font-weight-bold" onclick="abrirModalDireccion()">
                        <i class="mdi mdi-plus mr-1"></i> Nueva Dirección
                    </button>
                </div>
                <div class="card-body">
                    <?php if (!empty($direcciones)): ?>
                        <div class="row">
                            <?php foreach ($direcciones as $dir): ?>
                                <div class="col-md-6 mb-3">
                                    <div class="card h-100 border <?= $dir['es_principal'] ? 'border-primary bg-soft-primary' : 'bg-light' ?> shadow-none">
                                        <div class="card-body p-3 d-flex flex-column">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h6 class="font-weight-bold text-dark mb-0">
                                                    <i class="mdi mdi-home-map-marker text-primary mr-1"></i>
                                                    <?= htmlspecialchars($dir['direccion']) ?>
                                                </h6>
                                                <?php if ($dir['es_principal']): ?>
                                                    <span class="badge badge-primary">Predeterminada</span>
                                                <?php endif; ?>
                                            </div>

                                            <p class="text-muted font-size-13 mb-1">
                                                <strong>Ciudad:</strong> <?= htmlspecialchars($dir['ciudad']) ?>
                                            </p>
                                            <?php if (!empty($dir['codigo_postal'])): ?>
                                                <p class="text-muted font-size-13 mb-2">
                                                    <strong>Código Postal:</strong> <?= htmlspecialchars($dir['codigo_postal']) ?>
                                                </p>
                                            <?php endif; ?>

                                            <div class="mt-auto pt-2 border-top d-flex justify-content-between align-items-center">
                                                <?php if (!$dir['es_principal']): ?>
                                                    <a href="<?= BASE_URL ?>/tienda/perfil/setPrincipalDireccion/<?= $dir['id'] ?>" class="btn btn-sm btn-link text-primary p-0 font-size-12">
                                                        <i class="mdi mdi-star-outline mr-1"></i> Hacer principal
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-muted font-size-12"><i class="mdi mdi-check"></i> Dirección actual</span>
                                                <?php endif; ?>

                                                <div>
                                                    <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" 
                                                            onclick="editarDireccion(<?= htmlspecialchars(json_encode($dir)) ?>)">
                                                        <i class="mdi mdi-pencil"></i>
                                                    </button>
                                                    <a href="<?= BASE_URL ?>/tienda/perfil/eliminarDireccion/<?= $dir['id'] ?>" 
                                                       class="btn btn-sm btn-outline-danger py-0 px-2 ml-1" 
                                                       onclick="return confirm('¿Deseas eliminar esta dirección?');">
                                                        <i class="mdi mdi-trash-can"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="mdi mdi-map-marker-off text-muted font-size-48"></i>
                            <p class="text-muted mt-2">No tienes direcciones registradas todavía.</p>
                            <button type="button" class="btn btn-primary font-weight-bold" onclick="abrirModalDireccion()">
                                <i class="mdi mdi-plus mr-1"></i> Agregar mi primera dirección
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>

</div>

<!-- MODAL PARA AGREGAR / EDITAR DIRECCIÓN -->
<div class="modal fade" id="modalDireccion" tabindex="-1" role="dialog" aria-labelledby="modalDireccionLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow">
            <form action="<?= BASE_URL ?>/tienda/perfil/guardarDireccion" method="POST">
                <input type="hidden" name="id" id="modal_dir_id" value="">

                <div class="modal-header bg-primary text-white py-3">
                    <h5 class="modal-title text-white font-weight-bold" id="modalDireccionLabel">
                        <i class="mdi mdi-map-marker-plus mr-1"></i> Dirección de Entrega
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body p-4">
                    <div class="form-group">
                        <label class="font-weight-bold">Dirección Completa <span class="text-danger">*</span></label>
                        <input type="text" name="direccion" id="modal_dir_direccion" class="form-control" placeholder="Ej: Calle Los Pinos 456, Depto 201" required>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Ciudad / Distrito <span class="text-danger">*</span></label>
                        <input type="text" name="ciudad" id="modal_dir_ciudad" class="form-control" placeholder="Ej: Lima, Miraflores" required>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Código Postal</label>
                        <input type="text" name="codigo_postal" id="modal_dir_cp" class="form-control" placeholder="Ej: 15074">
                    </div>

                    <div class="custom-control custom-checkbox mt-3">
                        <input type="checkbox" name="es_principal" value="1" class="custom-control-input" id="modal_dir_principal">
                        <label class="custom-control-label font-weight-bold" for="modal_dir_principal">
                            Establecer como dirección predeterminada
                        </label>
                    </div>
                </div>

                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary font-weight-bold">
                        <i class="mdi mdi-check mr-1"></i> Guardar Dirección
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function abrirModalDireccion() {
    document.getElementById('modal_dir_id').value = '';
    document.getElementById('modal_dir_direccion').value = '';
    document.getElementById('modal_dir_ciudad').value = '';
    document.getElementById('modal_dir_cp').value = '';
    document.getElementById('modal_dir_principal').checked = false;
    document.getElementById('modalDireccionLabel').innerHTML = '<i class="mdi mdi-map-marker-plus mr-1"></i> Nueva Dirección de Entrega';
    $('#modalDireccion').modal('show');
}

function editarDireccion(dir) {
    document.getElementById('modal_dir_id').value = dir.id;
    document.getElementById('modal_dir_direccion').value = dir.direccion;
    document.getElementById('modal_dir_ciudad').value = dir.ciudad;
    document.getElementById('modal_dir_cp').value = dir.codigo_postal || '';
    document.getElementById('modal_dir_principal').checked = dir.es_principal == 1;
    document.getElementById('modalDireccionLabel').innerHTML = '<i class="mdi mdi-pencil mr-1"></i> Editar Dirección de Entrega';
    $('#modalDireccion').modal('show');
}
</script>

<?php require __DIR__ . '/../../layouts/footer.php'; ?>
