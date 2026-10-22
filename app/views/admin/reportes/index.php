<?php
require_once __DIR__ . '/../../layouts/header.php';
?>

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between pb-3">
            <div>
                <h4 class="mb-1 font-weight-bold" style="color: #1e293b;">Reportes Generales</h4>
                <p class="text-muted font-size-13 mb-0">Consolidado y consulta interactiva con filtros automáticos y paginación</p>
            </div>
            <div style="gap: 10px;" class="d-flex">
                <a href="<?= BASE_URL ?>/admin/reportes/exportarExcel" id="btn-exportar-excel" class="btn btn-success font-weight-bold" style="border-radius: 8px;" data-no-loader="true">
                    <i class="fa-solid fa-file-excel mr-1"></i> Exportar a Excel
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Filtros de Reporte Automáticos -->
<div class="row">
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
                <h5 class="card-title font-weight-bold mb-0 font-size-15">
                    <i class="fa-solid fa-filter mr-2 text-primary"></i> Filtros de Reporte
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Por Curso -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label font-weight-bold font-size-13">Por Curso Específico</label>
                        <select id="filtro-curso" class="form-control form-control-sm">
                            <option value="">-- Todos los Cursos --</option>
                            <?php foreach ($cursos as $c): ?>
                                <option value="<?= $c['id'] ?>">
                                    <?= htmlspecialchars($c['codigo'] . ' - ' . $c['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Fecha Desde -->
                    <div class="col-md-2 mb-3">
                        <label class="form-label font-weight-bold font-size-13">Emisión Desde</label>
                        <input type="date" id="filtro-desde" class="form-control form-control-sm">
                    </div>

                    <!-- Fecha Hasta -->
                    <div class="col-md-2 mb-3">
                        <label class="form-label font-weight-bold font-size-13">Emisión Hasta</label>
                        <input type="date" id="filtro-hasta" class="form-control form-control-sm">
                    </div>

                    <!-- Por DNI -->
                    <div class="col-md-3 mb-3">
                        <label class="form-label font-weight-bold font-size-13">Por DNI de Participante</label>
                        <input type="text" id="filtro-dni" class="form-control form-control-sm" placeholder="Escriba DNI...">
                    </div>

                    <!-- Botón Limpiar -->
                    <div class="col-md-1 mb-3 d-flex align-items-end">
                        <button type="button" id="btn-limpiar" class="btn btn-outline-info btn-sm btn-block" title="Restablecer filtros">
                            <i class="fa-solid fa-rotate-left"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabla de Resultados Paginada con DataTables -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-white d-flex align-items-center justify-content-between py-3">
                <h5 class="card-title font-weight-bold mb-0 font-size-15">
                    <i class="fa-solid fa-table-list mr-2 text-primary"></i> Registros de Certificados Emitidos
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="tabla-reportes" class="table table-bordered table-striped table-hover dt-responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>DNI</th>
                                <th>Participante</th>
                                <th>Curso / Capacitación</th>
                                <th>Horas</th>
                                <th>Condición</th>
                                <th>Fecha Emisión</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/../../layouts/footer.php';
?>

<script>
    $(document).ready(function() {
        // Función para actualizar enlace de exportación Excel con filtros activos
        function actualizarEnlaceExcel() {
            const cursoId = $('#filtro-curso').val();
            const desde = $('#filtro-desde').val();
            const hasta = $('#filtro-hasta').val();
            const dni = $('#filtro-dni').val().trim();

            let url = '<?= BASE_URL ?>/admin/reportes/exportarExcel?';
            const params = [];
            if (cursoId) params.push('curso_id=' + encodeURIComponent(cursoId));
            if (desde) params.push('desde=' + encodeURIComponent(desde));
            if (hasta) params.push('hasta=' + encodeURIComponent(hasta));
            if (dni) params.push('dni=' + encodeURIComponent(dni));

            $('#btn-exportar-excel').attr('href', url + params.join('&'));
        }

        // Inicializar DataTable Server-Side con filtros automáticos
        const table = $('#tabla-reportes').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            pageLength: 10,
            lengthMenu: [
                [10, 25, 50, 100],
                [10, 25, 50, 100]
            ],
            order: [
                [0, 'desc']
            ],
            ajax: {
                url: '<?= BASE_URL ?>/admin/reportes/data',
                type: 'GET',
                headers: {
                    'X-SIGI-DT': '1'
                },
                data: function(d) {
                    d.curso_id = $('#filtro-curso').val();
                    d.desde = $('#filtro-desde').val();
                    d.hasta = $('#filtro-hasta').val();
                    d.dni = $('#filtro-dni').val().trim();
                }
            },
            columns: [{
                    data: 'id'
                },
                {
                    data: 'dni',
                },
                {
                    data: 'participante',
                },
                {
                    data: 'curso_nombre',
                    render: function(data, type, row) {
                        return '<div class="text-truncate" style="max-width: 250px;" title="' + data + '">' + data + '</div>';
                    }
                },
                {
                    data: 'horas',
                    render: function(data) {
                        return '<span class="">' + data + ' h</span>';
                    }
                },
                {
                    data: 'condicion',
                    render: function(data) {
                        return '<span class="badge badge-success px-2 py-1 font-size-11">' + data + '</span>';
                    }
                },
                {
                    data: 'fecha_emision',
                    render: function(data) {
                        return data ? data.substring(0, 10) : '';
                    }
                }
            ]
        });

        // Filtro automático al cambiar el curso
        $('#filtro-curso').on('change', function() {
            actualizarEnlaceExcel();
            table.ajax.reload();
        });

        // Filtro automático al cambiar fechas
        $('#filtro-desde, #filtro-hasta').on('change', function() {
            actualizarEnlaceExcel();
            table.ajax.reload();
        });

        // Filtro automático con debounce al escribir en el DNI
        let timerDni = null;
        $('#filtro-dni').on('keyup', function() {
            clearTimeout(timerDni);
            timerDni = setTimeout(function() {
                actualizarEnlaceExcel();
                table.ajax.reload();
            }, 350);
        });

        // Botón para limpiar filtros
        $('#btn-limpiar').on('click', function() {
            $('#filtro-curso').val('');
            $('#filtro-desde').val('');
            $('#filtro-hasta').val('');
            $('#filtro-dni').val('');
            actualizarEnlaceExcel();
            table.ajax.reload();
        });
    });
</script>