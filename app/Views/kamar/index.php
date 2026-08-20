<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-end mb-3">
    <a href="<?= base_url('kamar/create') ?>" class="btn btn-dark">
        <i class="bi bi-plus-circle"></i> Tambah Kamar
    </a>
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-dark">
                <tr class="text-center">
                    <th>No</th>
                    <th>Foto</th>
                    <th>Nomor Kamar</th>
                    <th>Tipe</th>
                    <th>Harga / Bulan</th>
                    <th>Status</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($kamar)) : ?>
                    <tr><td colspan="7" class="text-center text-muted py-4">Belum ada data kamar.</td></tr>
                <?php else : ?>
                    <?php foreach ($kamar as $i => $row) : ?>
                        <tr class="text-center">
                            <td><?= $i + 1 ?></td>
                            <td>
                                <?php if (! empty($row['foto_kamar'])) : ?>
                                    <img src="<?= base_url('uploads/kamar/' . $row['foto_kamar']) ?>" alt="Foto Kamar" style="width: 60px; height: 45px; object-fit: cover; border-radius: .375rem;">
                                <?php else : ?>
                                    <span class="text-muted small">Tidak ada foto</span>
                                <?php endif; ?>
                            </td>
                            <td><?= esc($row['nomor_kamar']) ?></td>
                            <td><?= esc($row['tipe_kamar']) ?></td>
                            <td>Rp <?= number_format($row['harga_bulanan'], 0, ',', '.') ?></td>
                            <td>
                                <?php if ($row['status'] === 'kosong') : ?>
                                    <span class="badge bg-success">Kosong</span>
                                <?php else : ?>
                                    <span class="badge bg-warning text-dark">Terisi</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <a href="<?= base_url('kamar/edit/' . $row['id']) ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </a>
                                <a href="<?= base_url('kamar/delete/' . $row['id']) ?>" class="btn btn-sm btn-outline-danger"
                                   onclick="return confirm('Yakin ingin menghapus kamar ini?')">
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
