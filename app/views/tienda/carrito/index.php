<?php require __DIR__ . '/../../layouts/header.php'; ?>

<div class="container-fluid py-3">

    <!-- BREADCRUMB -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb bg-transparent p-0">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/tienda">Inicio</a></li>
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/tienda/productos">Catálogo</a></li>
            <li class="breadcrumb-item active" aria-current="page">Carrito de Compras</li>
        </ol>
    </nav>

    <!-- MENSAJES FLASH -->
    <?php if (!empty($_SESSION['flash_success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="mdi mdi-check-circle mr-1"></i> <?= htmlspecialchars($_SESSION['flash_success']);
                                                        unset($_SESSION['flash_success']); ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>
    <?php if (!empty($_SESSION['flash_error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="mdi mdi-alert-circle mr-1"></i> <?= htmlspecialchars($_SESSION['flash_error']);
                                                        unset($_SESSION['flash_error']); ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <div class="d-flex align-items-center justify-content-between mb-3">
        <h3 class="font-weight-bold text-dark mb-0">
            <i class="mdi mdi-cart-outline text-primary mr-2"></i> Mi Carrito de Compras
        </h3>
        <?php if (!empty($carrito)): ?>
            <a href="<?= BASE_URL ?>/tienda/carrito/vaciar" class="btn btn-sm btn-outline-danger"
                onclick="return confirm('¿Estás seguro de que deseas vaciar todos los productos del carrito?');">
                <i class="mdi mdi-trash-can-outline mr-1"></i> Vaciar Carrito
            </a>
        <?php endif; ?>
    </div>

    <?php if (!empty($carrito)): ?>
        <div class="row">
            <!-- TABLA DE PRODUCTOS -->
            <div class="col-lg-8 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table align-middle table-nowrap mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th style="width: 100px;">Producto</th>
                                        <th>Detalles</th>
                                        <th class="text-right">Precio Unit.</th>
                                        <th class="text-center" style="width: 140px;">Cantidad</th>
                                        <th class="text-right">Subtotal</th>
                                        <th class="text-center" style="width: 50px;">Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($carrito as $id => $item): ?>
                                        <?php
                                        $imgSrc = !empty($item['imagen']) ? BASE_URL . '/public/uploads/productos/' . htmlspecialchars($item['imagen']) : BASE_URL . '/img/no-image.png';
                                        $subtotalItem = $item['precio'] * $item['cantidad'];
                                        ?>
                                        <tr id="fila-carrito-<?= $id ?>">
                                            <td class="text-center">
                                                <img src="<?= $imgSrc ?>" alt="<?= htmlspecialchars($item['nombre']) ?>"
                                                    class="rounded border p-1" style="width: 70px; height: 70px; object-fit: contain; background: #fafafa;">
                                            </td>
                                            <td>
                                                <h6 class="font-weight-bold mb-1">
                                                    <a href="<?= BASE_URL ?>/tienda/productos/detalle/<?= $id ?>" class="text-dark">
                                                        <?= htmlspecialchars($item['nombre']) ?>
                                                    </a>
                                                </h6>
                                                <small class="text-muted d-block">SKU: <?= htmlspecialchars($item['sku'] ?? 'N/A') ?></small>
                                                <small class="text-success"><i class="mdi mdi-check"></i> Stock disponible: <?= (int)$item['stock'] ?></small>
                                            </td>
                                            <td class="text-right font-weight-bold text-muted">
                                                S/ <?= number_format($item['precio'], 2) ?>
                                            </td>
                                            <td class="text-center">
                                                <div class="input-group input-group-sm mx-auto" style="width: 110px;">
                                                    <div class="input-group-prepend">
                                                        <button class="btn btn-outline-secondary btn-cantidad-ajuste" type="button"
                                                            onclick="actualizarCantidadItem(<?= $id ?>, -1)">
                                                            <i class="mdi mdi-minus"></i>
                                                        </button>
                                                    </div>
                                                    <input type="number" id="cantidad-input-<?= $id ?>"
                                                        class="form-control text-center font-weight-bold"
                                                        value="<?= $item['cantidad'] ?>" min="1" max="<?= $item['stock'] ?>"
                                                        onchange="enviarCantidadItem(<?= $id ?>, this.value)">
                                                    <div class="input-group-append">
                                                        <button class="btn btn-outline-secondary btn-cantidad-ajuste" type="button"
                                                            onclick="actualizarCantidadItem(<?= $id ?>, 1)">
                                                            <i class="mdi mdi-plus"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-right font-weight-bold text-primary font-size-15" id="subtotal-item-<?= $id ?>">
                                                S/ <?= number_format($subtotalItem, 2) ?>
                                            </td>
                                            <td class="text-center">
                                                <a href="<?= BASE_URL ?>/tienda/carrito/eliminar/<?= $id ?>"
                                                    class="btn btn-sm btn-outline-danger rounded-circle p-1"
                                                    title="Eliminar producto"
                                                    onclick="return confirm('¿Quitar este producto del carrito?');">
                                                    <i class="mdi mdi-trash-can font-size-16"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="mt-3">
                    <a href="<?= BASE_URL ?>/tienda/productos" class="btn btn-outline-primary">
                        <i class="mdi mdi-arrow-left mr-1"></i> Seguir Comprando
                    </a>
                </div>
            </div>

            <!-- RESUMEN DE COMPRA Y CHECKOUT -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="card-title font-weight-bold mb-0">Resumen del Pedido</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Total de Artículos:</span>
                            <span class="font-weight-bold" id="resumen-total-items"><?= $totalItems ?> items</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Subtotal:</span>
                            <span class="font-weight-bold text-dark font-size-15" id="resumen-subtotal">S/ <?= number_format($subtotal, 2) ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Costo de Envío:</span>
                            <span class="text-success font-weight-bold">¡Gratis!</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <span class="h5 font-weight-bold text-dark mb-0">Total a Pagar:</span>
                            <span class="h4 font-weight-bold text-primary mb-0" id="resumen-total">S/ <?= number_format($subtotal, 2) ?></span>
                        </div>

                        <!-- BOTÓN PROCEDER AL CHECKOUT -->
                        <a href="<?= BASE_URL ?>/tienda/checkout" class="btn btn-primary btn-block btn-lg font-weight-bold shadow">
                            <i class="mdi mdi-credit-card-check mr-1"></i> Proceder al Pago
                        </a>

                        <?php if (!\Core\Auth::user()): ?>
                            <div class="alert alert-light border mt-3 mb-0 p-2 text-center font-size-12 text-muted">
                                <i class="mdi mdi-account-lock-outline text-primary mr-1"></i>
                                Se te solicitará iniciar sesión o registrarte al finalizar la compra para asociar tu pedido.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- INFO DE CONFIANZA -->
                <div class="card border-0 shadow-sm bg-light">
                    <div class="card-body p-3">
                        <h6 class="font-weight-bold mb-2 font-size-13 text-dark"><i class="mdi mdi-shield-check text-success mr-1"></i> Garantía y Seguridad</h6>
                        <ul class="list-unstyled mb-0 font-size-12 text-muted pl-1">
                            <li class="mb-1"><i class="mdi mdi-check text-primary mr-1"></i> Pagos 100% seguros y transparentes</li>
                            <li class="mb-1"><i class="mdi mdi-check text-primary mr-1"></i> Despacho coordinado para tu dirección</li>
                            <li><i class="mdi mdi-check text-primary mr-1"></i> Seguimiento en tiempo real desde tu perfil</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <!-- CARRITO VACÍO -->
        <div class="card border-0 shadow-sm text-center py-5">
            <div class="card-body py-5">
                <i class="mdi mdi-cart-off text-muted" style="font-size: 72px;"></i>
                <h4 class="font-weight-bold text-dark mt-3">Tu carrito está vacío</h4>
                <p class="text-muted">Aún no has agregado ningún producto a tu carrito de compras.</p>
                <a href="<?= BASE_URL ?>/tienda/productos" class="btn btn-primary btn-lg font-weight-bold mt-2">
                    <i class="mdi mdi-store mr-1"></i> Explorar Catálogo de Productos
                </a>
            </div>
        </div>
    <?php endif; ?>

</div>

<!-- SCRIPT PARA GESTIONAR CANTIDADES EN VIVO VIA AJAX -->
<script>
    function actualizarCantidadItem(id, delta) {
        const input = document.getElementById('cantidad-input-' + id);
        if (!input) return;
        let actual = parseInt(input.value) || 1;
        let max = parseInt(input.getAttribute('max')) || 999;
        let nuevo = actual + delta;
        if (nuevo < 1) nuevo = 1;
        if (nuevo > max) {
            if (window.Swal) {
                Swal.fire('Stock Límite', 'Solo disponemos de ' + max + ' unidades de este producto.', 'info');
            }
            nuevo = max;
        }
        input.value = nuevo;
        enviarCantidadItem(id, nuevo);
    }

    function enviarCantidadItem(id, cantidad) {
        const formData = new FormData();
        formData.append('producto_id', id);
        formData.append('cantidad', cantidad);

        fetch('<?= BASE_URL ?>/tienda/carrito/actualizar', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    // Actualizar fila
                    const subtotalElem = document.getElementById('subtotal-item-' + id);
                    if (subtotalElem) {
                        subtotalElem.textContent = 'S/ ' + data.item_subtotal;
                    }
                    // Actualizar resumen
                    const resSub = document.getElementById('resumen-subtotal');
                    const resTot = document.getElementById('resumen-total');
                    const resItm = document.getElementById('resumen-total-items');
                    if (resSub) resSub.textContent = 'S/ ' + data.total_general;
                    if (resTot) resTot.textContent = 'S/ ' + data.total_general;
                    if (resItm) resItm.textContent = data.total_items + ' items';

                    // Actualizar badge en header
                    const badge = document.getElementById('badge-contador-carrito');
                    if (badge) {
                        badge.textContent = data.total_items;
                    }
                } else {
                    if (window.Swal) {
                        Swal.fire('Atención', data.mensaje || 'Error al actualizar', 'warning');
                    } else {
                        alert(data.mensaje || 'Error al actualizar');
                    }
                    window.location.reload();
                }
            })
            .catch(err => {
                console.error(err);
                window.location.reload();
            });
    }
</script>

<?php require __DIR__ . '/../../layouts/footer.php'; ?>