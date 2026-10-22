<?php

namespace App\Controllers\Admin;

require_once __DIR__ . '/../../models/Admin/Certificado.php';
require_once __DIR__ . '/../../models/Admin/Curso.php';
require_once __DIR__ . '/../../models/Admin/Persona.php';
require_once __DIR__ . '/../../models/Admin/Configuracion.php';

use Core\Controller;
use Core\Auth;
use App\Models\Admin\Certificado;
use App\Models\Admin\Curso;
use App\Models\Admin\Persona;
use App\Models\Admin\Configuracion;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class CertificadosController extends Controller
{
    protected $model;
    protected $cursoModel;
    protected $personaModel;
    protected $cfgModel;

    public function __construct()
    {
        parent::__construct();
        $this->model = new Certificado();
        $this->cursoModel = new Curso();
        $this->personaModel = new Persona();
        $this->cfgModel = new Configuracion();
    }

    public function index()
    {
        $cursos = $this->cursoModel->getAll();

        $this->view('admin/certificados/index', [
            'cursos'    => $cursos,
            'pageTitle' => 'Emisión y Control de Certificados',
            'isAdmin'   => true,
            'module'    => 'admin'
        ]);
    }

    public function data()
    {
        header('Content-Type: application/json; charset=utf-8');

        $draw      = isset($_GET['draw']) ? (int)$_GET['draw'] : 1;
        $start     = isset($_GET['start']) ? (int)$_GET['start'] : 0;
        $length    = isset($_GET['length']) ? (int)$_GET['length'] : 10;
        $orderCol  = isset($_GET['order'][0]['column']) ? (int)$_GET['order'][0]['column'] : 0;
        $orderDir  = isset($_GET['order'][0]['dir']) ? $_GET['order'][0]['dir'] : 'desc';
        $search    = isset($_GET['search']['value']) ? trim($_GET['search']['value']) : '';
        $curso_id  = !empty($_GET['curso_id']) ? (int)$_GET['curso_id'] : null;
        $condicion = isset($_GET['condicion']) ? trim($_GET['condicion']) : '';

        try {
            $result = $this->model->getPaginated($search, $curso_id, $condicion, $length, $start, $orderCol, $orderDir);

            echo json_encode([
                'draw'            => $draw,
                'recordsTotal'    => $result['recordsTotal'],
                'recordsFiltered' => $result['recordsFiltered'],
                'data'            => $result['data']
            ], JSON_UNESCAPED_UNICODE);
        } catch (\Throwable $e) {
            echo json_encode([
                'draw'            => $draw,
                'recordsTotal'    => 0,
                'recordsFiltered' => 0,
                'data'            => [],
                'error'           => $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
        exit;
    }

    /**
     * Formulario para vinculación y emisión individual de certificado
     */
    public function nuevo()
    {
        $cursos = $this->cursoModel->getAll();
        $personas = $this->personaModel->getAll();
        $cursoSeleccionado = isset($_GET['curso_id']) ? (int)$_GET['curso_id'] : null;
        $personaSeleccionada = isset($_GET['persona_id']) ? (int)$_GET['persona_id'] : null;

        $this->view('admin/certificados/nuevo', [
            'cursos'              => $cursos,
            'personas'            => $personas,
            'cursoSeleccionado'   => $cursoSeleccionado,
            'personaSeleccionada' => $personaSeleccionada,
            'pageTitle'           => 'Emitir Certificado / Vincular a Curso',
            'isAdmin'             => true,
            'module'              => 'admin'
        ]);
    }

    public function editar($id = null)
    {
        if (!$id) {
            header('Location: ' . BASE_URL . '/admin/certificados');
            exit;
        }

        $certificado = $this->model->find($id);
        if (!$certificado) {
            $_SESSION['flash_error'] = 'Certificado no encontrado.';
            header('Location: ' . BASE_URL . '/admin/certificados');
            exit;
        }

        $cursos = $this->cursoModel->getAll();
        $personas = $this->personaModel->getAll();

        $this->view('admin/certificados/editar', [
            'certificado' => $certificado,
            'cursos'      => $cursos,
            'personas'    => $personas,
            'pageTitle'   => 'Editar Certificado: ' . htmlspecialchars($certificado['participante'] ?? 'Registro'),
            'isAdmin'     => true,
            'module'      => 'admin'
        ]);
    }

    public function guardar()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/admin/certificados');
            exit;
        }

        $id         = !empty($_POST['id']) ? (int)$_POST['id'] : null;
        $curso_id   = !empty($_POST['curso_id']) ? (int)$_POST['curso_id'] : null;
        $persona_id = !empty($_POST['persona_id']) ? (int)$_POST['persona_id'] : null;
        $nota       = trim($_POST['nota'] ?? '');
        $condicion  = $_POST['condicion'] ?? 'Aprobado';
        $fecha      = !empty($_POST['fecha_emision']) ? $_POST['fecha_emision'] : date('Y-m-d');
        $obs        = trim($_POST['observaciones'] ?? '');

        if (!$curso_id || !$persona_id) {
            $_SESSION['flash_error'] = 'Seleccione el curso y la persona participante.';
            header('Location: ' . ($id ? BASE_URL . '/admin/certificados/editar/' . $id : BASE_URL . '/admin/certificados/nuevo'));
            exit;
        }

        // Validar si la persona ya está vinculada a ese curso
        $yaVinculado = $this->model->findByCursoPersona($curso_id, $persona_id, $id);
        if ($yaVinculado) {
            $_SESSION['flash_error'] = 'Esta persona ya tiene un certificado emitido para este mismo curso.';
            header('Location: ' . ($id ? BASE_URL . '/admin/certificados/editar/' . $id : BASE_URL . '/admin/certificados/nuevo'));
            exit;
        }

        $this->model->guardar([
            'id'            => $id,
            'curso_id'      => $curso_id,
            'persona_id'    => $persona_id,
            'nota'          => $nota,
            'condicion'     => $condicion,
            'fecha_emision' => $fecha,
            'observaciones' => $obs
        ]);

        $_SESSION['flash_success'] = $id ? 'Certificado actualizado correctamente.' : 'Certificado emitido con éxito.';
        header('Location: ' . BASE_URL . '/admin/certificados');
        exit;
    }

    /**
     * Vista de Carga Masiva de Certificados (Vinculación Personas <-> Cursos)
     */
    public function cargaMasiva()
    {
        $cursos = $this->cursoModel->getAll();

        $this->view('admin/certificados/carga_masiva', [
            'cursos'    => $cursos,
            'pageTitle' => 'Carga Masiva de Certificados con Excel',
            'isAdmin'   => true,
            'module'    => 'admin'
        ]);
    }

    /**
     * Procesar subida de archivo Excel para vincular personas masivamente a cursos
     */
    public function procesarCargaMasiva()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/admin/certificados/cargaMasiva');
            exit;
        }

        $curso_id = !empty($_POST['curso_id']) ? (int)$_POST['curso_id'] : null;
        if (!$curso_id) {
            $_SESSION['flash_error'] = 'Debe seleccionar el curso al cual se vincularán los certificados.';
            header('Location: ' . BASE_URL . '/admin/certificados/cargaMasiva');
            exit;
        }

        $curso = $this->cursoModel->find($curso_id);
        if (!$curso) {
            $_SESSION['flash_error'] = 'El curso seleccionado no existe.';
            header('Location: ' . BASE_URL . '/admin/certificados/cargaMasiva');
            exit;
        }

        if (empty($_FILES['archivo_excel']['tmp_name'])) {
            $_SESSION['flash_error'] = 'Debe seleccionar un archivo Excel (.xlsx, .xls o .csv).';
            header('Location: ' . BASE_URL . '/admin/certificados/cargaMasiva');
            exit;
        }

        $archivo = $_FILES['archivo_excel']['tmp_name'];
        $ext = strtolower(pathinfo($_FILES['archivo_excel']['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, ['xlsx', 'xls', 'csv'])) {
            $_SESSION['flash_error'] = 'Formato de archivo inválido. Solo se admiten archivos Excel (.xlsx, .xls, .csv).';
            header('Location: ' . BASE_URL . '/admin/certificados/cargaMasiva');
            exit;
        }

        try {
            $spreadsheet = IOFactory::load($archivo);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray(null, true, true, true);

            $insertados = 0;
            $omitidos = 0;
            $errores = [];

            $primeraFila = true;
            $filaIndex = 1;

            foreach ($rows as $row) {
                if ($primeraFila) {
                    $primeraFila = false;
                    $filaIndex++;
                    continue;
                }

                // Columnas admitidas:
                // A: DNI
                // B: Nombres
                // C: Apellidos
                // D: Correo (opcional)
                // E: Telefono (opcional)
                // F: Nota (opcional)
                // G: Condicion (opcional)
                // H: Fecha Emision (YYYY-MM-DD o DD/MM/YYYY)

                $dni       = trim((string)($row['A'] ?? ''));
                $nombres   = trim((string)($row['B'] ?? ''));
                $apellidos = trim((string)($row['C'] ?? ''));
                $email     = trim((string)($row['D'] ?? ''));
                $telefono  = trim((string)($row['E'] ?? ''));
                $nota      = trim((string)($row['F'] ?? '18'));
                $condicion = !empty($row['G']) ? trim((string)$row['G']) : 'Aprobado';
                $fechaRaw  = trim((string)($row['H'] ?? ''));

                if (empty($dni) && empty($nombres) && empty($apellidos)) {
                    continue; // fila vacía
                }

                if (empty($dni)) {
                    $errores[] = "Fila {$filaIndex}: Falta el DNI del participante.";
                    $omitidos++;
                    $filaIndex++;
                    continue;
                }

                // Normalizar DNI
                $dni = preg_replace('/[^0-9A-Za-z]/', '', $dni);

                // 1. Buscar o registrar a la persona
                $persona = $this->personaModel->findByDni($dni);
                if (!$persona) {
                    if (empty($nombres) || empty($apellidos)) {
                        $errores[] = "Fila {$filaIndex}: El DNI {$dni} es nuevo pero no se indicaron Nombres y Apellidos.";
                        $omitidos++;
                        $filaIndex++;
                        continue;
                    }
                    $personaId = $this->personaModel->guardar([
                        'dni'       => $dni,
                        'nombres'   => $nombres,
                        'apellidos' => $apellidos,
                        'email'     => $email,
                        'telefono'  => $telefono
                    ]);
                } else {
                    $personaId = (int)$persona['id'];
                    // Si se suministraron nombres y apellidos más completos, actualizarlos
                    if (!empty($nombres) && !empty($apellidos)) {
                        $this->personaModel->guardar([
                            'id'        => $personaId,
                            'dni'       => $dni,
                            'nombres'   => $nombres,
                            'apellidos' => $apellidos,
                            'email'     => !empty($email) ? $email : $persona['email'],
                            'telefono'  => !empty($telefono) ? $telefono : $persona['telefono']
                        ]);
                    }
                }

                // 2. Verificar si ya tiene certificado en este curso
                $yaExiste = $this->model->findByCursoPersona($curso_id, $personaId);
                if ($yaExiste) {
                    $errores[] = "Fila {$filaIndex}: La persona con DNI {$dni} ya tiene un certificado emitido en este curso.";
                    $omitidos++;
                    $filaIndex++;
                    continue;
                }

                // 3. Formatear fecha
                $fechaEmision = date('Y-m-d');
                if (!empty($fechaRaw)) {
                    $timestamp = strtotime(str_replace('/', '-', $fechaRaw));
                    if ($timestamp) {
                        $fechaEmision = date('Y-m-d', $timestamp);
                    }
                }

                // 4. Insertar certificado (las horas se toman automáticamente del curso)
                $this->model->guardar([
                    'curso_id'      => $curso_id,
                    'persona_id'    => $personaId,
                    'nota'          => $nota,
                    'condicion'     => $condicion,
                    'fecha_emision' => $fechaEmision,
                    'observaciones' => 'Carga masiva Excel'
                ]);

                $insertados++;
                $filaIndex++;
            }

            $msg = "Carga masiva finalizada: {$insertados} certificados emitidos para el curso '{$curso['nombre']}'.";
            if ($omitidos > 0) {
                $msg .= " ({$omitidos} registros omitidos o duplicados).";
            }

            $_SESSION['flash_success'] = $msg;
            if (!empty($errores)) {
                $_SESSION['carga_errores'] = $errores;
            }

            header('Location: ' . BASE_URL . '/admin/certificados');
            exit;
        } catch (\Exception $e) {
            $_SESSION['flash_error'] = 'Error al procesar el archivo Excel: ' . $e->getMessage();
            header('Location: ' . BASE_URL . '/admin/certificados/cargaMasiva');
            exit;
        }
    }

    /**
     * Descargar plantilla Excel para vinculación de certificados
     */
    public function plantilla()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Vinculacion_Certificados');

        $headers = [
            'DNI',
            'NOMBRES',
            'APELLIDOS',
            'CORREO',
            'TELEFONO',
            'NOTA',
            'CONDICION',
            'FECHA_EMISION'
        ];
        $cols = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];

        foreach ($headers as $idx => $header) {
            $col = $cols[$idx];
            $sheet->setCellValue("{$col}1", $header);
            $sheet->getStyle("{$col}1")->getFont()->setBold(true)->getColor()->setARGB('FFFFFFFF');
            $sheet->getStyle("{$col}1")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FF1E3A8A');
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Ejemplos
        $hoy = date('Y-m-d');
        $ejemplos = [
            ['71234567', 'Juan Carlos', 'Pérez Salazar', 'juan.perez@gmail.com', '981234567', '19', 'Aprobado', $hoy],
            ['45892134', 'María Elena', 'Gómez Torres', 'maria.gomez@hotmail.com', '982345678', '18', 'Aprobado', $hoy],
            ['74561230', 'Ana Lucía', 'Fernández Quispe', 'ana.fernandez@gmail.com', '984567890', '20', 'Aprobado', $hoy]
        ];

        $r = 2;
        foreach ($ejemplos as $fila) {
            foreach ($fila as $idx => $val) {
                $col = $cols[$idx];
                $sheet->setCellValueExplicit("{$col}{$r}", $val, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            }
            $r++;
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="plantilla_vinculacion_certificados.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function eliminar($id = null)
    {
        if (!$id) {
            $this->responderJson(['success' => false, 'message' => 'ID no proporcionado']);
        }

        $ok = $this->model->eliminar($id);
        $msg = $ok ? 'Certificado eliminado correctamente.' : 'No se pudo eliminar el certificado.';

        if ($this->esAjax()) {
            $this->responderJson(['success' => (bool)$ok, 'message' => $msg]);
        }

        if ($ok) {
            $_SESSION['flash_success'] = $msg;
        } else {
            $_SESSION['flash_error'] = $msg;
        }

        header('Location: ' . BASE_URL . '/admin/certificados');
        exit;
    }

    private function esAjax(): bool
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    private function responderJson(array $data)
    {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }
}
