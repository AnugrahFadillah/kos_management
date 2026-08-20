<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Kos Manager') ?> - Kos Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        body { background-color: #f8fafc; }
        .hero {
            background: linear-gradient(135deg, #1e293b, #334155);
            color: #fff;
            padding: 3rem 1rem;
        }
        .hero h1 { font-size: 1.75rem; }
        .hero p { font-size: 1rem; }
        @media (min-width: 768px) {
            .hero { padding: 4.5rem 0; }
            .hero h1 { font-size: 2.5rem; }
            .hero p { font-size: 1.15rem; }
        }
        .room-card { border: none; border-radius: .75rem; transition: transform .15s ease; }
        .room-card:hover { transform: translateY(-4px); }
        .room-thumb {
            height: 160px;
            background: linear-gradient(135deg, #94a3b8, #cbd5e1);
            border-radius: .75rem .75rem 0 0;
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: 2.5rem;
        }
        footer { padding: 2rem 0; color: #94a3b8; }
    </style>
</head>
<body>

<nav class="navbar navbar-dark bg-dark">
    <!-- Pake justify-content-between biar kebagi rapi Kiri dan Kanan -->
    <div class="container d-flex justify-content-between align-items-center">
        
        <!-- BAGIAN KIRI: Logo & Lokasi Gmaps -->
        <div class="d-flex align-items-center gap-3">
            <a class="navbar-brand mb-0" href="<?= base_url('/') ?>">
                <i class="bi bi-house-door-fill"></i> Kos Manager
            </a>
        </div>

        <!-- BAGIAN KANAN: Menu User & Auth -->
        <div class="d-flex align-items-center gap-2">
            <?php if (session()->get('logged_in')) : ?>
                
                <!-- Tombol "Pengajuan Saya" (Hanya muncul jika bukan Admin) -->
                <?php if (! session()->get('is_admin')) : ?>
                    <a href="<?= base_url('riwayat-pengajuan') ?>" class="btn btn-primary btn-sm me-1 shadow-sm">
                        <i class="bi bi-clock-history"></i> <span class="d-none d-sm-inline">Pengajuan Saya</span>
                    </a>
                <?php endif; ?>

                <span class="text-light small me-2 d-none d-md-block">
                    Halo, <?= esc(session()->get('nama') ?? session()->get('username')) ?>
                </span>
                <a href="<?= base_url('logout') ?>" class="btn btn-outline-light btn-sm">
                    <i class="bi bi-box-arrow-right"></i> <span class="d-none d-sm-inline">Logout</span>
                </a>
            <?php else: ?>
                <a href="<?= base_url('login') ?>" class="btn btn-outline-light btn-sm">
                    <i class="bi bi-box-arrow-in-right"></i> Login
                </a>
            <?php endif; ?>
        </div>

    </div>
</nav>

<?php if (session()->getFlashdata('success')) : ?>
    <div class="container mt-3">
        <div id="flash-success" class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            <div class="flex-grow-1"><?= esc(session()->getFlashdata('success')) ?></div>
            <button type="button" class="btn-close ms-2" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>

    <script>
        // auto dismiss after 4 detik (4000 ms)
        setTimeout(function() {
            var el = document.getElementById('flash-success');
            if (el) {
                var bsAlert = bootstrap.Alert.getOrCreateInstance(el);
                bsAlert.close();
            }
        }, 4000);
    </script>
<?php endif; ?>

<?php if (session()->getFlashdata('errors')) : ?>
    <div class="container mt-3">
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                <?php foreach (session()->getFlashdata('errors') as $error) : ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>
<?php endif; ?>

<?= $this->renderSection('content') ?>

<footer class="text-center border-top">
    <div class="container">
        &copy; <?= date('Y') ?> Kos Manager. Semua hak cipta dilindungi.
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>