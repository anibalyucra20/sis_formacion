<?php

namespace App\Controllers\Admin;

include __DIR__ . '/../../models/Admin/Usuario.php';
require_once __DIR__ . '/../../utils/Mailer.php';

use Core\Controller;
use Core\Auth;
use App\Models\Admin\Usuario;
use App\Utils\Mailer;

class UsuariosController extends Controller
{
    protected $model;

    public function __construct()
    {
        parent::__construct();
        $this->model = new Usuario();
    }

    public function index()
    {
        $this->view('admin/usuarios/index', [
            'pageTitle' => 'Gestión de Usuarios del Sistema',
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
        $this->view('admin/usuarios/nuevo', [
            'pageTitle' => 'Nuevo Usuario Administrador',
            'isAdmin'   => true,
            'module'    => 'admin'
        ]);
    }

    public function editar($id = null)
    {
        if (!$id) {
            header('Location: ' . BASE_URL . '/admin/usuarios');
            exit;
        }

        $usuario = $this->model->find($id);
        if (!$usuario) {
            $_SESSION['flash_error'] = 'El usuario no existe.';
            header('Location: ' . BASE_URL . '/admin/usuarios');
            exit;
        }

        $this->view('admin/usuarios/editar', [
            'usuario'   => $usuario,
            'pageTitle' => 'Editar Usuario: ' . htmlspecialchars($usuario['nombre']),
            'isAdmin'   => true,
            'module'    => 'admin'
        ]);
    }

    public function guardar()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/admin/usuarios');
            exit;
        }

        $id        = !empty($_POST['id']) ? (int)$_POST['id'] : null;
        $nombre    = trim($_POST['nombre'] ?? '');
        $apellidos = trim($_POST['apellidos'] ?? '');
        $email     = trim($_POST['email'] ?? '');
        $telefono  = trim($_POST['telefono'] ?? '');
        $password  = $_POST['password'] ?? '';
        $activo    = isset($_POST['activo']) ? (int)$_POST['activo'] : 1;

        if (empty($nombre) || empty($apellidos) || empty($email)) {
            $_SESSION['flash_error'] = 'Por favor complete todos los campos obligatorios.';
            header('Location: ' . ($id ? BASE_URL . '/admin/usuarios/editar/' . $id : BASE_URL . '/admin/usuarios/nuevo'));
            exit;
        }

        if (!$id && empty($password)) {
            $_SESSION['flash_error'] = 'La contraseña es requerida para nuevos usuarios.';
            header('Location: ' . BASE_URL . '/admin/usuarios/nuevo');
            exit;
        }

        // Validar correo duplicado
        $existente = $this->model->findByEmail($email, $id);
        if ($existente) {
            $_SESSION['flash_error'] = 'El correo electrónico ya se encuentra registrado por otro usuario.';
            header('Location: ' . ($id ? BASE_URL . '/admin/usuarios/editar/' . $id : BASE_URL . '/admin/usuarios/nuevo'));
            exit;
        }

        $data = [
            'id'        => $id,
            'nombre'    => $nombre,
            'apellidos' => $apellidos,
            'email'     => $email,
            'telefono'  => $telefono,
            'activo'    => $activo
        ];

        if (!empty($password)) {
            $data['password'] = $password;
        }

        $this->model->guardar($data);

        $_SESSION['flash_success'] = $id ? 'Usuario actualizado con éxito.' : 'Usuario creado correctamente.';
        header('Location: ' . BASE_URL . '/admin/usuarios');
        exit;
    }

    /**
     * Enviar correo con enlace para cambio/restablecimiento de contraseña
     */
    public function enviarReset($id = null)
    {
        if (!$id) {
            header('Location: ' . BASE_URL . '/admin/usuarios');
            exit;
        }

        $usuario = $this->model->find($id);
        if (!$usuario) {
            $_SESSION['flash_error'] = 'Usuario no encontrado.';
            header('Location: ' . BASE_URL . '/admin/usuarios');
            exit;
        }

        $token = bin2hex(random_bytes(24));
        $db = \Core\Model::getDB();
        $db->prepare("UPDATE usuarios SET token_password = ?, reset_password = 1 WHERE id = ?")->execute([$token, $id]);

        $resetUrl = BASE_URL . '/restablecer?token=' . urlencode($token) . '&email=' . urlencode($usuario['email']);

        $html = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 8px; border: 1px solid #e2e8f0;'>
            <div style='background: #1e3a8a; padding: 20px; text-align: center; color: #ffffff;'>
                <h3 style='margin: 0;'>Sistema de Control de Certificados</h3>
                <p style='margin: 4px 0 0; font-size: 13px;'>Restablecimiento de Contraseña</p>
            </div>
            <div style='padding: 24px; color: #334155; line-height: 1.6;'>
                <p>Hola <strong>" . htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellidos']) . "</strong>,</p>
                <p>Se ha generado una solicitud para restablecer o cambiar su contraseña de acceso.</p>
                <div style='text-align: center; margin: 24px 0;'>
                    <a href='{$resetUrl}' style='background: #2563eb; color: #ffffff; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: bold; display: inline-block;'>Cambiar Contraseña</a>
                </div>
                <p style='font-size: 12px; color: #64748b;'>Enlace directo:<br><a href='{$resetUrl}'>{$resetUrl}</a></p>
            </div>
        </div>";

        $res = Mailer::send($usuario['email'], $usuario['nombre'], 'Restablecer contraseña de usuario', $html);

        if ($res['ok']) {
            $_SESSION['flash_success'] = "Se ha enviado el correo de restablecimiento a {$usuario['email']}.";
        } else {
            $_SESSION['flash_success'] = "Token generado. Enlace de restablecimiento listo:";
            $_SESSION['dev_reset_link'] = $resetUrl;
        }

        header('Location: ' . BASE_URL . '/admin/usuarios');
        exit;
    }

    /**
     * Vista de Mi Perfil (para el usuario en sesión)
     */
    public function perfil()
    {
        $id = $_SESSION['user_id'] ?? 1;
        $usuario = $this->model->find($id);

        $this->view('admin/usuarios/perfil', [
            'usuario'   => $usuario,
            'pageTitle' => 'Mi Perfil de Usuario',
            'isAdmin'   => true,
            'module'    => 'admin'
        ]);
    }

    /**
     * Guardar cambios en Mi Perfil
     */
    public function guardarPerfil()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/admin/usuarios/perfil');
            exit;
        }

        $id        = $_SESSION['user_id'] ?? 1;
        $nombre    = trim($_POST['nombre'] ?? '');
        $apellidos = trim($_POST['apellidos'] ?? '');
        $email     = trim($_POST['email'] ?? '');
        $telefono  = trim($_POST['telefono'] ?? '');
        $passActual = $_POST['password_actual'] ?? '';
        $passNueva = $_POST['password_nueva'] ?? '';
        $passConf  = $_POST['password_confirm'] ?? '';

        if (empty($nombre) || empty($apellidos) || empty($email)) {
            $_SESSION['flash_error'] = 'Complete los campos obligatorios.';
            header('Location: ' . BASE_URL . '/admin/usuarios/perfil');
            exit;
        }

        $usuario = $this->model->find($id);

        $data = [
            'id'        => $id,
            'nombre'    => $nombre,
            'apellidos' => $apellidos,
            'email'     => $email,
            'telefono'  => $telefono,
            'activo'    => 1
        ];

        // Si desea cambiar contraseña
        if (!empty($passNueva)) {
            if (empty($passActual) || !password_verify($passActual, $usuario['password_hash'])) {
                $_SESSION['flash_error'] = 'La contraseña actual ingresada es incorrecta.';
                header('Location: ' . BASE_URL . '/admin/usuarios/perfil');
                exit;
            }

            if (strlen($passNueva) < 6) {
                $_SESSION['flash_error'] = 'La nueva contraseña debe tener al menos 6 caracteres.';
                header('Location: ' . BASE_URL . '/admin/usuarios/perfil');
                exit;
            }

            if ($passNueva !== $passConf) {
                $_SESSION['flash_error'] = 'Las nuevas contraseñas no coinciden.';
                header('Location: ' . BASE_URL . '/admin/usuarios/perfil');
                exit;
            }

            $data['password'] = $passNueva;
        }

        $this->model->guardar($data);

        // Actualizar sesión
        $_SESSION['user_name'] = $nombre . ' ' . $apellidos;
        $_SESSION['user_email'] = $email;

        $_SESSION['flash_success'] = 'Perfil actualizado correctamente.';
        header('Location: ' . BASE_URL . '/admin/usuarios/perfil');
        exit;
    }

    public function cambiarEstado($id = null)
    {
        if (!$id) {
            $this->responderJson(['success' => false, 'message' => 'ID no proporcionado']);
        }

        if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $id) {
            $msg = 'No puede desactivar su propia cuenta en sesión.';
            if ($this->esAjax()) {
                $this->responderJson(['success' => false, 'message' => $msg]);
            }
            $_SESSION['flash_error'] = $msg;
            header('Location: ' . BASE_URL . '/admin/usuarios');
            exit;
        }

        $usuario = $this->model->find($id);
        if (!$usuario) {
            $this->responderJson(['success' => false, 'message' => 'Usuario no encontrado']);
        }

        $nuevoEstado = $usuario['activo'] == 1 ? 0 : 1;
        $actualizado = $this->model->cambiarEstado($id, $nuevoEstado);
        $msg = $nuevoEstado == 1 ? 'Usuario activado.' : 'Usuario desactivado.';

        if ($this->esAjax()) {
            $this->responderJson([
                'success'     => (bool)$actualizado,
                'message'     => $msg,
                'nuevoEstado' => $nuevoEstado
            ]);
        }

        $_SESSION['flash_success'] = $msg;
        header('Location: ' . BASE_URL . '/admin/usuarios');
        exit;
    }

    public function eliminar($id = null)
    {
        if (!$id) {
            $this->responderJson(['success' => false, 'message' => 'ID no proporcionado']);
        }

        if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $id) {
            $msg = 'No puede eliminar su propia cuenta de administrador.';
            if ($this->esAjax()) {
                $this->responderJson(['success' => false, 'message' => $msg]);
            }
            $_SESSION['flash_error'] = $msg;
            header('Location: ' . BASE_URL . '/admin/usuarios');
            exit;
        }

        $eliminado = $this->model->eliminar($id);
        $msg = $eliminado ? 'Usuario eliminado correctamente.' : 'No se pudo eliminar el usuario.';

        if ($this->esAjax()) {
            $this->responderJson(['success' => (bool)$eliminado, 'message' => $msg]);
        }

        if ($eliminado) {
            $_SESSION['flash_success'] = $msg;
        } else {
            $_SESSION['flash_error'] = $msg;
        }

        header('Location: ' . BASE_URL . '/admin/usuarios');
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
