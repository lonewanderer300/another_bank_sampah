<div class="container-fluid">
    <h4 class="fw-bold mb-4">Riwayat Transaksi</h4>

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-primary text-white rounded-3 d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                        <i class="bi bi-receipt fs-4"></i>
                    </div>
                    <div>
                        <h6 class="card-subtitle mb-1 text-muted">Total Transaksi</h6>
                        <h4 class="card-title fw-bold mb-0"><?= $total_transactions; ?> Kali</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-success text-white rounded-3 d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                        <i class="bi bi-cash-stack fs-4"></i>
                    </div>
                    <div>
                        <h6 class="card-subtitle mb-1 text-muted">Total Pendapatan</h6>
                        <h4 class="card-title fw-bold mb-0">Rp <?= number_format($total_income, 0, ',', '.'); ?></h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-info text-white rounded-3 d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                        <i class="bi bi-trash3-fill fs-4"></i>
                    </div>
                    <div>
                        <h6 class="card-subtitle mb-1 text-muted">Total Sampah</h6>
                        <h4 class="card-title fw-bold mb-0"><?= number_format($total_waste, 2, ',', '.'); ?> kg</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <h5 class="card-title fw-bold mb-3">Semua Transaksi</h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">Tanggal</th>
                            <th scope="col">Agen Penyetoran</th>
                            <th scope="col">Wilayah</th>
                            <th scope="col">Total Berat</th>
                            <th scope="col" class="text-end">Pendapatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($transactions)): ?>
                            <?php foreach ($transactions as $trans): ?>
                                <tr>
                                    <td><?= date('d M Y, H:i', strtotime($trans['tanggal_setor'])); ?></td>
                                    <td><?= html_escape($trans['agent_name'] ?? 'Bank Sampah Pusat'); ?></td>
                                    <td><?= html_escape($trans['agent_area'] ?? '-'); ?></td>
                                    <td><?= number_format($trans['total_berat'], 2, ',', '.'); ?> kg</td>
                                    <td class="text-end fw-bold text-success">+ Rp <?= number_format($trans['transaction_value'], 0, ',', '.'); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    Anda belum memiliki riwayat transaksi.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>