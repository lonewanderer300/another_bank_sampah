<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Agent Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: #f9fafb;
      font-family: Arial, sans-serif;
    }
    .sidebar {
      width: 260px;
      min-height: 100vh;
      background: #fff;
      border-right: 1px solid #e5e7eb;
      position: fixed;
      left: 0;
      top: 0;
      padding: 20px;
    }
    .content {
      margin-left: 280px;
      padding: 20px;
    }
    .nav-link.active {
      background: #dcfce7;
      border-radius: 8px;
      font-weight: bold;
      color: #16a34a !important;
    }
  </style>
</head>
<body>
  <!-- Sidebar -->
  <div class="sidebar">
    <h4 class="fw-bold mb-4">Garbage Bank</h4>

    <!-- Agent Info -->
    <div class="d-flex align-items-center mb-4">
      <img src="<?= $agent['avatar']; ?>" class="rounded-circle me-2" alt="agent" width="40" height="40">
      <div>
        <p class="mb-0 fw-semibold"><?= $agent['name']; ?></p>
        <small class="text-muted"><?= $agent['role']; ?></small>
      </div>
    </div>

    <!-- Navigation -->
    <nav class="mt-10">
    <a href="<?= base_url('agent/dashboard') ?>" class="flex items-center mt-4 py-2 px-6 bg-gray-700 bg-opacity-25 text-gray-100">
        <span class="mx-3">Dashboard</span>
    </a>
    <a href="<?= base_url('agent/my_user') ?>" class="flex items-center mt-4 py-2 px-6 text-gray-500 hover:bg-gray-700 hover:bg-opacity-25 hover:text-gray-100">
        <span class="mx-3">Nasabah</span>
    </a>
    <a href="<?= base_url('agent/transactions') ?>" class="flex items-center mt-4 py-2 px-6 text-gray-500 hover:bg-gray-700 hover:bg-opacity-25 hover:text-gray-100">
        <span class="mx-3">Transaksi</span>
    </a>
    <a href="<?= base_url('agent/profile') ?>" class="flex items-center mt-4 py-2 px-6 text-gray-500 hover:bg-gray-700 hover:bg-opacity-25 hover:text-gray-100">
        <span class="mx-3">Profil</span>
    </a>
    </nav>
  </div>

  <!-- Content -->
<div class="content">
  <?php 
    // Cek apakah ada variabel 'content' yang dikirim dari controller
    if (isset($content)) {
        $this->load->view($content);
    } else {
        echo "<p>Halaman tidak ditemukan.</p>";
    }
  ?>
</div>

</body>
</html>
