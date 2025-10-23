<div class="card shadow-sm border-0">
    <div class="card-body">
        <h5 class="card-title fw-bold mb-3">Manajemen Agen</h5>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Nama Agen</th>
                        <th>Wilayah</th>
                        <th>Telepon</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($agents as $agent): ?>
                    <tr>
                        <td>
                            <div><?= html_escape($agent['nama']); ?></div>
                            <small class="text-muted"><?= html_escape($agent['email']); ?></small>
                        </td>
                        <td><?= html_escape($agent['wilayah']); ?></td>
                        <td><?= html_escape($agent['phone'] ?? '-'); ?></td>
                        <td>
                            <?php if($agent['status'] == 'aktif'): ?>
                                <span class="badge bg-success">Aktif</span>
                            <?php elseif($agent['status'] == 'pending'): ?>
                                <span class="badge bg-warning">Pending</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Nonaktif</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>