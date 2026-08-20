<?= $this->extend('layout/public') ?>

<?= $this->section('content') ?>

<div class="container my-4 my-md-5">
    <div class="d-flex align-items-center mb-4">
        <h3 class="mb-0"><i class="bi bi-clock-history"></i> Riwayat Pengajuan Saya</h3>
    </div>

    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle text-nowrap">
                <thead class="table-dark">
                    <tr class="text-center">
                        <th>No</th>
                        <th>Kamar Diminati</th>
                        <th>Harga Sewa</th>
                        <th>Tanggal Masuk</th>
                        <th>Tanggal Pengajuan</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($riwayat)) : ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                Belum ada riwayat pengajuan sewa. <br>
                                <a href="<?= base_url('/') ?>" class="btn btn-outline-primary btn-sm mt-2">Cari Kamar Kos</a>
                            </td>
                        </tr>
                    <?php else : ?>
                        <?php foreach ($riwayat as $i => $row) : ?>
                            <tr class="text-center">
                                <td><?= $i + 1 ?></td>
                                <td>
                                    <strong>Kamar <?= esc($row['nomor_kamar']) ?></strong> <br>
                                    <small class="text-muted"><?= esc($row['tipe_kamar']) ?></small>
                                </td>
                                <td>Rp <?= number_format($row['harga_bulanan'], 0, ',', '.') ?>/bln</td>
                                <td><?= date('d-m-Y', strtotime($row['tanggal_masuk'])) ?></td>
                                <td><?= date('d-m-Y', strtotime($row['created_at'])) ?></td>
                                <td>
                                    <!-- Logika warna badge status -->
                                    <?php 
                                        $status = strtolower($row['status']);
                                        $badgeClass = 'bg-secondary'; // Default
                                        $icon = '';
                                        
                                        if ($status == 'baru') {
                                            $badgeClass = 'bg-primary';
                                            $icon = '<i class="bi bi-envelope-paper"></i> ';
                                        } elseif ($status == 'diproses') {
                                            $badgeClass = 'bg-warning text-dark';
                                            $icon = '<i class="bi bi-hourglass-split"></i> ';
                                        } elseif ($status == 'diterima') {
                                            $badgeClass = 'bg-success';
                                            $icon = '<i class="bi bi-check-circle"></i> ';
                                        } elseif ($status == 'ditolak') {
                                            $badgeClass = 'bg-danger';
                                            $icon = '<i class="bi bi-x-circle"></i> ';
                                        }
                                    ?>
                                    <span class="badge <?= $badgeClass ?> px-3 py-2">
                                        <?= $icon . ucfirst($row['status']) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="mt-4">
        <div class="card border-0 shadow-sm p-3">
            <h6 class="text-secondary mb-3 fw-bold"><i class="bi bi-info-circle text-primary"></i> Keterangan Status Pengajuan:</h6>
            <div class="row g-2">
                <div class="col-md-6 col-lg-3">
                    <div class="p-2 border rounded bg-light d-flex align-items-center gap-2 h-100">
                        <span class="badge bg-primary px-2 py-1">Baru</span>
                        <small class="text-muted lh-sm">Menunggu dicek oleh admin.</small>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="p-2 border rounded bg-light d-flex align-items-center gap-2 h-100">
                        <span class="badge bg-warning text-dark px-2 py-1">Diproses</span>
                        <small class="text-muted lh-sm">Sedang ditinjau admin.</small>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="p-2 border rounded bg-light d-flex align-items-center gap-2 h-100">
                        <span class="badge bg-success px-2 py-1">Diterima</span>
                        <small class="text-muted lh-sm">Pengajuan disetujui.</small>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="p-2 border rounded bg-light d-flex align-items-center gap-2 h-100">
                        <span class="badge bg-danger px-2 py-1">Ditolak</span>
                        <small class="text-muted lh-sm">Kamar penuh / tidak valid.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>