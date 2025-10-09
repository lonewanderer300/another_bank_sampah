<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: #f8f9fa;
      margin: 0;
      padding: 0;
      overflow-x: hidden; /* cegah scroll horizontal */
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
    .sidebar .nav-link {
      color: #333;
      font-weight: 500;
      margin-bottom: 8px;
    }
    .sidebar .nav-link.active {
      color: #198754;
      font-weight: 600;
    }
    .content {
      margin-left: 260px;
      padding: 30px;
      max-width: calc(100% - 260px); /* biar pas sesuai sisa layar */
    }
  </style>
</head>
<body>
  <!-- Sidebar -->
  <div class="sidebar">
    <h4 class="fw-bold mb-4">Garbage Bank</h4>

    <!-- User Info -->
    <div class="d-flex align-items-center mb-4">
      <div class="rounded-circle bg-primary text-white d-flex justify-content-center align-items-center"
           style="width:40px; height:40px;">
        <?= isset($user['name']) ? substr($user['name'], 0, 1) : 'G' ?>
      </div>
      <div class="ms-2">
        <p class="mb-0 fw-semibold"><?= isset($user['name']) ? $user['name'] : 'Guest' ?></p>
        <small class="text-muted"><?= isset($user['role']) ? $user['role'] : '' ?></small>
      </div>
    </div>

    <!-- Navigation -->
    <ul class="nav flex-column">
      <li class="nav-item mb-2">
        <a class="nav-link <?=($page=='dashboard'?'active':'')?>" href="<?=site_url('dashboard')?>">Dashboard</a>
      </li>
      <li class="nav-item mb-2">
        <a class="nav-link <?=($page=='waste_banks'?'active':'')?>" href="<?=site_url('waste_banks')?>">Waste Banks</a>
      </li>
      <li class="nav-item mb-2">
        <a class="nav-link <?=($page=='transactions'?'active':'')?>" href="<?=site_url('transactions')?>">Transactions</a>
      </li>
      <li class="nav-item mb-2">
        <a class="nav-link <?=($page=='profile'?'active':'')?>" href="<?=site_url('profile')?>">Profile</a>
      </li>
      <li class="nav-item mt-4">
        <a class="nav-link text-danger" href="<?=site_url('logout')?>">Logout</a>
      </li>
    </ul>
  </div>

  <!-- Content -->
  <div class="content">
    <div class="container-fluid">
      <?php $this->load->view('user/'.$page); ?>
    </div>
  </div>
</body>
</html>
