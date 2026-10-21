<?php
require_once __DIR__ . '/../../layouts/header.php';
?>

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between pb-3">
            <div>
                <h4 class="mb-1 font-weight-bold" style="color: #1e293b;">Gestión de Usuarios</h4>
                <p class="text-muted font-size-13 mb-0">Administradores con acceso de gestión al sistema de certificados</p>
            </div>
            <div>
                <a href="<?= BASE_URL ?>/admin/usuarios/nuevo" class="btn btn-primary font-weight-bold" style="border-radius: 8px;">
                    <i class="fa-solid fa-user-plus mr-1"></i> Nuevo Usuario
                </a>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($_SESSION['dev_reset_link'])): ?>
    <div class="alert alert-info alert-dismissible fade show" role="alert" style="border-radius: 8px;">
        <i class="fa-solid fa-key mr-2"></i> <strong>Enlace de restablecimiento generado:</strong><br>
        <a href="<?= $_SESSION['dev_reset_link'] ?>" target="_blank" class="font-weight-bold"><?= $_SESSION['dev_reset_link'] ?></a>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <?php unset($_SESSION['dev_reset_link']); ?>
<?php endif; ?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="tabla-usuarios" class="table table-bordered table-striped table-hover dt-responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nombre Completo</th>
                                <th>Correo Electrónico</th>
                                <th>Teléfono</th>
                                <th>Estado</th>
                                <th>Fecha Registro</th>
                                <th class="text-center" style="width: 170px;">Acciones</th>
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
        const table = $('#tabla-usuarios').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            order: [
                [0, 'desc']
            ],
            ajax: {
                url: '<?= BASE_URL ?>/admin/usuarios/data',
                type: 'GET',
                headers: {
                    'X-SIGI-DT': '1'
                }
            },
            columns: [{
                    data: 'id'
                },
                {
                    data: null,
                    render: function(data, type, row) {
                        return '<div class="font-weight-bold">' + row.nombre + ' ' + row.apellidos + '</div>';
                    }
                },
                {
                    data: 'email'
                },
                {
                    data: 'telefono',
                    render: function(data) {
                        return data ? data : '<span class="text-muted">-</span>';
                    }
                },
                {
                    data: 'activo',
                    render: function(data, type, row) {
                        const badge = data == 1 ?
                            '<span class="badge badge-success px-2 py-1">Activo</span>' :
                            '<span class="badge badge-danger px-2 py-1">Inactivo</span>';
                        return badge;
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
                            <a href="<?= BASE_URL ?>/admin/usuarios/enviarReset/${row.id}" class="btn btn-outline-info" title="Enviar enlace de restablecimiento de contraseña por correo">
                                <i class="fa-solid fa-key"></i>
                            </a>
                            <a href="<?= BASE_URL ?>/admin/usuarios/editar/${row.id}" class="btn btn-outline-primary" title="Editar Usuario">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                            <button type="button" class="btn btn-outline-warning btn-estado" data-id="${row.id}" title="Cambiar Estado">
                                <i class="fa-solid fa-power-off"></i>
                            </button>
                            <button type="button" class="btn btn-outline-danger btn-eliminar" data-id="${row.id}" data-nombre="${row.nombre}" title="Eliminar Usuario">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    `;
                    }
                }
            ]
        });


        $(document).on('click', '.btn-estado', function() {
            const id = $(this).data('id');
            $.ajax({
                url: '<?= BASE_URL ?>/admin/usuarios/cambiarEstado/' + id,
                type: 'GET',
                dataType: 'json',
                success: function(res) {
                    if (res.success) {
                        table.ajax.reload(null, false);
                    } else {
                        Swal.fire('Atención', res.message, 'warning');
                    }
                }
            });
        });

        $(document).on('click', '.btn-eliminar', function() {
            const id = $(this).data('id');
            const nombre = $(this).data('nombre');

            Swal.fire({
                title: '¿Eliminar usuario?',
                text: `¿Desea eliminar la cuenta de ${nombre}?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '<?= BASE_URL ?>/admin/usuarios/eliminar/' + id,
                        type: 'GET',
                        dataType: 'json',
                        success: function(res) {
                            if (res.success) {
                                Swal.fire('¡Eliminado!', res.message, 'success');
                                table.ajax.reload(null, false);
                            } else {
                                Swal.fire('Atención', res.message, 'error');
                            }
                        }
                    });
                }
            });
        });
    });
</script>