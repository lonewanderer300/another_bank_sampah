<div class="card shadow-sm border-0">
    <div class="card-body">
        <h5 class="card-title fw-bold mb-3">Manajemen Nasabah</h5>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Nama Nasabah</th>
                        <th>Telepon</th>
                        <th>Saldo</th>
                        <th>Poin</th>
                        <th>Bergabung</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td>
                            <div><?= html_escape($user['nama']); ?></div>
                            <small class="text-muted"><?= html_escape($user['email']); ?></small>
                        </td>
                        <td><?= html_escape($user['phone'] ?? '-'); ?></td>
                        <td>Rp <?= number_format($user['saldo'], 0, ',', '.'); ?></td>
                        <td><?= number_format($user['poin'], 0, ',', '.'); ?></td>
                        <td><?= date('d M Y', strtotime($user['created_at'])); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>