<div class="card shadow-sm border-0">
    <div class="card-body">
        <h5 class="card-title fw-bold mb-3">Manajemen Harga Sampah</h5>
		<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0">Manajemen Harga Sampah</h5>
    <button class="btn btn-success btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#addWasteForm" aria-expanded="false">
        + Tambah Jenis Sampah
    </button>
</div>

<div class="collapse mb-4" id="addWasteForm">
    <div class="card card-body">
        <form action="<?= base_url('admin/waste_prices') ?>" method="post" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Nama Jenis Sampah</label>
                <input type="text" name="nama_jenis" class="form-control" placeholder="Contoh: Kardus" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Kategori</label>
                <select name="id_kategori" class="form-select" required>
                    <option value="">-- Pilih Kategori --</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id_kategori']; ?>"><?= ucfirst($cat['nama_kategori']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Satuan</label>
                <input type="text" name="satuan" value="kg" class="form-control" required>
            </div>
            <div class="col-md-2">
                <label class="form-label">Harga Awal (Rp)</label>
                <input type="number" name="harga" class="form-control" placeholder="1500" required>
            </div>
            <div class="col-md-1 d-flex align-items-end">
                <button type="submit" name="add_waste_type" value="true" class="btn btn-primary w-100">Tambah</button>
            </div>
        </form>
    </div>
</div>

        <div class="table-responsive">
            <table class="table align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Jenis Sampah</th>
                        <th>Satuan</th>
                        <th>Harga Saat Ini (Rp)</th>
                        <th>Update Terakhir</th>
                        <th style="width: 250px;">Update Harga Baru</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($waste_types as $type): ?>
                    <tr>
                        <td><?= html_escape($type['nama_jenis']); ?></td>
                        <td>/ <?= html_escape($type['satuan']); ?></td>
                        <td><?= number_format($type['harga'] ?? 0, 0, ',', '.'); ?></td>
                        <td><?= $type['tanggal_update'] ? date('d M Y', strtotime($type['tanggal_update'])) : '-'; ?></td>
                        <td>
                            <form action="<?= base_url('admin/waste_prices') ?>" method="post" class="d-flex">
                                <input type="hidden" name="id_jenis" value="<?= $type['id_jenis']; ?>">
                                <input type="number" name="harga" class="form-control form-control-sm me-2" placeholder="contoh: 1500" required>
                                <button type="submit" name="update_price" value="true" class="btn btn-sm btn-primary">Simpan</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
