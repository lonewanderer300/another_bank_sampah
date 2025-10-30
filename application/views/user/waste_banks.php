<div class="container-fluid">
    <h4 class="fw-bold mb-1">Bank Sampah</h4>
    <p class="text-muted mb-4">Temukan, pilih, dan hubungi bank sampah pilihan Anda.</p>

    <?php if ($this->session->flashdata('info')): ?>
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <?= $this->session->flashdata('info'); ?> <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
     <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= $this->session->flashdata('success'); ?> <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
     <?php if ($this->session->flashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $this->session->flashdata('error'); ?> <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="mb-3 text-end" id="reset-button-container" style="display: none;">
        <button class="btn btn-sm btn-outline-secondary" onclick="resetSelection()">Tampilkan Semua Bank Sampah</button>
    </div>

    <div class="row" id="agent-list-container">
        <?php if (!empty($agents)): ?>
            <?php foreach ($agents as $agent): ?>
                <div class="col-lg-6 mb-4 agent-card" data-agent-id="<?= $agent['id_agent']; ?>">
                    <div class="card shadow-sm h-100 border-0">
                        <div class="card-body d-flex flex-column p-4">
                            <div>
                                <h5 class="card-title fw-bold text-primary"><?= html_escape($agent['name']); ?></h5>
                                <span class="badge bg-secondary fw-normal mb-3"><?= html_escape($agent['wilayah']); ?></span>
                                <p class="card-text text-muted mb-1">
                                    <i class="bi bi-geo-alt-fill me-2" style="width: 16px;"></i>
                                    <?= !empty($agent['address']) ? html_escape($agent['address']) : 'Alamat detail tidak tersedia.'; ?>
                                </p>
                                <?php if (isset($agent['distance'])): // Hanya tampilkan jarak jika ada (berarti user punya lokasi) ?>
                                <p class="card-text text-muted mb-2">
                                    <i class="bi bi-stopwatch-fill me-2" style="width: 16px;"></i>
                                    Diperkirakan <strong><?= number_format($agent['distance'], 2); ?> km</strong> dari lokasi Anda.
                                </p>
                                <?php endif; ?>
                            </div>
                            <div class="mt-auto pt-3">
                                <?php
                                    if (!empty($agent['phone'])) {
                                        $phone_number = preg_replace('/[^0-9]/', '', $agent['phone']);
                                        if (substr($phone_number, 0, 1) === '0') { $phone_number = '62' . substr($phone_number, 1); }
                                        $whatsapp_url = 'https://wa.me/' . $phone_number . '?text=' . urlencode("Halo Bank Sampah " . $agent['name'] . ", saya nasabah dari aplikasi Bank Sampah dan ingin menyetor sampah.");
                                ?>
                                    <button class="btn btn-success w-100 btn-select-agent"
                                            data-agent-id="<?= $agent['id_agent']; ?>"
                                            data-whatsapp-url="<?= $whatsapp_url; ?>">
                                        <i class="bi bi-whatsapp me-2"></i>Pilih & Hubungi via WhatsApp
                                    </button>
                                <?php } else { ?>
                                    <button class="btn btn-secondary w-100" disabled><i class="bi bi-whatsapp me-2"></i>Nomor tidak tersedia</button>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-warning text-center">
                    <h5 class="alert-heading">Tidak Ada Bank Sampah</h5>
                    <p class="mb-0">Tidak ada bank sampah aktif yang ditemukan.</p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    function showOnlySelected(selectedAgentId) {
        const agentCards = document.querySelectorAll('.agent-card');
        agentCards.forEach(card => {
            if (card.dataset.agentId != selectedAgentId) {
                card.style.display = 'none';
            } else {
                card.classList.remove('col-lg-6');
                card.classList.add('col-12');
            }
        });
        document.getElementById('reset-button-container').style.display = 'block';
    }

    function resetSelection() {
        // Panggil controller untuk mereset pilihan di database
        fetch(`<?= base_url('user/select_agent/0'); ?>`, { // Kirim 0 atau ID non-valid untuk reset
            method: 'POST',
            headers: {'X-Requested-With': 'XMLHttpRequest'}
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload(); // Reload halaman untuk menampilkan semua
            } else {
                alert('Gagal mereset pilihan: ' + (data.message || 'Silakan coba lagi.'));
            }
        })
        .catch(error => {
             console.error('Error:', error);
             alert('Terjadi kesalahan saat mereset.');
        });
    }

    document.querySelectorAll('.btn-select-agent').forEach(button => {
        button.addEventListener('click', function() {
            const agentId = this.dataset.agentId;
            const whatsappUrl = this.dataset.whatsappUrl;
            const selectButton = this;

            selectButton.disabled = true;
            selectButton.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Memilih...';

            fetch(`<?= base_url('user/select_agent/'); ?>${agentId}`, {
                method: 'POST',
                headers: {'X-Requested-With': 'XMLHttpRequest'}
            })
            .then(response => {
                if (!response.ok) { // Handle HTTP errors
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
             })
            .then(data => {
                if (data.success) {
                    showOnlySelected(agentId);
                    window.open(whatsappUrl, '_blank');
                } else {
                    alert('Gagal memilih agent: ' + (data.message || 'Silakan coba lagi.'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan. Pastikan Anda terhubung ke internet dan coba lagi. Error: ' + error.message);
            })
            .finally(() => {
                 selectButton.disabled = false;
                 selectButton.innerHTML = '<i class="bi bi-whatsapp me-2"></i>Pilih & Hubungi via WhatsApp';
            });
        });
    });

    // Cek saat load jika ada agent terpilih
    const selectedAgentId = <?= json_encode($selected_agent_id); ?>; // Ambil dari PHP
    if (selectedAgentId) {
        showOnlySelected(selectedAgentId);
    }
</script>