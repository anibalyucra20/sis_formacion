</div> <!-- container-fluid -->
</div>
<!-- End Page-content -->
    <footer class="footer">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6 font-size-13 text-muted">
                    Sistema de Control de Certificados &copy; <?= date('Y') ?>
                </div>
                <div class="col-sm-6">
                    <div class="text-sm-right d-none d-sm-block font-size-12 text-muted">
                        Panel Administrativo
                    </div>
                </div>
            </div>
        </div>
    </footer>


</div>
<!-- end main content-->
</div>
<!-- END layout-wrapper -->
<!-- Núcleo (jQuery + Bootstrap 4) -->
<script src="<?= BASE_URL ?>/assets/js/jquery.min.js"></script>
<!-- Bootstrap 4 bundle (incluye Popper) -->
<script src="<?= BASE_URL ?>/assets/js/bootstrap.bundle.min.js"></script>

<!-- ============================= -->
<!-- DataTables (base + integración BS4) -->
<!-- ============================= -->
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js"></script>

<!-- Si usas Responsive de DataTables, descomenta esta línea -->
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap4.min.js"></script>

<!-- ============================= -->
<!-- Extensión Buttons (exportar)  -->
<!-- ============================= -->
<!-- Dependencias de Buttons/HTML5 (cargar antes de buttons.html5) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>

<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<!-- Si vas a usar imprimir/copiar: -->
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.colVis.min.js"></script>

<!-- ============================= -->
<!-- Plugins de tu tema (después)  -->
<!-- ============================= -->
<script src="<?= BASE_URL ?>/assets/js/metismenu.min.js"></script>
<script src="<?= BASE_URL ?>/assets/js/simplebar.min.js"></script>
<script src="<?= BASE_URL ?>/assets/js/waves.js"></script>
<script src="<?= BASE_URL ?>/assets/js/theme.js"></script>
<script src="<?= BASE_URL ?>/assets/js/sweetalert2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Configuración global de idioma español para DataTables (100% local, sin fallos de CDN)
if (window.jQuery && jQuery.fn.dataTable) {
    jQuery.extend(true, jQuery.fn.dataTable.defaults, {
        language: {
            processing: "Procesando...",
            search: "Buscar:",
            lengthMenu: "Mostrar _MENU_ registros",
            info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
            infoEmpty: "Mostrando 0 a 0 de 0 registros",
            infoFiltered: "(filtrado de un total de _MAX_ registros)",
            infoPostFix: "",
            loadingRecords: "Cargando registros...",
            zeroRecords: "No se encontraron resultados",
            emptyTable: "No hay datos disponibles en la tabla",
            paginate: {
                first: "Primero",
                previous: "Anterior",
                next: "Siguiente",
                last: "Último"
            },
            aria: {
                sortAscending: ": Activar para ordenar la columna de manera ascendente",
                sortDescending: ": Activar para ordenar la columna de manera descendente"
            },
            buttons: {
                copy: "Copiar",
                colvis: "Visibilidad",
                excel: "Excel",
                pdf: "PDF",
                print: "Imprimir"
            }
        }
    });
}
</script>


<!-- LOADER GLOBAL -->
<div id="global-loader" class="gl-overlay" aria-hidden="true">
    <div class="gl-box">
        <div class="gl-spinner" role="status" aria-label="Cargando"></div>
        <div class="gl-text">Procesando…</div>
    </div>
</div>

<style>
    .gl-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, .35);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 99999;
    }

    .gl-overlay.is-active {
        display: flex;
    }

    .gl-box {
        background: #fff;
        padding: 18px 22px;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, .25);
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 220px;
    }

    .gl-spinner {
        width: 24px;
        height: 24px;
        border: 3px solid #ddd;
        border-top-color: #333;
        border-radius: 50%;
        animation: glspin 0.9s linear infinite;
    }

    .gl-text {
        font-size: 14px;
        color: #222;
    }

    @keyframes glspin {
        to {
            transform: rotate(360deg);
        }
    }
</style>

<script>
    (function() {
        const overlay = document.getElementById('global-loader');
        let counter = 0;
        let showTimer = null;
        let safetyTimer = null;

        function showLoader(text) {
            counter++;
            if (!showTimer) {
                showTimer = setTimeout(() => {
                    if (counter > 0) {
                        if (text && overlay.querySelector('.gl-text')) {
                            overlay.querySelector('.gl-text').textContent = text;
                        }
                        overlay.classList.add('is-active');
                        overlay.setAttribute('aria-hidden', 'false');

                        // Safety timer: auto-ocultar a los 6s para evitar que el loader se quede pegado en descargas
                        clearTimeout(safetyTimer);
                        safetyTimer = setTimeout(() => {
                            hideLoader();
                        }, 6000);
                    }
                    showTimer = null;
                }, 150);
            }
        }

        function hideLoader() {
            counter = Math.max(0, counter - 1);
            if (counter === 0) {
                overlay.classList.remove('is-active');
                overlay.setAttribute('aria-hidden', 'true');
                clearTimeout(safetyTimer);
            }
        }

        window.SIGI_LOADER = {
            show: showLoader,
            hide: hideLoader
        };

        window.addEventListener('pageshow', () => {
            counter = 0;
            overlay.classList.remove('is-active');
            overlay.setAttribute('aria-hidden', 'true');
            clearTimeout(safetyTimer);
        });

        // 1) Mostrar en submit de formularios (excluyendo exportaciones y formularios con data-no-loader)
        document.addEventListener('submit', function(e) {
            const form = e.target;
            if (!form || form.tagName !== 'FORM') return;
            if (form.hasAttribute('data-no-loader') || form.classList.contains('no-loader')) return;

            const action = (form.getAttribute('action') || '').toLowerCase();
            if (action.includes('exportar') || 
                action.includes('plantilla') || 
                action.includes('descargar') || 
                action.includes('excel') || 
                action.includes('pdf')) {
                return;
            }

            showLoader('Procesando…');
        }, true);

        // 2) Mostrar al hacer click en links que navegan
        document.addEventListener('click', function(e) {
            // Ignorar elementos de control de Bootstrap o modales
            const btn = e.target.closest('button, a, input[type="submit"]');
            if (btn) {
                if (btn.hasAttribute('data-toggle') || 
                    btn.hasAttribute('data-dismiss') || 
                    btn.hasAttribute('data-no-loader') || 
                    btn.classList.contains('no-loader')) {
                    return;
                }
                const onclickStr = (btn.getAttribute('onclick') || '').toLowerCase();
                if (onclickStr.includes('print') || onclickStr.includes('export') || onclickStr.includes('descargar')) {
                    return;
                }
            }

            const a = e.target.closest('a');
            if (!a) return;

            if (a.hasAttribute('data-sigi-confirm')) return;
            if (a.hasAttribute('data-no-loader') || a.classList.contains('no-loader')) return;

            const href = a.getAttribute('href') || '';
            const target = (a.getAttribute('target') || '').toLowerCase();

            // Ignorar anclas, javascript, vacío, nueva pestaña
            if (!href || href.startsWith('#') || href.startsWith('javascript:')) return;
            if (target === '_blank') return;

            // Ignorar enlaces de descargas (Excel, PDF, plantillas, exportaciones) para que no se congele el loader
            const hrefLower = href.toLowerCase();
            if (a.hasAttribute('download') || 
                hrefLower.includes('plantilla') || 
                hrefLower.includes('exportar') || 
                hrefLower.includes('excel') || 
                hrefLower.includes('/pdf') || 
                hrefLower.includes('descargar') || 
                hrefLower.match(/\.(xlsx|xls|csv|pdf|zip|rar|png|jpg|jpeg)$/i)) {
                return;
            }

            showLoader('Cargando…');
        }, true);


        // 3) jQuery AJAX global (si usas jQuery/DataTables)
        if (window.jQuery) {
            jQuery(document).ajaxStart(function() {
                //showLoader('Procesando…');
            });
            jQuery(document).ajaxStop(function() {
                hideLoader();
            });
            jQuery(document).ajaxComplete(function(e, jqXHR, settings) {
                const hdr = (settings && settings.headers) ? settings.headers : {};
                const isDataTables = hdr['X-SIGI-DT'] === '1' || hdr['x-sigi-dt'] === '1';

                if (!isDataTables) hideLoader();
            });

            jQuery(document).ajaxError(function(e, jqXHR, settings) {
                const hdr = (settings && settings.headers) ? settings.headers : {};
                const isDataTables = hdr['X-SIGI-DT'] === '1' || hdr['x-sigi-dt'] === '1';

                if (!isDataTables) hideLoader();
            });
            jQuery(document).ajaxSend(function(e, jqXHR, settings) {
                const isDT = jqXHR && jqXHR.getResponseHeader ?
                    false // aún no hay headers de respuesta
                    :
                    false;

                // Detecta DataTables por nuestro header de REQUEST (settings.headers)
                const hdr = (settings && settings.headers) ? settings.headers : {};
                const isDataTables = hdr['X-SIGI-DT'] === '1' || hdr['x-sigi-dt'] === '1';

                if (!isDataTables) showLoader('Procesando…');
            });
        }

        // 4) Parche global para fetch
        if (window.fetch) {
            const _fetch = window.fetch;
            window.fetch = function() {
                showLoader('Procesando…');
                return _fetch.apply(this, arguments)
                    .then(res => {
                        hideLoader();
                        return res;
                    })
                    .catch(err => {
                        hideLoader();
                        throw err;
                    });
            }
        }

        // 5) Parche global para XHR (por si hay libs sin jQuery)
        (function(open, send, setHeader) {
            XMLHttpRequest.prototype.open = function() {
                this._sigi_loader_track = true;
                this._sigi_is_dt = false;
                return open.apply(this, arguments);
            };

            XMLHttpRequest.prototype.setRequestHeader = function(k, v) {
                if (String(k).toLowerCase() === 'x-sigi-dt') this._sigi_is_dt = true;
                return setHeader.apply(this, arguments);
            };

            XMLHttpRequest.prototype.send = function() {
                if (this._sigi_loader_track && !this._sigi_is_dt) showLoader('Procesando…');
                this.addEventListener('loadend', function() {
                    if (this._sigi_loader_track && !this._sigi_is_dt) hideLoader();
                });
                return send.apply(this, arguments);
            };
        })(XMLHttpRequest.prototype.open, XMLHttpRequest.prototype.send, XMLHttpRequest.prototype.setRequestHeader);


        // 6) DataTables processing (si quieres que se prenda cuando está “Processing…”)
        if (window.jQuery) {
            jQuery(document).on('processing.dt', function(e, settings, processing) {
                if (processing) showLoader('Cargando tabla…');
                else hideLoader();
            });
        }

    })();
</script>


</body>

</html>