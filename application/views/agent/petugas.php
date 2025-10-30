<div class="container-fluid">
    <h4 class="fw-bold mb-4">Daftar Petugas</h4>
<?php if($this->session->flashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= $this->session->flashdata('success'); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php elseif($this->session->flashdata('error_form')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= $this->session->flashdata('error_form'); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

    <!-- Flash Messages -->
    <?php if($this->session->flashdata('success')): ?>
        <div class="alert alert-success"><?= $this->session->flashdata('success'); ?></div>
    <?php elseif($this->session->flashdata('error_form')): ?>
        <div class="alert alert-danger"><?= $this->session->flashdata('error_form'); ?></div>
    <?php endif; ?>

    <!-- Form Tambah Petugas -->
    <div class="card mb-4">
        <div class="card-header bg-success text-white">Tambah Petugas Baru</div>
        <div class="card-body">
            <form method="post" action="<?= base_url('agent/register_petugas'); ?>">
                <div class="row g-3">
                    <div class="col-md-8">
                        <input type="text" name="nama_petugas" class="form-control" placeholder="Nama Petugas" required>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-success w-100">Tambah</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

   <!-- Tabel Daftar Petugas -->
<div class="card">
    <div class="card-header bg-light">
        <strong>Petugas Terdaftar</strong>
    </div>
    <div class="card-body p-0">
        <table class="table table-striped mb-0 align-middle">
            <thead class="table-success">
                <tr>
                    <th width="5%">#</th>
                    <th>Nama Petugas</th>
                    <th width="10%" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($petugas)): ?>
                    <?php foreach ($petugas as $i => $p): ?>
                        <tr>
                            <td><?= $i + 1; ?></td>
                            <td><?= htmlspecialchars($p['nama_petugas']); ?></td>
                            <td class="text-center">
                                <a href="<?= base_url('agent/delete_petugas/' . $p['id_petugas']); ?>" 
                                   class="btn btn-sm btn-outline-danger"
                                   onclick="return confirm('Yakin ingin menghapus petugas ini?');">
                                   <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="3" class="text-center text-muted">Belum ada petugas terdaftar.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

