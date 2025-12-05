<div class="card shadow-sm border-0">
    <div class="card-body">
        <h5 class="card-title fw-bold mb-3">Daftar Nasabah Saya</h5>
        <p class="text-muted">Nasabah yang telah memilih Anda sebagai bank sampah pilihan.</p>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>no.</th>
                        <th>Nama Nasabah</th>
                        <th>Email</th>
                        <th>Telepon</th>
                        <th>Tipe Nasabah</th> 
                        <th>Iuran Bulan Ini</th> 
                        <th>Tgl Bayar Terakhir/Deadline</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="8" class="text-center py-3">Tidak ada nasabah yang memilih Anda sebagai agen saat ini.</td>
                        </tr>
                    <?php endif; ?>

                    <?php $no = 1; foreach ($users as $user): ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= html_escape($user['nama']); ?></td>
                                <td><?= html_escape($user['email']); ?></td>
                                <td><?= html_escape($user['phone'] ?? '-'); ?></td>
                                <td><?= html_escape($user['tipe_nasabah'] ?? 'Belum Daftar'); ?></td>
                                <td>
                                    <?php if ($user['iuran_status'] === 'belum bayar'): ?>
                                        <span class="badge bg-warning text-dark">
                                            Rp. <?= number_format($user['iuran_biaya'], 0, ',', '.'); ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-success">Lunas</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($user['iuran_status'] === 'belum bayar'): ?>
                                        <span class="badge bg-danger">Deadline: <?= date('d M Y', strtotime($user['iuran_deadline'])); ?></span>
                                    <?php else: ?>
                                        <span class="text-success small">Dibayar: <?= $user['iuran_paid_date'] ? date('d M Y', strtotime($user['iuran_paid_date'])) : '-'; ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($user['iuran_status'] === 'belum bayar'): ?>
                                        <a href="<?= base_url('agent/pay_iuran/' . $user['iuran_id']) ?>" 
                                            class="btn btn-sm btn-success mb-1 confirm-bayar"
                                            data-nasabah="<?= html_escape($user['nama']); ?>"
                                            data-biaya="Rp. <?= number_format($user['iuran_biaya'], 0, ',', '.'); ?>">
                                             <i class="bi bi-check-circle-fill"></i> Bayar
                                        </a>
                                    <?php endif; ?>
                                    
                                    <button class="btn btn-sm btn-info text-white" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#historyModal"
                                            data-user-id="<?= $user['id_user']; ?>"
                                            data-user-name="<?= html_escape($user['nama']); ?>">
                                        <i class="bi bi-info-circle-fill"></i> Detail
                                    </button>
                                </td>
                            </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="historyModal" tabindex="-1" aria-labelledby="historyModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="historyModalLabel">Riwayat Pembayaran Iuran Nasabah: <span id="nasabahName"></span></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="historyTable">
                <thead>
                    <tr>
                        <th>Bulan Iuran</th>
                        <th>Biaya</th>
                        <th>Tgl Pembayaran</th>
                        <th>Deadline</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    </tbody>
            </table>
        </div>
        <p class="text-center text-muted mt-3" id="loadingMessage">Memuat data...</p>
      </div>
    </div>
  </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', function () {
    const confirmButtons = document.querySelectorAll('.confirm-bayar');

    confirmButtons.forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();
            const url = this.getAttribute('href');
            const nasabah = this.getAttribute('data-nasabah');
            const biaya = this.getAttribute('data-biaya');

            if (confirm(`Anda yakin ingin menandai iuran nasabah ${nasabah} sebesar ${biaya} sebagai SUDAH DIBAYAR?`)) {
                window.location.href = url;
            }
        });
    });

    const historyModal = document.getElementById('historyModal');
    const nasabahName = document.getElementById('nasabahName');
    const historyTableBody = document.querySelector('#historyTable tbody');
    const loadingMessage = document.getElementById('loadingMessage');

    historyModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const userId = button.getAttribute('data-user-id');
        const userName = button.getAttribute('data-user-name');
        
        nasabahName.textContent = userName;
        historyTableBody.innerHTML = '';
        loadingMessage.classList.remove('d-none');

        // Fetch data via AJAX
        fetch(`<?= base_url('agent/get_iuran_history/'); ?>${userId}`)
            .then(response => response.json())
            .then(data => {
                loadingMessage.classList.add('d-none');
                if (data.length > 0) {
                    data.forEach(item => {
                        let statusBadge = item.status_iuran === 'sudah bayar' 
                            ? `<span class="badge bg-success">Sudah Bayar</span>` 
                            : `<span class="badge bg-danger">Belum Bayar</span>`;
                        
                        let paidDate = item.tanggal_bayar ? new Date(item.tanggal_bayar).toLocaleDateString('id-ID', { year: 'numeric', month: 'short', day: '2-digit' }) : '-';
                        let deadlineDate = new Date(item.deadline);
                        let deadlineDisplay = deadlineDate.toLocaleDateString('id-ID', { year: 'numeric', month: 'short', day: '2-digit' });
                        
                        // Menampilkan bulan iuran berdasarkan deadline
                        let iuranMonth = deadlineDate.toLocaleDateString('id-ID', { year: 'numeric', month: 'long' });

                        const row = `
                            <tr>
                                <td>${iuranMonth}</td>
                                <td>Rp. ${Number(item.biaya).toLocaleString('id-ID')}</td>
                                <td>${paidDate}</td>
                                <td>${deadlineDisplay}</td>
                                <td>${statusBadge}</td>
                            </tr>
                        `;
                        historyTableBody.innerHTML += row;
                    });
                } else {
                    historyTableBody.innerHTML = `<tr><td colspan="5" class="text-center">Belum ada riwayat iuran.</td></tr>`;
                }
            })
            .catch(error => {
                loadingMessage.classList.add('d-none');
                historyTableBody.innerHTML = `<tr><td colspan="5" class="text-center text-danger">Gagal memuat data riwayat.</td></tr>`;
                safeLog('Error fetching history:', error);
            });
    });
    
    // Fungsi dummy safeLog (ganti dengan fungsi log Anda jika diperlukan)
    function safeLog(...args) {
        if (window.console) console.log(...args);
    }
});
</script>