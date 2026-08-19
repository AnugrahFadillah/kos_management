<?= $this->extend('layout/public') ?>

<?= $this->section('content') ?>

<div class="hero text-center">
    <div class="container">
        <h1 class="fw-bold"><i class="bi bi-house-heart-fill"></i> Kos Nyaman & Strategis</h1>
        <p class="lead mb-0">Cari kamar kos idaman Anda, lihat ketersediaan kamar secara langsung di bawah ini.</p>
    </div>
</div>

<div class="container my-4 my-md-5 px-3">
    <h3 class="mb-4 text-center h4 h-md-3">Kamar Tersedia Saat Ini</h3>

    <?php if (empty($kamarTersedia)) : ?>
        <div class="alert alert-warning text-center">
            Mohon maaf, saat ini semua kamar sedang terisi. Silakan hubungi kami untuk info waiting list.
        </div>
    <?php else : ?>
        <div class="row g-4">
            <?php foreach ($kamarTersedia as $kamar) : ?>
                <div class="col-md-4">
                    <div class="card room-card shadow-sm h-100">
                        <div class="room-thumb" <?php if (! empty($kamar['foto_kamar'])) : ?>style="background-image: url('<?= base_url('uploads/kamar/' . $kamar['foto_kamar']) ?>'); background-size: cover; background-position: center;"<?php endif; ?>>
                            <?php if (empty($kamar['foto_kamar'])) : ?>
                                <i class="bi bi-door-open"></i>
                            <?php endif; ?>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <h5 class="card-title mb-1">Kamar <?= esc($kamar['nomor_kamar']) ?></h5>
                                <span class="badge bg-success">Kosong</span>
                            </div>
                            <p class="text-muted mb-2"><?= esc($kamar['tipe_kamar']) ?></p>
                            <p class="fw-bold text-primary mb-2">
                                Rp <?= number_format($kamar['harga_bulanan'], 0, ',', '.') ?> / bulan
                            </p>
                            <?php if (! empty($kamar['fasilitas'])) : ?>
                                <p class="small text-muted mb-3"><?= esc($kamar['fasilitas']) ?></p>
                            <?php endif; ?>
                            <?php if (! session()->get('logged_in')) : ?>
    <!-- Kalau belum login, arahkan ke halaman login -->
    <a href="<?= base_url('login') ?>" class="btn btn-dark w-100">
        <i class="bi bi-box-arrow-in-right"></i> Login untuk Ajukan Sewa
    </a>
<?php else : ?>
    <!-- Kalau sudah login, buka modal form sewa -->
    <button type="button" class="btn btn-dark w-100" data-bs-toggle="modal" data-bs-target="#formSewaModal<?= $kamar['id'] ?>">
        <i class="bi bi-send"></i> Ajukan Sewa
    </button>
<?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Modal form pengajuan sewa khusus kamar ini -->
                <div class="modal fade" id="formSewaModal<?= $kamar['id'] ?>" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form action="<?= base_url('ajukan-sewa') ?>" method="post" enctype="multipart/form-data">
                                <?= csrf_field() ?>
                                <input type="hidden" name="kamar_id" value="<?= $kamar['id'] ?>">

                                <div class="modal-header">
                                    <h5 class="modal-title">Ajukan Sewa Kamar <?= esc($kamar['nomor_kamar']) ?></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label class="form-label">Tanggal Masuk</label>
                                        <input type="date" name="tanggal_masuk" class="form-control" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Foto Identitas (KTP/SIM) <span class="text-muted small">*wajib</span></label>
                                        <input type="file" name="foto_identitas" class="form-control" accept="image/*,application/pdf" required>
                                        <small class="text-muted">Maks 2MB. Format: JPG/PNG/PDF.</small>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-dark">Kirim Pengajuan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
