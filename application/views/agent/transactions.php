<h4 class="fw-bold mb-4">Riwayat Transaksi Agen</h4>
<div class="row">
    </div>

<div class="card shadow-sm border-0 mt-4">
    <div class="card-body">
        <h5 class="card-title fw-bold mb-3">Semua Transaksi Masuk</h5>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Tanggal</th>
                        <th>Nama Nasabah</th>
                        <th>Total Berat</th>
                        <th class="text-end">Nilai Transaksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($transactions as $trans): ?>
                    <tr>
                        <td><?= date('d M Y', strtotime($trans['tanggal_setor'])); ?></td>
                        <td><?= html_escape($trans['customer_name']); ?></td>
                        <td><?= number_format($trans['total_berat'], 2); ?> kg</td>
                        <td class="text-end fw-bold text-success">+ Rp <?= number_format($trans['transaction_value'], 0, ',', '.'); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>