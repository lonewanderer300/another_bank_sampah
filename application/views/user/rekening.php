<div class="container mt-4">
    <h3 class="mb-4">Nomor Rekening Anda</h3>

    <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success"><?= $this->session->flashdata('success') ?></div>
    <?php elseif ($this->session->flashdata('error')): ?>
        <div class="alert alert-danger"><?= $this->session->flashdata('error') ?></div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="post" action="<?= base_url('user/rekening') ?>">
                <div class="mb-3">
                    <label for="no_rekening" class="form-label">Nomor Rekening</label>
                    <input type="text" name="no_rekening" id="no_rekening"
                           class="form-control"
                           placeholder="Masukkan nomor rekening Anda"
                           value="<?= isset($rekening['no_rekening']) ? $rekening['no_rekening'] : '' ?>" required>
                </div>
                <button type="submit" class="btn btn-primary">
                    <?= isset($rekening['no_rekening']) ? 'Perbarui Nomor Rekening' : 'Simpan Nomor Rekening' ?>
                </button>
            </form>
        </div>
    </div>
</div>
