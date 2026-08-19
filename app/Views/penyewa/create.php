<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="card shadow-sm p-3 p-md-4" style="max-width: 650px;">
    <form action="<?= base_url('penyewa/store') ?>" method="post">
        <?= csrf_field() ?>

        <div class="mb-3">
            <label class="form-label">Pilih Kamar (hanya kamar kosong yang tampil)</label>
            <select name="kamar_id" class="form-select" required>
                <option value="">-- Pilih Kamar --</option>
                <?php foreach ($kamarKosong as $k) : ?>
                    <option value="<?= $k['id'] ?>" <?= old('kamar_id') == $k['id'] ? 'selected' : '' ?>>
                        <?= esc($k['nomor_kamar']) ?> - <?= esc($k['tipe_kamar']) ?> (Rp <?= number_format($k['harga_bulanan'], 0, ',', '.') ?>)
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if (empty($kamarKosong)) : ?>
                <small class="text-danger">Tidak ada kamar kosong tersedia saat ini.</small>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label class="form-label">Nama Lengkap</label>
            <input type="text" name="nama" class="form-control" value="<?= old('nama') ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">No. HP</label>
            <input type="text" name="no_hp" class="form-control" value="<?= old('no_hp') ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Email (opsional)</label>
            <input type="email" name="email" class="form-control" value="<?= old('email') ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Alamat Asal</label>
            <textarea name="alamat_asal" class="form-control" rows="2"><?= old('alamat_asal') ?></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Tanggal Masuk</label>
            <input type="date" name="tanggal_masuk" class="form-control" value="<?= old('tanggal_masuk', date('Y-m-d')) ?>" required>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-dark">Simpan</button>
            <a href="<?= base_url('penyewa') ?>" class="btn btn-outline-secondary">Batal</a>
        </div>
    </form>
</div>

<?= $this->endSection() ?>
