<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="card shadow-sm p-3 p-md-4" style="max-width: 600px;">
    <form action="<?= base_url('pembayaran/update/' . $pembayaran['id']) ?>" method="post" enctype="multipart/form-data">
        <?= csrf_field() ?>

        <div class="mb-3">
            <label class="form-label">Penyewa</label>
            <select name="penyewa_id" id="penyewaSelect" class="form-select" required>
                <?php foreach ($penyewa as $p) : ?>
                    <option value="<?= $p['id'] ?>" <?= old('penyewa_id', $pembayaran['penyewa_id']) == $p['id'] ? 'selected' : '' ?>>
                        <?= esc($p['nama']) ?> - Kamar <?= esc($p['nomor_kamar']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <small class="text-muted">Ganti penyewa di sini hanya jika data pembayaran ini memang salah penyewa. Mengganti pilihan akan menyarankan ulang bulan & harga.</small>
        </div>

        <div class="alert alert-light border d-none" id="infoKamarBox">
            <i class="bi bi-info-circle"></i>
            Kamar: <strong id="infoKamarText">-</strong> &nbsp;|&nbsp;
            Harga sewa/bulan: <strong id="infoHargaText">-</strong>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Bulan</label>
                <select name="bulan" id="bulanSelect" class="form-select" required>
                    <?php
                    $bulanList = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
                    foreach ($bulanList as $b) :
                    ?>
                        <option value="<?= $b ?>" <?= old('bulan', $pembayaran['bulan']) === $b ? 'selected' : '' ?>><?= $b ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Tahun</label>
                <input type="number" name="tahun" id="tahunInput" class="form-control" value="<?= old('tahun', $pembayaran['tahun']) ?>" required>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Jumlah Bayar (Rp)</label>
            <input type="number" name="jumlah_bayar" id="jumlahBayarInput" class="form-control" value="<?= old('jumlah_bayar', $pembayaran['jumlah_bayar']) ?>" min="0" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Tanggal Bayar</label>
            <input type="date" name="tanggal_bayar" class="form-control" value="<?= old('tanggal_bayar', $pembayaran['tanggal_bayar']) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Bukti Bayar</label>
            <?php if (! empty($pembayaran['bukti_bayar'])) : ?>
                <div class="mb-2">
                    <a href="<?= base_url('uploads/pembayaran/' . $pembayaran['bukti_bayar']) ?>" target="_blank" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-eye"></i> Lihat Bukti Saat Ini
                    </a>
                </div>
            <?php endif; ?>
            <input type="file" name="bukti_bayar" class="form-control" accept="image/*,.pdf">
            <small class="text-muted">Kosongkan jika tidak ingin mengganti bukti bayar.</small>
        </div>

        <div class="mb-3">
            <label class="form-label">Catatan (opsional)</label>
            <textarea name="catatan" class="form-control" rows="2"><?= old('catatan', $pembayaran['catatan']) ?></textarea>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-dark">Update</button>
            <a href="<?= base_url('pembayaran') ?>" class="btn btn-outline-secondary">Batal</a>
        </div>
    </form>
</div>

<script>
// Data ini dikirim dari PembayaranController::buildInfoPenyewaUntukJs()
const infoPenyewa = <?= json_encode($infoPenyewa) ?>;

// Sengaja HANYA terpasang di event 'change', BUKAN dijalankan otomatis saat halaman dimuat.
// Alasannya: saat halaman edit pertama kali dibuka, kita ingin tetap menampilkan
// data pembayaran yang SUDAH ADA (bulan/tahun/jumlah aslinya), bukan menimpanya
// dengan saran otomatis. Saran otomatis baru dipakai kalau admin sengaja ganti penyewa.
document.getElementById('penyewaSelect').addEventListener('change', function () {
    const info = infoPenyewa[this.value];
    const box = document.getElementById('infoKamarBox');

    if (!info) {
        box.classList.add('d-none');
        return;
    }

    document.getElementById('infoKamarText').textContent = info.kamar;
    document.getElementById('infoHargaText').textContent = 'Rp ' + Number(info.harga).toLocaleString('id-ID');
    document.getElementById('jumlahBayarInput').value = info.harga;
    document.getElementById('bulanSelect').value = info.bulan;
    document.getElementById('tahunInput').value = info.tahun;
    box.classList.remove('d-none');
});
</script>

<?= $this->endSection() ?>
