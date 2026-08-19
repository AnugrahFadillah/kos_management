<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Manajemen Kos') ?> - Kos Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        body { background-color: #f4f6f9; }

        /* Sidebar versi DESKTOP (>= 768px). Di layar kecil, elemen ini otomatis
           disembunyikan lewat class Bootstrap "d-none d-md-flex" di HTML-nya. */
        .sidebar {
            min-height: 100vh;
            background: #1e293b;
            width: 250px;
            flex-shrink: 0;
        }
        .sidebar a, .offcanvas-body a { color: #cbd5e1; text-decoration: none; }
        .sidebar a.active, .sidebar a:hover,
        .offcanvas-body a.active, .offcanvas-body a:hover { background: #334155; color: #fff; }
        .nav-link { border-radius: .375rem; margin-bottom: 2px; }
        .card-summary { border: none; border-radius: .75rem; }

        /* Supaya konten utama tidak "mepet" di HP */
        .main-content { min-width: 0; } /* mencegah flex item overflow di layar sempit */

        @media (max-width: 767.98px) {
            .card { border-radius: .5rem; }
        }
    </style>
</head>
<body>
<div class="d-flex">

    <!-- ============ SIDEBAR DESKTOP (tampil mulai breakpoint md / >=768px) ============ -->
    <nav class="sidebar p-3 d-none d-md-flex flex-column">
        <h5 class="text-white mb-4"><i class="bi bi-house-door-fill"></i> Kos Manager</h5>
        <?= view('layout/partials/menu') ?>
    </nav>

    <!-- ============ SIDEBAR MOBILE (offcanvas, muncul lewat tombol hamburger) ============ -->
    <div class="offcanvas offcanvas-start text-bg-dark" tabindex="-1" id="sidebarMobile">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title"><i class="bi bi-house-door-fill"></i> Kos Manager</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Tutup"></button>
        </div>
        <div class="offcanvas-body">
            <?= view('layout/partials/menu') ?>
        </div>
    </div>

    <!-- ============ KONTEN UTAMA ============ -->
    <div class="flex-grow-1 main-content">
        <nav class="navbar navbar-light bg-white shadow-sm px-3 px-md-4">
            <div class="d-flex align-items-center gap-2">
                <!-- Tombol hamburger, HANYA tampil di layar kecil (< 768px) -->
                <button class="btn btn-outline-secondary d-md-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMobile" aria-label="Buka menu">
                    <i class="bi bi-list" style="font-size: 1.2rem;"></i>
                </button>
                <span class="navbar-text mb-0">
                    <i class="bi bi-person-circle"></i> Halo, <strong><?= esc(session()->get('nama')) ?></strong>
                </span>
            </div>
        </nav>

        <div class="p-3 p-md-4">
            <h4 class="mb-4"><?= esc($title ?? '') ?></h4>

            <?php if (session()->getFlashdata('success')) : ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= esc(session()->getFlashdata('success')) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')) : ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= esc(session()->getFlashdata('error')) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('errors')) : ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        <?php foreach (session()->getFlashdata('errors') as $error) : ?>
                            <li><?= esc($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?= $this->renderSection('content') ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
