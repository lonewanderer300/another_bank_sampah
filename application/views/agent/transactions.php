<div class="container-fluid" x-data="{ showTransactionForm: false }">
  <h4 class="fw-bold mb-4">Transaksi Agen</h4>

  <!-- ===== FORM TAMBAH TRANSAKSI ===== -->
  <div x-show="showTransactionForm"
       x-cloak
       x-transition:enter="transition ease-out duration-300"
       x-transition:enter-start="opacity-0 transform -translate-y-4"
       x-transition:enter-end="opacity-100 transform translate-y-0"
       x-transition:leave="transition ease-in duration-200"
       x-transition:leave-start="opacity-100 transform translate-y-0"
       x-transition:leave-end="opacity-0 transform -translate-y-4"
       class="card shadow-sm border-0 mb-4">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-start mb-3">
        <h5 class="card-title fw-bold">Input Setoran Sampah Baru</h5>
        <button type="button" class="btn-close" @click="showTransactionForm = false" aria-label="Tutup"></button>
      </div>

      <!-- Alert -->
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

      <!-- Dropdown kategori -->
      <div class="mb-3">
        <label for="kategori_id" class="form-label">Pilih Kategori Sampah</label>
        <select id="kategori_id" class="form-control" required>
          <option value="">-- Pilih Kategori --</option>
          <?php foreach ($categories as $cat): ?>
            <option value="<?= $cat['id_kategori']; ?>"><?= html_escape($cat['nama_kategori']); ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- Container jenis sampah -->
      <div id="jenis-container">
        <p class="text-muted"><i>Pilih kategori terlebih dahulu untuk menampilkan jenis sampah.</i></p>
      </div>

      <button type="submit" class="btn btn-success mt-4">
        <i class="bi bi-save me-2"></i> Simpan Transaksi
      </button>
      <?= form_close(); ?>
    </div>
  </div>

  <!-- ===== RIWAYAT TRANSAKSI ===== -->
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
            <?php if (!empty($transactions)): ?>
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

<!-- Alpine.js + Dynamic Jenis Loader -->
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<style>[x-cloak] { display: none !important; }</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const kategoriSelect = document.getElementById('kategori_id');
  const container = document.getElementById('jenis-container');

  kategoriSelect.addEventListener('change', function() {
    const kategoriId = this.value;
    if (!kategoriId) {
      container.innerHTML = '<p class="text-muted"><i>Pilih kategori terlebih dahulu untuk menampilkan jenis sampah.</i></p>';
      return;
    }

    fetch('<?= site_url("agent/get_jenis_by_kategori/"); ?>' + kategoriId)
      .then(response => response.json())
      .then(data => {
        if (!data.length) {
          container.innerHTML = '<p class="text-danger small">Tidak ada jenis sampah dalam kategori ini.</p>';
          return;
        }

        let html = '<div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3">';
        data.forEach(type => {
          const disabled = (!type.harga || type.harga <= 0);
          const priceInfo = disabled ? ' (Harga belum diatur)' : ` (Rp ${Number(type.harga).toLocaleString()}/kg)`;
          html += `
            <div class="col">
              <label class="form-label small d-flex justify-content-between">
                <span>${type.nama_jenis}</span>
                <span class="text-muted">${priceInfo}</span>
              </label>
              <input type="number" step="0.01" min="0"
                     name="waste_items[${type.id_jenis}]"
                     class="form-control form-control-sm"
                     placeholder="Berat (kg)"
                     ${disabled ? 'disabled title="Harga belum diatur"' : ''}>
            </div>`;
        });
        html += '</div>';
        container.innerHTML = html;
      })
      .catch(() => {
        container.innerHTML = '<p class="text-danger small">Gagal memuat data jenis sampah.</p>';
      });
  });
});
</script>
