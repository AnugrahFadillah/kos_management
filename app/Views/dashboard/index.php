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

<div class="card mt-4 p-3 shadow-sm">
    <p class="mb-0">
        <i class="bi bi-cash-coin text-success"></i>
        Jumlah transaksi pembayaran bulan ini: <strong><?= esc($pembayaran_bulan_ini) ?></strong>
    </p>
</div>

<?= $this->endSection() ?>
