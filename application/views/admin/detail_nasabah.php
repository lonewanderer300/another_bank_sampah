<div class="card shadow-sm border-0 mt-4">
    <div class="card-body">

        <h5 class="card-title fw-bold mb-3">Detail Nasabah</h5>

        <p><strong>Nama:</strong> <?= $nasabah['nama'] ?></p>
        <p><strong>Email:</strong> <?= $nasabah['email'] ?></p>
        <p><strong>Telepon:</strong> <?= $nasabah['phone'] ?></p>
        <p><strong>Tipe Nasabah:</strong> <?= $nasabah['tipe_nasabah'] ?></p>
        <p><strong>Jumlah Anggota:</strong> <?= $nasabah['jumlah_nasabah'] ?></p>

        <a href="<?= base_url('admin/manage_iuran') ?>" class="btn btn-secondary mt-3">Kembali</a>
    </div>
</div>
