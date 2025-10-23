<div class="card shadow-sm border-0">
    <div class="card-body">
        <h5 class="card-title fw-bold mb-3">Daftar Nasabah</h5>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Nama Nasabah</th>
                        <th>Email</th>
                        <th>Telepon</th>
                        <th>Transaksi Terakhir</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($customers as $customer): ?>
                    <tr>
                        <td><?= html_escape($customer['nama']); ?></td>
                        <td><?= html_escape($customer['email']); ?></td>
                        <td><?= html_escape($customer['phone'] ?? '-'); ?></td>
                        <td><?= date('d M Y', strtotime($customer['last_transaction'])); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>