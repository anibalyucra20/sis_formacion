<?php

namespace App\Controllers\Auth;

include __DIR__ . '/../../models/Admin/Usuario.php';
require_once __DIR__ . '/../../utils/Mailer.php';

use Core\Controller;
use Core\Model;
use Core\Auth;
use App\Utils\Mailer;
use PDO;

class LoginController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Vista de formulario de inicio de sesión
     */
    public function index()
    {
        // Si ya está logueado, redirigir directo al admin
        if (Auth::user() && Auth::validarSesion()) {
            header('Location: ' . BASE_URL . '/admin');
            exit;
        }

        $this->view('auth/login', [
            'pageTitle' => 'Iniciar Sesión - Control de Certificados',
            'module'    => 'auth'
        ]);
    }

    /**
     * Procesar POST de credenciales de login
     */
    public function acceder()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $correo = trim($_POST['correo'] ?? '');
        $pass   = $_POST['password'] ?? '';

        if (empty($correo) || empty($pass)) {
            $_SESSION['flash_error'] = 'Por favor complete todos los campos.';
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $db = Model::getDB();
        $stmt = $db->prepare("SELECT * FROM usuarios WHERE email = :correo AND activo = 1 LIMIT 1");
        $stmt->execute([':correo' => $correo]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($pass, $user['password_hash'])) {
            $llave = bin2hex(random_bytes(16));
            $token = password_hash($llave, PASSWORD_DEFAULT);

            $expira = date('Y-m-d H:i:s', strtotime('+4 hour'));
            $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
            $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';

            $sql = "INSERT INTO sesiones (usuario_id, token, ip_address, user_agent, fecha_expiracion, activa) 
                    VALUES (?, ?, ?, ?, ?, 1)";
            $stmt = $db->prepare($sql);
            $stmt->execute([$user['id'], $llave, $ip, $ua, $expira]);

            $user['id_session'] = $db->lastInsertId();
            $user['token'] = $token;

            Auth::login($user);

            // Redirección si venía de intentar acceder a una ruta protegida
            if (!empty($_SESSION['redirect_after_login'])) {
                $redir = $_SESSION['redirect_after_login'];
                unset($_SESSION['redirect_after_login']);
                header('Location: ' . $redir);
                exit;
            }

            header('Location: ' . BASE_URL . '/admin');
            exit;
        }

        $_SESSION['flash_error'] = 'Correo electrónico o contraseña incorrectos, o usuario inactivo.';
        header('Location: ' . BASE_URL . '/login?error=1');
        exit;
    }

    /**
     * Cerrar sesión
     */
    public function salir()
    {
        Auth::logout();
        header('Location: ' . BASE_URL . '/login');
        exit;
    }

    /**
     * Vista de solicitud de recuperación de contraseña
     */
    public function recuperar()
    {
        $this->view('auth/recuperar', [
            'pageTitle' => 'Recuperar Contraseña',
            'module'    => 'auth'
        ]);
    }

    /**
     * Procesar solicitud de recuperación por correo
     */
    public function enviarRecuperacion()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/recuperar');
            exit;
        }

        $email = trim($_POST['email'] ?? '');

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['flash_error'] = 'Por favor ingrese un correo electrónico válido.';
            header('Location: ' . BASE_URL . '/recuperar');
            exit;
        }

        $db = Model::getDB();
        $stmt = $db->prepare("SELECT id, nombre, apellidos, email FROM usuarios WHERE email = ? AND activo = 1 LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            // Mensaje genérico de seguridad para no exponer existencia de cuentas
            $_SESSION['flash_success'] = 'Si el correo está registrado en el sistema, recibirá las instrucciones para restablecer su contraseña.';
            header('Location: ' . BASE_URL . '/recuperar');
            exit;
        }

        // Generar token seguro
        $token = bin2hex(random_bytes(24));
        $updateStmt = $db->prepare("UPDATE usuarios SET token_password = ?, reset_password = 1 WHERE id = ?");
        $updateStmt->execute([$token, $user['id']]);

        $resetUrl = BASE_URL . '/restablecer?token=' . urlencode($token) . '&email=' . urlencode($user['email']);

        // Plantilla HTML de correo
        $html = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 8px; overflow: hidden; border: 1px solid #e2e8f0;'>
            <div style='background: #2563eb; padding: 24px; text-align: center; color: #ffffff;'>
                <h2 style='margin: 0; font-size: 22px;'>Control de Certificados de Cursos</h2>
                <p style='margin: 6px 0 0; opacity: 0.9; font-size: 14px;'>Recuperación de Contraseña</p>
            </div>
            <div style='padding: 30px 24px; color: #334155; line-height: 1.6;'>
                <p>Hola <strong>" . htmlspecialchars($user['nombre'] . ' ' . $user['apellidos']) . "</strong>,</p>
                <p>Hemos recibido una solicitud para restablecer la contraseña de su cuenta de acceso al sistema.</p>
                <div style='text-align: center; margin: 30px 0;'>
                    <a href='{$resetUrl}' style='background: #2563eb; color: #ffffff; padding: 12px 28px; text-decoration: none; border-radius: 6px; font-weight: bold; display: inline-block;'>Restablecer mi Contraseña</a>
                </div>
                <p style='font-size: 13px; color: #64748b;'>Si no solicitó este cambio, puede ignorar este mensaje de forma segura.</p>
                <p style='font-size: 12px; color: #94a3b8; word-break: break-all;'>Si el botón no funciona, copie y pegue este enlace en su navegador:<br><a href='{$resetUrl}'>{$resetUrl}</a></p>
            </div>
        </div>";

        $res = Mailer::send($user['email'], $user['nombre'], 'Restablecer contraseña - Control de Certificados', $html);

        if ($res['ok']) {
            $_SESSION['flash_success'] = 'Se ha enviado un correo electrónico con el enlace de restablecimiento.';
        } else {
            // Si SMTP no está configurado (ej. entorno local XAMPP), proveemos el enlace directo para facilitar las pruebas
            $_SESSION['flash_success'] = 'Solicitud procesada. Enlace de restablecimiento generado.';
            $_SESSION['dev_reset_link'] = $resetUrl;
        }

        header('Location: ' . BASE_URL . '/recuperar');
        exit;
    }

    /**
     * Vista para ingresar la nueva contraseña con el token
     */
    public function restablecer()
    {
        $token = trim($_GET['token'] ?? '');
        $email = trim($_GET['email'] ?? '');

        if (empty($token) || empty($email)) {
            $_SESSION['flash_error'] = 'Enlace de recuperación inválido o incompleto.';
            header('Location: ' . BASE_URL . '/recuperar');
            exit;
        }

        $db = Model::getDB();
        $stmt = $db->prepare("SELECT id, nombre, email FROM usuarios WHERE email = ? AND token_password = ? AND activo = 1 LIMIT 1");
        $stmt->execute([$email, $token]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            $_SESSION['flash_error'] = 'El token es inválido o ya ha sido utilizado anteriormente.';
            header('Location: ' . BASE_URL . '/recuperar');
            exit;
        }

        $this->view('auth/restablecer', [
            'token'     => $token,
            'email'     => $email,
            'usuario'   => $user,
            'pageTitle' => 'Establecer Nueva Contraseña',
            'module'    => 'auth'
        ]);
    }

    /**
     * Procesar guardado de la nueva contraseña
     */
    public function guardarNuevaPassword()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $token    = trim($_POST['token'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm  = $_POST['password_confirm'] ?? '';

        if (empty($token) || empty($email) || empty($password)) {
            $_SESSION['flash_error'] = 'Por favor complete todos los campos.';
            header('Location: ' . BASE_URL . '/restablecer?token=' . urlencode($token) . '&email=' . urlencode($email));
            exit;
        }

        if (strlen($password) < 6) {
            $_SESSION['flash_error'] = 'La contraseña debe tener al menos 6 caracteres.';
            header('Location: ' . BASE_URL . '/restablecer?token=' . urlencode($token) . '&email=' . urlencode($email));
            exit;
        }

        if ($password !== $confirm) {
            $_SESSION['flash_error'] = 'Las contraseñas no coinciden.';
            header('Location: ' . BASE_URL . '/restablecer?token=' . urlencode($token) . '&email=' . urlencode($email));
            exit;
        }

        $db = Model::getDB();
        $stmt = $db->prepare("SELECT id FROM usuarios WHERE email = ? AND token_password = ? AND activo = 1 LIMIT 1");
        $stmt->execute([$email, $token]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            $_SESSION['flash_error'] = 'El token no es válido o ha expirado.';
            header('Location: ' . BASE_URL . '/recuperar');
            exit;
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);
        $upd = $db->prepare("UPDATE usuarios SET password_hash = ?, token_password = '', reset_password = 0 WHERE id = ?");
        $upd->execute([$hash, $user['id']]);

        $_SESSION['flash_success'] = '¡Su contraseña ha sido actualizada con éxito! Ya puede iniciar sesión.';
        header('Location: ' . BASE_URL . '/login');
        exit;
    }

    /**
     * Atajo desde el panel de administración para resetear contraseña a un usuario específico
     */
    public function resetPassword()
    {
        if (!Auth::esAdmin()) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $id = isset($_GET['data']) ? (int)base64_decode($_GET['data']) : (int)($_GET['id'] ?? 0);
        $back = $_GET['back'] ?? (BASE_URL . '/admin/usuarios');

        $db = Model::getDB();
        $stmt = $db->prepare("SELECT id, nombre, apellidos, email FROM usuarios WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            $_SESSION['flash_error'] = 'Usuario no encontrado.';
            header('Location: ' . $back);
            exit;
        }

        $token = bin2hex(random_bytes(24));
        $db->prepare("UPDATE usuarios SET token_password = ?, reset_password = 1 WHERE id = ?")->execute([$token, $id]);

        $resetUrl = BASE_URL . '/restablecer?token=' . urlencode($token) . '&email=' . urlencode($user['email']);

        $html = "<p>Hola {$user['nombre']}, un administrador generó un enlace para cambiar su contraseña:</p><p><a href='{$resetUrl}'>{$resetUrl}</a></p>";
        $res = Mailer::send($user['email'], $user['nombre'], 'Restablecer contraseña de usuario', $html);

        if ($res['ok']) {
            $_SESSION['flash_success'] = "Se envió el enlace de restablecimiento al correo {$user['email']}.";
        } else {
            $_SESSION['flash_success'] = "Token generado. Enlace de restablecimiento:";
            $_SESSION['dev_reset_link'] = $resetUrl;
        }

        header('Location: ' . $back);
        exit;
    }
}
