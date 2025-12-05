<div class="container mt-4">
    <h4 class="mb-3">Daftar Iuran (View Only)</h4>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>nama</th>
				<th>Bank Sampah (Agen)</th>
                <th>Tipe</th>
                <th>Jumlah Nasabah</th>
                <th>Biaya</th>
                <th>Deadline</th>
                <th>Status</th>
            </tr>
        </thead>

        <tbody>
            <?php if (!empty($nasabah_list)): ?>
                <?php foreach ($nasabah_list as $n): ?>
                    <tr>
                        <td><?= $n['nama_user'] ?></td>
						<td><?= $n['nama_agent'] ?: '-' ?></td>
                        <td><?= ucfirst($n['tipe_nasabah']) ?></td>
                        <td><?= $n['jumlah_nasabah'] ?></td>

                        <td>
                            <?= $n['biaya'] ? 'Rp ' . number_format($n['biaya'], 0, ',', '.') : '-' ?>
                        </td>

                        <td><?= $n['deadline'] ?: '-' ?></td>

                        <td>
                            <span class="badge bg-<?= ($n['status_iuran'] == 'sudah bayar') ? 'success' : 'warning' ?>">
                                <?= ucfirst($n['status_iuran'] ?: 'Belum diatur') ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="text-center">Belum ada data iuran.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
