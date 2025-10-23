<div class="card shadow-sm border-0">
    <div class="card-body">
        <h5 class="card-title fw-bold mb-3">Manajemen Harga Sampah</h5>
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