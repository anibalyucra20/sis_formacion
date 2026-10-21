<?php
require_once __DIR__ . '/../../layouts/header.php';
?>

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between pb-3">
            <div>
                <h4 class="mb-1 font-weight-bold" style="color: #1e293b;">Personas y Participantes</h4>
                <p class="text-muted font-size-13 mb-0">Directorio de participantes y personas que llevaron cursos</p>
            </div>
            <div style="gap: 10px;" class="d-flex">
                <a href="<?= BASE_URL ?>/admin/personas/cargaMasiva" class="btn btn-outline-success font-weight-bold" style="border-radius: 8px;">
                    <i class="fa-solid fa-file-excel mr-1"></i> Carga Masiva Excel
                </a>
                <a href="<?= BASE_URL ?>/admin/personas/nuevo" class="btn btn-primary font-weight-bold" style="border-radius: 8px;">
                    <i class="fa-solid fa-user-plus mr-1"></i> Nueva Persona
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
                <div class="table-responsive">
                    <table id="tabla-personas" class="table table-bordered table-striped table-hover dt-responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>DNI</th>
                                <th>Apellidos</th>
                                <th>Nombres</th>
                                <th>Correo Electrónico</th>
                                <th>Teléfono</th>
                                <th>Cursos Llevados</th>
                                <th>Fecha Registro</th>
                                <th class="text-center" style="width: 140px;">Acciones</th>
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
    const table = $('#tabla-personas').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        order: [[2, 'asc']],
        ajax: {
            url: '<?= BASE_URL ?>/admin/personas/data',
            type: 'GET',
            headers: { 'X-SIGI-DT': '1' }
        },
        columns: [
            { data: 'id' },
            { 
                data: 'dni',
                render: function(data) {
                    return '<span class="badge badge-light border text-primary font-weight-bold font-size-13 px-2 py-1">' + data + '</span>';
                }
            },
            { data: 'apellidos', className: 'font-weight-bold' },
            { data: 'nombres' },
            { 
                data: 'email',
                render: function(data) {
                    return data ? '<span class="text-muted">' + data + '</span>' : '<span class="text-muted font-italic">-</span>';
                }
            },
            { 
                data: 'telefono',
                render: function(data) {
                    return data ? data : '<span class="text-muted">-</span>';
                }
            },
            { 
                data: 'total_cursos',
                render: function(data) {
                    return '<span class="badge badge-pill badge-primary px-2 font-weight-bold">' + data + ' cursos</span>';
                }
            },
            { 
                data: 'fecha_registro',
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
                            <a href="<?= BASE_URL ?>/consulta?dni=${row.dni}" target="_blank" class="btn btn-outline-info" title="Ver Certificados Públicos">
                                <i class="fa-solid fa-graduation-cap"></i>
                            </a>
                            <a href="<?= BASE_URL ?>/admin/personas/editar/${row.id}" class="btn btn-outline-primary" title="Editar Persona">
                                <i class="fa-solid fa-user-pen"></i>
                            </a>
                            <button type="button" class="btn btn-outline-danger btn-eliminar" data-id="${row.id}" data-nombre="${row.nombres} ${row.apellidos}" title="Eliminar Persona">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    `;
                }
            }
        ]
    });


    $(document).on('click', '.btn-eliminar', function() {
        const id = $(this).data('id');
        const nombre = $(this).data('nombre');

        Swal.fire({
            title: '¿Eliminar persona?',
            text: `¿Desea eliminar a "${nombre}" del sistema?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= BASE_URL ?>/admin/personas/eliminar/' + id,
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
