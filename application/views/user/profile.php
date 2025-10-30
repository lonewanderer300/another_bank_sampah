<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
     integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
     crossorigin=""/>

<style>
    /* Style untuk container peta */
    #map {
        height: 350px;
        width: 100%;
        border-radius: 0.5rem;
        border: 1px solid #dee2e6;
    }
</style>

<div class="container-fluid">
    <h4 class="fw-bold mb-3">Profile Settings</h4>

    <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= $this->session->flashdata('success'); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php elseif ($this->session->flashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $this->session->flashdata('error'); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-lg-4">
            <div class="card shadow-sm mb-4">
                 <div class="card-body text-center">
                    <div class="rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center"
                        style="width: 100px; height: 100px; background: linear-gradient(135deg,#0f9,#09f); color: #fff; font-size: 32px; font-weight: bold;">
                        <?= strtoupper(substr($user['name'], 0, 2)); ?>
                    </div>
                    <h5 class="mb-0"><?= html_escape($user['name']); ?></h5>
                    <p class="text-muted mb-1"><?= html_escape($user['role']); ?></p>
                    <span class="badge bg-success mb-3">Verified User</span>
                    <hr>
                    <p class="text-start mb-1"><i class="bi bi-envelope text-primary me-2"></i> <?= html_escape($user['email']); ?></p>
                    <p class="text-start mb-1"><i class="bi bi-telephone text-primary me-2"></i> <?= html_escape($user['phone']); ?></p>
                    <p class="text-start mb-1"><i class="bi bi-geo-alt text-primary me-2"></i> <?= html_escape($user['address']); ?></p>
                </div>
            </div>

            <div class="card shadow-sm mb-4"> <div class="card-body">
                    <h6 class="fw-bold mb-3">Statistik Akun</h6>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Total Setoran <span class="badge bg-primary rounded-pill"><?= $stats['collections']; ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Total Poin <span class="badge bg-primary rounded-pill"><?= $stats['points']; ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Sampah Terkumpul <span class="badge bg-primary rounded-pill"><?= $stats['waste_collected']; ?> kg</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Bergabung Sejak <span class="badge bg-secondary rounded-pill"><?= $user['member_since']; ?></span>
                        </li>
                    </ul>
                </div>
            </div>

             <div class="card shadow-sm mb-4"> <div class="card-body">
                    <h6 class="fw-bold mb-3">Informasi Nasabah</h6>

                    <?php if ($nasabah): // Jika data nasabah ADA ?>
                        <div class="alert alert-info mb-0">
                            <strong>Tipe Nasabah:</strong> <?= ucfirst(html_escape($nasabah['tipe_nasabah'])); ?><br>
                            <?php if (isset($nasabah['jumlah_nasabah']) && $nasabah['jumlah_nasabah'] > 0 && $nasabah['tipe_nasabah'] == 'Kelompok'): ?>
                                <strong>Jumlah Anggota:</strong> <?= html_escape($nasabah['jumlah_nasabah']); ?>
                            <?php endif; ?>
                        </div>
                         <?php else: // Jika data nasabah BELUM ADA, tampilkan form ?>
                        <p class="text-muted small">Anda belum terdaftar sebagai nasabah perorangan atau kelompok.</p>
                        <form action="<?= base_url('user/profile') ?>" method="POST">
                            <input type="hidden" name="add_nasabah" value="1">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="tipe_nasabah" class="form-label">Tipe Nasabah</label>
                                    <select class="form-select" id="tipe_nasabah" name="tipe_nasabah" required>
                                        <option value="">-- Pilih Tipe --</option>
                                        <option value="Perorangan">Perorangan</option>
                                        <option value="Kelompok">Kelompok</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3" id="jumlah_nasabah_group" style="display: none;"> <label for="jumlah_nasabah" class="form-label">Jumlah Anggota</label>
                                    <input type="number" class="form-control" id="jumlah_nasabah" name="jumlah_nasabah" min="2" placeholder="Minimal 2">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success">Daftarkan Tipe</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>

        </div> <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Update Informasi Pribadi</h6>
                    <form action="<?= base_url('user/profile') ?>" method="POST">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-control" id="name" name="name" value="<?= html_escape($user['name']); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Alamat Email</label>
                                <input type="email" class="form-control" id="email" value="<?= html_escape($user['email']); ?>" readonly style="background-color: #e9ecef;">
                                <div class="form-text">Email tidak dapat diubah.</div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Nomor Telepon</label>
                            <input type="tel" class="form-control" id="phone" name="phone" value="<?= html_escape($user['phone'] != 'Belum diisi' ? $user['phone'] : ''); ?>" placeholder="Contoh: 081234567890">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tentukan Lokasi Anda di Peta</label>
                            <div id="map"></div>
                            <div class="form-text">Klik pada peta atau geser penanda untuk mengatur lokasi.</div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="latitude" class="form-label">Latitude</label>
                                <input type="text" class="form-control" id="latitude" name="latitude" value="<?= html_escape($user['latitude']); ?>" placeholder="Latitude otomatis" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="longitude" class="form-label">Longitude</label>
                                <input type="text" class="form-control" id="longitude" name="longitude" value="<?= html_escape($user['longitude']); ?>" placeholder="Longitude otomatis" readonly>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="address" class="form-label">Detail Alamat (Opsional)</label>
                            <input type="text" class="form-control" id="address" name="address" value="<?= html_escape($user['raw_address']); ?>" placeholder="Contoh: Jl. Merdeka No. 17">
                        </div>
                        <div class="mb-3">
                            <label for="bio" class="form-label">Bio</label>
                            <textarea class="form-control" id="bio" name="bio" rows="3"><?= html_escape($user['bio'] != 'Ceritakan tentang diri Anda.' ? $user['bio'] : ''); ?></textarea>
                        </div>
                        <hr>
                        <h6 class="fw-bold mb-3 mt-4">Ubah Password</h6>
                        <p class="text-muted small">Kosongkan jika tidak ingin mengubah password.</p>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password Baru</label>
                            <input type="password" class="form-control" id="password" name="password">
                        </div>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </form>
                </div>
            </div>
        </div> </div> </div> <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
     integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
     crossorigin=""></script>

<script>
document.addEventListener('DOMContentLoaded', function() {

    // --- LOGIKA UNTUK FORM TIPE NASABAH ---
    const tipeNasabahSelect = document.getElementById('tipe_nasabah');
    const jumlahNasabahGroup = document.getElementById('jumlah_nasabah_group');
    const jumlahNasabahInput = document.getElementById('jumlah_nasabah');

    // Hanya jalankan jika elemen form tipe nasabah ADA di halaman
    if (tipeNasabahSelect && jumlahNasabahGroup && jumlahNasabahInput) {
        tipeNasabahSelect.addEventListener('change', function() {
            if (this.value === 'Kelompok') {
                jumlahNasabahGroup.style.display = 'block';
                jumlahNasabahInput.required = true;
                jumlahNasabahInput.min = 2;
            } else {
                jumlahNasabahGroup.style.display = 'none';
                jumlahNasabahInput.required = false;
                jumlahNasabahInput.value = ''; // Kosongkan jika bukan kelompok
                 // Jika Perorangan, jumlah otomatis 1 (tapi tidak ditampilkan)
                 // Anda bisa set value di sini jika perlu mengirim angka 1 ke backend
                 // if (this.value === 'Perorangan') { jumlahNasabahInput.value = 1; }
            }
        });

        // Trigger saat load jika 'Kelompok' sudah terpilih (misal saat validasi gagal)
        if (tipeNasabahSelect.value === 'Kelompok') {
             tipeNasabahSelect.dispatchEvent(new Event('change'));
        }
    }


    // --- LOGIKA UNTUK PETA LEAFLET ---
    const latInput = document.getElementById('latitude');
    const lonInput = document.getElementById('longitude');
    const mapElement = document.getElementById('map'); // Cek elemen peta

     // Hanya jalankan jika elemen peta ADA di halaman
     if (mapElement && latInput && lonInput) {
        const initialLat = latInput.value || -1.86667; // Default Indonesia
        const initialLon = lonInput.value || 114.73333;
        const initialZoom = latInput.value ? 16 : 5; // Zoom beda

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
         // Invalidate map size slightly after load if needed
         setTimeout(() => { map.invalidateSize(); }, 100);
     }

}); // Akhir DOMContentLoaded
</script>