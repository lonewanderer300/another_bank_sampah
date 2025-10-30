<div class="card shadow-sm border-0">
    <div class="card-body">
        <h5 class="card-title fw-bold mb-3">Daftar Nasabah Saya</h5>
        <p class="text-muted">Nasabah yang telah memilih Anda sebagai bank sampah pilihan.</p>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Nama Nasabah</th>
                        <th>Email</th>
                        <th>Telepon</th>
                        <th>Tanggal Terdaftar di Aplikasi</th> </tr>
                </thead>
                <tbody>
                    <?php if (!empty($customers)): ?>
                        <?php foreach ($customers as $customer): ?>
                        <tr>
                            <td><?= html_escape($customer['nama']); ?></td>
                            <td><?= html_escape($customer['email']); ?></td>
                            <td><?= html_escape($customer['phone'] ?? '-'); ?></td>
                            <td><?= date('d M Y', strtotime($customer['join_date'])); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">Belum ada nasabah yang memilih Anda.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>