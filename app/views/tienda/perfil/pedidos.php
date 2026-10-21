<?php require __DIR__ . '/../../layouts/header.php'; ?>

<div class="container-fluid py-3">

    <!-- BREADCRUMB -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb bg-transparent p-0">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/tienda">Inicio</a></li>
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/tienda/perfil">Mi Cuenta</a></li>
            <li class="breadcrumb-item active" aria-current="page">Mis Pedidos</li>
        </ol>
    </nav>

    <div class="row">
        <!-- BARRA LATERAL -->
        <div class="col-lg-3 col-md-4 mb-4">
            <div class="card border-0 shadow-sm p-3">
                <div class="list-group text-left border-0 font-size-14">
                    <a href="<?= BASE_URL ?>/tienda/perfil" class="list-group-item list-group-item-action">
                        <i class="mdi mdi-account-circle-outline mr-2"></i> Mis Datos y Direcciones
                    </a>
                    <a href="<?= BASE_URL ?>/tienda/perfil/pedidos" class="list-group-item list-group-item-action active font-weight-bold">
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

        <!-- LISTADO DE PEDIDOS -->
        <div class="col-lg-9 col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="card-title font-weight-bold mb-0">
                        <i class="mdi mdi-package-variant-closed text-primary mr-2"></i> Historial de Compras Realizadas
                    </h5>
                </div>
                <div class="card-body p-0">
                    <?php if (!empty($pedidos)): ?>
                        <div class="table-responsive">
                            <table class="table align-middle table-hover mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th>N° Pedido</th>
                                        <th>Fecha</th>
                                        <th>Método de Pago</th>
                                        <th class="text-center">Artículos</th>
                                        <th class="text-right">Total</th>
                                        <th class="text-center">Estado de Envío</th>
                                        <th class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pedidos as $p): ?>
                                        <?php 
                                            $badgeClass = 'badge-warning';
                                            if ($p['estado'] === 'Pagado') $badgeClass = 'badge-success';
                                            elseif ($p['estado'] === 'Enviado') $badgeClass = 'badge-info';
                                            elseif ($p['estado'] === 'Entregado') $badgeClass = 'badge-primary';
                                            elseif ($p['estado'] === 'Cancelado') $badgeClass = 'badge-danger';
                                        ?>
                                        <tr>
                                            <td class="font-weight-bold text-dark">
                                                #<?= str_pad($p['id'], 6, '0', STR_PAD_LEFT) ?>
                                            </td>
                                            <td class="text-muted font-size-13">
                                                <?= date('d/m/Y H:i', strtotime($p['fecha_venta'])) ?>
                                            </td>
                                            <td>
                                                <span class="badge badge-light border text-dark">
                                                    <?= htmlspecialchars($p['metodo_pago']) ?>
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <?= (int)($p['total_items'] ?? 1) ?>
                                            </td>
                                            <td class="text-right font-weight-bold text-primary font-size-15">
                                                S/ <?= number_format($p['total_venta'], 2) ?>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge <?= $badgeClass ?> font-size-12 px-2 py-1">
                                                    <?= htmlspecialchars($p['estado']) ?>
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <a href="<?= BASE_URL ?>/tienda/checkout/confirmacion/<?= $p['id'] ?>" class="btn btn-sm btn-outline-primary" title="Ver Detalle del Pedido">
                                                    <i class="mdi mdi-eye"></i> Detalle
                                                </a>
                                                <a href="<?= BASE_URL ?>/admin/ventas/comprobante/<?= $p['id'] ?>" target="_blank" class="btn btn-sm btn-outline-success ml-1" title="Imprimir Comprobante">
                                                    <i class="mdi mdi-printer"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="mdi mdi-cart-outline text-muted font-size-48"></i>
                            <h5 class="font-weight-bold text-dark mt-3">Aún no tienes compras realizadas</h5>
                            <p class="text-muted">Explora nuestra tienda y realiza tu primer pedido hoy mismo.</p>
                            <a href="<?= BASE_URL ?>/tienda/productos" class="btn btn-primary font-weight-bold">
                                <i class="mdi mdi-store mr-1"></i> Ir al Catálogo
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

</div>

<?php require __DIR__ . '/../../layouts/footer.php'; ?>
