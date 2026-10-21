<?php

namespace App\Controllers\Admin;

include __DIR__ . '/../../models/Admin/Certificado.php';
include __DIR__ . '/../../models/Admin/Persona.php';

use Core\Controller;
use Core\Auth;
use App\Models\Admin\Persona;
use App\Models\Admin\Certificado;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class PersonasController extends Controller
{
    protected $model;
    protected $certModel;

    public function __construct()
    {
        parent::__construct();
        $this->model = new Persona();
        $this->certModel = new Certificado();
    }

    public function index()
    {
        $this->view('admin/personas/index', [
            'pageTitle' => 'Gestión de Personas / Participantes',
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
        $orderDir  = isset($_GET['order'][0]['dir']) ? $_GET['order'][0]['dir'] : 'asc';
        $search    = isset($_GET['search']['value']) ? trim($_GET['search']['value']) : '';

        $result = $this->model->getPaginated($search, $length, $start, $orderCol, $orderDir);

        echo json_encode([
            'draw'            => $draw,
            'recordsTotal'    => $result['recordsTotal'],
            'recordsFiltered' => $result['recordsFiltered'],
            'data'            => $result['data']
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    public function nuevo()
    {
        $this->view('admin/personas/nuevo', [
            'pageTitle' => 'Registrar Nueva Persona',
            'isAdmin'   => true,
            'module'    => 'admin'
        ]);
    }

    public function editar($id = null)
    {
        if (!$id) {
            header('Location: ' . BASE_URL . '/admin/personas');
            exit;
        }

        $persona = $this->model->find($id);
        if (!$persona) {
            $_SESSION['flash_error'] = 'Persona no encontrada.';
            header('Location: ' . BASE_URL . '/admin/personas');
            exit;
        }

        $this->view('admin/personas/editar', [
            'persona'   => $persona,
            'pageTitle' => 'Editar Persona - DNI: ' . htmlspecialchars($persona['dni']),
            'isAdmin'   => true,
            'module'    => 'admin'
        ]);
    }

    public function guardar()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/admin/personas');
            exit;
        }

        $id        = !empty($_POST['id']) ? (int)$_POST['id'] : null;
        $dni       = trim($_POST['dni'] ?? '');
        $nombres   = trim($_POST['nombres'] ?? '');
        $apellidos = trim($_POST['apellidos'] ?? '');
        $email     = trim($_POST['email'] ?? '');
        $telefono  = trim($_POST['telefono'] ?? '');

        if (empty($dni) || empty($nombres) || empty($apellidos)) {
            $_SESSION['flash_error'] = 'El DNI, los nombres y apellidos son campos obligatorios.';
            header('Location: ' . ($id ? BASE_URL . '/admin/personas/editar/' . $id : BASE_URL . '/admin/personas/nuevo'));
            exit;
        }

        // Verificar DNI duplicado
        $existente = $this->model->findByDni($dni, $id);
        if ($existente) {
            $_SESSION['flash_error'] = "El DNI '{$dni}' ya se encuentra registrado con {$existente['nombres']} {$existente['apellidos']}.";
            header('Location: ' . ($id ? BASE_URL . '/admin/personas/editar/' . $id : BASE_URL . '/admin/personas/nuevo'));
            exit;
        }

        $personaId = $this->model->guardar([
            'id'        => $id,
            'dni'       => $dni,
            'nombres'   => $nombres,
            'apellidos' => $apellidos,
            'email'     => $email,
            'telefono'  => $telefono
        ]);

        $_SESSION['flash_success'] = $id ? 'Datos de la persona actualizados correctamente.' : 'Persona registrada con éxito.';
        header('Location: ' . BASE_URL . '/admin/personas');
        exit;
    }

    /**
     * Vista de Carga Masiva de Personas
     */
    public function cargaMasiva()
    {
        $this->view('admin/personas/carga_masiva', [
            'pageTitle' => 'Carga Masiva de Personas con Excel',
            'isAdmin'   => true,
            'module'    => 'admin'
        ]);
    }

    /**
     * Procesar subida de archivo Excel para Carga Masiva de Personas
     */
    public function procesarCargaMasiva()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/admin/personas/cargaMasiva');
            exit;
        }

        if (empty($_FILES['archivo_excel']['tmp_name'])) {
            $_SESSION['flash_error'] = 'Debe seleccionar un archivo Excel (.xlsx, .xls o .csv).';
            header('Location: ' . BASE_URL . '/admin/personas/cargaMasiva');
            exit;
        }

        $archivo = $_FILES['archivo_excel']['tmp_name'];
        $ext = strtolower(pathinfo($_FILES['archivo_excel']['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, ['xlsx', 'xls', 'csv'])) {
            $_SESSION['flash_error'] = 'Formato de archivo inválido. Solo se admiten archivos Excel (.xlsx, .xls, .csv).';
            header('Location: ' . BASE_URL . '/admin/personas/cargaMasiva');
            exit;
        }

        try {
            $spreadsheet = IOFactory::load($archivo);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray(null, true, true, true);

            $insertados = 0;
            $actualizados = 0;
            $omitidos = 0;
            $errores = [];

            // Comenzar en fila 2 (asumiendo que fila 1 son encabezados)
            $primeraFila = true;
            $filaIndex = 1;

            foreach ($rows as $row) {
                if ($primeraFila) {
                    $primeraFila = false;
                    $filaIndex++;
                    continue;
                }

                $dni       = trim((string)($row['A'] ?? ''));
                $nombres   = trim((string)($row['B'] ?? ''));
                $apellidos = trim((string)($row['C'] ?? ''));
                $email     = trim((string)($row['D'] ?? ''));
                $telefono  = trim((string)($row['E'] ?? ''));

                // Fila vacía
                if (empty($dni) && empty($nombres) && empty($apellidos)) {
                    continue;
                }

                if (empty($dni) || empty($nombres) || empty($apellidos)) {
                    $errores[] = "Fila {$filaIndex}: Faltan datos obligatorios (DNI, Nombres o Apellidos).";
                    $omitidos++;
                    $filaIndex++;
                    continue;
                }

                // Normalizar DNI
                $dni = preg_replace('/[^0-9A-Za-z]/', '', $dni);

                $res = $this->model->upsertByDni($dni, $nombres, $apellidos, $email, $telefono);
                if ($res['action'] === 'inserted') {
                    $insertados++;
                } else {
                    $actualizados++;
                }

                $filaIndex++;
            }

            $msg = "Carga completada: {$insertados} personas nuevas registradas, {$actualizados} actualizadas.";
            if ($omitidos > 0) {
                $msg .= " ({$omitidos} filas omitidas por datos incompletos)";
            }

            $_SESSION['flash_success'] = $msg;
            if (!empty($errores)) {
                $_SESSION['carga_errores'] = $errores;
            }

            header('Location: ' . BASE_URL . '/admin/personas');
            exit;
        } catch (\Exception $e) {
            $_SESSION['flash_error'] = 'Error al procesar el archivo Excel: ' . $e->getMessage();
            header('Location: ' . BASE_URL . '/admin/personas/cargaMasiva');
            exit;
        }
    }

    /**
     * Descarga de plantilla oficial en Excel (.xlsx) para personas
     */
    public function plantilla()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Personas');

        // Encabezados
        $headers = ['DNI', 'NOMBRES', 'APELLIDOS', 'CORREO', 'TELEFONO'];
        $cols = ['A', 'B', 'C', 'D', 'E'];

        foreach ($headers as $idx => $header) {
            $col = $cols[$idx];
            $sheet->setCellValue("{$col}1", $header);
            $sheet->getStyle("{$col}1")->getFont()->setBold(true)->getColor()->setARGB('FFFFFFFF');
            $sheet->getStyle("{$col}1")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FF1E3A8A');
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Datos de ejemplo
        $ejemplos = [
            ['71234567', 'Juan Carlos', 'Pérez Salazar', 'juan.perez@gmail.com', '981234567'],
            ['45892134', 'María Elena', 'Gómez Torres', 'maria.gomez@hotmail.com', '982345678'],
            ['08912345', 'Luis Alberto', 'Rodríguez Chávez', 'luis.rodriguez@outlook.com', '983456789']
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
        header('Content-Disposition: attachment;filename="plantilla_carga_personas.xlsx"');
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

        $persona = $this->model->find($id);
        if (!$persona) {
            $this->responderJson(['success' => false, 'message' => 'Persona no encontrada']);
        }

        if ((int)$persona['total_cursos'] > 0) {
            $msg = 'No se puede eliminar la persona porque tiene certificados de cursos asociados. Elimine primero los certificados.';
            if ($this->esAjax()) {
                $this->responderJson(['success' => false, 'message' => $msg]);
            }
            $_SESSION['flash_error'] = $msg;
            header('Location: ' . BASE_URL . '/admin/personas');
            exit;
        }

        $ok = $this->model->eliminar($id);
        $msg = $ok ? 'Persona eliminada correctamente.' : 'No se pudo eliminar la persona.';

        if ($this->esAjax()) {
            $this->responderJson(['success' => (bool)$ok, 'message' => $msg]);
        }

        if ($ok) {
            $_SESSION['flash_success'] = $msg;
        } else {
            $_SESSION['flash_error'] = $msg;
        }

        header('Location: ' . BASE_URL . '/admin/personas');
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
