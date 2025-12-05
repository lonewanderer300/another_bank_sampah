<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background-color: #f8f9fa; }
        .sidebar {
            background-color: #ffffff;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            padding-top: 20px;
            border-right: 1px solid #dee2e6;
        }
        .sidebar .nav-link {
            color: #495057;
            padding: 10px 20px;
            margin: 5px 0;
            border-radius: 0.25rem;
        }
        .sidebar .nav-link.active,
        .sidebar .nav-link:hover {
            background-color: #dc3545; /* Warna merah untuk admin */
            color: #ffffff;
        }
        .sidebar .nav-link .bi { margin-right: 10px; }
        .sidebar-header {
            padding: 0 20px 20px 20px;
            font-weight: bold;
            font-size: 1.5rem;
            color: #343a40;
        }
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }
        .logout-link {
            position: absolute;
            bottom: 20px;
            width: calc(100% - 40px);
            margin: 0 20px;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <h3 class="sidebar-header">Admin Panel</h3>
    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link <?= uri_string() == 'admin/dashboard' ? 'active' : '' ?>" href="<?= base_url('admin/dashboard') ?>">
                <i class="bi bi-grid-1x2-fill"></i> Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= uri_string() == 'admin/waste_prices' ? 'active' : '' ?>" href="<?= base_url('admin/waste_prices') ?>">
                <i class="bi bi-tags-fill"></i> Harga Sampah
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= uri_string() == 'admin/manage_agents' ? 'active' : '' ?>" href="<?= base_url('admin/manage_agents') ?>">
                <i class="bi bi-shop"></i> Manajemen Agen
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= uri_string() == 'admin/manage_users' ? 'active' : '' ?>" href="<?= base_url('admin/manage_users') ?>">
                <i class="bi bi-people-fill"></i> Manajemen Nasabah
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= uri_string() == 'admin/manage_transactions' ? 'active' : '' ?>" href="<?= base_url('admin/manage_transactions') ?>">
                <i class="bi bi-receipt"></i> Manajemen Transaksi
            </a>
        </li>
		<li class="nav-item">
        <a class="nav-link <?= uri_string() == 'admin/manage_iuran' ? 'active' : '' ?>" href="<?= base_url('admin/manage_iuran') ?>">
            <i class="bi bi-cash-stack"></i>Master Manajemen Iuran
        </a>
        </li>
		<li class="nav-item">
        <a class="nav-link <?= uri_string() == 'admin/iuran_view' ? 'active' : '' ?>" href="<?= base_url('admin/view_iuran') ?>">
            <i class="bi bi-cash-stack"></i> Manajemen Iuran
        </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= uri_string() == 'admin/laporan_transaksi' ? 'active' : '' ?>" href="<?= base_url('admin/laporan_transaksi') ?>">
                <i class="bi bi-file-earmark-text"></i> Laporan Transaksi
            </a>
        </li>

    </ul>
    <a href="<?= base_url('admin/logout') ?>" class="btn btn-outline-danger logout-link"><i class="bi bi-box-arrow-right"></i> Logout</a>
</div>

<div class="main-content">
    <?php if (isset($view_name)) { $this->load->view($view_name); } ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
