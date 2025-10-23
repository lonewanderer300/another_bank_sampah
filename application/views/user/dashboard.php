<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold">Selamat Datang, <?= html_escape($this->session->userdata('name')); ?>!</h4>
        <p class="text-muted mb-0">Berikut adalah ringkasan aktivitas akun Anda.</p>
    </div>
    <a href="<?= base_url('user/waste_banks') ?>" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>Setor Sampah
    </a>
</div>

<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="bg-primary text-white rounded-3 d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                    <i class="bi bi-wallet2 fs-4"></i>
                </div>
                <div>
                    <h6 class="card-subtitle mb-1 text-muted">Total Saldo</h6>
                    <h4 class="card-title fw-bold mb-0">Rp <?= number_format($balance, 0, ',', '.'); ?></h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm h-100">
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
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <?php if ($iuran['status'] == 'paid'): ?>
                    <div class="bg-success text-white rounded-3 d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                        <i class="bi bi-check-circle-fill fs-4"></i>
                    </div>
                    <div>
                        <h6 class="card-subtitle mb-1 text-muted">Status Iuran Bulan Ini</h6>
                        <h4 class="card-title fw-bold mb-0 text-success">Sudah Dibayar</h4>
                    </div>
                <?php else: ?>
                    <div class="bg-warning text-white rounded-3 d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                        <i class="bi bi-exclamation-triangle-fill fs-4"></i>
                    </div>
                    <div>
                        <h6 class="card-subtitle mb-1 text-muted">Iuran Belum Dibayar</h6>
                        <h4 class="card-title fw-bold mb-0">Rp <?= number_format($iuran['amount'], 0, ',', '.'); ?></h4>
                        <small class="text-muted">Jatuh tempo: <?= $iuran['due_date']; ?></small>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-7 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <h5 class="card-title fw-bold">Komposisi Sampah</h5>
                <p class="text-muted">Grafik total berat sampah berdasarkan jenis.</p>
                <div style="height: 300px;">
                    <canvas id="wasteChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-5 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title fw-bold mb-0">Transaksi Terakhir</h5>
                    <a href="<?= base_url('user/transactions') ?>" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                </div>
                <ul class="list-group list-group-flush">
                    <?php if (!empty($transactions)): ?>
                        <?php foreach ($transactions as $trans): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0">Setoran ke <?= html_escape($trans['agent_name'] ?? 'Pusat'); ?></h6>
                                    <small class="text-muted"><?= date('d M Y', strtotime($trans['tanggal_setor'])); ?></small>
                                </div>
                                <span class="fw-bold text-success">+ Rp <?= number_format($trans['total_harga'], 0, ',', '.'); ?></span>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li class="list-group-item text-center text-muted">Belum ada transaksi.</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('wasteChart');

        // Cek jika ada data untuk ditampilkan
        const wasteData = <?= $waste_data; ?>;
        if (wasteData.length > 0) {
            new Chart(ctx, {
                type: 'pie', // Tipe grafik
                data: {
                    labels: <?= $waste_labels; ?>,
                    datasets: [{
                        label: ' kg',
                        data: wasteData,
                        backgroundColor: [ // Anda bisa menambahkan lebih banyak warna
                            'rgba(255, 99, 132, 0.8)',
                            'rgba(54, 162, 235, 0.8)',
                            'rgba(255, 206, 86, 0.8)',
                            'rgba(75, 192, 192, 0.8)',
                            'rgba(153, 102, 255, 0.8)',
                            'rgba(255, 159, 64, 0.8)'
                        ],
                        borderColor: '#fff',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed !== null) {
                                        // Menambahkan 'kg' di belakang nilai
                                        label += context.parsed.toLocaleString() + ' kg';
                                    }
                                    return label;
                                }
                            }
                        }
                    }
                }
            });
        } else {
            // Tampilkan pesan jika tidak ada data
            ctx.parentNode.innerHTML = '<div class="text-center text-muted d-flex align-items-center justify-content-center h-100">Belum ada data sampah untuk ditampilkan.</div>';
        }
    });
</script>