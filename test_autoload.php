<?php
require 'vendor/autoload.php';

// Verificación de carga de clases
var_dump(class_exists('App\Models\Admin\Usuario'));
var_dump(class_exists('App\Models\Admin\Rol'));
var_dump(class_exists('App\Models\Admin\Producto'));
var_dump(class_exists('App\Models\Admin\Categoria'));
var_dump(class_exists('App\Controllers\Admin\UsuariosController'));
