<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div x-data="{ loginModal: false, registerModal: false, registerRole: '', mobileMenuOpen: false }">

    <header class="bg-white shadow-md fixed w-full z-20 md:hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex-shrink-0">
                    <a href="<?= base_url() ?>" class="text-2xl font-bold text-gray-800">Bank Sampah</a>
                </div>
                <div>
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-gray-600 hover:text-gray-800 focus:outline-none">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        <div x-show="mobileMenuOpen" @click.away="mobileMenuOpen = false" class="md:hidden bg-white border-t">
            <nav class="px-2 pt-2 pb-4 space-y-1">
                <button @click="loginModal = true; mobileMenuOpen = false" class="block w-full text-left px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-100">Masuk</button>
                <button @click="registerModal = true; registerRole = 'user'; mobileMenuOpen = false" class="block w-full text-left px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-100">Daftar Nasabah</button>
                <button @click="registerModal = true; registerRole = 'agent'; mobileMenuOpen = false" class="block w-full text-left px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-100">Daftar Agen</button>
            </nav>
        </div>
    </header>

    <section class="relative bg-gray-900 text-white" style="background: url('https://images.unsplash.com/photo-1501594907352-04cda38ebc29') no-repeat center center; background-size: cover; height: 100vh; display: flex; justify-content: center; align-items: center;">
        <div class="absolute inset-0 bg-black opacity-50"></div>
        <div class="relative z-10 text-center py-24 px-4">
            <?php if($this->session->flashdata('success')): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative max-w-md mx-auto mb-4" role="alert">
                    <?= $this->session->flashdata('success'); ?>
                </div>
            <?php endif; ?>
            <?php if($this->session->flashdata('error')): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative max-w-md mx-auto mb-4">
                    <?= $this->session->flashdata('error'); ?>
                </div>
            <?php endif; ?>

            <h1 class="text-4xl md:text-5xl font-bold mb-4">Bank Sampah</h1>
            <p class="mb-6 max-w-2xl mx-auto">Ubah sampah Anda menjadi nilai. Bergabunglah dengan komunitas pengumpul dan agen kami yang bekerja sama untuk kota yang lebih bersih.</p>

            <div class="hidden md:block">
                <div class="flex flex-col sm:flex-row justify-center gap-4">
                    <button @click="registerModal = true; registerRole = 'user'" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-semibold">Daftar sebagai Nasabah</button>
                    <button @click="registerModal = true; registerRole = 'agent'" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold">Daftar sebagai Agen</button>
                </div>
                <p class="mt-4">Sudah punya akun? <button @click="loginModal = true" class="underline font-semibold hover:text-green-300">Masuk</button></p>
            </div>

            <p class="mt-2"><a href="#impact" class="underline hover:text-green-300">Lihat Statistik</a></p>
        </div>
    </section>

    <?php $this->load->view('partials/modal_auth'); ?>

    <section id="impact" class="py-16 text-center bg-gray-50">
        <h2 class="text-3xl font-bold mb-4">Dampak Kami Sejauh Ini</h2>
        <p class="text-gray-600 mb-10 max-w-2xl mx-auto px-4">Lihat bagaimana komunitas kami membuat perbedaan.</p>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 max-w-6xl mx-auto px-4">
            
            <div class="bg-white p-6 rounded-xl shadow text-center flex flex-col justify-between">
                <div>
                    <div class="text-3xl">♻️</div>
                    <h3 class="text-xl font-bold"><?= number_format($summary['total_waste'] ?? 0, 2); ?> kg</h3>
                    <p class="text-gray-600">Total Sampah Terkumpul</p>
                </div>
                <button onclick="toggleDetail('wasteDetail')" class="mt-4 text-blue-600 underline">Lihat Detail</button>
            </div>

            <div class="bg-white p-6 rounded-xl shadow text-center flex flex-col justify-between">
                <div>
                    <div class="text-3xl">🙋‍♂️🙋‍♀️</div>
                    <h3 class="text-xl font-bold"><?= $summary['total_customers'] ?? 0; ?></h3>
                    <p class="text-gray-600">Total Nasabah Terdaftar</p>
                </div>
                <button onclick="toggleDetail('customerDetail')" class="mt-4 text-blue-600 underline">Lihat Detail</button>
            </div>

            <div class="bg-white p-6 rounded-xl shadow text-center flex flex-col justify-between">
                <div>
                    <div class="text-3xl">👥</div>
                    <h3 class="text-xl font-bold"><?= $summary['active_agents'] ?? 0; ?></h3>
                    <p class="text-gray-600">Agen Aktif</p>
                </div>
                <button onclick="toggleDetail('agentDetail')" class="mt-4 text-blue-600 underline">Lihat Detail</button>
            </div>

            <div class="bg-white p-6 rounded-xl shadow text-center flex flex-col justify-between">
                <div>
                    <div class="text-3xl">💰</div>
                    <h3 class="text-xl font-bold">Harga Sampah Terkini</h3>
                    <ul class="text-sm text-gray-600 mt-2 space-y-1">
                        <?php if (!empty($latest_prices)): foreach($latest_prices as $price): ?>
                            <li><?= htmlspecialchars($price['nama_jenis']) ?>: Rp <?= number_format($price['harga']) ?>/kg</li>
                        <?php endforeach; else: ?>
                            <li>Tidak ada data harga.</li>
                        <?php endif; ?>
                    </ul>
                </div>
                <button onclick="toggleDetail('priceDetail')" class="mt-4 text-blue-600 underline">Lihat Detail</button>
            </div>
        </div>
    </section>

    <div id="wasteDetail" class="hidden max-w-5xl mx-auto bg-white p-6 -mt-8 mb-6 rounded-xl shadow relative z-10">
        <h3 class="text-xl font-bold mb-4 text-center">Rincian Statistik Sampah</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-center">
            <div class="flex justify-center items-center h-64 md:h-80">
                <canvas id="wastePieChart"></canvas>
            </div>
            <div class="h-64 md:h-80">
                <canvas id="wasteBarChart"></canvas>
            </div>
        </div>
    </div>

    <div id="agentDetail" class="hidden max-w-5xl mx-auto bg-white p-6 -mt-8 mb-6 rounded-xl shadow relative z-10">
        <h3 class="text-xl font-bold mb-4 text-center">Distribusi Agen per Wilayah</h3>
        <div class="h-80">
            <canvas id="agentDistributionChart"></canvas>
        </div>
    </div>

    <div id="customerDetail" class="hidden max-w-5xl mx-auto bg-white p-6 -mt-8 mb-6 rounded-xl shadow relative z-10">
        <h3 class="text-xl font-bold mb-4 text-center">Distribusi Nasabah per Wilayah Agen</h3>
        <div class="h-80">
            <canvas id="customerDistributionChart"></canvas>
        </div>
    </div>

    <div id="priceDetail" class="hidden max-w-5xl mx-auto bg-white p-6 -mt-8 mb-6 rounded-xl shadow relative z-10">
        <h3 id="priceChartTitle" class="text-xl font-bold mb-4 text-center">Grafik Harga Sampah per Kategori</h3>

        <div class="flex flex-wrap justify-center gap-4 mb-4">
            <select id="filterCategory" class="border rounded p-2">
                <option value="">Semua Kategori</option>
                <?php if (!empty($waste_categories)): foreach($waste_categories as $cat): ?>
                    <option value="<?= $cat['id_kategori']; ?>"><?= htmlspecialchars($cat['nama_kategori']); ?></option>
                <?php endforeach; endif; ?>
            </select>

            <select id="filterMonth" class="border rounded p-2">
                <option value="">Semua Bulan</option>
                <?php for ($m = 1; $m <= 12; $m++): ?>
                    <option value="<?= $m; ?>"><?= date('F', mktime(0, 0, 0, $m, 10)); ?></option>
                <?php endfor; ?>
            </select>

            <select id="filterYear" class="border rounded p-2">
                <option value="">Semua Tahun</option>
                <?php for ($y = 2023; $y <= date('Y'); $y++): ?>
                    <option value="<?= $y; ?>"><?= $y; ?></option>
                <?php endfor; ?>
            </select>

            <button id="applyFilter" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Terapkan</button>
            <button id="resetFilter" class="bg-gray-300 text-gray-800 px-4 py-2 rounded hover:bg-gray-400">Reset</button>
        </div>

        <div class="h-96">
            <canvas id="wastePriceChart"></canvas>
        </div>
    </div>

    <section id="statistics" class="py-16 bg-white">
        <div class="max-w-6xl mx-auto px-4">
            <h3 class="text-2xl font-bold mb-4 text-center">Daftar Bank Sampah / Agen</h3>
            <div class="overflow-x-auto rounded-lg shadow">
                <table class="w-full border-collapse">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border-b px-4 py-3 text-left">Nama</th>
                            <th class="border-b px-4 py-3 text-left">Wilayah</th>
                            <th class="border-b px-4 py-3 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($agents)): foreach($agents as $a): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="border-b px-4 py-3"><?= htmlspecialchars($a['name']); ?></td>
                                <td class="border-b px-4 py-3"><?= htmlspecialchars($a['area']); ?></td>
                                <td class="border-b px-4 py-3 text-center">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full <?= ($a['status'] ?? '') == 'aktif' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' ?>">
                                        <?= ucfirst((($a['status'] ?? '') == 'aktif') ? 'Aktif' : 'Pending'); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; else: ?>
                            <tr><td colspan="3" class="px-4 py-3 text-center">Tidak ada agen.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <section class="py-16 bg-gray-50">
        <div class="max-w-6xl mx-auto px-4">
            <h3 class="text-2xl font-bold mb-4 text-center">Peta Persebaran Agen</h3>
            <div id="map" class="w-full h-96 rounded-lg shadow-md"></div>
        </div>
    </section>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/luxon@3"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-luxon@1"></script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/luxon@3"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-luxon@1"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    
    function safeLog(...args) {
        if (window.console) console.log(...args);
    }

    // Fungsi toggle detail (sudah benar)
    function toggleDetail(id) {
        try {
            const all = ['wasteDetail', 'agentDetail', 'customerDetail', 'priceDetail'];
            
            const target = document.getElementById(id);
            if (!target) return;
            
            const isAlreadyVisible = !target.classList.contains('hidden');

            // Sembunyikan semua
            all.forEach(i => {
                const el = document.getElementById(i);
                if (el) el.classList.add('hidden');
            });
            
            // Jika target tadi belum terlihat, tampilkan
            if (!isAlreadyVisible) {
                target.classList.remove('hidden');
            }
        } catch (e) {
            safeLog('toggleDetail error', e);
        }
    }
    window.toggleDetail = toggleDetail;

    // === Load PHP data safely ===
    const wasteStats = <?php echo json_encode($waste_stats ?? []); ?>;
    const monthlyStats = <?php echo json_encode($monthly_stats ?? []); ?>;
    
    // Data ini sudah otomatis menjadi Array JavaScript berkat 'echo'
    const agentDistributionLabels = <?php echo $agent_distribution_labels ?? json_encode([]); ?>;
    const agentDistributionData = <?php echo $agent_distribution_data ?? json_encode([]); ?>;
    const customerDistributionLabels = <?php echo $customer_distribution_labels ?? json_encode([]); ?>;
    const customerDistributionData = <?php echo $customer_distribution_data ?? json_encode([]); ?>;
    
    const initialPriceHistory = <?php echo json_encode($price_history ?? []); ?>;


    // --- Chart initializers with guards ---
    try {
        // Waste Pie
        const pieEl = document.getElementById('wastePieChart');
        if (pieEl && wasteStats.length) {
            const labels = wasteStats.map(i => i.name);
            const data = wasteStats.map(i => parseFloat(i.amount) || 0);
            new Chart(pieEl, { type: 'pie', data: { labels, datasets: [{ data, backgroundColor: ["#f87171","#60a5fa","#34d399","#fbbf24","#d971f8ff"] }] }, options: { responsive: true, maintainAspectRatio: false } });
        }

        // Monthly bar
        const barEl = document.getElementById('wasteBarChart');
        if (barEl && monthlyStats.length) {
            const labels = monthlyStats.map(i => i.month);
            const data = monthlyStats.map(i => parseFloat(i.amount) || 0);
            new Chart(barEl, { type: 'bar', data: { labels, datasets: [{ label: 'Total Sampah (kg)', data, backgroundColor: "#34d399" }] }, options: { responsive: true, maintainAspectRatio: false } });
        }

        // Agent distribution (PERBAIKAN: Menghapus JSON.parse())
        const agentEl = document.getElementById('agentDistributionChart');
        if (agentEl && (agentDistributionLabels.length || agentDistributionData.length)) {
            new Chart(agentEl, {
                type: 'bar',
                //               ▼▼ PERBAIKAN DI SINI ▼▼
                data: { labels: agentDistributionLabels, datasets: [{ label: 'Jumlah Agen', data: agentDistributionData, backgroundColor: 'rgba(59,130,246,0.5)', borderColor: 'rgba(59,130,246,1)', borderWidth: 1 }] },
                //               ▲▲ PERBAIKAN DI SINI ▲▲
                options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
            });
        }

        // Customer Distribution (PERBAIKAN: Menghapus JSON.parse())
        const customerEl = document.getElementById('customerDistributionChart');
        if (customerEl && (customerDistributionLabels.length || customerDistributionData.length)) {
            new Chart(customerEl, {
                type: 'bar',
                //               ▼▼ PERBAIKAN DI SINI ▼▼
                data: {
                    labels: customerDistributionLabels,
                    datasets: [{
                        label: 'Jumlah Nasabah',
                        data: customerDistributionData,
                //               ▲▲ PERBAIKAN DI SINI ▲▲
                        backgroundColor: 'rgba(251, 146, 60, 0.5)', // Oranye
                        borderColor: 'rgba(251, 146, 60, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    indexAxis: 'x',
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } },
                    plugins: { title: { display: true, text: 'Distribusi Nasabah Berdasarkan Wilayah Agen Pilihan' } }
                }
            });
        }

    } catch (err) {
        safeLog('chart init error', err);
    }

    // === PRICE CHART (Kode ini sudah benar, tidak diubah) ===
    const priceCanvas = document.getElementById("wastePriceChart");
    if (priceCanvas) {
        let priceChart;
        const ctx = priceCanvas.getContext("2d");

        function randomColor() {
            const hue = Math.floor(Math.random() * 360);
            return `hsl(${hue}, 70%, 50%)`;
        }

        function groupByJenis(data) {
            const grouped = {};
            (data || []).forEach(item => {
                const jenis = item.nama_jenis || "Tidak diketahui";
                if (!grouped[jenis]) grouped[jenis] = [];
                grouped[jenis].push({
                    x: item.tanggal_update,
                    y: Number(item.harga)
                });
            });
            for (const key in grouped) {
                grouped[key].sort((a, b) => new Date(a.x) - new Date(b.x));
            }
            return grouped;
        }

        function renderChart(data, titleText = "Grafik Harga per Jenis Sampah") {
            const grouped = groupByJenis(data);
            const datasets = Object.keys(grouped).map(jenis => ({
                label: jenis,
                data: grouped[jenis],
                borderColor: randomColor(),
                backgroundColor: "transparent",
                tension: 0.2
            }));

            if (priceChart) priceChart.destroy();

            document.getElementById("priceChartTitle").innerText = titleText;

            priceChart = new Chart(ctx, {
                type: "line",
                data: { datasets },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'nearest', intersect: false },
                    scales: {
                        x: {
                            type: "time",
                            time: {
                                unit: "week",
                                tooltipFormat: "dd MMM yyyy",
                                displayFormats: { week: "dd MMM" }
                            },
                            title: { display: true, text: "Tanggal" }
                        },
                        y: {
                            title: { display: true, text: "Harga (Rp)" },
                            ticks: {
                                callback: v => 'Rp ' + v.toLocaleString('id-ID')
                            }
                        }
                    },
                    plugins: {
                        legend: { position: 'top' },
                        tooltip: { mode: 'index', intersect: false }
                    }
                }
            });
        }

        renderChart(initialPriceHistory);

        document.getElementById("applyFilter").addEventListener("click", function() {
            const category = document.getElementById("filterCategory").value;
            const month = document.getElementById("filterMonth").value;
            const year = document.getElementById("filterYear").value;

            fetch("<?= base_url('home/filter_price_chart'); ?>", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `category_id=${category}&month=${month}&year=${year}`
            })
            .then(res => res.json())
            .then(data => {
                const catName = document.querySelector("#filterCategory option:checked").text;
                const monthName = month ? new Date(0, month - 1).toLocaleString('id-ID', { month: 'long' }) : "";
                const title = `Harga Sampah ${category ? "" + catName : ""} ${monthName} ${year || ""}`;
                renderChart(data, title.trim());
            })
            .catch(err => console.error(err));
        });

        document.getElementById("resetFilter").addEventListener("click", () => {
            document.getElementById("filterCategory").value = "";
            document.getElementById("filterMonth").value = "";
            document.getElementById("filterYear").value = "";
            renderChart(initialPriceHistory, "Grafik Harga per Jenis Sampah");
        });
    }


    // MAP (leaflet)
    try {
        const mapEl = document.getElementById('map');
        if (mapEl && typeof L !== 'undefined') {
            const defaultLat = -1.86667;
            const defaultLng = 114.73333;
            const map = L.map('map').setView([defaultLat, defaultLng], 10);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(map);

            <?php if (!empty($agents)): foreach($agents as $a): if (!empty($a['lat']) && !empty($a['lng'])): ?>
                try {
                    L.marker([<?= (float)$a['lat']; ?>, <?= (float)$a['lng']; ?>]).addTo(map).bindPopup("<b><?= addslashes(htmlspecialchars($a['name'])); ?></b><br><?= addslashes(htmlspecialchars($a['area'])); ?>");
                } catch(e) { safeLog('marker error', e); }
            <?php endif; endforeach; endif; ?>

            setTimeout(() => { try { map.invalidateSize(); } catch(e) { safeLog('invalidateSize error', e); } }, 400);
        } else {
            safeLog('Leaflet not available or map element missing');
        }
    } catch (e) {
        safeLog('map init error', e);
    }
});
</script>