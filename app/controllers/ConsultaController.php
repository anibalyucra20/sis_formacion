<?php

namespace App\Controllers;

include __DIR__ . '/../models/Admin/Certificado.php';
include __DIR__ . '/../models/Admin/Configuracion.php';

use Core\Controller;
use App\Models\Admin\Certificado;
use App\Models\Admin\Configuracion;

class ConsultaController extends Controller
{
    protected $certModel;
    protected $cfgModel;

    public function __construct()
    {
        parent::__construct();
        $this->certModel = new Certificado();
        $this->cfgModel  = new Configuracion();
    }

    /**
     * Vista pública principal de consulta por DNI
     */
    public function index()
    {
        $dni = isset($_GET['dni']) ? trim($_GET['dni']) : '';
        $resultado = null;
        $buscado = false;

        if (!empty($dni)) {
            $buscado = true;
            $resultado = $this->certModel->buscarPorDni($dni);
        }

        $configuracion = $this->cfgModel->get();

        $this->view('consulta/index', [
            'pageTitle'     => 'Consulta de Certificados de Cursos',
            'dni'           => $dni,
            'buscado'       => $buscado,
            'resultado'     => $resultado,
            'configuracion' => $configuracion,
            'module'        => 'public'
        ]);
    }

    /**
     * Endpoint AJAX para búsqueda rápida por DNI
     */
    public function buscar()
    {
        $dni = trim($_GET['dni'] ?? $_POST['dni'] ?? '');

        if (empty($dni)) {
            return $this->json(['success' => false, 'message' => 'Por favor ingrese un número de DNI válido.']);
        }

        $resultado = $this->certModel->buscarPorDni($dni);

        if (!$resultado) {
            return $this->json([
                'success' => false,
                'message' => 'No se encontraron registros de certificados emitidos para el DNI ' . htmlspecialchars($dni) . '.'
            ]);
        }

        return $this->json([
            'success' => true,
            'data'    => $resultado
        ]);
    }

    /**
     * Redirigir a la vista oficial de consulta por DNI
     */
    public function verificar($id = null)
    {
        if (empty($id)) {
            $id = trim($_GET['id'] ?? '');
        }

        if (!empty($id)) {
            $certificado = $this->certModel->find((int)$id);
            if ($certificado && !empty($certificado['dni'])) {
                header('Location: ' . BASE_URL . '/consulta?dni=' . urlencode($certificado['dni']));
                exit;
            }
        }

        header('Location: ' . BASE_URL . '/consulta');
        exit;
    }

    /**
     * Alias para verificar certificado
     */
    public function certificado($id = null)
    {
        return $this->verificar($id);
    }
}
