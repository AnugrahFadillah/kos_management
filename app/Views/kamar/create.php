<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="card shadow-sm p-3 p-md-4" style="max-width: 600px;">
    <form action="<?= base_url('kamar/store') ?>" method="post" enctype="multipart/form-data">
        <?= csrf_field() ?>

        <div class="mb-3">
            <label class="form-label">Foto Kamar</label>
            <input type="file" name="foto_kamar" class="form-control" accept="image/*">
            <small class="text-muted">Opsional. Format JPG/PNG/WEBP, maksimal 2MB.</small>
        </div>
        <div class="mb-3">
            <label class="form-label">Nomor Kamar</label>
            <input type="text" name="nomor_kamar" class="form-control" value="<?= old('nomor_kamar') ?>" placeholder="Contoh: A01" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Tipe Kamar</label>
            <select name="tipe_kamar" class="form-select" required>
                <option value="Standar" <?= old('tipe_kamar') === 'Standar' ? 'selected' : '' ?>>Standar</option>
                <option value="VIP" <?= old('tipe_kamar') === 'VIP' ? 'selected' : '' ?>>VIP</option>
                <option value="Deluxe" <?= old('tipe_kamar') === 'Deluxe' ? 'selected' : '' ?>>Deluxe</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Harga per Bulan (Rp)</label>
            <input type="number" name="harga_bulanan" class="form-control" value="<?= old('harga_bulanan') ?>" min="0" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Fasilitas</label>
            <textarea name="fasilitas" class="form-control" rows="3" placeholder="Contoh: AC, Kamar mandi dalam, WiFi"><?= old('fasilitas') ?></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="kosong">Kosong</option>
                <option value="terisi">Terisi</option>
            </select>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-dark">Simpan</button>
            <a href="<?= base_url('kamar') ?>" class="btn btn-outline-secondary">Batal</a>
        </div>
    </form>
</div>

<?= $this->endSection() ?>
