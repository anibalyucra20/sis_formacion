<?php

use Core\Auth;

Auth::start();
$logueado = Auth::user() !== null;
$userName = $_SESSION['user_name'] ?? 'Administrador';
$userId = $_SESSION['user_id'] ?? 1;
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="utf-8" />
  <title><?= $pageTitle ?? 'Panel de Control - Certificados' ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  
  <link href="<?= BASE_URL ?>/assets/css/bootstrap.min.css" rel="stylesheet" />
  <link href="<?= BASE_URL ?>/assets/css/sweetalert2.min.css" rel="stylesheet" />
  <link href="<?= BASE_URL ?>/assets/css/icons.min.css" rel="stylesheet" />
  <link href="<?= BASE_URL ?>/assets/css/theme.min.css" rel="stylesheet" type="text/css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  
  <!-- DataTables -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap4.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css">

  <link rel="icon" type="image/x-icon" href="<?= BASE_URL ?>/img/favicon.ico">

  <style>
    body {
      font-family: 'Plus Jakarta Sans', sans-serif !important;
      background-color: #f4f6f9;
    }
    .navbar-header {
      background: #ffffff;
      border-bottom: 1px solid #e9ecef;
    }
    .topnav {
      background: #1e293b;
      box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }
    .topnav .navbar-nav .nav-link {
      color: #cbd5e1 !important;
      font-weight: 500;
      padding: 1rem 1.15rem;
      font-size: 14px;
    }
    .topnav .navbar-nav .nav-link:hover,
    .topnav .navbar-nav .nav-link.active {
      color: #ffffff !important;
      background: rgba(255,255,255,0.08);
      border-radius: 6px;
    }
    .brand-title {
      font-weight: 800;
      font-size: 17px;
      color: #1e3a8a;
      letter-spacing: -0.02em;
    }
    .card {
      border-radius: 12px;
      border: 1px solid #e2e8f0;
      box-shadow: 0 2px 8px rgba(0,0,0,0.02);
    }
    .table thead th {
      border-top: none;
      background: #f8fafc;
      color: #475569;
      font-weight: 700;
      font-size: 13px;
    }
  </style>
</head>

<body data-sidebar="light">
  <div id="layout-wrapper">
    <!-- Header superior -->
    <header id="page-topbar">
      <div class="navbar-header">
        <div class="navbar-brand-box d-flex align-items-center">
          <a href="<?= BASE_URL ?>/admin" class="logo d-flex align-items-center text-decoration-none" style="gap: 10px;">
            <div style="width: 36px; height: 36px; border-radius: 8px; background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%); display: flex; align-items: center; justify-content: center; color: #ffffff; font-size: 18px;">
              <i class="fa-solid fa-award"></i>
            </div>
            <div>
              <span class="brand-title d-block" style="line-height: 1.1;">CERTIFICADOS</span>
              <small class="text-muted font-size-11">Control y Emisión</small>
            </div>
          </a>
          <button type="button" class="btn btn-sm mr-2 font-size-16 d-lg-none header-item waves-effect waves-light"
            data-toggle="collapse" data-target="#topnav-menu-content">
            <i class="fa fa-fw fa-bars"></i>
          </button>
        </div>

        <div class="d-flex align-items-center">
          <!-- Enlace al portal público -->
          <a href="<?= BASE_URL ?>/consulta" target="_blank" class="btn btn-outline-primary btn-sm font-weight-bold mr-3 d-none d-sm-inline-flex align-items-center" style="border-radius: 8px;">
            <i class="fa-solid fa-arrow-up-right-from-square mr-1"></i> Ver Portal Consulta
          </a>

          <!-- Perfil de usuario -->
          <div class="dropdown d-inline-block">
            <button type="button" class="btn header-item waves-effect waves-light" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <div class="rounded-circle d-inline-flex align-items-center justify-content-center bg-primary text-white font-weight-bold" style="width: 36px; height: 36px; font-size: 14px;">
                <?= strtoupper(substr($userName, 0, 1)) ?>
              </div>
              <span class="d-none d-sm-inline-block ml-2 font-weight-bold text-dark"><?= htmlspecialchars($userName) ?></span>
              <i class="mdi mdi-chevron-down d-none d-sm-inline-block text-muted"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-right shadow-sm" style="border-radius: 10px; border: 1px solid #e2e8f0; min-width: 200px;">
              <div class="dropdown-header font-weight-bold text-muted font-size-11 text-uppercase">Administrador</div>
              <a class="dropdown-item d-flex align-items-center" href="<?= BASE_URL ?>/admin/usuarios/perfil">
                <i class="mdi mdi-account-cog mr-2 font-size-16 text-primary"></i> Mi Perfil
              </a>
              <a class="dropdown-item d-flex align-items-center" href="<?= BASE_URL ?>/admin/usuarios">
                <i class="mdi mdi-account-multiple mr-2 font-size-16 text-info"></i> Usuarios del Sistema
              </a>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item d-flex align-items-center text-danger font-weight-bold" href="<?= BASE_URL ?>/logout">
                <i class="mdi mdi-logout mr-2 font-size-16"></i> Cerrar Sesión
              </a>
            </div>
          </div>
        </div>
      </div>
    </header>

    <!-- Barra de menú horizontal -->
    <div class="topnav">
      <div class="container-fluid">
        <?php include __DIR__ . "/menus/admin.php"; ?>
      </div>
    </div>

    <!-- Contenido principal -->
    <div class="main-content">
      <div class="page-content">
        <div class="container-fluid">
          <!-- Alertas Flash -->
          <?php if (!empty($_SESSION['flash_success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius: 8px;">
              <i class="fa-solid fa-circle-check mr-2"></i> <?= $_SESSION['flash_success'] ?>
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <?php unset($_SESSION['flash_success']); ?>
          <?php endif; ?>

          <?php if (!empty($_SESSION['flash_error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius: 8px;">
              <i class="fa-solid fa-triangle-exclamation mr-2"></i> <?= $_SESSION['flash_error'] ?>
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <?php unset($_SESSION['flash_error']); ?>
          <?php endif; ?>