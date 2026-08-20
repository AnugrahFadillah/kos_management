<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-end mb-3">
    <a href="<?= base_url('pembayaran/create') ?>" class="btn btn-dark">
        <i class="bi bi-plus-circle"></i> Tambah Pembayaran
    </a>
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-dark">
                <tr class="text-center">
                    <th>No</th>
                    <th>Penyewa</th>
                    <th>Kamar</th>
                    <th>Tgl Masuk</th>
                    <th>Jumlah</th>
                    <th>Tgl Bayar</th>
                    <th>Bukti</th>
                    <th >Catatan</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($pembayaran)) : ?>
                    <tr><td colspan="9" class="text-center text-muted py-4">Belum ada data pembayaran.</td></tr>
                <?php else : ?>
                    <?php foreach ($pembayaran as $i => $row) : ?>
                        <tr class="text-center">
                            <td><?= $i + 1 ?></td>
                            <td><?= esc($row['nama_penyewa']) ?></td>
                            <td><?= esc($row['nomor_kamar']) ?></td>
                            <td><?= date('d-m-Y', strtotime($row['tanggal_masuk'])) ?></td>
                            <!--<td><?= esc($row['bulan']) ?> <?= esc($row['tahun']) ?></td>-->
                            <td>Rp <?= number_format($row['jumlah_bayar'], 0, ',', '.') ?></td>
                            <td><?= esc(date('d-m-Y', strtotime($row['tanggal_bayar']))) ?></td>
                            <td>
                                <?php if (! empty($row['bukti_bayar'])) : ?>
                                    <a href="<?= base_url('uploads/pembayaran/' . $row['bukti_bayar']) ?>" target="_blank" class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-file-earmark-image"></i> Lihat
                                    </a>
                                <?php else : ?>
                                    <span class="text-muted small">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?= esc($row['catatan'] ?? '-') ?></td>
                            <td>
                                <?php if ($row['status'] === 'lunas') : ?>
                                    <span class="badge bg-success">Lunas</span>
                                <?php elseif ($row['status'] === 'belum_lunas') : ?>
                                    <span class="badge bg-warning text-dark">Belum Lunas</span>
                                <?php else : ?>
                                    <!-- Menambahkan kondisi belum bayar -->
                                    <span class="badge bg-danger">Belum Bayar</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <a href="<?= base_url('pembayaran/edit/' . $row['id']) ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </a>
                                <a href="<?= base_url('pembayaran/delete/' . $row['id']) ?>" class="btn btn-sm btn-outline-danger"
                                   onclick="return confirm('Yakin ingin menghapus data pembayaran ini?')">
                                    <i class="bi bi-trash"></i> Hapus
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>
