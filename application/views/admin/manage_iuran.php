<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Iuran Master</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container py-4">

    <h2 class="mb-4">Master Iuran</h2>

    <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success"><?= $this->session->flashdata('success'); ?></div>
    <?php endif; ?>

    <!-- FORM TAMBAH ROW -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">Tambah Data Iuran Master</div>
        <div class="card-body">
            <form action="<?= base_url('admin/add_iuran_master'); ?>" method="post">

                <div class="row">
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Tipe Nasabah</label>
                        <select name="tipe_nasabah" class="form-control" required>
                            <option value="Perorangan">Perorangan</option>
                            <option value="Kelompok">Kelompok</option>
                        </select>
                    </div>

                    <div class="col-md-4 mb-2">
                        <label class="form-label">Jumlah Nasabah</label>
                        <input type="number" name="jumlah_nasabah" class="form-control" required>
                    </div>

                    <div class="col-md-4 mb-2">
                        <label class="form-label">Biaya (Rp)</label>
                        <input type="number" name="biaya" class="form-control" required>
                    </div>
                </div>

                <button class="btn btn-success mt-3">Tambah</button>
            </form>
        </div>
    </div>

    <!-- TABEL IURAN MASTER -->
    <div class="card">
        <div class="card-header bg-secondary text-white">Daftar Iuran Master</div>

        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Tipe Nasabah</th>
                        <th>Jumlah Nasabah</th>
                        <th>Biaya</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($iuran_master as $i): ?>
                        <tr>
                            <td><?= $i['id_master']; ?></td>
                            <td><?= $i['tipe_nasabah']; ?></td>
                            <td><?= $i['jumlah_nasabah']; ?></td>
                            <td>Rp <?= number_format($i['biaya'], 0, ',', '.'); ?></td>

                            <td>
                                <button 
                                    class="btn btn-warning btn-sm"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editModal<?= $i['id_master']; ?>">
                                    Edit
                                </button>
                            </td>
                        </tr>

                        <!-- MODAL EDIT -->
                        <div class="modal fade" id="editModal<?= $i['id_master']; ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">

                                    <form action="<?= base_url('admin/update_iuran_master'); ?>" method="post">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Update Biaya</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>

                                        <div class="modal-body">
                                            <input type="hidden" name="id_master" value="<?= $i['id_master']; ?>">

                                            <label class="form-label">Biaya (Rp)</label>
                                            <input type="number" name="biaya" class="form-control" value="<?= $i['biaya']; ?>" required>
                                        </div>

                                        <div class="modal-footer">
                                            <button class="btn btn-success">Simpan</button>
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        </div>

                                    </form>

                                </div>
                            </div>
                        </div>

                    <?php endforeach; ?>
                </tbody>

            </table>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
