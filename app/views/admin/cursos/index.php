<?php
require_once __DIR__ . '/../../layouts/header.php';
?>

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between pb-3">
            <div>
                <h4 class="mb-1 font-weight-bold" style="color: #1e293b;">Cursos y Capacitaciones</h4>
                <p class="text-muted font-size-13 mb-0">Administración de programas académicos y cursos de certificación</p>
            </div>
            <div>
                <a href="<?= BASE_URL ?>/admin/cursos/nuevo" class="btn btn-primary font-weight-bold" style="border-radius: 8px;">
                    <i class="fa-solid fa-plus mr-1"></i> Nuevo Curso
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Filtros y Tabla -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <!-- Filtros rápidos -->
                <div class="row mb-3 align-items-center">
                    <div class="col-md-3 mb-2 mb-md-0">
                        <label class="font-size-12 font-weight-bold text-muted mb-1">Filtrar por Estado</label>
                        <select id="filtro-estado" class="form-control form-control-sm">
                            <option value="">Todos los Estados</option>
                            <option value="Activo">Activo</option>
                            <option value="En Curso">En Curso</option>
                            <option value="Finalizado">Finalizado</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-2 mb-md-0">
                        <label class="font-size-12 font-weight-bold text-muted mb-1">Filtrar por Modalidad</label>
                        <select id="filtro-modalidad" class="form-control form-control-sm">
                            <option value="">Todas las Modalidades</option>
                            <option value="Virtual">Virtual</option>
                            <option value="Presencial">Presencial</option>
                            <option value="Semipresencial">Semipresencial</option>
                        </select>
                    </div>
                    <div class="col-md-6 text-md-right mt-3 mt-md-0">
                        <button type="button" id="btn-limpiar-filtros" class="btn btn-outline-info btn-sm font-weight-bold mt-3">
                            <i class="fa-solid fa-rotate-left mr-1"></i> Restablecer Filtros
                        </button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="tabla-cursos" class="table table-bordered table-striped table-hover dt-responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Código</th>
                                <th>Nombre del Curso</th>
                                <th>Horas</th>
                                <!--<th>Instructor</th>-->
                                <th>Modalidad</th>
                                <th>Estado</th>
                                <th>Certificados</th>
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
        const table = $('#tabla-cursos').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            order: [
                [0, 'desc']
            ],
            ajax: {
                url: '<?= BASE_URL ?>/admin/cursos/data',
                type: 'GET',
                headers: {
                    'X-SIGI-DT': '1'
                },
                data: function(d) {
                    d.estado = $('#filtro-estado').val();
                    d.modalidad = $('#filtro-modalidad').val();
                }
            },
            columns: [{
                    data: 'id'
                },
                {
                    data: 'codigo',
                    render: function(data) {
                        return '<span class="badge badge-light border text-primary font-weight-bold font-size-10">' + data + '</span>';
                    }
                },
                {
                    data: 'nombre',
                    render: function(data, type, row) {
                        return '<a href="<?= BASE_URL ?>/admin/cursos/detalle/' + row.id + '" class="font-weight-bold text-dark text-decoration-none">' + data + '</a>';
                    }
                },
                {
                    data: 'horas_academicas',
                    render: function(data) {
                        return '<span class="font-weight-bold">' + data + ' h</span>';
                    }
                },
                /*{
                    data: 'instructor',
                    render: function(data) {
                        return data ? data : '<span class="text-muted font-italic">No asignado</span>';
                    }
                }*/
                {
                    data: 'modalidad',
                    render: function(data) {
                        let badgeClass = 'badge-info';
                        if (data === 'Presencial') badgeClass = 'badge-secondary';
                        if (data === 'Semipresencial') badgeClass = 'badge-warning';
                        return '<span class="badge ' + badgeClass + ' px-2 py-1">' + data + '</span>';
                    }
                },
                {
                    data: 'estado',
                    render: function(data) {
                        let badgeClass = 'badge-success';
                        if (data === 'Finalizado') badgeClass = 'badge-danger';
                        if (data === 'En Curso') badgeClass = 'badge-primary';
                        return '<span class="badge ' + badgeClass + ' px-2 py-1 font-size-10">' + data + '</span>';
                    }
                },
                {
                    data: 'total_certificados',
                    render: function(data) {
                        return '<span class="badge badge-pill badge-primary px-2 font-weight-bold">' + data + '</span>';
                    }
                },
                {
                    data: null,
                    orderable: false,
                    className: 'text-center',
                    render: function(data, type, row) {
                        return `
                        <div class="btn-group btn-group-sm" role="group">
                            <a href="<?= BASE_URL ?>/admin/cursos/detalle/${row.id}" class="btn btn-outline-info" title="Ver Detalle y Alumnos">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            <a href="<?= BASE_URL ?>/admin/cursos/editar/${row.id}" class="btn btn-outline-primary" title="Editar Curso">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                            <button type="button" class="btn btn-outline-danger btn-eliminar" data-id="${row.id}" data-nombre="${row.nombre}" title="Eliminar Curso">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    `;
                    }
                }
            ]
        });


        $('#filtro-estado, #filtro-modalidad').on('change', function() {
            table.ajax.reload();
        });

        $('#btn-limpiar-filtros').on('click', function() {
            $('#filtro-estado').val('');
            $('#filtro-modalidad').val('');
            table.ajax.reload();
        });

        // Eliminar con SweetAlert2
        $(document).on('click', '.btn-eliminar', function() {
            const id = $(this).data('id');
            const nombre = $(this).data('nombre');

            Swal.fire({
                title: '¿Eliminar curso?',
                text: `Está a punto de eliminar el curso "${nombre}". Esta acción no se puede deshacer.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '<?= BASE_URL ?>/admin/cursos/eliminar/' + id,
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