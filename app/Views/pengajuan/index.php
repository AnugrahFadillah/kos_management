<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-dark">
                <tr>
                    <th>No</th>
                    <th>Nama Pemohon</th>
                    <th>Kamar Diminati</th>
                    <th>Kontak</th>
                    <th>Email</th>
                    <th>Foto Identitas</th>
                    <th>Tanggal Masuk</th>
                    <th>Status</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($pengajuan)) : ?>
                    <tr><td colspan="9" class="text-center text-muted py-4">Belum ada pengajuan sewa dari pengunjung.</td></tr>
                <?php else : ?>
                    <?php foreach ($pengajuan as $i => $row) : ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= esc($row['nama']) ?></td>
                            <td><?= esc($row['nomor_kamar']) ?> (<?= esc($row['tipe_kamar']) ?>)</td>
                            <td><?= esc($row['no_hp']) ?></td>
                            <td><?= !empty($row['email']) ? esc($row['email']) : '<span class="text-muted small">-</span>' ?></td>
                            <td>
                                <?php if (! empty($row['foto_identitas'])) : ?>
                                    <a href="<?= base_url('uploads/identitas/' . $row['foto_identitas']) ?>" target="_blank" class="d-inline-block">
                                        <img src="<?= base_url('uploads/identitas/' . $row['foto_identitas']) ?>" alt="Foto Identitas" style="width: 60px; height: 45px; object-fit: cover; border-radius: .25rem; border:1px solid #e9ecef;">
                                    </a>
                                <?php else : ?>
                                    <span class="text-muted small">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?= !empty($row['tanggal_masuk']) ? esc(date('d-m-Y', strtotime($row['tanggal_masuk']))) : '<span class="text-muted small">-</span>' ?>
                            </td>
                            <td>
                                <?php
                                $badge = [
                                    'baru'     => 'bg-primary',
                                    'diproses' => 'bg-warning text-dark',
                                    'diterima' => 'bg-success',
                                    'ditolak'  => 'bg-danger',
                                ][$row['status']] ?? 'bg-secondary';
                                ?>
                                <span class="badge <?= $badge ?>"><?= esc(ucfirst($row['status'])) ?></span>
                            </td>
                            <td class="text-center">
                                <form action="<?= base_url('pengajuan/update-status/' . $row['id']) ?>" method="post" class="d-flex gap-1 justify-content-center">
                                    <?= csrf_field() ?>
                                    <select name="status" class="form-select form-select-sm" style="width: 140px;" onchange="this.form.submit()">
                                        <option value="baru" <?= $row['status'] === 'baru' ? 'selected' : '' ?>>Baru</option>
                                        <option value="diproses" <?= $row['status'] === 'diproses' ? 'selected' : '' ?>>Diproses</option>
                                        <option value="diterima" <?= $row['status'] === 'diterima' ? 'selected' : '' ?>>Diterima</option>
                                        <option value="ditolak" <?= $row['status'] === 'ditolak' ? 'selected' : '' ?>>Ditolak</option>
                                    </select>
                                </form>
                                <a href="<?= base_url('pengajuan/delete/' . $row['id']) ?>" class="btn btn-sm btn-outline-danger mt-1"
                                   onclick="return confirm('Yakin ingin menghapus pengajuan ini?')">
                                    <i class="bi bi-trash"></i>
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
