<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="container-fluid" x-data="{ id_setoran: <?= $transaction['id_setoran']; ?> }">
    <h4 class="fw-bold mb-4">Edit Transaksi #<?= $transaction['id_setoran']; ?></h4>
    <p class="text-muted">Mengubah setoran sampah yang sudah ada (Bank Sampah: <?= html_escape($transaction['agent_name'] ?? 'N/A'); ?>).</p>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">

            <?php if ($this->session->flashdata('error_form')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= $this->session->flashdata('error_form'); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if ($this->session->flashdata('warning')): ?>
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <?= $this->session->flashdata('warning'); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?= form_open('agent/edit_transaction/' . $transaction['id_setoran']); ?>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="customer_id" class="form-label">Pilih Nasabah <span class="text-danger">*</span></label>
                    <select name="customer_id" id="customer_id" class="form-select <?= form_error('customer_id') ? 'is-invalid' : ''; ?>" required>
                        <option value="">-- Pilih Nasabah Terdaftar --</option>
                        <?php foreach ($customers as $customer): ?>
                            <option value="<?= $customer['id_user']; ?>" 
                                <?= set_select('customer_id', $customer['id_user'], $customer['id_user'] == $transaction['id_user']); ?>>
                                <?= html_escape($customer['nama']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="invalid-feedback"><?= form_error('customer_id'); ?></div>
                </div>
                <div class="col-md-6">
                    <label for="transaction_date" class="form-label">Tanggal Transaksi <span class="text-danger">*</span></label>
                    <input type="datetime-local" name="transaction_date" id="transaction_date"
                                   class="form-control <?= form_error('transaction_date') ? 'is-invalid' : ''; ?>"
                                   value="<?= set_value('transaction_date', date('Y-m-d\TH:i', strtotime($transaction['tanggal_setor']))); ?>" required>
                    <div class="invalid-feedback"><?= form_error('transaction_date'); ?></div>
                </div>
            </div>

            <hr>
            <h6 class="mb-3">Detail Sampah</h6>

            <div class="row mb-3">
                <div id="wasteItemsContainer">
                <?php $item_index = 0; ?>
                <?php if (!empty($details)): ?>
                    <?php foreach ($details as $detail): ?>
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
                                        <?= $detail['nama_jenis']; ?> (Rp <?= number_format($detail['current_price'], 0, ',', '.'); ?>/kg)
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
                    <?php $item_index++; ?>
                    <?php endforeach; ?>
                <?php else: ?>
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

            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="petugas" class="form-label">Petugas</label>
                    <select name="id_petugas" id="id_petugas" class="form-select">
                        <option value="">-- Pilih Petugas --</option>
                        <?php 
                        // Akses id_petugas yang aman dari $transaction (misalnya jika disimpan di field lain)
                        $current_petugas_id = $transaction['id_petugas'] ?? NULL;

                        foreach ($petugas as $p): 
                        ?>
                            <option value="<?= $p['id_petugas']; ?>" 
                                <?= set_select('id_petugas', $p['id_petugas'], $p['id_petugas'] == $current_petugas_id); ?>>
                                <?= $p['nama_petugas']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <button type="submit" class="btn btn-primary mt-3">
                <i class="bi bi-arrow-repeat me-2"></i>Perbarui Transaksi
            </button>
            <a href="<?= base_url('agent/transactions'); ?>" class="btn btn-secondary mt-3">
                Batal
            </a>
            <?= form_close(); ?>
        </div>
    </div>
</div>

<script>
let wasteIndex = <?= $item_index; ?>; 
const baseUrl = '<?= base_url('agent/get_jenis_by_kategori?id_kategori='); ?>'; 

// 1. Logic for adding new waste item rows (Sama seperti di transactions.php)
document.getElementById('addWasteItem').addEventListener('click', function () {
    const container = document.getElementById('wasteItemsContainer');
    const firstItem = container.querySelector('.waste-item');
    if (!firstItem) return;
    
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

// 3. Logic to fetch waste types based on category selection (Sama seperti di transactions.php)
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
                        const harga = item.harga ? ` (Rp ${Number(item.harga).toLocaleString('id-ID')}/kg)` : ' (Harga belum diatur)';
                        
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