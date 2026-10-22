<?php

namespace App\Controllers\Admin;

require_once __DIR__ . '/../../models/Admin/Curso.php';
require_once __DIR__ . '/../../models/Admin/Certificado.php';

use Core\Controller;
use Core\Auth;
use App\Models\Admin\Curso;
use App\Models\Admin\Certificado;

class CursosController extends Controller
{
    protected $model;
    protected $certModel;

    public function __construct()
    {
        parent::__construct();
        $this->model = new Curso();
        $this->certModel = new Certificado();
    }

    public function index()
    {
        $this->view('admin/cursos/index', [
            'pageTitle' => 'Gestión de Cursos y Capacitaciones',
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
        $estado    = isset($_GET['estado']) ? trim($_GET['estado']) : '';
        $modalidad = isset($_GET['modalidad']) ? trim($_GET['modalidad']) : '';

        try {
            $result = $this->model->getPaginated($search, $estado, $modalidad, $length, $start, $orderCol, $orderDir);

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

    public function nuevo()
    {
        $this->view('admin/cursos/nuevo', [
            'pageTitle' => 'Nuevo Curso / Capacitación',
            'isAdmin'   => true,
            'module'    => 'admin'
        ]);
    }

    public function editar($id = null)
    {
        if (!$id) {
            header('Location: ' . BASE_URL . '/admin/cursos');
            exit;
        }

        $curso = $this->model->find($id);
        if (!$curso) {
            $_SESSION['flash_error'] = 'El curso seleccionado no existe.';
            header('Location: ' . BASE_URL . '/admin/cursos');
            exit;
        }

        $this->view('admin/cursos/editar', [
            'curso'     => $curso,
            'pageTitle' => 'Editar Curso: ' . htmlspecialchars($curso['codigo']),
            'isAdmin'   => true,
            'module'    => 'admin'
        ]);
    }

    public function detalle($id = null)
    {
        if (!$id) {
            header('Location: ' . BASE_URL . '/admin/cursos');
            exit;
        }

        $curso = $this->model->find($id);
        if (!$curso) {
            $_SESSION['flash_error'] = 'Curso no encontrado.';
            header('Location: ' . BASE_URL . '/admin/cursos');
            exit;
        }

        $alumnos = $this->certModel->getReporteCurso($id);

        $this->view('admin/cursos/detalle', [
            'curso'     => $curso,
            'alumnos'   => $alumnos,
            'pageTitle' => 'Detalle del Curso - ' . htmlspecialchars($curso['codigo']),
            'isAdmin'   => true,
            'module'    => 'admin'
        ]);
    }

    public function guardar()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/admin/cursos');
            exit;
        }

        $id        = !empty($_POST['id']) ? (int)$_POST['id'] : null;
        $codigo    = strtoupper(trim($_POST['codigo'] ?? ''));
        $nombre    = trim($_POST['nombre'] ?? '');
        $horas     = (int)($_POST['horas_academicas'] ?? 40);
        $instructor = trim($_POST['instructor'] ?? '');
        $modalidad = $_POST['modalidad'] ?? 'Virtual';
        $estado    = $_POST['estado'] ?? 'Activo';
        $f_inicio  = !empty($_POST['fecha_inicio']) ? $_POST['fecha_inicio'] : null;
        $f_fin     = !empty($_POST['fecha_fin']) ? $_POST['fecha_fin'] : null;
        $desc      = trim($_POST['descripcion'] ?? '');

        if (empty($codigo) || empty($nombre)) {
            $_SESSION['flash_error'] = 'El código y el nombre del curso son obligatorios.';
            header('Location: ' . ($id ? BASE_URL . '/admin/cursos/editar/' . $id : BASE_URL . '/admin/cursos/nuevo'));
            exit;
        }

        // Verificar código duplicado
        $existente = $this->model->findByCodigo($codigo, $id);
        if ($existente) {
            $_SESSION['flash_error'] = "El código '{$codigo}' ya se encuentra asignado a otro curso.";
            header('Location: ' . ($id ? BASE_URL . '/admin/cursos/editar/' . $id : BASE_URL . '/admin/cursos/nuevo'));
            exit;
        }

        $cursoId = $this->model->guardar([
            'id'               => $id,
            'codigo'           => $codigo,
            'nombre'           => $nombre,
            'descripcion'      => $desc,
            'horas_academicas' => $horas,
            'fecha_inicio'     => $f_inicio,
            'fecha_fin'        => $f_fin,
            'instructor'       => $instructor,
            'modalidad'        => $modalidad,
            'estado'           => $estado
        ]);

        $_SESSION['flash_success'] = $id ? 'Curso actualizado correctamente.' : 'Curso registrado con éxito.';
        header('Location: ' . BASE_URL . '/admin/cursos');
        exit;
    }

    public function eliminar($id = null)
    {
        if (!$id) {
            $this->responderJson(['success' => false, 'message' => 'ID no proporcionado']);
        }

        $curso = $this->model->find($id);
        if (!$curso) {
            $this->responderJson(['success' => false, 'message' => 'Curso no encontrado']);
        }

        if ((int)$curso['total_certificados'] > 0) {
            $msg = 'No se puede eliminar el curso porque tiene certificados emitidos asociados. Elimine primero los certificados vinculados.';
            if ($this->esAjax()) {
                $this->responderJson(['success' => false, 'message' => $msg]);
            }
            $_SESSION['flash_error'] = $msg;
            header('Location: ' . BASE_URL . '/admin/cursos');
            exit;
        }

        $ok = $this->model->eliminar($id);
        $msg = $ok ? 'Curso eliminado correctamente.' : 'No se pudo eliminar el curso.';

        if ($this->esAjax()) {
            $this->responderJson(['success' => (bool)$ok, 'message' => $msg]);
        }

        if ($ok) {
            $_SESSION['flash_success'] = $msg;
        } else {
            $_SESSION['flash_error'] = $msg;
        }

        header('Location: ' . BASE_URL . '/admin/cursos');
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
