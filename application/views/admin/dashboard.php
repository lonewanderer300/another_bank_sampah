<h4 class="fw-bold mb-4">Dashboard Admin</h4>

<div class="row">
    <div class="col-md-8">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body">
                <h5 class="card-title fw-bold mb-3">Persetujuan Agen Baru</h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <tbody>
                            <?php if (!empty($pending_agents)): ?>
                                <?php foreach ($pending_agents as $agent): ?>
                                    <tr>
                                        <td>
                                            <div class="fw-bold"><?= html_escape($agent['nama']); ?></div>
                                            <div class="small text-muted"><?= html_escape($agent['email']); ?></div>
                                        </td>
                                        <td class="text-end">
                                            <a href="<?= base_url('admin/approve_agent/' . $agent['id_agent']) ?>" class="btn btn-sm btn-success">
                                                <i class="bi bi-check-lg"></i> Setujui
                                            </a>
                                            <a href="<?= base_url('admin/reject_agent/' . $agent['id_agent']) ?>" class="btn btn-sm btn-danger">
                                                <i class="bi bi-x-lg"></i> Tolak
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td class="text-center text-muted py-3">Tidak ada permintaan agen baru.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm border-0 text-white bg-warning h-100">
            <div class="card-body text-center">
                <i class="bi bi-person-fill-exclamation fs-1"></i>
                <h1 class="display-4 fw-bold my-2"><?= $unpaid_customers; ?></h1>
                <p class="lead">Nasabah Belum Membayar Iuran</p>
            </div>
        </div>
    </div>
</div>