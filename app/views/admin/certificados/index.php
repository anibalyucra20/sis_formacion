<?php
require_once __DIR__ . '/../../layouts/header.php';
?>

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between pb-3">
            <div>
                <h4 class="mb-1 font-weight-bold" style="color: #1e293b;">Control de Certificados</h4>
                <p class="text-muted font-size-13 mb-0">Listado, emisión y verificación de certificados emitidos a los alumnos</p>
            </div>
            <div style="gap: 10px;" class="d-flex">
                <a href="<?= BASE_URL ?>/admin/certificados/cargaMasiva" class="btn btn-outline-success font-weight-bold" style="border-radius: 8px;">
                    <i class="fa-solid fa-file-excel mr-1"></i> Carga Masiva Excel
                </a>
                <a href="<?= BASE_URL ?>/admin/certificados/nuevo" class="btn btn-primary font-weight-bold" style="border-radius: 8px;">
                    <i class="fa-solid fa-award mr-1"></i> Emitir Certificado
                </a>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($_SESSION['carga_errores'])): ?>
    <div class="alert alert-warning alert-dismissible fade show" role="alert" style="border-radius: 8px;">
        <h6 class="font-weight-bold mb-2"><i class="fa-solid fa-triangle-exclamation mr-1"></i> Observaciones en la Carga Masiva:</h6>
        <ul class="mb-0 font-size-13 pl-3">
            <?php foreach (array_slice($_SESSION['carga_errores'], 0, 8) as $err): ?>
                <li><?= htmlspecialchars($err) ?></li>
            <?php endforeach; ?>
        </ul>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <?php unset($_SESSION['carga_errores']); ?>
<?php endif; ?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <!-- Filtros -->
                <div class="row mb-3 align-items-center">
                    <div class="col-md-4 mb-2 mb-md-0">
                        <label class="font-size-12 font-weight-bold text-muted mb-1">Filtrar por Curso</label>
                        <select id="filtro-curso" class="form-control form-control-sm">
                            <option value="">Todos los Cursos</option>
                            <?php foreach ($cursos as $c): ?>
                                <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['codigo'] . ' - ' . $c['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3 mb-2 mb-md-0">
                        <label class="font-size-12 font-weight-bold text-muted mb-1">Filtrar por Condición</label>
                        <select id="filtro-condicion" class="form-control form-control-sm">
                            <option value="">Todas las Condiciones</option>
                            <option value="Aprobado">Aprobado</option>
                            <option value="Participante">Participante</option>
                            <option value="Ponente">Ponente</option>
                            <option value="Organizador">Organizador</option>
                        </select>
                    </div>
                    <div class="col-md-5 text-md-right mt-3 mt-md-0">
                        <button type="button" id="btn-limpiar-filtros" class="btn btn-outline-info btn-sm font-weight-bold mt-3">
                            <i class="fa-solid fa-rotate-left mr-1"></i> Limpiar Filtros
                        </button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="tabla-certificados" class="table table-bordered table-striped table-hover dt-responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Cód. Certificado</th>
                                <th>DNI</th>
                                <th>Participante</th>
                                <th>Curso / Capacitación</th>
                                <th>Horas</th>
                                <th>Fecha Emisión</th>
                                <th class="text-center">Acciones</th>
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
        const table = $('#tabla-certificados').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            order: [
                [0, 'desc']
            ],
            ajax: {
                url: '<?= BASE_URL ?>/admin/certificados/data',
                type: 'GET',
                headers: {
                    'X-SIGI-DT': '1'
                },
                data: function(d) {
                    d.curso_id = $('#filtro-curso').val();
                    d.condicion = $('#filtro-condicion').val();
                }
            },
            columns: [{
                    data: 'id'
                },
                {
                    data: 'codigo_certificado',
                    render: function(data) {
                        return '<span class="font-weight-bold font-size-12 px-2 py-1">' + data + '</span>';
                    }
                },
                {
                    data: 'dni',
                    render: function(data) {
                        return '<span class="">' + data + '</span>';
                    }
                },
                {
                    data: 'participante',
                },
                {
                    data: 'curso_nombre',
                    render: function(data, type, row) {
                        return '<div class="text-truncate" style="max-width: 240px;" title="' + data + '">' + data + '</div>';
                    }
                },
                {
                    data: 'horas',
                    render: function(data) {
                        return '<span class="font-weight-bold">' + data + ' h</span>';
                    }
                },
                {
                    data: 'fecha_emision',
                    render: function(data) {
                        return data ? data.substring(0, 10) : '';
                    }
                },
                {
                    data: null,
                    orderable: false,
                    className: 'text-center',
                    render: function(data, type, row) {
                        return `
                        <div class="btn-group btn-group-sm" role="group">
                            <a href="<?= BASE_URL ?>/consulta/pdf/${encodeURIComponent(row.codigo_certificado)}" target="_blank" class="btn btn-outline-info" title="Descargar Certificado PDF" data-no-loader="true">
                                <i class="fa-solid fa-file-pdf"></i>
                            </a>
                            <a href="<?= BASE_URL ?>/verificar/${encodeURIComponent(row.codigo_certificado)}" target="_blank" class="btn btn-outline-success" title="Verificar Digital" data-no-loader="true">
                                <i class="fa-solid fa-shield-halved"></i>
                            </a>
                            <a href="<?= BASE_URL ?>/admin/certificados/editar/${row.id}" class="btn btn-outline-primary" title="Editar Certificado">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                            <button type="button" class="btn btn-outline-danger btn-eliminar" data-id="${row.id}" data-codigo="${row.codigo_certificado}" title="Eliminar">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    `;
                    }
                }
            ]
        });


        $('#filtro-curso, #filtro-condicion').on('change', function() {
            table.ajax.reload();
        });

        $('#btn-limpiar-filtros').on('click', function() {
            $('#filtro-curso').val('');
            $('#filtro-condicion').val('');
            table.ajax.reload();
        });

        $(document).on('click', '.btn-eliminar', function() {
            const id = $(this).data('id');
            const codigo = $(this).data('codigo');

            Swal.fire({
                title: '¿Eliminar certificado?',
                text: `¿Desea anular y eliminar el certificado ${codigo}?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '<?= BASE_URL ?>/admin/certificados/eliminar/' + id,
                        type: 'GET',
                        dataType: 'json',
                        success: function(res) {
                            if (res.success) {
                                Swal.fire('¡Eliminado!', res.message, 'success');
                                table.ajax.reload(null, false);
                            } else {
                                Swal.fire('Atención', res.message, 'error');
                            }
                        },
                        error: function() {
                            Swal.fire('Error', 'Ocurrió un error en la solicitud', 'error');
                        }
                    });
                }
            });
        });
    });
</script>