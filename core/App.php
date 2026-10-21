<?php

namespace Core;

class App
{
    protected $module     = 'Admin';
    protected $controller = 'HomeController';
    protected $method     = 'index';
    protected $params     = [];

    public function __construct()
    {
        date_default_timezone_set('America/Lima');

        $segments = $this->parseUrl();

        // -------------------------------------------------------------
        // Atajos de Autenticación
        // -------------------------------------------------------------
        $authActions = [
            'logout'               => 'salir',
            'recuperar'            => 'recuperar',
            'enviarrecuperacion'   => 'enviarRecuperacion',
            'restablecer'          => 'restablecer',
            'guardarnuevapassword' => 'guardarNuevaPassword',
            'resetpassword'        => 'resetPassword'
        ];

        if (!empty($segments[0]) && isset($authActions[strtolower($segments[0])])) {
            $method = $authActions[strtolower($segments[0])];
            require_once __DIR__ . "/../app/controllers/Auth/LoginController.php";
            $class = "App\\Controllers\\Auth\\LoginController";
            $controller = new $class();
            if (method_exists($controller, $method)) {
                array_shift($segments);
                return call_user_func_array([$controller, $method], $segments);
            }
        }

        // -------------------------------------------------------------
        // Atajos de Consulta Pública (DNI y Verificación de Certificados)
        // -------------------------------------------------------------
        if (empty($segments[0]) || strtolower($segments[0]) === 'consulta' || strtolower($segments[0]) === 'verificar' || strtolower($segments[0]) === 'certificado') {
            require_once __DIR__ . "/../app/controllers/ConsultaController.php";
            $controller = new \App\Controllers\ConsultaController();

            if (empty($segments[0])) {
                return $controller->index();
            }

            $first = strtolower(array_shift($segments));
            if ($first === 'certificado') {
                $codigo = array_shift($segments);
                return $controller->certificado($codigo);
            } elseif ($first === 'verificar') {
                $codigo = array_shift($segments);
                return $controller->verificar($codigo);
            } else {
                // 'consulta'
                $subMethod = !empty($segments[0]) ? array_shift($segments) : 'index';
                if (method_exists($controller, $subMethod)) {
                    return call_user_func_array([$controller, $subMethod], $segments);
                }
                return $controller->index();
            }
        }

        // -------------------------------------------------------------
        // Ruteo estándar MVC
        // -------------------------------------------------------------
        \Core\Auth::start();
        $_SESSION['modulo_vista'] = 'admin';

        /* 1. Mapeo de Login */
        if (strtolower($segments[0]) === 'login') {
            $this->module = 'Auth';
            $this->controller = 'LoginController';
            require_once __DIR__ . "/../app/controllers/Auth/LoginController.php";
            array_shift($segments);
            $class = "App\\Controllers\\Auth\\LoginController";
            $this->controller = new $class();
            $this->method = !empty($segments[0]) && method_exists($this->controller, $segments[0]) ? array_shift($segments) : 'index';
            $this->params = $segments;
            return call_user_func_array([$this->controller, $this->method], $this->params);
        }

        /* 2. Módulo Admin */
        if (strtolower($segments[0]) === 'admin') {
            $this->module = 'Admin';
            array_shift($segments);
        }

        /* 3. Controlador */
        if (!empty($segments[0])) {
            $ctrlName = ucfirst($segments[0]) . 'Controller';
            $ctrlFile = __DIR__ . "/../app/controllers/{$this->module}/{$ctrlName}.php";
            if (file_exists($ctrlFile)) {
                $this->controller = $ctrlName;
                require_once $ctrlFile;
                array_shift($segments);
            } else {
                return $this->render404("Controlador [{$ctrlName}] no encontrado en {$this->module}");
            }
        } else {
            // Controlador por defecto dentro de Admin
            $ctrlFile = __DIR__ . "/../app/controllers/{$this->module}/HomeController.php";
            if (file_exists($ctrlFile)) {
                $this->controller = 'HomeController';
                require_once $ctrlFile;
            } else {
                return $this->render404("Controlador por defecto no encontrado");
            }
        }

        /* 4. Instancia */
        $class = "App\\Controllers\\{$this->module}\\{$this->controller}";
        if (!class_exists($class)) {
            return $this->render404("Clase {$class} no existe");
        }
        $this->controller = new $class();

        /* 5. Método */
        if (!empty($segments[0]) && method_exists($this->controller, $segments[0])) {
            $this->method = array_shift($segments);
        } elseif (!empty($segments[0])) {
            return $this->render404("Método [{$segments[0]}] no encontrado en {$class}");
        }

        /* 6. Parámetros */
        $this->params = $segments;

        /* 7. Ejecutar */
        return call_user_func_array([$this->controller, $this->method], $this->params);
    }

    private function parseUrl(): array
    {
        if (!empty($_GET['url'])) {
            $route = rtrim($_GET['url'], '/');
        } else {
            $base  = '/';
            $uri   = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';
            $route = trim(substr($uri, strlen($base)), '/');
        }
        $route = filter_var($route, FILTER_SANITIZE_URL);
        return empty($route) ? [] : explode('/', $route);
    }

    private function render404(string $msg)
    {
        http_response_code(404);
        echo "<!DOCTYPE html><html lang='es'><head><meta charset='utf-8'><title>404 - No Encontrado</title>";
        echo "<link href='" . BASE_URL . "/assets/css/bootstrap.min.css' rel='stylesheet'/></head>";
        echo "<body class='bg-light d-flex align-items-center min-vh-100'>";
        echo "<div class='container text-center py-5'>";
        echo "<h1 class='display-1 font-weight-bold text-primary'>404</h1>";
        echo "<h3 class='text-dark mb-3'>Página no encontrada</h3>";
        echo "<p class='text-muted mb-4'>{$msg}</p>";
        echo "<a href='" . BASE_URL . "/consulta' class='btn btn-primary px-4 mr-2'>Portal de Certificados</a>";
        echo "<a href='" . BASE_URL . "/admin' class='btn btn-outline-secondary px-4'>Panel de Control</a>";
        echo "</div></body></html>";
        exit;
    }
}
