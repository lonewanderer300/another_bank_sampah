<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="container-fluid" x-data="{ showTransactionForm: false }">
    <h4 class="fw-bold mb-4">Transaksi Agen</h4>

    <!-- ===== FORM TAMBAH TRANSAKSI ===== -->
    <div x-show="showTransactionForm"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform -translate-y-4"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 transform translate-y-0"
         x-transition:leave-end="opacity-0 transform -translate-y-4"
         class="card shadow-sm border-0 mb-4"
         style="display: none;">
        <div class="card-body">

            <div class="d-flex justify-content-between align-items-start mb-3">
                <h5 class="card-title fw-bold">Input Setoran Sampah Baru</h5>
                <button type="button" class="btn-close" @click="showTransactionForm = false" aria-label="Tutup"></button>
            </div>

            <?php if ($this->session->flashdata('error_form')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= $this->session->flashdata('error_form'); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if ($this->session->flashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= $this->session->flashdata('success'); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?= form_open('agent/add_transaction'); ?>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="customer_id" class="form-label">Pilih Nasabah <span class="text-danger">*</span></label>
                    <select name="customer_id" id="customer_id" class="form-select <?= form_error('customer_id') ? 'is-invalid' : ''; ?>" required>
                        <option value="">-- Pilih Nasabah Terdaftar --</option>
                        <?php foreach ($customers as $customer): ?>
                            <option value="<?= $customer['id_user']; ?>" <?= set_select('customer_id', $customer['id_user']); ?>>
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
                           value="<?= set_value('transaction_date', date('Y-m-d\TH:i')); ?>" required>
                    <div class="invalid-feedback"><?= form_error('transaction_date'); ?></div>
                </div>
            </div>

            <hr>
            <h6 class="mb-3">Detail Sampah</h6>

            <!-- Kategori / Jenis / Berat -->
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="kategori_sampah" class="form-label">Kategori Sampah</label>
                    <select id="kategori_sampah" class="form-select">
                        <option value="">-- Pilih Kategori --</option>
                        <?php foreach ($categories as $kategori): ?>
                            <option value="<?= $kategori['id_kategori']; ?>"><?= $kategori['nama_kategori']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="jenis_sampah" class="form-label">Jenis Sampah</label>
                    <select name="waste_items[id_jenis]" id="jenis_sampah" class="form-select">
                        <option value="">-- Pilih Jenis --</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="berat" class="form-label">Berat (kg)</label>
                    <input type="number" step="0.01" min="0" name="waste_items[berat]" id="berat"
                           class="form-control" placeholder="Berat (kg)">
                </div>
            </div>

            <!-- Petugas -->
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="petugas" class="form-label">Petugas</label>
                    <select name="petugas" id="petugas" class="form-select">
                        <option value="">-- Pilih Petugas --</option>
                        <?php foreach ($petugas as $p): ?>
                            <option value="<?= $p['id_petugas']; ?>"><?= $p['nama_petugas']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <button type="submit" class="btn btn-success mt-3">
                <i class="bi bi-save me-2"></i>Simpan Transaksi
            </button>
            <?= form_close(); ?>
        </div>
    </div>

    <!-- ===== TABEL RIWAYAT TRANSAKSI ===== -->
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title fw-bold mb-0">Riwayat Transaksi Masuk</h5>
                <button class="btn btn-sm btn-success" @click="showTransactionForm = !showTransactionForm">
                    <i class="bi bi-plus-lg me-1"></i>
                    <span x-show="!showTransactionForm">Tambah</span>
                    <span x-show="showTransactionForm">Tutup Form</span>
                </button>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Tanggal</th>
                            <th>Nama Nasabah</th>
                            <th class="text-end">Total Berat</th>
                            <th class="text-end">Total Nilai (Poin/Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($transactions)): ?>
                            <?php foreach ($transactions as $trans): ?>
                                <tr>
                                    <td><?= date('d M Y, H:i', strtotime($trans['tanggal_setor'])); ?></td>
                                    <td><?= html_escape($trans['customer_name']); ?></td>
                                    <td class="text-end"><?= number_format($trans['total_berat'] ?? 0, 2); ?> kg</td>
                                    <td class="text-end fw-bold text-success">+ <?= number_format($trans['transaction_value'] ?? 0, 0, ',', '.'); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="4" class="text-center text-muted py-3">Belum ada transaksi.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- ===== SCRIPT AJAX UNTUK FILTER JENIS BERDASARKAN KATEGORI ===== -->
<script>
document.getElementById('kategori_sampah').addEventListener('change', function() {
    const idKategori = this.value;
    const jenisSelect = document.getElementById('jenis_sampah');
    jenisSelect.innerHTML = '<option value="">-- Pilih Jenis --</option>';

    if (idKategori) {
        fetch(`<?= base_url('agent/get_jenis_by_kategori?id_kategori='); ?>${idKategori}`)
            .then(res => res.json())
            .then(data => {
                data.forEach(item => {
                    const harga = item.harga ? ` (Rp ${Number(item.harga).toLocaleString()}/kg)` : ' (Harga belum diatur)';
                    const option = document.createElement('option');
                    option.value = item.id_jenis;
                    option.textContent = `${item.nama_jenis}${harga}`;
                    jenisSelect.appendChild(option);
                });
            })
            .catch(err => console.error(err));
    }
});
</script>
