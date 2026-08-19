<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="card shadow-sm p-3 p-md-4" style="max-width: 600px;">
    <form action="<?= base_url('kamar/update/' . $kamar['id']) ?>" method="post" enctype="multipart/form-data">
        <?= csrf_field() ?>

        <div class="mb-3">
            <label class="form-label">Foto Kamar</label>
            <?php if (! empty($kamar['foto_kamar'])) : ?>
                <div class="mb-2">
                    <img src="<?= base_url('uploads/kamar/' . $kamar['foto_kamar']) ?>" alt="Foto Kamar" style="max-width: 200px; border-radius: .5rem;">
                </div>
            <?php endif; ?>
            <input type="file" name="foto_kamar" class="form-control" accept="image/*">
            <small class="text-muted">Kosongkan jika tidak ingin mengganti foto. Maksimal 2MB.</small>
        </div>
        <div class="mb-3">
            <label class="form-label">Nomor Kamar</label>
            <input type="text" name="nomor_kamar" class="form-control" value="<?= old('nomor_kamar', $kamar['nomor_kamar']) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Tipe Kamar</label>
            <select name="tipe_kamar" class="form-select" required>
                <?php foreach (['Standar', 'VIP', 'Deluxe'] as $tipe) : ?>
                    <option value="<?= $tipe ?>" <?= old('tipe_kamar', $kamar['tipe_kamar']) === $tipe ? 'selected' : '' ?>><?= $tipe ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Harga per Bulan (Rp)</label>
            <input type="number" name="harga_bulanan" class="form-control" value="<?= old('harga_bulanan', $kamar['harga_bulanan']) ?>" min="0" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Fasilitas</label>
            <textarea name="fasilitas" class="form-control" rows="3"><?= old('fasilitas', $kamar['fasilitas']) ?></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="kosong" <?= $kamar['status'] === 'kosong' ? 'selected' : '' ?>>Kosong</option>
                <option value="terisi" <?= $kamar['status'] === 'terisi' ? 'selected' : '' ?>>Terisi</option>
            </select>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-dark">Update</button>
            <a href="<?= base_url('kamar') ?>" class="btn btn-outline-secondary">Batal</a>
        </div>
    </form>
</div>

<?= $this->endSection() ?>
