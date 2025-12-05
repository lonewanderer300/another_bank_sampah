<h4 class="fw-bold mb-4">Dashboard Admin</h4>

<div class="row mb-4">
    <div class="col-lg-6 col-md-12 mb-4">
        <div class="card shadow-sm border-0 text-white bg-success h-100">
            <div class="card-body text-center">
                <i class="bi bi-recycle fs-1"></i>
                <h1 class="display-4 fw-bold my-2"><?= number_format($summary['total_waste'] ?? 0, 2); ?> kg</h1>
                <p class="lead">Total Sampah Terkumpul</p>
            </div>
        </div>
    </div>
    <div class="col-lg-6 col-md-12 mb-4">
        <div class="card shadow-sm border-0 text-white bg-primary h-100">
            <div class="card-body text-center">
                <i class="bi bi-person-lines-fill fs-1"></i>
                <h1 class="display-4 fw-bold my-2"><?= $total_customers ?? 0; ?></h1>
                <p class="lead">Total Nasabah Terdaftar</p>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-12">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h5 class="card-title fw-bold mb-3">Rincian Statistik Sampah</h5>
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <h6 class="text-center">Persentase Jenis Sampah</h6>
                        <div class="chart-container" style="height: 300px;">
                            <canvas id="adminWastePieChart"></canvas>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <h6 class="text-center">Total Sampah per Bulan</h6>
                        <div class="chart-container" style="height: 300px;">
                            <canvas id="adminWasteBarChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="row">
    <div class="col-md-10">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body">
                <h5 class="card-title fw-bold mb-3">Persetujuan Agen Baru</h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <tbody>
                            <?php if (!empty($pending_agents)): ?>
                                <?php foreach ($pending_agents as $agent): ?>
                                    <tr>
                                        <td>
                                            <div class="fw-bold"><?= html_escape($agent['nama']); ?></div>
                                            <div class="small text-muted"><?= html_escape($agent['email']); ?></div>
                                        </td>
                                        <td class="text-end">
                                            <a href="<?= base_url('admin/approve_agent/' . $agent['id_agent']) ?>" class="btn btn-sm btn-success">
                                                <i class="bi bi-check-lg"></i> Setujui
                                            </a>
                                            <a href="<?= base_url('admin/reject_agent/' . $agent['id_agent']) ?>" class="btn btn-sm btn-danger">
                                                <i class="bi bi-x-lg"></i> Tolak
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td class="text-center text-muted py-3" colspan="2">Tidak ada permintaan agen baru.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    
    function safeLog(...args) {
        if (window.console) console.log(...args);
    }

    // === Load PHP data safely (Pastikan variabel ini tersedia dari controller Admin) ===
    const wasteStats = <?php echo json_encode($waste_stats ?? []); ?>;
    const monthlyStats = <?php echo json_encode($monthly_stats ?? []); ?>;

    // --- Chart initializers ---
    try {
        // Waste Pie Chart (Persentase Jenis Sampah)
        const pieEl = document.getElementById('adminWastePieChart');
        if (pieEl && wasteStats.length) {
            const labels = wasteStats.map(i => i.name);
            const data = wasteStats.map(i => parseFloat(i.amount) || 0);
            
            new Chart(pieEl, { 
                type: 'pie', 
                data: { 
                    labels, 
                    datasets: [{ 
                        data, 
                        // Menggunakan set warna yang sama seperti di Landing Page
                        backgroundColor: ["#f87171","#60a5fa","#34d399","#fbbf24","#d971f8ff", "#2dd4bf", "#f97316", "#a855f7"] 
                    }] 
                }, 
                options: { 
                    responsive: true, 
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'right' }
                    }
                } 
            });
        }

        // Monthly Bar Chart (Total Sampah per Bulan)
        const barEl = document.getElementById('adminWasteBarChart');
        if (barEl && monthlyStats.length) {
            const labels = monthlyStats.map(i => i.month);
            const data = monthlyStats.map(i => parseFloat(i.amount) || 0);
            
            new Chart(barEl, { 
                type: 'bar', 
                data: { 
                    labels, 
                    datasets: [{ 
                        label: 'Total Sampah (kg)', 
                        data, 
                        backgroundColor: "#34d399" // Warna hijau
                    }] 
                }, 
                options: { 
                    responsive: true, 
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Berat (kg)'
                            }
                        }
                    }
                } 
            });
        }
    } catch (err) {
        safeLog('Chart init error in Admin Dashboard', err);
    }
});
</script>