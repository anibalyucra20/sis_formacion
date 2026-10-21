<?php

namespace Core;

use Core\Auth;

if (!function_exists('str_starts_with')) {
    function str_starts_with($haystack, $needle)
    {
        return strpos($haystack, $needle) === 0;
    }
}

class Controller
{
    public function __construct()
    {
        Auth::start();
        
        $current = $_GET['url'] ?? '';
        $current = trim($current, '/');

        // Identificamos las rutas públicas
        $isAuthRoute = str_starts_with(static::class, 'App\\Controllers\\Auth');
        $isConsultaRoute = str_starts_with(static::class, 'App\\Controllers\\Consulta') || 
                           str_starts_with($current, 'consulta') || 
                           str_starts_with($current, 'certificado') || 
                           str_starts_with($current, 'verificar') || 
                           $current === '';
        
        $allowedNoAuth = [
            'login', 'logout', 'recuperar', 'enviarRecuperacion', 
            'restablecer', 'guardarNuevaPassword', 'resetPassword',
            'consulta', 'verificar', 'certificado'
        ]; 

        $firstSegment = explode('/', $current)[0] ?? '';
        $isRutaPublica = $isAuthRoute || $isConsultaRoute || in_array(strtolower($firstSegment), $allowedNoAuth);
        $isUserLogged = Auth::user() !== null;

        // Proteger las rutas privadas (Módulo de Administración)
        if (!$isRutaPublica) {
            if (!$isUserLogged || !Auth::validarSesion()) {
                $_SESSION['redirect_after_login'] = BASE_URL . '/' . $current;
                header('Location: ' . BASE_URL . '/login');
                exit;
            }
        }
    }

    public function modelo($modelo)
    {
        $modelo = str_replace('/', '\\', $modelo);
        $clase = "App\\Models\\" . $modelo;
        return new $clase();
    }

    /**
     * Cargar una vista y pasarle datos
     */
    public function view(string $view, array $data = [])
    {
        extract($data);
        $viewPath = __DIR__ . '/../app/views/' . $view . '.php';
        if (file_exists($viewPath)) {
            require $viewPath;
        } else {
            http_response_code(500);
            echo "<h1>Error en vista</h1><p>La vista [{$view}] no existe.</p>";
            exit;
        }
    }

    /**
     * Responder con JSON
     */
    protected function json(array $data, int $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }
}