<ul class="nav nav-pills flex-column gap-1">
    <li class="nav-item">
        <a class="nav-link <?= uri_string() === 'dashboard' ? 'active' : '' ?>" href="<?= base_url('dashboard') ?>">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= strpos(uri_string(), 'kamar') === 0 ? 'active' : '' ?>" href="<?= base_url('kamar') ?>">
            <i class="bi bi-door-closed"></i> Data Kamar
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= strpos(uri_string(), 'penyewa') === 0 ? 'active' : '' ?>" href="<?= base_url('penyewa') ?>">
            <i class="bi bi-people"></i> Data Penyewa
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= strpos(uri_string(), 'pembayaran') === 0 ? 'active' : '' ?>" href="<?= base_url('pembayaran') ?>">
            <i class="bi bi-cash-coin"></i> Data Pembayaran
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= strpos(uri_string(), 'pengajuan') === 0 ? 'active' : '' ?>" href="<?= base_url('pengajuan') ?>">
            <i class="bi bi-inbox"></i> Pengajuan Sewa
            <?php $jumlahBaru = (new \App\Models\PengajuanModel())->countBaru(); ?>
            <?php if ($jumlahBaru > 0) : ?>
                <span class="badge bg-danger ms-1"><?= $jumlahBaru ?></span>
            <?php endif; ?>
        </a>
    </li>
</ul>
<hr class="text-secondary">
<a href="<?= base_url('logout') ?>" class="nav-link text-danger">
    <i class="bi bi-box-arrow-right"></i> Logout
</a>
