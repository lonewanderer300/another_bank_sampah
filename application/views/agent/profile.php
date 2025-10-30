<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<style> #map { height: 350px; width: 100%; border-radius: 0.5rem; border: 1px solid #dee2e6; } </style>

<div class="container-fluid">
    <h4 class="fw-bold mb-3">Pengaturan Profil Agen</h4>

    <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= $this->session->flashdata('success'); ?> <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php elseif ($this->session->flashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $this->session->flashdata('error'); ?> <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-lg-4">
            <div class="card shadow-sm mb-4">
                 <div class="card-body text-center">
                    <div class="rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center"
                        style="width: 100px; height: 100px; background: linear-gradient(135deg,#198754,#157347); color: #fff; font-size: 32px; font-weight: bold;">
                        <?= strtoupper(substr($agent['name'], 0, 2)); ?>
                    </div>
                    <h5 class="mb-0"><?= html_escape($agent['name']); ?></h5>
                    <p class="text-muted mb-1">Agen</p>
                    <span class="badge bg-success mb-3">Agen Terverifikasi</span>
                    <hr>
                    <p class="text-start mb-1"><i class="bi bi-envelope text-success me-2"></i> <?= html_escape($agent['email']); ?></p>
                    <p class="text-start mb-1"><i class="bi bi-telephone text-success me-2"></i> <?= html_escape($agent['phone']); ?></p>
                    <p class="text-start mb-1"><i class="bi bi-geo-alt text-success me-2"></i> <?= html_escape($agent['address']); ?></p> </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Statistik Agen</h6>
                    <ul class="list-group list-group-flush">
                         <li class="list-group-item d-flex justify-content-between align-items-center">
                            Total Nasabah <span class="badge bg-success rounded-pill"><?= $stats['customers']; ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Transaksi Masuk <span class="badge bg-success rounded-pill"><?= $stats['transactions']; ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Sampah Terkumpul <span class="badge bg-success rounded-pill"><?= number_format($stats['waste_collected'], 2); ?> kg</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Update Informasi Bank Sampah</h6>
                    <form action="<?= base_url('agent/profile') ?>" method="POST">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Bank Sampah</label>
                            <input type="text" class="form-control" id="name" name="name" value="<?= html_escape($agent['name']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" value="<?= html_escape($agent['email']); ?>" readonly style="background-color: #e9ecef;">
                            <div class="form-text">Email tidak dapat diubah.</div>
                        </div>
                         <div class="mb-3">
                            <label for="phone" class="form-label">Nomor Telepon</label>
                            <input type="tel" class="form-control" id="phone" name="phone" value="<?= html_escape($agent['phone'] != 'Belum diisi' ? $agent['phone'] : ''); ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tentukan Lokasi Bank Sampah di Peta</label>
                            <div id="map"></div>
                            <div class="form-text">Klik peta atau geser penanda untuk mengatur lokasi.</div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="latitude" class="form-label">Latitude</label>
                                <input type="text" class="form-control" id="latitude" name="latitude" value="<?= html_escape($agent['latitude']); ?>" placeholder="Latitude otomatis" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="longitude" class="form-label">Longitude</label>
                                <input type="text" class="form-control" id="longitude" name="longitude" value="<?= html_escape($agent['longitude']); ?>" placeholder="Longitude otomatis" readonly>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="address" class="form-label">Alamat Lengkap</label>
                            <input type="text" class="form-control" id="address" name="address" value="<?= html_escape($agent['raw_address'] != 'Belum diisi' ? $agent['raw_address'] : ''); ?>">
                        </div>
                        <div class="mb-3">
                            <label for="wilayah" class="form-label">Wilayah Operasi</label>
                            <select class="form-select" id="wilayah" name="wilayah" required>
                                <?php foreach ($wilayah_options as $option): ?>
                                    <option value="<?= $option; ?>" <?= ($agent['wilayah'] == $option) ? 'selected' : ''; ?>><?= $option; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="bio" class="form-label">Bio / Deskripsi</label>
                            <textarea class="form-control" id="bio" name="bio" rows="3"><?= html_escape($agent['bio'] != 'Ceritakan tentang bank sampah Anda.' ? $agent['bio'] : ''); ?></textarea>
                        </div>
                        <hr>
                        <h6 class="fw-bold mb-3 mt-4">Ubah Password</h6>
                        <p class="text-muted small">Kosongkan jika tidak ingin mengubah password.</p>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password Baru</label>
                            <input type="password" class="form-control" id="password" name="password">
                        </div>
                        <button type="submit" class="btn btn-success">Simpan Perubahan</button>
                    </form>
                </div>
            </div>
        </div> </div> </div> <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const latInput = document.getElementById('latitude');
    const lonInput = document.getElementById('longitude');
    const mapElement = document.getElementById('map');

    if (mapElement && latInput && lonInput) {
        const initialLat = latInput.value || -1.86667; // Default Indonesia atau lokasi lain
        const initialLon = lonInput.value || 114.73333;
        const initialZoom = latInput.value ? 16 : 5;

        const map = L.map('map').setView([initialLat, initialLon], initialZoom);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        const marker = L.marker([initialLat, initialLon], { draggable: true }).addTo(map);

        function updateInputs(latlng) {
            latInput.value = latlng.lat.toFixed(8);
            lonInput.value = latlng.lng.toFixed(8);
        }

        if(latInput.value && lonInput.value) {
            updateInputs(marker.getLatLng());
        }

        marker.on('dragend', function(event) {
            updateInputs(marker.getLatLng());
        });

        map.on('click', function(e) {
            marker.setLatLng(e.latlng);
            updateInputs(e.latlng);
        });
        setTimeout(() => { map.invalidateSize(); }, 100);
    }
});
</script>