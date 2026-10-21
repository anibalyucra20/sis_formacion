<?php
require_once __DIR__ . '/../../layouts/header.php';
?>

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between pb-3">
            <div>
                <h4 class="mb-1 font-weight-bold" style="color: #1e293b;">Carga Masiva de Personas con Excel</h4>
                <p class="text-muted font-size-13 mb-0">Importación rápida de cientos o miles de participantes desde una hoja de cálculo</p>
            </div>
            <div>
                <a href="<?= BASE_URL ?>/admin/personas" class="btn btn-outline-info font-weight-bold" style="border-radius: 8px;">
                    <i class="fa-solid fa-arrow-left mr-1"></i> Volver a la Lista
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-9">
        <!-- Paso 1: Descargar Plantilla -->
        <div class="card mb-4 border-primary" style="border-width: 1.5px;">
            <div class="card-body p-4 d-flex flex-column flex-md-row align-items-center justify-content-between">
                <div class="d-flex align-items-center mb-3 mb-md-0" style="gap: 16px;">
                    <div style="width: 50px; height: 50px; border-radius: 12px; background: #ecfdf5; color: #10b981; display: flex; align-items: center; justify-content: center; font-size: 24px;">
                        <i class="fa-solid fa-file-excel"></i>
                    </div>
                    <div>
                        <h5 class="font-weight-bold mb-1" style="font-size: 16px;">1. Descargue la Plantilla Oficial</h5>
                        <p class="text-muted font-size-13 mb-0">Utilice el formato preestablecido con las columnas requeridas (DNI, Nombres, Apellidos, Correo, Teléfono).</p>
                    </div>
                </div>
                <a href="<?= BASE_URL ?>/admin/personas/plantilla" class="btn btn-success font-weight-bold px-4 py-2" style="border-radius: 8px; white-space: nowrap;" data-no-loader="true">
                    <i class="fa-solid fa-download mr-1"></i> Descargar Plantilla Excel (.xlsx)
                </a>
            </div>
        </div>

        <!-- Paso 2: Subir Archivo -->
        <div class="card">
            <div class="card-header bg-white py-3">
                <h5 class="card-title font-weight-bold mb-0" style="font-size: 15px;">
                    <i class="fa-solid fa-cloud-arrow-up mr-2 text-primary"></i> 2. Subir Archivo con Datos de Personas
                </h5>
            </div>
            <div class="card-body p-4">
                <form action="<?= BASE_URL ?>/admin/personas/procesarCargaMasiva" method="POST" enctype="multipart/form-data">
                    <div class="form-group mb-4">
                        <label class="form-label font-weight-bold font-size-13">Seleccionar archivo Excel (.xlsx, .xls, .csv) <span class="text-danger">*</span></label>
                        <div class="custom-file">
                            <input type="file" name="archivo_excel" class="custom-file-input" id="customFile" accept=".xlsx, .xls, .csv" required>
                            <label class="custom-file-label" for="customFile">Examinar archivo de su equipo...</label>
                        </div>
                    </div>

                    <!-- Instrucciones y Normas -->
                    <div class="bg-light p-3 rounded mb-4" style="border: 1px dashed #cbd5e1;">
                        <h6 class="font-weight-bold font-size-13 text-dark mb-2"><i class="fa-solid fa-circle-question mr-1 text-info"></i> Pautas de Importación:</h6>
                        <ul class="font-size-12 text-muted mb-0 pl-3">
                            <li>La primera fila del archivo se considera encabezado y será ignorada en la inserción.</li>
                            <li><strong>Columna A:</strong> DNI (Obligatorio, numérico o alfanumérico).</li>
                            <li><strong>Columna B:</strong> Nombres del participante (Obligatorio).</li>
                            <li><strong>Columna C:</strong> Apellidos del participante (Obligatorio).</li>
                            <li><strong>Columna D:</strong> Correo electrónico (Opcional).</li>
                            <li><strong>Columna E:</strong> Teléfono (Opcional).</li>
                        </ul>
                    </div>

                    <div class="d-flex justify-content-end" style="gap: 10px;">
                        <a href="<?= BASE_URL ?>/admin/personas" class="btn btn-outline-danger font-weight-bold px-4">Cancelar</a>
                        <button type="submit" class="btn btn-primary font-weight-bold px-4">
                            <i class="fa-solid fa-upload mr-1"></i> Iniciar Importación Masiva
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/../../layouts/footer.php';
?>

<script>
    $('#customFile').on('change', function() {
        let fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').addClass("selected").html(fileName);
    });
</script>