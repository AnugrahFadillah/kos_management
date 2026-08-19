<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="card shadow-sm p-3 p-md-4" style="max-width: 600px;">
    <form action="<?= base_url('pembayaran/store') ?>" method="post" enctype="multipart/form-data" id="formPembayaran">
        <?= csrf_field() ?>

        <div class="mb-3">
            <label class="form-label">Penyewa</label>
            <select name="penyewa_id" id="penyewaSelect" class="form-select" required>
                <option value="">-- Pilih Penyewa --</option>
                <?php foreach ($penyewa as $p) : ?>
                    <option value="<?= $p['id'] ?>" <?= old('penyewa_id') == $p['id'] ? 'selected' : '' ?>>
                        <?= esc($p['nama']) ?> - Kamar <?= esc($p['nomor_kamar']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Info kamar, harga & tanggal masuk tampil otomatis lewat JS saat penyewa dipilih -->
        <div class="alert alert-light border d-none" id="infoKamarBox">
            <div class="d-flex flex-column gap-1">
                <div><i class="bi bi-door-open"></i> Kamar: <strong id="infoKamarText">-</strong></div>
                <div><i class="bi bi-cash"></i> Harga sewa/bulan: <strong id="infoHargaText">-</strong></div>
                <div><i class="bi bi-calendar-check"></i> Tanggal Masuk: <strong id="infoTanggalMasuk">-</strong></div>
            </div>
            <div class="small text-muted mt-2 border-top pt-2">Jumlah bayar di bawah disarankan otomatis, silakan ubah jika perlu.</div>
        </div>

        <!-- INPUT TERSEMBUNYI (Penting! Supaya bulan & tahun tetap terkirim ke database) -->
        <input type="hidden" name="bulan" id="bulanInput">
        <input type="hidden" name="tahun" id="tahunInput">

        <div class="mb-3">
            <label class="form-label">Jumlah Bayar (Rp)</label>
            <input type="number" name="jumlah_bayar" id="jumlahBayarInput" class="form-control" value="<?= old('jumlah_bayar') ?>" min="0" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Tanggal Bayar</label>
            <input type="date" name="tanggal_bayar" class="form-control" value="<?= old('tanggal_bayar', date('Y-m-d')) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Bukti Bayar (opsional)</label>
            <input type="file" name="bukti_bayar" class="form-control" accept="image/*,.pdf">
            <small class="text-muted">Format JPG/PNG/WEBP/PDF, maksimal 2MB.</small>
        </div>

        <div class="mb-3">
            <label class="form-label">Catatan (opsional)</label>
            <textarea name="catatan" class="form-control" rows="2"><?= old('catatan') ?></textarea>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-dark">Simpan</button>
            <a href="<?= base_url('pembayaran') ?>" class="btn btn-outline-secondary">Batal</a>
        </div>
    </form>
</div>

<!-- INI BAGIAN SCRIPT YANG LU TANYAIN, DITARUH DI SINI -->
<script>
const infoPenyewa = <?= json_encode($infoPenyewa) ?>;

function isiOtomatis(penyewaId) {
    const info = infoPenyewa[penyewaId];
    const box = document.getElementById('infoKamarBox');

    if (!info) {
        box.classList.add('d-none');
        return;
    }

    // Isi teks di dalam kotak info (hanya tampilan)
    document.getElementById('infoKamarText').textContent = info.kamar;
    document.getElementById('infoHargaText').textContent = 'Rp ' + Number(info.harga).toLocaleString('id-ID');
    document.getElementById('infoTanggalMasuk').textContent = info.tanggal_masuk ? info.tanggal_masuk : '-';
    
    // Set default jumlah bayar & isi data hidden
    document.getElementById('jumlahBayarInput').value = info.harga;
    document.getElementById('bulanInput').value = info.bulan;
    document.getElementById('tahunInput').value = info.tahun;
    
    box.classList.remove('d-none');
}

document.getElementById('penyewaSelect').addEventListener('change', function () {
    isiOtomatis(this.value);
});

window.addEventListener('DOMContentLoaded', function () {
    const selected = document.getElementById('penyewaSelect').value;
    if (selected) {
        isiOtomatis(selected);
        <?php if (old('jumlah_bayar')) : ?>
            document.getElementById('jumlahBayarInput').value = <?= (float) old('jumlah_bayar') ?>;
        <?php endif; ?>
    }
});
</script>

<?= $this->endSection() ?>