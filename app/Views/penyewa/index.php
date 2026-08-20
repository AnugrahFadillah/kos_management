<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="card shadow-sm mt-3">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-dark">
                <tr class="text-center">
                    <th>No</th>
                    <th>Nama Lengkap</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>No. HP</th>
                    <th>Status</th>
                    <th class="text-center">Tgl Masuk</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($pengguna)) : ?>
                    <tr><td colspan="7" class="text-center text-muted py-4">Belum ada data pengguna teregistrasi.</td></tr>
                <?php else : ?>
                    <?php foreach ($pengguna as $i => $row) : ?>
                        <!-- Opsional: Sembunyikan akun admin dari tabel ini jika role-nya admin -->
                        <?php if ($row['username'] !== 'admin') : ?>
                            <tr class="text-center">
                                <td><?= $i + 1 ?></td>
                                <td><?= esc($row['nama_lengkap'] ?? '-') ?></td>
                                <td><?= esc($row['username']) ?></td>
                                <td><?= esc($row['email'] ?? '-') ?></td>
                                <td><?= esc($row['no_hp'] ?? '-') ?></td>
                                <td>
                                    <?php if ($row['status'] === 'penyewa') : ?>
                                        <span class="badge bg-success">Penyewa</span>
                                    <?php else : ?>
                                        <span class="badge bg-secondary">Calon Penyewa</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= esc($row['tanggal_masuk'] ?? '-') ?></td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>