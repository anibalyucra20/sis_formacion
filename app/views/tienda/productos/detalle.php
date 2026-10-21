<?php require __DIR__ . '/../../layouts/header.php'; ?>

<div class="container-fluid py-3">

    <!-- BREADCRUMB -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb bg-transparent p-0">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/tienda">Inicio</a></li>
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/tienda/productos">Catálogo</a></li>
            <?php if ($categoria): ?>
                <li class="breadcrumb-item">
                    <a href="<?= BASE_URL ?>/tienda/productos?categoria=<?= $categoria['id'] ?>"><?= htmlspecialchars($categoria['nombre']) ?></a>
                </li>
            <?php endif; ?>
            <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($producto['nombre']) ?></li>
        </ol>
    </nav>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="row">
                <!-- GALERÍA DE IMÁGENES -->
                <div class="col-lg-5 mb-4 mb-lg-0">
                    <?php
                    $imgPrincipal = !empty($imagenes) ? BASE_URL . '/public/uploads/productos/' . htmlspecialchars($imagenes[0]['url_imagen']) : BASE_URL . '/img/no-image.png';
                    ?>
                    <!-- Imagen Principal Grande -->
                    <div class="text-center p-3 rounded mb-3" style="background: #f8fafc; border: 1px solid #e2e8f0; min-height: 380px; display: flex; align-items: center; justify-content: center;">
                        <img id="imgDetallePrincipal" src="<?= $imgPrincipal ?>"
                            alt="<?= htmlspecialchars($producto['nombre']) ?>"
                            class="img-fluid" style="max-height: 340px; object-fit: contain; transition: opacity 0.2s;">
                    </div>

                    <!-- Miniaturas / Thumbnails -->
                    <?php if (!empty($imagenes) && count($imagenes) > 1): ?>
                        <div class="d-flex flex-wrap justify-content-center">
                            <?php foreach ($imagenes as $idx => $img): ?>
                                <?php $thumbSrc = BASE_URL . '/public/uploads/productos/' . htmlspecialchars($img['url_imagen']); ?>
                                <div class="m-1 p-1 border rounded cursor-pointer img-thumb-item <?= $idx === 0 ? 'border-primary shadow-sm' : '' ?>"
                                    style="width: 70px; height: 70px; display: flex; align-items: center; justify-content: center; cursor: pointer; background: #fff;"
                                    onclick="cambiarImagenPrincipal('<?= $thumbSrc ?>', this)">
                                    <img src="<?= $thumbSrc ?>" class="img-fluid" style="max-height: 60px; object-fit: contain;">
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- INFORMACIÓN Y ACCIONES DEL PRODUCTO -->
                <div class="col-lg-7">
                    <div class="pl-lg-3">
                        <?php if ($categoria): ?>
                            <span class="badge badge-soft-primary px-3 py-1 font-size-12 font-weight-bold text-uppercase mb-2">
                                <?= htmlspecialchars($categoria['nombre']) ?>
                            </span>
                        <?php endif; ?>

                        <h2 class="font-weight-bold text-dark mb-2"><?= htmlspecialchars($producto['nombre']) ?></h2>

                        <div class="d-flex align-items-center mb-3">
                            <span class="text-muted font-size-13 mr-3">
                                <strong>SKU:</strong> <?= htmlspecialchars($producto['codigo_sku'] ?? 'N/A') ?>
                            </span>
                            <span class="text-muted font-size-13">
                                <strong>Disponibilidad:</strong>
                                <?php if ($producto['stock_actual'] > 10): ?>
                                    <span class="text-success font-weight-bold"><i class="mdi mdi-check-circle"></i> En Stock (<?= $producto['stock_actual'] ?> unidades)</span>
                                <?php elseif ($producto['stock_actual'] > 0): ?>
                                    <span class="text-warning font-weight-bold"><i class="mdi mdi-alert-circle"></i> Pocas unidades (<?= $producto['stock_actual'] ?> restantes)</span>
                                <?php else: ?>
                                    <span class="text-danger font-weight-bold"><i class="mdi mdi-close-circle"></i> Agotado</span>
                                <?php endif; ?>
                            </span>
                        </div>

                        <!-- PRECIO -->
                        <div class="bg-light p-3 rounded mb-4">
                            <span class="text-muted font-size-13 d-block">Precio por unidad:</span>
                            <div class="d-flex align-items-baseline">
                                <span class="display-5 font-weight-bold text-primary mr-3">
                                    S/ <?= number_format($producto['precio_venta'], 2) ?>
                                </span>
                                <?php if (!empty($producto['precio_compra']) && $producto['precio_compra'] > 0): ?>
                                    <span class="text-muted font-size-14"><i class="mdi mdi-shield-check text-success"></i> Mejor precio garantizado</span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- DESCRIPCIÓN CORTA -->
                        <div class="mb-4">
                            <h6 class="font-weight-bold text-dark mb-2">Descripción del Producto:</h6>
                            <p class="text-muted leading-relaxed">
                                <?= nl2br(htmlspecialchars($producto['descripcion'] ?? 'Sin descripción detallada.')) ?>
                            </p>
                        </div>

                        <!-- FORMULARIO DE COMPRA / AGREGAR AL CARRITO -->
                        <?php if ($producto['stock_actual'] > 0): ?>
                            <form id="formAgregarCarrito" action="<?= BASE_URL ?>/tienda/carrito/agregar" method="POST" class="mb-4">
                                <input type="hidden" name="producto_id" value="<?= $producto['id'] ?>">

                                <div class="form-row align-items-center">
                                    <div class="col-auto mr-3">
                                        <label class="font-weight-bold text-dark mb-1 d-block font-size-13">Cantidad:</label>
                                        <div class="input-group" style="width: 140px;">
                                            <div class="input-group-prepend">
                                                <button class="btn btn-outline-secondary" type="button" onclick="ajustarCantidad(-1)">
                                                    <i class="mdi mdi-minus"></i>
                                                </button>
                                            </div>
                                            <input type="number" id="inputCantidad" name="cantidad"
                                                class="form-control text-center font-weight-bold"
                                                value="1" min="1" max="<?= $producto['stock_actual'] ?>">
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-secondary" type="button" onclick="ajustarCantidad(1)">
                                                    <i class="mdi mdi-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-auto mt-auto">
                                        <button type="submit" class="btn btn-primary btn-lg font-weight-bold px-4 shadow">
                                            <i class="mdi mdi-cart-plus mr-1"></i> Agregar al Carrito
                                        </button>
                                        <a href="<?= BASE_URL ?>/tienda/carrito" class="btn btn-outline-dark btn-lg font-weight-bold ml-2">
                                            <i class="mdi mdi-cart-outline mr-1"></i> Ver Carrito
                                        </a>
                                    </div>
                                </div>
                            </form>
                        <?php else: ?>
                            <div class="alert alert-danger mb-4">
                                <i class="mdi mdi-information-outline mr-1"></i> Este producto se encuentra actualmente agotado. Vuelve pronto para consultar nuevas existencias.
                            </div>
                        <?php endif; ?>

                        <!-- GARANTÍAS -->
                        <div class="border-top pt-3 row text-muted font-size-13">
                            <div class="col-sm-4 mb-2">
                                <i class="mdi mdi-truck-check text-primary mr-1"></i> Envío a Domicilio
                            </div>
                            <div class="col-sm-4 mb-2">
                                <i class="mdi mdi-shield-check text-success mr-1"></i> Compra Protegida
                            </div>
                            <div class="col-sm-4 mb-2">
                                <i class="mdi mdi-receipt text-warning mr-1"></i> Boleta o Factura
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- PRODUCTOS RELACIONADOS -->
    <?php if (!empty($relacionados)): ?>
        <div class="mt-5">
            <h4 class="font-weight-bold text-dark mb-3">
                <i class="mdi mdi-view-grid-plus text-primary mr-1"></i> Productos Relacionados
            </h4>
            <div class="row">
                <?php foreach ($relacionados as $rel): ?>
                    <?php
                    $imgRel = !empty($rel['imagen']) ? BASE_URL . '/' . htmlspecialchars($rel['imagen']) : BASE_URL . '/img/no-image.png';
                    ?>
                    <div class="col-6 col-md-3 mb-4">
                        <div class="card h-100 border-0 shadow-sm product-card">
                            <div class="text-center p-3" style="background: #f8fafc; height: 160px; display: flex; align-items: center; justify-content: center;">
                                <a href="<?= BASE_URL ?>/tienda/productos/detalle/<?= $rel['id'] ?>">
                                    <img src="<?= $imgRel ?>" alt="<?= htmlspecialchars($rel['nombre']) ?>" class="img-fluid" style="max-height: 130px; object-fit: contain;">
                                </a>
                            </div>
                            <div class="card-body p-3 d-flex flex-column">
                                <h6 class="font-weight-bold text-dark mb-1 text-truncate">
                                    <a href="<?= BASE_URL ?>/tienda/productos/detalle/<?= $rel['id'] ?>" class="text-dark">
                                        <?= htmlspecialchars($rel['nombre']) ?>
                                    </a>
                                </h6>
                                <div class="mt-auto pt-2 border-top d-flex align-items-center justify-content-between">
                                    <span class="font-weight-bold text-primary font-size-15">S/ <?= number_format($rel['precio_venta'], 2) ?></span>
                                    <a href="<?= BASE_URL ?>/tienda/productos/detalle/<?= $rel['id'] ?>" class="btn btn-sm btn-outline-primary">
                                        Ver
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

</div>

<script>
    function cambiarImagenPrincipal(src, thumbElement) {
        const principal = document.getElementById('imgDetallePrincipal');
        principal.style.opacity = '0.3';
        setTimeout(() => {
            principal.src = src;
            principal.style.opacity = '1';
        }, 150);

        document.querySelectorAll('.img-thumb-item').forEach(el => {
            el.classList.remove('border-primary', 'shadow-sm');
        });
        thumbElement.classList.add('border-primary', 'shadow-sm');
    }

    function ajustarCantidad(delta) {
        const input = document.getElementById('inputCantidad');
        let val = parseInt(input.value) || 1;
        let max = parseInt(input.getAttribute('max')) || 999;
        val += delta;
        if (val < 1) val = 1;
        if (val > max) val = max;
        input.value = val;
    }
</script>

<?php require __DIR__ . '/../../layouts/footer.php'; ?>