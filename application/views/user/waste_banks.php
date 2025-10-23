<div class="container-fluid">
    <h4 class="fw-bold mb-1">Bank Sampah Terdekat</h4>
    <p class="text-muted mb-4">Temukan dan hubungi bank sampah terdekat dari lokasi Anda.</p>

    <?php if ($this->session->flashdata('info')): ?>
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <?= $this->session->flashdata('info'); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <?php if (!empty($agents)): ?>
            <?php foreach ($agents as $agent): ?>
                <div class="col-lg-6 mb-4">
                    <div class="card shadow-sm h-100 border-0">
                        <div class="card-body d-flex flex-column p-4">
                            <div>
                                <h5 class="card-title fw-bold text-primary"><?= html_escape($agent['name']); ?></h5>
                                <span class="badge bg-secondary fw-normal mb-3"><?= html_escape($agent['wilayah']); ?></span>
                                
                                <p class="card-text text-muted mb-1">
                                    <i class="bi bi-geo-alt-fill me-2" style="width: 16px;"></i>
                                    <?php
                                        if (!empty($agent['address'])) {
                                            echo html_escape($agent['address']);
                                        } else {
                                            echo 'Alamat detail tidak tersedia.';
                                        }
                                    ?>
                                </p>

                                <?php if (isset($agent['distance'])): ?>
                                <p class="card-text text-muted mb-2">
                                    <i class="bi bi-stopwatch-fill me-2" style="width: 16px;"></i>
                                    Diperkirakan <strong><?= number_format($agent['distance'], 2); ?> km</strong> dari lokasi Anda.
                                </p>
                                <?php endif; ?>
                            </div>
                            
                            <div class="mt-auto pt-3">
                                <?php
                                    if (!empty($agent['phone'])) {
                                        // Membersihkan dan memformat nomor telepon untuk URL WhatsApp
                                        $phone_number = preg_replace('/[^0-9]/', '', $agent['phone']);
                                        if (substr($phone_number, 0, 1) === '0') {
                                            $phone_number = '62' . substr($phone_number, 1);
                                        }
                                        $whatsapp_url = 'https://wa.me/' . $phone_number;
                                ?>
                                    <a href="<?= $whatsapp_url; ?>" target="_blank" class="btn btn-success w-100">
                                        <i class="bi bi-whatsapp me-2"></i>Hubungi via WhatsApp
                                    </a>
                                <?php
                                    } else {
                                ?>
                                    <button class="btn btn-secondary w-100" disabled>
                                        <i class="bi bi-whatsapp me-2"></i>Nomor tidak tersedia
                                    </button>
                                <?php
                                    }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-warning text-center">
                    <h5 class="alert-heading">Tidak Ada Bank Sampah Ditemukan</h5>
                    <p class="mb-0">Saat ini tidak ada bank sampah yang terdaftar atau ditemukan di dekat Anda. Silakan coba lagi nanti.</p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>