<?php require __DIR__ . '/../../layouts/header.php'; ?>

<div class="container-fluid py-3">

    <!-- BREADCRUMB Y TITULO -->
    <div class="row align-items-center mb-3">
        <div class="col-md-6">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent p-0 mb-1">
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/tienda">Inicio</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Catálogo de Productos</li>
                    <?php if ($categoriaSeleccionada): ?>
                        <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($categoriaSeleccionada['nombre']) ?></li>
                    <?php endif; ?>
                </ol>
            </nav>
            <h3 class="font-weight-bold text-dark mb-0">
                <?php if ($esOferta): ?>
                    <i class="mdi mdi-tag text-danger mr-1"></i> Ofertas Especiales
                <?php elseif ($categoriaSeleccionada): ?>
                    <?= htmlspecialchars($categoriaSeleccionada['nombre']) ?>
                <?php elseif (!empty($busqueda)): ?>
                    Resultados para: "<span class="text-primary"><?= htmlspecialchars($busqueda) ?></span>"
                <?php else: ?>
                    Todos los Productos
                <?php endif; ?>
                <small class="text-muted font-size-14">(<?= $totalProductos ?> encontrados)</small>
            </h3>
        </div>

        <!-- ORDENAMIENTO -->
        <div class="col-md-6 text-md-right mt-2 mt-md-0">
            <form id="formFiltroOrden" method="GET" action="<?= BASE_URL ?>/tienda/productos" class="form-inline justify-content-md-end">
                <?php if ($categoria_id): ?>
                    <input type="hidden" name="categoria" value="<?= $categoria_id ?>">
                <?php endif; ?>
                <?php if (!empty($busqueda)): ?>
                    <input type="hidden" name="q" value="<?= htmlspecialchars($busqueda) ?>">
                <?php endif; ?>
                <?php if ($esOferta): ?>
                    <input type="hidden" name="ofertas" value="1">
                <?php endif; ?>

                <label class="mr-2 font-weight-bold text-muted font-size-13">Ordenar por:</label>
                <select name="orden" class="form-control form-control-sm" onchange="document.getElementById('formFiltroOrden').submit();">
                    <option value="recientes" <?= $orden === 'recientes' ? 'selected' : '' ?>>Más Recientes</option>
                    <option value="precio_asc" <?= $orden === 'precio_asc' ? 'selected' : '' ?>>Precio: Menor a Mayor</option>
                    <option value="precio_desc" <?= $orden === 'precio_desc' ? 'selected' : '' ?>>Precio: Mayor a Menor</option>
                    <option value="nombre" <?= $orden === 'nombre' ? 'selected' : '' ?>>Nombre (A-Z)</option>
                </select>
            </form>
        </div>
    </div>

    <div class="row">
        <!-- COLUMNA LATERAL: CATEGORIAS Y FILTROS -->
        <div class="col-lg-3 col-md-4 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="card-title font-weight-bold mb-0">
                        <i class="mdi mdi-filter-variant text-primary mr-1"></i> Categorías
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <a href="<?= BASE_URL ?>/tienda/productos<?= !empty($busqueda) ? '?q=' . urlencode($busqueda) : '' ?>"
                            class="list-group-item list-group-item-action d-flex justify-content-between align-items-center <?= empty($categoria_id) && !$esOferta ? 'active font-weight-bold' : '' ?>">
                            <span><i class="mdi mdi-checkbox-blank-circle-outline mr-2 font-size-12"></i> Todas las Categorías</span>
                        </a>

                        <a href="<?= BASE_URL ?>/tienda/productos?ofertas=1"
                            class="list-group-item list-group-item-action d-flex justify-content-between align-items-center text-danger <?= $esOferta ? 'active text-white font-weight-bold' : '' ?>">
                            <span><i class="mdi mdi-tag mr-2 font-size-14"></i> Ofertas Especiales</span>
                            <span class="badge badge-danger badge-pill">Top</span>
                        </a>

                        <?php if (!empty($categorias)): ?>
                            <?php foreach ($categorias as $cat): ?>
                                <a href="<?= BASE_URL ?>/tienda/productos?categoria=<?= $cat['id'] ?><?= !empty($busqueda) ? '&q=' . urlencode($busqueda) : '' ?>"
                                    class="list-group-item list-group-item-action d-flex justify-content-between align-items-center <?= $categoria_id == $cat['id'] ? 'active font-weight-bold' : '' ?>">
                                    <span class="text-truncate"><?= htmlspecialchars($cat['nombre']) ?></span>
                                    <span class="badge badge-light badge-pill text-dark"><?= (int)($cat['total_productos'] ?? 0) ?></span>
                                </a>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- CARD BANNER PROMOCIONAL LATERAL -->
            <div class="card border-0 shadow-sm mt-3 text-white text-center p-3" style="background: linear-gradient(135deg, #2563eb, #1e40af);">
                <div class="card-body p-2">
                    <i class="mdi mdi-truck-delivery-outline font-size-36 text-warning mb-2"></i>
                    <h6 class="font-weight-bold text-white mb-1">¡Envíos a todo el país!</h6>
                    <p class="font-size-12 text-light mb-2">Gestiona tus compras seguras directamente desde tu cuenta.</p>
                </div>
            </div>
        </div>

        <!-- LISTA DE PRODUCTOS -->
        <div class="col-lg-9 col-md-8">
            <!-- Chips de filtro activo si hay búsqueda o categoría -->
            <?php if (!empty($busqueda) || !empty($categoria_id) || $esOferta): ?>
                <div class="mb-3 d-flex flex-wrap align-items-center">
                    <span class="text-muted font-size-13 mr-2">Filtros aplicados:</span>
                    <?php if (!empty($busqueda)): ?>
                        <span class="badge badge-info p-2 mr-2 mb-1">
                            Búsqueda: <?= htmlspecialchars($busqueda) ?>
                            <a href="<?= BASE_URL ?>/tienda/productos?<?= http_build_query(array_merge($_GET, ['q' => null])) ?>" class="text-white ml-1">&times;</a>
                        </span>
                    <?php endif; ?>
                    <?php if (!empty($categoriaSeleccionada)): ?>
                        <span class="badge badge-primary p-2 mr-2 mb-1">
                            Categoría: <?= htmlspecialchars($categoriaSeleccionada['nombre']) ?>
                            <a href="<?= BASE_URL ?>/tienda/productos?<?= http_build_query(array_merge($_GET, ['categoria' => null])) ?>" class="text-white ml-1">&times;</a>
                        </span>
                    <?php endif; ?>
                    <?php if ($esOferta): ?>
                        <span class="badge badge-danger p-2 mr-2 mb-1">
                            Solo Ofertas
                            <a href="<?= BASE_URL ?>/tienda/productos" class="text-white ml-1">&times;</a>
                        </span>
                    <?php endif; ?>
                    <a href="<?= BASE_URL ?>/tienda/productos" class="btn btn-sm btn-link text-danger font-size-12 p-0 ml-2 mb-1">Limpiar todos</a>
                </div>
            <?php endif; ?>

            <?php if (!empty($productos)): ?>
                <div class="row">
                    <?php foreach ($productos as $prod): ?>
                        <?php
                        $imgSrc = !empty($prod['imagen']) ? BASE_URL . '/public/uploads/productos/' . htmlspecialchars($prod['imagen']) : BASE_URL . '/img/no-image.png';
                        ?>
                        <div class="col-6 col-lg-4 mb-4">
                            <div class="card h-100 border-0 shadow-sm product-card position-relative">
                                <!-- Badge Stock -->
                                <?php if ($prod['stock_actual'] <= 0): ?>
                                    <span class="badge badge-danger position-absolute" style="top: 10px; right: 10px; z-index: 2;">Agotado</span>
                                <?php elseif ($prod['stock_actual'] <= 5): ?>
                                    <span class="badge badge-warning position-absolute" style="top: 10px; right: 10px; z-index: 2;">¡Últimos <?= $prod['stock_actual'] ?>!</span>
                                <?php endif; ?>

                                <!-- Imagen -->
                                <div class="text-center p-3" style="background: #f8fafc; height: 190px; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                                    <a href="<?= BASE_URL ?>/tienda/productos/detalle/<?= $prod['id'] ?>">
                                        <img src="<?= $imgSrc ?>" alt="<?= htmlspecialchars($prod['nombre']) ?>"
                                            class="img-fluid" style="max-height: 160px; object-fit: contain; transition: transform 0.3s ease;">
                                    </a>
                                </div>

                                <!-- Info -->
                                <div class="card-body d-flex flex-column p-3">
                                    <small class="text-muted text-uppercase mb-1 font-size-11">
                                        <?= htmlspecialchars($prod['categoria_nombre'] ?? 'General') ?>
                                    </small>
                                    <h6 class="font-weight-bold text-dark mb-1" style="height: 42px; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
                                        <a href="<?= BASE_URL ?>/tienda/productos/detalle/<?= $prod['id'] ?>" class="text-dark">
                                            <?= htmlspecialchars($prod['nombre']) ?>
                                        </a>
                                    </h6>
                                    <div class="text-muted font-size-12 mb-2">SKU: <?= htmlspecialchars($prod['codigo_sku'] ?? 'N/A') ?></div>

                                    <div class="mt-auto pt-2 border-top d-flex align-items-center justify-content-between">
                                        <div>
                                            <span class="text-muted font-size-11 d-block">Precio</span>
                                            <span class="h5 font-weight-bold text-primary mb-0">S/ <?= number_format($prod['precio_venta'], 2) ?></span>
                                        </div>
                                        <div>
                                            <a href="<?= BASE_URL ?>/tienda/productos/detalle/<?= $prod['id'] ?>" class="btn btn-sm btn-outline-secondary mr-1" title="Ver Detalle">
                                                <i class="mdi mdi-eye"></i>
                                            </a>
                                            <button type="button"
                                                class="btn btn-sm btn-primary rounded-circle p-2 btn-agregar-carrito shadow-sm"
                                                data-id="<?= $prod['id'] ?>"
                                                title="Añadir al Carrito"
                                                <?= $prod['stock_actual'] <= 0 ? 'disabled' : '' ?>>
                                                <i class="mdi mdi-cart-plus font-size-18"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- PAGINACIÓN -->
                <?php if ($totalPaginas > 1): ?>
                    <div class="d-flex justify-content-center mt-3">
                        <nav aria-label="Navegación de páginas">
                            <ul class="pagination pagination-rounded">
                                <li class="page-item <?= $paginaActual <= 1 ? 'disabled' : '' ?>">
                                    <a class="page-link" href="<?= BASE_URL ?>/tienda/productos?<?= http_build_query(array_merge($_GET, ['page' => $paginaActual - 1])) ?>">
                                        <i class="mdi mdi-chevron-left"></i>
                                    </a>
                                </li>
                                <?php for ($p = 1; $p <= $totalPaginas; $p++): ?>
                                    <li class="page-item <?= $p == $paginaActual ? 'active' : '' ?>">
                                        <a class="page-link" href="<?= BASE_URL ?>/tienda/productos?<?= http_build_query(array_merge($_GET, ['page' => $p])) ?>">
                                            <?= $p ?>
                                        </a>
                                    </li>
                                <?php endfor; ?>
                                <li class="page-item <?= $paginaActual >= $totalPaginas ? 'disabled' : '' ?>">
                                    <a class="page-link" href="<?= BASE_URL ?>/tienda/productos?<?= http_build_query(array_merge($_GET, ['page' => $paginaActual + 1])) ?>">
                                        <i class="mdi mdi-chevron-right"></i>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                <?php endif; ?>

            <?php else: ?>
                <div class="card border-0 shadow-sm text-center py-5">
                    <div class="card-body">
                        <i class="mdi mdi-package-variant-closed text-muted" style="font-size: 64px;"></i>
                        <h4 class="font-weight-bold text-dark mt-3">No encontramos productos</h4>
                        <p class="text-muted">Prueba buscando con otros términos o seleccionando otra categoría.</p>
                        <a href="<?= BASE_URL ?>/tienda/productos" class="btn btn-primary mt-2">
                            Ver Todos los Productos
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- SCRIPT AJAX PARA AGREGAR AL CARRITO -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const botonesAgregar = document.querySelectorAll('.btn-agregar-carrito');
        botonesAgregar.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const productoId = this.getAttribute('data-id');
                const originalHtml = this.innerHTML;
                this.innerHTML = '<i class="mdi mdi-spin mdi-loading font-size-18"></i>';
                this.disabled = true;

                const formData = new FormData();
                formData.append('producto_id', productoId);
                formData.append('cantidad', 1);

                fetch('<?= BASE_URL ?>/tienda/carrito/agregarAjax', {
                        method: 'POST',
                        body: formData
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === 'success') {
                            const badge = document.getElementById('badge-contador-carrito');
                            if (badge) {
                                badge.textContent = data.total_items;
                            }
                            if (window.Swal) {
                                Swal.fire({
                                    icon: 'success',
                                    title: '¡Producto Agregado!',
                                    text: data.mensaje || 'Se añadió al carrito de compras con éxito.',
                                    showCancelButton: true,
                                    confirmButtonText: 'Ir al Carrito',
                                    cancelButtonText: 'Seguir Comprando',
                                    confirmButtonColor: '#3085d6',
                                    cancelButtonColor: '#aaa'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        window.location.href = '<?= BASE_URL ?>/tienda/carrito';
                                    }
                                });
                            } else {
                                alert(data.mensaje || 'Producto agregado al carrito');
                            }
                        } else {
                            if (window.Swal) {
                                Swal.fire('Atención', data.mensaje || 'No se pudo agregar el producto', 'warning');
                            } else {
                                alert(data.mensaje || 'Error al agregar');
                            }
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        alert('Ocurrió un error al agregar al carrito.');
                    })
                    .finally(() => {
                        this.innerHTML = originalHtml;
                        this.disabled = false;
                    });
            });
        });
    });
</script>

<style>
    .product-card {
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .product-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.12) !important;
    }

    .product-card:hover img {
        transform: scale(1.05);
    }
</style>

<?php require __DIR__ . '/../../layouts/footer.php'; ?>