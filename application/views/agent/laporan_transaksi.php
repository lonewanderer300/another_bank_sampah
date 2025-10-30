<div class="container mt-4">
    <h3>Laporan Transaksi</h3>
    <form method="get" action="<?= base_url('agent/laporan_transaksi') ?>" class="row mb-3">
        <div class="col-md-3">
            <select name="bulan" class="form-control" required>
                <option value="">Pilih Bulan</option>
                <?php for($m=1; $m<=12; $m++): ?>
                    <option value="<?= $m ?>" <?= ($bulan == $m ? 'selected' : '') ?>><?= date('F', mktime(0,0,0,$m,1)) ?></option>
                <?php endfor; ?>
            </select>
        </div>
        <div class="col-md-3">
            <select name="tahun" class="form-control" required>
                <option value="">Pilih Tahun</option>
                <?php for($y=2024; $y<=date('Y'); $y++): ?>
                    <option value="<?= $y ?>" <?= ($tahun == $y ? 'selected' : '') ?>><?= $y ?></option>
                <?php endfor; ?>
            </select>
        </div>
        <div class="col-md-3">
            <button type="submit" class="btn btn-success"><i class="bi bi-search"></i> Tampilkan</button>
            <?php if (!empty($laporan)) : ?>
                <a href="<?= base_url("agent/export_excel?bulan={$bulan}&tahun={$tahun}") ?>" class="btn btn-primary"><i class="bi bi-file-earmark-excel"></i> Download Excel</a>
            <?php endif; ?>
        </div>
    </form>

    <?php if (!empty($laporan)): ?>
        <table class="table table-bordered table-striped">
            <thead class="table-success">
                <tr>
                    <th>NO</th>
                    <th>TANGGAL</th>
                    <th>NO REKENING</th>
                    <th>NAMA NASABAH</th>
                    <th>TIPE SAMPAH</th>
                    <th>JENIS</th>
                    <th>KODE</th>
                    <th>URAIAN BARANG</th>
                    <th>JUMLAH (Kg)</th>
                    <th>JUMLAH BOTOL (Biji)</th>
                    <th>HARGA SATUAN (Rp)</th>
                    <th>PENDAPATAN (Rp)</th>
                    <th>TARIK TUNAI (Rp)</th>
                    <th>SALDO AKHIR (Rp)</th>
                    <th>PETUGAS</th>
                </tr>
            </thead>
            <tbody>
                <?php $no=1; foreach($laporan as $row): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= $row['tanggal_setor'] ?></td>
                        <td><?= $row['no_rekening'] ?></td>
                        <td><?= $row['nama_nasabah'] ?></td>
                        <td><?= $row['tipe_sampah'] ?></td>
                        <td><?= $row['nama_kategori'] ?></td>
                        <td><?= $row['kode'] ?></td>
                        <td><?= $row['nama_jenis'] ?></td>
                        <td><?= $row['berat'] ?></td>
                        <td><?= $row['jumlah_botol'] ?></td>
                        <td><?= number_format($row['harga'],0,',','.') ?></td>
                        <td><?= number_format($row['pendapatan'],0,',','.') ?></td>
                        <td><?= number_format($row['tarik_tunai'],0,',','.') ?></td>
                        <td><?= number_format($row['saldo_akhir'],0,',','.') ?></td>
                        <td><?= $row['nama_petugas'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
