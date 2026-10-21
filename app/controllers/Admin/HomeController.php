<?php

namespace App\Controllers\Admin;

use Core\Controller;
use App\Models\Admin\Curso;
use App\Models\Admin\Persona;
use App\Models\Admin\Certificado;
use App\Models\Admin\Usuario;

class HomeController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $cursoModel = new Curso();
        $personaModel = new Persona();
        $certModel = new Certificado();
        $usuarioModel = new Usuario();

        $totalCursos       = $cursoModel->countTotal();
        $totalPersonas     = $personaModel->countTotal();
        $totalCertificados = $certModel->countTotal();
        $totalHoras        = $certModel->sumHorasTotal();
        $topCursos         = $certModel->getTopCursos(5);

        // Últimos certificados emitidos
        $ultimosResult = $certModel->getPaginated('', null, '', 5, 0, 0, 'desc');
        $ultimosCertificados = $ultimosResult['data'] ?? [];

        $this->view('admin/index', [
            'totalCursos'         => $totalCursos,
            'totalPersonas'       => $totalPersonas,
            'totalCertificados'   => $totalCertificados,
            'totalHoras'          => $totalHoras,
            'topCursos'           => $topCursos,
            'ultimosCertificados' => $ultimosCertificados,
            'isAdmin'             => true,
            'pageTitle'           => 'Dashboard - Sistema de Certificados',
            'module'              => 'admin'
        ]);
    }
}
