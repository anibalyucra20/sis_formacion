<?php require __DIR__ . '/../../layouts/header.php'; ?>

<div class="container-fluid py-3">

    <!-- BREADCRUMB -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb bg-transparent p-0">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/tienda">Inicio</a></li>
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/tienda/carrito">Carrito</a></li>
            <li class="breadcrumb-item active" aria-current="page">Finalizar Compra</li>
        </ol>
    </nav>

    <!-- MENSAJES FLASH -->
    <?php if (!empty($_SESSION['flash_error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="mdi mdi-alert-circle mr-1"></i> <?= htmlspecialchars($_SESSION['flash_error']); unset($_SESSION['flash_error']); ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <h3 class="font-weight-bold text-dark mb-4">
        <i class="mdi mdi-credit-card-outline text-primary mr-2"></i> Finalizar Compra y Envío
    </h3>

    <form action="<?= BASE_URL ?>/tienda/checkout/procesar" method="POST" id="formCheckout">
        <div class="row">
            <!-- COLUMNA IZQUIERDA: DIRECCIÓN Y PAGO -->
            <div class="col-lg-7 mb-4">

                <!-- 1. DIRECCIÓN DE ENTREGA -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="card-title font-weight-bold mb-0">
                            <span class="badge badge-primary mr-2">1</span> Dirección de Entrega y Envío
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($direcciones)): ?>
                            <label class="font-weight-bold text-dark mb-2">Selecciona una de tus direcciones registradas:</label>
                            <?php foreach ($direcciones as $idx => $dir): ?>
                                <div class="custom-control custom-radio mb-3 p-3 border rounded <?= $dir['es_principal'] ? 'border-primary bg-soft-primary' : 'bg-light' ?>">
                                    <input type="radio" id="dir_<?= $dir['id'] ?>" name="direccion_id" value="<?= $dir['id'] ?>" 
                                           class="custom-control-input radio-direccion" 
                                           <?= ($dir['es_principal'] || $idx === 0) ? 'checked' : '' ?>
                                           onchange="toggleNuevaDireccion(false)">
                                    <label class="custom-control-label w-100 cursor-pointer" for="dir_<?= $dir['id'] ?>">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <strong><?= htmlspecialchars($dir['direccion']) ?></strong>
                                            <?php if ($dir['es_principal']): ?>
                                                <span class="badge badge-primary">Principal</span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="text-muted font-size-13">
                                            Ciudad: <?= htmlspecialchars($dir['ciudad']) ?>
                                            <?= !empty($dir['codigo_postal']) ? ' - CP: ' . htmlspecialchars($dir['codigo_postal']) : '' ?>
                                        </div>
                                    </label>
                                </div>
                            <?php endforeach; ?>

                            <div class="custom-control custom-radio mb-3 p-3 border rounded bg-light">
                                <input type="radio" id="dir_nueva" name="direccion_id" value="nueva" 
                                       class="custom-control-input radio-direccion" 
                                       onchange="toggleNuevaDireccion(true)">
                                <label class="custom-control-label w-100 cursor-pointer font-weight-bold" for="dir_nueva">
                                    <i class="mdi mdi-plus-circle text-success mr-1"></i> Usar una nueva dirección para este pedido
                                </label>
                            </div>
                        <?php else: ?>
                            <input type="hidden" name="direccion_id" value="nueva">
                            <div class="alert alert-info">
                                <i class="mdi mdi-information mr-1"></i> Aún no tienes direcciones registradas. Por favor ingresa a continuación la dirección donde deseas recibir tu pedido:
                            </div>
                        <?php endif; ?>

                        <!-- CAMPOS PARA NUEVA DIRECCIÓN -->
                        <div id="seccionNuevaDireccion" style="<?= !empty($direcciones) ? 'display: none;' : '' ?>" class="p-3 bg-light rounded border">
                            <h6 class="font-weight-bold text-primary mb-3">Nueva Dirección de Entrega:</h6>
                            <div class="form-group">
                                <label class="font-weight-bold">Dirección completa <span class="text-danger">*</span></label>
                                <input type="text" name="nueva_direccion" id="nueva_direccion" class="form-control" placeholder="Ej: Av. Las Flores 123, Urb. San José">
                            </div>
                            <div class="form-row">
                                <div class="col-md-7 form-group">
                                    <label class="font-weight-bold">Ciudad / Distrito <span class="text-danger">*</span></label>
                                    <input type="text" name="nueva_ciudad" id="nueva_ciudad" class="form-control" placeholder="Ej: Lima, Arequipa, etc.">
                                </div>
                                <div class="col-md-5 form-group">
                                    <label class="font-weight-bold">Código Postal (opcional)</label>
                                    <input type="text" name="nueva_cp" id="nueva_cp" class="form-control" placeholder="Ej: 15001">
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- 2. MÉTODO DE PAGO -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="card-title font-weight-bold mb-0">
                            <span class="badge badge-primary mr-2">2</span> Método de Pago
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-6 mb-3">
                                <div class="custom-control custom-radio p-3 border rounded h-100 bg-light">
                                    <input type="radio" id="pago_efectivo" name="metodo_pago" value="Efectivo" class="custom-control-input" checked>
                                    <label class="custom-control-label cursor-pointer" for="pago_efectivo">
                                        <i class="mdi mdi-cash-multiple text-success font-size-20 align-middle mr-1"></i>
                                        <strong>Pago Contra Entrega</strong>
                                        <small class="d-block text-muted">Paga en efectivo al recibir tus productos.</small>
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-6 mb-3">
                                <div class="custom-control custom-radio p-3 border rounded h-100 bg-light">
                                    <input type="radio" id="pago_tarjeta" name="metodo_pago" value="Tarjeta" class="custom-control-input">
                                    <label class="custom-control-label cursor-pointer" for="pago_tarjeta">
                                        <i class="mdi mdi-credit-card text-primary font-size-20 align-middle mr-1"></i>
                                        <strong>Tarjeta de Crédito / Débito</strong>
                                        <small class="d-block text-muted">Visa, Mastercard, Amex.</small>
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-6 mb-3">
                                <div class="custom-control custom-radio p-3 border rounded h-100 bg-light">
                                    <input type="radio" id="pago_yape" name="metodo_pago" value="Yape / Plin" class="custom-control-input">
                                    <label class="custom-control-label cursor-pointer" for="pago_yape">
                                        <i class="mdi mdi-cellphone-wireless text-info font-size-20 align-middle mr-1"></i>
                                        <strong>Yape / Plin</strong>
                                        <small class="d-block text-muted">Billeteras digitales rápidas.</small>
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-6 mb-3">
                                <div class="custom-control custom-radio p-3 border rounded h-100 bg-light">
                                    <input type="radio" id="pago_transf" name="metodo_pago" value="Transferencia" class="custom-control-input">
                                    <label class="custom-control-label cursor-pointer" for="pago_transf">
                                        <i class="mdi mdi-bank text-warning font-size-20 align-middle mr-1"></i>
                                        <strong>Transferencia Bancaria</strong>
                                        <small class="d-block text-muted">BCP, BBVA, Interbank, Scotiabank.</small>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- COLUMNA DERECHA: RESUMEN DE COMPRA -->
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="card-title font-weight-bold mb-0">
                            <span class="badge badge-primary mr-2">3</span> Resumen del Pedido (<?= $totalItems ?> items)
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive" style="max-height: 280px; overflow-y: auto;">
                            <table class="table table-borderless table-sm mb-0">
                                <tbody>
                                    <?php foreach ($carrito as $it): ?>
                                        <?php 
                                            $imgSrc = !empty($it['imagen']) ? BASE_URL . '/' . htmlspecialchars($it['imagen']) : BASE_URL . '/img/no-image.png';
                                        ?>
                                        <tr class="border-bottom">
                                            <td style="width: 60px;" class="p-2">
                                                <img src="<?= $imgSrc ?>" alt="<?= htmlspecialchars($it['nombre']) ?>" 
                                                     class="rounded border" style="width: 50px; height: 50px; object-fit: contain;">
                                            </td>
                                            <td class="p-2 align-middle">
                                                <h6 class="font-size-13 font-weight-bold mb-0 text-truncate" style="max-width: 180px;">
                                                    <?= htmlspecialchars($it['nombre']) ?>
                                                </h6>
                                                <small class="text-muted"><?= $it['cantidad'] ?> x S/ <?= number_format($it['precio'], 2) ?></small>
                                            </td>
                                            <td class="p-2 text-right align-middle font-weight-bold text-dark font-size-14">
                                                S/ <?= number_format($it['precio'] * $it['cantidad'], 2) ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="p-3 bg-light border-top">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Subtotal:</span>
                                <span class="font-weight-bold text-dark">S/ <?= number_format($subtotal, 2) ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Costo de Envío:</span>
                                <span class="text-success font-weight-bold">¡Envío Gratuito!</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="h5 font-weight-bold text-dark mb-0">Total a Pagar:</span>
                                <span class="h4 font-weight-bold text-primary mb-0">S/ <?= number_format($subtotal, 2) ?></span>
                            </div>

                            <button type="submit" id="btnConfirmarPedido" class="btn btn-primary btn-block btn-lg font-weight-bold shadow">
                                <i class="mdi mdi-check-decagram mr-1"></i> Confirmar y Realizar Pedido
                            </button>

                            <div class="text-center mt-2">
                                <small class="text-muted">
                                    <i class="mdi mdi-lock text-success mr-1"></i> Transacción segura y cifrada.
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm bg-soft-info p-3">
                    <div class="d-flex align-items-center">
                        <i class="mdi mdi-truck-fast-outline font-size-28 text-info mr-3"></i>
                        <div>
                            <h6 class="font-weight-bold text-info mb-1">Tu compra pasará a preparación de envío</h6>
                            <p class="font-size-12 text-muted mb-0">Una vez confirmada, el panel administrador de la tienda gestionará el empaque y despacho directo a tu dirección.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
function toggleNuevaDireccion(mostrar) {
    const seccion = document.getElementById('seccionNuevaDireccion');
    const inputDir = document.getElementById('nueva_direccion');
    const inputCiu = document.getElementById('nueva_ciudad');

    if (mostrar) {
        seccion.style.display = 'block';
        if (inputDir) inputDir.required = true;
        if (inputCiu) inputCiu.required = true;
    } else {
        seccion.style.display = 'none';
        if (inputDir) inputDir.required = false;
        if (inputCiu) inputCiu.required = false;
    }
}

document.getElementById('formCheckout').addEventListener('submit', function() {
    const btn = document.getElementById('btnConfirmarPedido');
    btn.innerHTML = '<i class="mdi mdi-spin mdi-loading mr-1"></i> Procesando Pedido...';
    btn.disabled = true;
});
</script>

<?php require __DIR__ . '/../../layouts/footer.php'; ?>
