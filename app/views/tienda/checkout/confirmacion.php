<?php require __DIR__ . '/../../layouts/header.php'; ?>

<div class="container-fluid py-4">

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- TARJETA PRINCIPAL DE ÉXITO -->
            <div class="card border-0 shadow-sm text-center p-4 mb-4" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                <div class="card-body text-white py-4">
                    <div class="avatar-lg bg-white rounded-circle mx-auto d-flex align-items-center justify-content-center text-success mb-3 shadow">
                        <i class="mdi mdi-check font-size-36"></i>
                    </div>
                    <h2 class="font-weight-bold text-white mb-2">¡Muchas gracias por tu compra!</h2>
                    <p class="font-size-16 text-light mb-1">Tu pedido se ha registrado correctamente con el número de orden:</p>
                    <span class="badge badge-light font-size-18 px-4 py-2 font-weight-bold text-dark mb-3">
                        PEDIDO #<?= str_pad($venta['id'], 6, '0', STR_PAD_LEFT) ?>
                    </span>
                    <p class="text-white-50 font-size-13 mb-0">Hemos notificado a nuestro equipo para la preparación y el despacho de tus productos.</p>
                </div>
            </div>

            <!-- DETALLES DEL PEDIDO -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <h5 class="card-title font-weight-bold mb-0">
                        <i class="mdi mdi-receipt text-primary mr-1"></i> Resumen de la Compra
                    </h5>
                    <?php 
                        $badgeClass = 'badge-warning';
                        if ($venta['estado'] === 'Pagado') $badgeClass = 'badge-success';
                        elseif ($venta['estado'] === 'Enviado') $badgeClass = 'badge-info';
                        elseif ($venta['estado'] === 'Entregado') $badgeClass = 'badge-primary';
                        elseif ($venta['estado'] === 'Cancelado') $badgeClass = 'badge-danger';
                    ?>
                    <span class="badge <?= $badgeClass ?> font-size-13 px-3 py-1">
                        Estado: <?= htmlspecialchars($venta['estado']) ?>
                    </span>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-sm-6 mb-3 mb-sm-0">
                            <h6 class="font-weight-bold text-muted text-uppercase font-size-12 mb-2">Datos del Cliente y Entrega</h6>
                            <p class="mb-1 font-weight-bold text-dark font-size-15"><?= htmlspecialchars($venta['cliente_nombre']) ?></p>
                            <p class="mb-1 text-muted font-size-13"><i class="mdi mdi-email-outline mr-1"></i> <?= htmlspecialchars($venta['cliente_email']) ?></p>
                            <?php if (!empty($venta['cliente_telefono'])): ?>
                                <p class="mb-1 text-muted font-size-13"><i class="mdi mdi-phone-outline mr-1"></i> <?= htmlspecialchars($venta['cliente_telefono']) ?></p>
                            <?php endif; ?>
                            <?php if (!empty($direccion)): ?>
                                <p class="mb-0 text-muted font-size-13">
                                    <i class="mdi mdi-map-marker-outline mr-1 text-danger"></i> 
                                    <?= htmlspecialchars($direccion['direccion']) ?>, <?= htmlspecialchars($direccion['ciudad']) ?>
                                </p>
                            <?php endif; ?>
                        </div>
                        <div class="col-sm-6 text-sm-right">
                            <h6 class="font-weight-bold text-muted text-uppercase font-size-12 mb-2">Información de Pago</h6>
                            <p class="mb-1 font-size-13"><strong>Método:</strong> <?= htmlspecialchars($venta['metodo_pago']) ?></p>
                            <p class="mb-1 font-size-13"><strong>Fecha:</strong> <?= date('d/m/Y H:i A', strtotime($venta['fecha_venta'])) ?></p>
                            <p class="mb-0 font-size-13"><strong>Envío:</strong> <span class="text-success font-weight-bold">Gratuito</span></p>
                        </div>
                    </div>

                    <!-- TABLA DE PRODUCTOS -->
                    <h6 class="font-weight-bold text-dark mb-2">Productos Adquiridos:</h6>
                    <div class="table-responsive border rounded">
                        <table class="table align-middle table-nowrap mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Producto</th>
                                    <th class="text-center">SKU</th>
                                    <th class="text-center">Cantidad</th>
                                    <th class="text-right">Precio Unit.</th>
                                    <th class="text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($detalles as $det): ?>
                                    <?php 
                                        $imgSrc = !empty($det['imagen']) ? BASE_URL . '/' . htmlspecialchars($det['imagen']) : BASE_URL . '/img/no-image.png';
                                    ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="<?= $imgSrc ?>" alt="<?= htmlspecialchars($det['producto_nombre']) ?>" 
                                                     class="rounded mr-2 border" style="width: 45px; height: 45px; object-fit: contain;">
                                                <span class="font-weight-bold text-dark"><?= htmlspecialchars($det['producto_nombre']) ?></span>
                                            </div>
                                        </td>
                                        <td class="text-center text-muted"><?= htmlspecialchars($det['codigo_sku'] ?? 'N/A') ?></td>
                                        <td class="text-center font-weight-bold"><?= $det['cantidad'] ?></td>
                                        <td class="text-right">S/ <?= number_format($det['precio_unitario'], 2) ?></td>
                                        <td class="text-right font-weight-bold text-primary">S/ <?= number_format($det['subtotal'], 2) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot class="bg-light">
                                <tr>
                                    <td colspan="4" class="text-right font-weight-bold font-size-15">Total Cancelado:</td>
                                    <td class="text-right font-weight-bold font-size-18 text-primary">S/ <?= number_format($venta['total_venta'], 2) ?></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white border-top py-3 d-flex flex-wrap justify-content-between align-items-center">
                    <div>
                        <a href="<?= BASE_URL ?>/tienda" class="btn btn-outline-secondary mr-2">
                            <i class="mdi mdi-arrow-left mr-1"></i> Ir a la Tienda
                        </a>
                        <a href="<?= BASE_URL ?>/tienda/perfil/pedidos" class="btn btn-outline-primary">
                            <i class="mdi mdi-package-variant-closed mr-1"></i> Mis Pedidos
                        </a>
                    </div>
                    <div>
                        <a href="<?= BASE_URL ?>/admin/ventas/comprobante/<?= $venta['id'] ?>" target="_blank" class="btn btn-success font-weight-bold shadow-sm">
                            <i class="mdi mdi-printer mr-1"></i> Imprimir Comprobante
                        </a>
                    </div>
                </div>
            </div>

            <!-- AVISO ADMINISTRADOR -->
            <div class="alert alert-soft-warning border-0 d-flex align-items-center">
                <i class="mdi mdi-truck-delivery-outline font-size-24 text-warning mr-3"></i>
                <div>
                    <h6 class="font-weight-bold mb-1">Información de despacho</h6>
                    <small>Este pedido ya se encuentra visible en el panel de administración con estado <strong><?= htmlspecialchars($venta['estado']) ?></strong>. El administrador gestionará su posterior envío y entrega.</small>
                </div>
            </div>

        </div>
    </div>

</div>

<?php require __DIR__ . '/../../layouts/footer.php'; ?>
