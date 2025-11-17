<div class="container-fluid p-6" x-data="{ id_setoran: <?= $transaction['id_setoran']; ?> }">
    <h2 class="text-2xl font-bold mb-4">Edit Transaksi #<?= $transaction['id_setoran']; ?> (Admin)</h2>
    
    <?php if ($this->session->flashdata('error_form')): ?>
        <div class="alert alert-danger" role="alert">
            <?= $this->session->flashdata('error_form'); ?>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body">
            
            <?= form_open('admin/edit_transaction/' . $transaction['id_setoran']); ?>
            
            <h5 class="mb-3 font-semibold">Data Dasar Transaksi</h5>
            
            <div class="row mb-4">
                <div class="col-md-6">
                    <label for="customer_id" class="form-label">Pilih Nasabah <span class="text-danger">*</span></label>
                    <select name="customer_id" id="customer_id" class="form-select" required>
                        <?php foreach ($all_customers as $customer): ?>
                            <option value="<?= $customer['id_user']; ?>" 
                                <?= set_select('customer_id', $customer['id_user'], $customer['id_user'] == $transaction['id_user']); ?>>
                                <?= html_escape($customer['nama']); ?> (<?= $customer['role'] ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="transaction_date" class="form-label">Tanggal Transaksi <span class="text-danger">*</span></label>
                    <input type="datetime-local" name="transaction_date" id="transaction_date"
                           class="form-control"
                           value="<?= set_value('transaction_date', date('Y-m-d\TH:i', strtotime($transaction['tanggal_setor']))); ?>" required>
                </div>
            </div>

            <hr class="my-4">
            <h5 class="mb-3 font-semibold">Detail Sampah</h5>

            <div id="wasteItemsContainer">
                <?php $item_index = 0; ?>
                <?php if (!empty($details)): foreach ($details as $detail): ?>
                    <div class="row mb-3 waste-item">
                        <div class="col-md-4">
                            <label class="form-label">Kategori Sampah</label>
                            <select class="form-select kategori_sampah" name="waste_items[<?= $item_index; ?>][id_kategori]" required>
                                <option value="">-- Pilih Kategori --</option>
                                <?php foreach ($categories as $kategori): ?>
                                    <option value="<?= $kategori['id_kategori']; ?>" 
                                        <?= set_select("waste_items[{$item_index}][id_kategori]", $kategori['id_kategori'], $kategori['id_kategori'] == $detail['id_kategori']); ?>>
                                        <?= $kategori['nama_kategori']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Jenis Sampah</label>
                            <select class="form-select jenis_sampah" name="waste_items[<?= $item_index; ?>][id_jenis]" required>
                                <option value="<?= $detail['id_jenis']; ?>" selected>
                                    <?= $detail['nama_jenis']; ?> (Harga saat ini: Rp <?= number_format($detail['current_price'], 0, ',', '.'); ?>/kg)
                                </option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Berat (kg)</label>
                            <input type="number" step="0.01" min="0" name="waste_items[<?= $item_index; ?>][berat]" 
                                class="form-control" placeholder="Berat (kg)" 
                                value="<?= set_value("waste_items[{$item_index}][berat]", $detail['berat']); ?>" required>
                        </div>

                        <div class="col-md-1 d-flex align-items-end">
                            <button type="button" class="btn btn-danger btn-remove-item w-100">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                <?php $item_index++; endforeach; else: ?>
                    <div class="row mb-3 waste-item">
                        <div class="col-md-4">
                            <label class="form-label">Kategori Sampah</label>
                            <select class="form-select kategori_sampah" name="waste_items[0][id_kategori]" required>
                                <option value="">-- Pilih Kategori --</option>
                                <?php foreach ($categories as $kategori): ?>
                                    <option value="<?= $kategori['id_kategori']; ?>"><?= $kategori['nama_kategori']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Jenis Sampah</label>
                            <select class="form-select jenis_sampah" name="waste_items[0][id_jenis]" required>
                                <option value="">-- Pilih Jenis --</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Berat (kg)</label>
                            <input type="number" step="0.01" min="0" name="waste_items[0][berat]" class="form-control" placeholder="Berat (kg)" required>
                        </div>
                        <div class="col-md-1 d-flex align-items-end">
                            <button type="button" class="btn btn-danger btn-remove-item w-100">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="text-end mb-3">
                <button type="button" id="addWasteItem" class="btn btn-outline-success btn-sm">
                    <i class="bi bi-plus-circle me-1"></i> Tambah Jenis Sampah
                </button>
            </div>

            <div class="row mb-4">
                <div class="col-md-4">
                    <label class="form-label">Agent (Bank Sampah)</label>
                    <select name="id_agent" id="id_agent" class="form-select" required>
                        <option value="">-- Pilih Agen --</option>
                        <?php foreach ($all_agents as $agent_item): ?>
                            <option value="<?= $agent_item['id_agent']; ?>" 
                                <?= set_select('id_agent', $agent_item['id_agent'], $agent_item['id_agent'] == $transaction['id_agent']); ?>>
                                <?= html_escape($agent_item['nama']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="id_petugas" class="form-label">Petugas</label>
                    <select name="id_petugas" id="id_petugas" class="form-select">
                        <option value="">-- Pilih Petugas --</option>
                        <?php 
                        // Akses id_petugas yang aman dari $transaction (jika ada, gunakan yang disimpan)
                        $current_petugas_id = $transaction['id_petugas'] ?? NULL;
                        
                        // Gunakan loop $petugas yang dimuat dari Admin Controller
                        foreach ($petugas as $p): 
                        ?>
                            <option value="<?= $p['id_petugas']; ?>"
                                <?= set_select('id_petugas', $p['id_petugas'], $p['id_petugas'] == $current_petugas_id); ?>>
                                <?= html_escape($p['nama_petugas']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <button type="submit" class="btn btn-primary mt-3">
                <i class="bi bi-arrow-repeat me-2"></i>Perbarui Transaksi
            </button>
            <a href="<?= base_url('admin/manage_transactions'); ?>" class="btn btn-secondary mt-3">
                Batal
            </a>
            <?= form_close(); ?>
        </div>
    </div>
</div>

<script>
let wasteIndex = <?= $item_index; ?>; // Lanjutkan indeks dari PHP
// Hati-hati: Admin menggunakan fungsi Agen untuk AJAX. Pastikan route ini benar.
const baseUrl = '<?= base_url('agent/get_jenis_by_kategori?id_kategori='); ?>'; 

// 1. Logic for adding new waste item rows
document.getElementById('addWasteItem').addEventListener('click', function () {
    const container = document.getElementById('wasteItemsContainer');
    const firstItem = container.querySelector('.waste-item');
    if (!firstItem) return; // Prevent error if container is empty
    
    const newItem = firstItem.cloneNode(true); 

    newItem.querySelectorAll('select, input').forEach(el => {
        el.name = el.name.replace(/\[\d+\]/, `[${wasteIndex}]`); 
        
        if (el.tagName === 'SELECT' && el.classList.contains('jenis_sampah')) {
             el.innerHTML = '<option value="">-- Pilih Jenis --</option>'; 
             el.selectedIndex = 0;
        } else {
             el.value = ''; 
             if (el.tagName === 'SELECT' && el.classList.contains('kategori_sampah')) {
                 el.selectedIndex = 0;
             }
        }
    });

    container.appendChild(newItem);
    wasteIndex++;
});

// 2. Logic for removing waste item rows
document.addEventListener('click', function (e) {
    if (e.target.closest('.btn-remove-item')) {
        const itemToRemove = e.target.closest('.waste-item');
        const items = document.querySelectorAll('.waste-item');
        if (items.length > 1) {
            itemToRemove.remove();
        } else {
            alert('Minimal satu item sampah harus ada.');
        }
    }
});

// 3. Logic to fetch waste types based on category selection
document.addEventListener('change', function (e) {
    if (e.target.classList.contains('kategori_sampah')) {
        const idKategori = e.target.value;
        const jenisSelect = e.target.closest('.waste-item').querySelector('.jenis_sampah');
        
        jenisSelect.innerHTML = '<option value="">-- Pilih Jenis --</option>';

        if (idKategori) {
            fetch(baseUrl + idKategori)
                .then(res => res.json())
                .then(data => {
                    data.forEach(item => {
                        const harga = item.harga ? ` (Harga saat ini: Rp ${Number(item.harga).toLocaleString('id-ID')}/kg)` : ' (Harga belum diatur)';
                        
                        const option = document.createElement('option');
                        option.value = item.id_jenis;
                        option.textContent = `${item.nama_jenis}${harga}`;
                        jenisSelect.appendChild(option);
                    });
                })
                .catch(err => console.error('Error fetching waste types:', err));
        }
    }
});
</script>