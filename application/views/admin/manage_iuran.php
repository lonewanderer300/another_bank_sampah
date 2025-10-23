<div class="container mt-4">
    <h4 class="mb-3">Manajemen Iuran</h4>

    <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success"><?= $this->session->flashdata('success') ?></div>
    <?php endif; ?>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>ID Nasabah</th>
                <th>Tipe</th>
                <th>Jumlah Nasabah</th>
                <th>Biaya</th>
                <th>Deadline</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($nasabah_list)): ?>
                <?php foreach ($nasabah_list as $n): ?>
                    <tr>
                        <td><?= $n['id_nasabah'] ?></td>
                        <td><?= ucfirst($n['tipe_nasabah']) ?></td>
                        <td><?= $n['jumlah_nasabah'] ?></td>
                        <td>
                            <?php if ($n['biaya']): ?>
                                Rp <?= number_format($n['biaya'], 0, ',', '.') ?>
                            <?php else: ?>
                                <form method="POST" class="d-flex">
                                    <input type="hidden" name="update_iuran" value="1">
                                    <input type="hidden" name="id_nasabah" value="<?= $n['id_nasabah'] ?>">
                                    <input type="number" name="biaya" class="form-control form-control-sm me-2" placeholder="Masukkan biaya" required>
                                    <button type="submit" class="btn btn-success btn-sm">Simpan</button>
                                </form>
                            <?php endif; ?>
                        </td>
                        <td><?= $n['deadline'] ?: '-' ?></td>
                        <td>
                            <span class="badge bg-<?= ($n['status_iuran'] == 'sudah bayar') ? 'success' : 'warning' ?>">
                                <?= ucfirst($n['status_iuran'] ?: 'Belum diatur') ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($n['biaya']): ?>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="update_iuran" value="1">
                                    <input type="hidden" name="id_nasabah" value="<?= $n['id_nasabah'] ?>">
                                    <input type="hidden" name="biaya" value="<?= $n['biaya'] ?>">
                                    <button type="submit" class="btn btn-primary btn-sm">Perpanjang 30 Hari</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="text-center">Belum ada nasabah.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
