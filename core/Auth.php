<?php

namespace Core;

class Auth
{
    public static function start()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    public static function login(array $user)
    {
        self::start();
        $_SESSION['session_id'] = $user['id_session'];
        $_SESSION['user_id']    = $user['id'];
        $_SESSION['user_name']  = $user['nombre'] . ' ' . $user['apellidos'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['token']      = $user['token'];
        $_SESSION['rol_actual'] = $user['rol_id'] ?? 1;
        
        // Regenerar ID para evitar session fixation
        session_regenerate_id(true);
    }

    public static function crearPassword(int $longitud = 8)
    {
        $parteAleatoria = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, $longitud);
        return 'Cert.' . $parteAleatoria . '!';
    }

    public static function user(): ?array
    {
        self::start();
        return (isset($_SESSION['user_id']) && $_SESSION['user_id']) ? $_SESSION : null;
    }

    public static function logout()
    {
        self::start();

        if (!empty($_SESSION['session_id'])) {
            try {
                $db = Model::getDB();
                $stmt = $db->prepare("UPDATE sesiones SET activa = 0 WHERE id = ?");
                $stmt->execute([$_SESSION['session_id']]);
            } catch (\Exception $e) {
                // Silently handle if DB error
            }
        }

        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, '/');
        }
        session_destroy();
    }

    public static function validarSesion(): bool
    {
        self::start();
        if (empty($_SESSION['session_id']) || empty($_SESSION['user_id'])) {
            return false;
        }

        $session_id = $_SESSION['session_id'];
        $user_id    = $_SESSION['user_id'];

        try {
            $db = Model::getDB();
            $stmt = $db->prepare("SELECT * FROM sesiones WHERE id = ? AND usuario_id = ? AND activa = 1 LIMIT 1");
            $stmt->execute([$session_id, $user_id]);
            $sesion = $stmt->fetch();

            if (!$sesion) {
                self::logout();
                return false;
            }

            // Verificar si expiró
            if (strtotime($sesion['fecha_expiracion']) < time()) {
                $db->prepare("UPDATE sesiones SET activa = 0 WHERE id = ?")->execute([$session_id]);
                self::logout();
                return false;
            }

            // Renovar expiración (+4 horas de actividad)
            $nuevaExp = date('Y-m-d H:i:s', strtotime('+4 hour'));
            $db->prepare("UPDATE sesiones SET fecha_expiracion = ? WHERE id = ?")->execute([$nuevaExp, $session_id]);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function esAdmin(): bool
    {
        return (isset($_SESSION['rol_actual']) && $_SESSION['rol_actual'] == 1);
    }
}
