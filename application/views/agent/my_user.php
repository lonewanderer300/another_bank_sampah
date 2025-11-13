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
                    <?php $no = 1; foreach ($users as $user): ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= html_escape($user['nama']); ?></td>
                                <td><?= html_escape($user['phone'] ?? '-'); ?></td>
                                <td><?= html_escape($user['address'] ?? '-'); ?></td>
                                <td><?= html_escape($user['tipe_nasabah'] ?? 'Belum Daftar'); ?></td>
                                <td>
                                    <?php if ($user['iuran_status'] === 'belum bayar'): ?>
                                        <span class="badge bg-warning text-dark">
                                            Rp. <?= number_format($user['iuran_biaya'], 0, ',', '.'); ?> (Belum Bayar)
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-success">Sudah Dibayar</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($user['iuran_status'] === 'belum bayar'): ?>
                                        <a href="<?= base_url('agent/pay_iuran/' . $user['iuran_id']) ?>" 
                                           class="btn btn-sm btn-success confirm-bayar"
                                           data-nasabah="<?= html_escape($user['nama']); ?>"
                                           data-biaya="Rp. <?= number_format($user['iuran_biaya'], 0, ',', '.'); ?>">
                                            <i class="bi bi-check-circle-fill"></i> Bayar
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const confirmButtons = document.querySelectorAll('.confirm-bayar');

    confirmButtons.forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();
            const url = this.getAttribute('href');
            const nasabah = this.getAttribute('data-nasabah');
            const biaya = this.getAttribute('data-biaya');

            if (confirm(`Anda yakin ingin menandai iuran nasabah ${nasabah} sebesar ${biaya} sebagai SUDAH DIBAYAR?`)) {
                window.location.href = url;
            }
        });
    });
});
</script>