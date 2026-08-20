<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="row g-3">
    <div class="col-md-3">
        <div class="card card-summary shadow-sm p-3 bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="mb-0">Total Kamar</p>
                    <h3 class="mb-0"><?= esc($total_kamar) ?></h3>
                </div>
                <i class="bi bi-door-closed" style="font-size: 2rem;"></i>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card card-summary shadow-sm p-3 bg-success text-white">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="mb-0">Kamar Kosong</p>
                    <h3 class="mb-0"><?= esc($kamar_kosong) ?></h3>
                </div>
                <i class="bi bi-door-open" style="font-size: 2rem;"></i>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card card-summary shadow-sm p-3 bg-warning text-dark">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="mb-0">Kamar Terisi</p>
                    <h3 class="mb-0"><?= esc($kamar_terisi) ?></h3>
                </div>
                <i class="bi bi-door-closed-fill" style="font-size: 2rem;"></i>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card card-summary shadow-sm p-3 bg-info text-white">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="mb-0">Penyewa Aktif</p>
                    <h3 class="mb-0"><?= esc($total_penyewa) ?></h3>
                </div>
                <i class="bi bi-people-fill" style="font-size: 2rem;"></i>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card card-summary shadow-sm p-3 bg-danger text-white">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="mb-0">Pengajuan Baru</p>
                    <h3 class="mb-0"><?= esc($pengajuan_baru) ?></h3>
                </div>
                <i class="bi bi-inbox-fill" style="font-size: 2rem;"></i>
            </div>
        </div>
    </div>
</div>
<!-- Hapus <div class="card mt-4 p-3 shadow-sm"> bawaan lu, ganti sama ini -->

<hr class="my-4 text-muted">
<h5 class="mb-3 text-secondary fw-bold">Ringkasan Keuangan Bulan Ini</h5>

<div class="row g-3">
    <!-- Card Pemasukan Lunas -->
    <div class="col-md-6">
        <div class="card card-summary shadow-sm p-4 bg-success text-white" style="border-radius: 12px;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="mb-1" style="font-size: 1.1rem;">Pemasukan Lunas</p>
                    <h2 class="mb-0 fw-bold">Rp <?= number_format($total_pemasukan, 0, ',', '.') ?></h2>
                </div>
                <i class="bi bi-wallet2" style="font-size: 3.5rem; opacity: 0.8;"></i>
            </div>
        </div>
    </div>

    <!-- Card Menunggu Pembayaran / Nunggak -->
    <div class="col-md-6">
        <div class="card card-summary shadow-sm p-4 bg-danger text-white" style="border-radius: 12px;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="mb-1" style="font-size: 1.1rem;">Menunggu Pembayaran</p>
                    <h2 class="mb-0 fw-bold"><?= esc($tagihan_nunggak) ?> Tagihan</h2>
                </div>
                <i class="bi bi-exclamation-octagon" style="font-size: 3.5rem; opacity: 0.8;"></i>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
