<div class="container-fluid">
  <h4 class="fw-bold mb-4">Rekap Transaksi</h4>

  <form method="get" action="<?= site_url('agent/rekap'); ?>" class="row g-3 mb-4">
    <div class="col-md-4">
      <label for="start_date" class="form-label">Dari Tanggal</label>
      <input type="date" id="start_date" name="start_date" class="form-control"
             value="<?= html_escape($start_date); ?>">
    </div>
    <div class="col-md-4">
      <label for="end_date" class="form-label">Sampai Tanggal</label>
      <input type="date" id="end_date" name="end_date" class="form-control"
             value="<?= html_escape($end_date); ?>">
    </div>
    <div class="col-md-4 align-self-end">
      <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> Tampilkan</button>
      <?php if (!empty($transactions)): ?>
        <a href="<?= site_url('agent/download_rekap?start_date=' . $start_date . '&end_date=' . $end_date); ?>" 
           class="btn btn-success"><i class="bi bi-download"></i> Unduh Excel</a>
      <?php endif; ?>
    </div>
  </form>

  <div class="card shadow-sm border-0">
    <div class="card-body">
      <h5 class="card-title fw-bold mb-3">Daftar Transaksi</h5>
      <div class="table-responsive">
        <table class="table table-hover align-middle">
          <thead class="table-light">
            <tr>
              <th>Tanggal</th>
              <th>Nama Nasabah</th>
              <th class="text-end">Total Berat</th>
              <th class="text-end">Total Nilai (Rp/Poin)</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($transactions)): ?>
              <?php foreach ($transactions as $t): ?>
                <tr>
                  <td><?= date('d M Y', strtotime($t['tanggal_setor'])); ?></td>
                  <td><?= html_escape($t['customer_name']); ?></td>
                  <td class="text-end"><?= number_format($t['total_berat'], 2); ?> kg</td>
                  <td class="text-end fw-bold text-success"><?= number_format($t['total_poin'], 0, ',', '.'); ?></td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="4" class="text-center text-muted py-3">Tidak ada transaksi pada periode ini.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
