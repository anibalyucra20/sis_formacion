<?php
$currentUri = $_SERVER['REQUEST_URI'] ?? '';
?>
<nav class="navbar navbar-light navbar-expand-lg topnav-menu">
    <div class="collapse navbar-collapse" id="topnav-menu-content">
        <ul class="navbar-nav">
            <!-- Dashboard -->
            <li class="nav-item">
                <a class="nav-link <?= ($currentUri === '/admin' || $currentUri === '/admin/' ? 'active' : '') ?>" href="<?= BASE_URL ?>/admin">
                    <i class="mdi mdi-view-dashboard mr-1"></i> Dashboard
                </a>
            </li>

            <!-- Cursos / Capacitaciones -->
            <li class="nav-item">
                <a class="nav-link <?= (strpos($currentUri, '/admin/cursos') !== false ? 'active' : '') ?>" href="<?= BASE_URL ?>/admin/cursos">
                    <i class="mdi mdi-book-education mr-1"></i> Cursos
                </a>
            </li>

            <!-- Personas / Participantes -->
            <li class="nav-item">
                <a class="nav-link <?= (strpos($currentUri, '/admin/personas') !== false ? 'active' : '') ?>" href="<?= BASE_URL ?>/admin/personas">
                    <i class="mdi mdi-account-group mr-1"></i> Personas
                </a>
            </li>

            <!-- Certificados / Vinculación -->
            <li class="nav-item">
                <a class="nav-link <?= (strpos($currentUri, '/admin/certificados') !== false ? 'active' : '') ?>" href="<?= BASE_URL ?>/admin/certificados">
                    <i class="mdi mdi-certificate mr-1"></i> Certificados
                </a>
            </li>

            <!-- Reportes Generales -->
            <li class="nav-item">
                <a class="nav-link <?= (strpos($currentUri, '/admin/reportes') !== false ? 'active' : '') ?>" href="<?= BASE_URL ?>/admin/reportes">
                    <i class="mdi mdi-file-chart mr-1"></i> Reportes
                </a>
            </li>

            <!-- Gestión de Usuarios -->
            <li class="nav-item">
                <a class="nav-link <?= (strpos($currentUri, '/admin/usuarios') !== false ? 'active' : '') ?>" href="<?= BASE_URL ?>/admin/usuarios">
                    <i class="mdi mdi-shield-account mr-1"></i> Usuarios
                </a>
            </li>
        </ul>

        <!-- Acceso rápido al Portal Público -->
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link text-primary font-weight-bold" href="<?= BASE_URL ?>/consulta" target="_blank">
                    <i class="mdi mdi-open-in-new mr-1"></i> Ver Portal de Consulta DNI
                </a>
            </li>
        </ul>
    </div>
</nav>