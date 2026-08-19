<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="card shadow-sm p-3 p-md-4" style="max-width: 650px;">
    <form action="<?= base_url('penyewa/update/' . $penyewa['id']) ?>" method="post">
        <?= csrf_field() ?>

        <div class="mb-3">
            <label class="form-label">Pilih Kamar</label>
            <select name="kamar_id" class="form-select" required>
                <?php foreach ($kamarKosong as $k) : ?>
                    <option value="<?= $k['id'] ?>" <?= old('kamar_id', $penyewa['kamar_id']) == $k['id'] ? 'selected' : '' ?>>
                        <?= esc($k['nomor_kamar']) ?> - <?= esc($k['tipe_kamar']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Nama Lengkap</label>
            <input type="text" name="nama" class="form-control" value="<?= old('nama', $penyewa['nama']) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">No. HP</label>
            <input type="text" name="no_hp" class="form-control" value="<?= old('no_hp', $penyewa['no_hp']) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Email (opsional)</label>
            <input type="email" name="email" class="form-control" value="<?= old('email', $penyewa['email']) ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Alamat Asal</label>
            <textarea name="alamat_asal" class="form-control" rows="2"><?= old('alamat_asal', $penyewa['alamat_asal']) ?></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Tanggal Masuk</label>
            <input type="date" name="tanggal_masuk" class="form-control" value="<?= old('tanggal_masuk', $penyewa['tanggal_masuk']) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="aktif" <?= $penyewa['status'] === 'aktif' ? 'selected' : '' ?>>Aktif</option>
                <option value="nonaktif" <?= $penyewa['status'] === 'nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
            </select>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-dark">Update</button>
            <a href="<?= base_url('penyewa') ?>" class="btn btn-outline-secondary">Batal</a>
        </div>
    </form>
</div>

<?= $this->endSection() ?>
