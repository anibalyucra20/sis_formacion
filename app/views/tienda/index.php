<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container-fluid py-3">

    <!-- ALERTA MENSAJES FLASH -->
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

    <!-- 1. CARRUSEL DE OFERTAS Y PRODUCTOS DESTACADOS -->
    <div class="row mb-4">
        <div class="col-12">
            <div id="carouselOfertas" class="carousel slide shadow-sm rounded overflow-hidden" data-ride="carousel" data-interval="4500">
                <ol class="carousel-indicators">
                    <?php if (!empty($ofertas)): ?>
                        <?php foreach ($ofertas as $i => $item): ?>
                            <li data-target="#carouselOfertas" data-slide-to="<?= $i ?>" class="<?= $i === 0 ? 'active' : '' ?>"></li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li data-target="#carouselOfertas" data-slide-to="0" class="active"></li>
                    <?php endif; ?>
                </ol>

                <div class="carousel-inner" role="listbox">
                    <?php if (!empty($ofertas)): ?>
                        <?php foreach ($ofertas as $i => $p): ?>
                            <?php
                            $imgSrc = !empty($p['imagen']) ? BASE_URL . '/public/uploads/productos/' . htmlspecialchars($p['imagen']) : BASE_URL . '/img/no-image.png';
                            ?>
                            <div class="carousel-item <?= $i === 0 ? 'active' : '' ?>" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); min-height: 380px;">
                                <div class="container py-4">
                                    <div class="row align-items-center" style="min-height: 340px;">
                                        <div class="col-md-7 text-white px-4">
                                            <span class="badge badge-warning px-3 py-2 text-uppercase font-weight-bold mb-3 font-size-12">
                                                <i class="mdi mdi-fire mr-1"></i> ¡Oferta Exclusiva!
                                            </span>
                                            <h2 class="display-5 font-weight-bold text-white mb-2"><?= htmlspecialchars($p['nombre']) ?></h2>
                                            <p class="text-light mb-3 font-size-15" style="max-height: 60px; overflow: hidden; text-overflow: ellipsis;">
                                                <?= htmlspecialchars(substr($p['descripcion'] ?? 'Aprovecha nuestros mejores precios en productos seleccionados.', 0, 160)) ?>...
                                            </p>
                                            <div class="d-flex align-items-baseline mb-4">
                                                <span class="text-white-50 mr-2 font-size-14">Precio especial:</span>
                                                <span class="h2 font-weight-bold text-warning mb-0">S/ <?= number_format($p['precio_venta'], 2) ?></span>
                                                <?php if (!empty($p['categoria_nombre'])): ?>
                                                    <span class="badge badge-secondary ml-3"><?= htmlspecialchars($p['categoria_nombre']) ?></span>
                                                <?php endif; ?>
                                            </div>
                                            <div>
                                                <a href="<?= BASE_URL ?>/tienda/productos/detalle/<?= $p['id'] ?>" class="btn btn-warning btn-lg font-weight-bold px-4 mr-2 shadow">
                                                    <i class="mdi mdi-eye-outline mr-1"></i> Ver Detalles
                                                </a>
                                                <button type="button" class="btn btn-outline-light btn-lg font-weight-bold px-3 btn-agregar-carrito" data-id="<?= $p['id'] ?>">
                                                    <i class="mdi mdi-cart-plus mr-1"></i> Agregar al Carrito
                                                </button>
                                            </div>
                                        </div>
                                        <div class="col-md-5 text-center mt-3 mt-md-0">
                                            <div class="position-relative d-inline-block">
                                                <img src="<?= $imgSrc ?>" alt="<?= htmlspecialchars($p['nombre']) ?>"
                                                    class="img-fluid rounded shadow"
                                                    style="max-height: 300px; width: auto; object-fit: contain; background: rgba(255,255,255,0.05); padding: 10px;">
                                                <span class="position-absolute badge badge-danger font-size-13 p-2 shadow" style="top: 10px; right: 10px;">
                                                    Stock: <?= (int)$p['stock_actual'] ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <!-- Slide por defecto si aún no hay productos cargados -->
                        <div class="carousel-item active" style="background: linear-gradient(135deg, #4f46e5 0%, #312e81 100%); min-height: 340px;">
                            <div class="container py-5 text-white text-center">
                                <h1 class="display-4 font-weight-bold text-white">¡Bienvenido a Nuestra Tienda Online!</h1>
                                <p class="lead">Descubre las mejores ofertas, productos de calidad y envíos garantizados a todo el país.</p>
                                <a href="<?= BASE_URL ?>/tienda/productos" class="btn btn-warning btn-lg font-weight-bold mt-2">
                                    Explorar Catálogo Completo
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <a class="carousel-control-prev" href="#carouselOfertas" role="button" data-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="sr-only">Anterior</span>
                </a>
                <a class="carousel-control-next" href="#carouselOfertas" role="button" data-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="sr-only">Siguiente</span>
                </a>
            </div>
        </div>
    </div>

    <!-- BENEFICIOS / VALOR AGREGADO -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3 mb-md-0">
            <div class="card h-100 border-0 shadow-sm bg-white">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar-sm bg-soft-primary rounded-circle mr-3 d-flex align-items-center justify-content-center text-primary font-size-24">
                        <i class="mdi mdi-truck-fast"></i>
                    </div>
                    <div>
                        <h6 class="font-weight-bold mb-1">Envíos Rápidos y Seguros</h6>
                        <small class="text-muted">Despacho y seguimiento directo a tu puerta.</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3 mb-md-0">
            <div class="card h-100 border-0 shadow-sm bg-white">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar-sm bg-soft-success rounded-circle mr-3 d-flex align-items-center justify-content-center text-success font-size-24">
                        <i class="mdi mdi-shield-check"></i>
                    </div>
                    <div>
                        <h6 class="font-weight-bold mb-1">Compra 100% Protegida</h6>
                        <small class="text-muted">Garantía total en todos nuestros productos.</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm bg-white">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar-sm bg-soft-warning rounded-circle mr-3 d-flex align-items-center justify-content-center text-warning font-size-24">
                        <i class="mdi mdi-credit-card-check"></i>
                    </div>
                    <div>
                        <h6 class="font-weight-bold mb-1">Múltiples Formas de Pago</h6>
                        <small class="text-muted">Efectivo, Tarjeta, Transferencia o Yape/Plin.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. EXPLORAR POR CATEGORÍAS -->
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="font-weight-bold text-dark mb-0">
            <i class="mdi mdi-shape-outline text-primary mr-2"></i> Categorías Populares
        </h4>
        <a href="<?= BASE_URL ?>/tienda/productos" class="btn btn-sm btn-outline-primary font-weight-bold">
            Ver Todo el Catálogo <i class="mdi mdi-arrow-right ml-1"></i>
        </a>
    </div>

    <div class="row mb-5">
        <?php if (!empty($categorias)): ?>
            <?php foreach (array_slice($categorias, 0, 6) as $cat): ?>
                <div class="col-6 col-md-4 col-lg-2 mb-3">
                    <a href="<?= BASE_URL ?>/tienda/productos?categoria=<?= $cat['id'] ?>" class="text-decoration-none text-dark">
                        <div class="card text-center border-0 shadow-sm h-100 hover-card p-3" style="transition: transform 0.2s, box-shadow 0.2s;">
                            <div class="avatar-md mx-auto mb-2 bg-soft-info rounded-circle d-flex align-items-center justify-content-center text-info font-size-24">
                                <i class="mdi mdi-tag"></i>
                            </div>
                            <h6 class="font-weight-bold mb-1 text-truncate" title="<?= htmlspecialchars($cat['nombre']) ?>">
                                <?= htmlspecialchars($cat['nombre']) ?>
                            </h6>
                            <small class="text-muted"><?= (int)($cat['total_productos'] ?? 0) ?> productos</small>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <p class="text-muted">No hay categorías registradas por el momento.</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- 3. PRODUCTOS DESTACADOS RECIENTES -->
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="font-weight-bold text-dark mb-0">
            <i class="mdi mdi-fire text-danger mr-2"></i> Productos Recomendados
        </h4>
        <a href="<?= BASE_URL ?>/tienda/productos" class="btn btn-sm btn-primary font-weight-bold">
            Ver Todos <i class="mdi mdi-chevron-right"></i>
        </a>
    </div>

    <div class="row">
        <?php if (!empty($destacados)): ?>
            <?php foreach ($destacados as $prod): ?>
                <?php
                $imgSrc = !empty($prod['imagen']) ? BASE_URL . '/public/uploads/productos/' . htmlspecialchars($prod['imagen']) : BASE_URL . '/img/no-image.png';
                ?>
                <div class="col-6 col-md-4 col-lg-3 mb-4">
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
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <i class="mdi mdi-package-variant-closed text-muted font-size-48"></i>
                <p class="text-muted mt-2">No se encontraron productos disponibles por el momento.</p>
            </div>
        <?php endif; ?>
    </div>

</div>

<!-- SCRIPT AJAX PARA AGREGAR AL CARRITO DE MANERA FLUIDA -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const botonesAgregar = document.querySelectorAll('.btn-agregar-carrito');
        botonesAgregar.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const productoId = this.getAttribute('data-id');
                const originalHtml = this.innerHTML;
                this.innerHTML = '<i class="mdi mdi-spin mdi-loading"></i>';
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
                            // Actualizar badge en header
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
    .hover-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }

    .product-card {
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .product-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.12) !important;
    }

    .product-card:hover img {
        transform: scale(1.05);
    }
</style>

<?php require __DIR__ . '/../layouts/footer.php'; ?>