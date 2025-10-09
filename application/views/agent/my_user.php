<div class="container-fluid">
  <h3 class="fw-bold mb-3">My Users</h3>
  <p class="text-muted">Overview of users you manage</p>

  <!-- Stats Cards -->
  <div class="row mb-4">
    <div class="col-md-3">
      <div class="card shadow-sm text-center p-3">
        <h5><?= $stats['total_users']; ?></h5>
        <p class="text-muted">Total Users</p>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card shadow-sm text-center p-3">
        <h5><?= $stats['active_users']; ?></h5>
        <p class="text-muted">Active Users</p>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card shadow-sm text-center p-3">
        <h5><?= $stats['unpaid_users']; ?></h5>
        <p class="text-muted">Unpaid Users</p>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card shadow-sm text-center p-3">
        <h5><?= $stats['new_users']; ?></h5>
        <p class="text-muted">New Users</p>
      </div>
    </div>
  </div>

  <!-- User List -->
  <div class="card shadow-sm">
    <div class="card-header bg-light fw-bold">User List</div>
    <div class="card-body table-responsive">
      <table class="table table-striped">
        <thead>
          <tr>
            <th>#</th>
            <th>Name</th>
            <th>Email</th>
            <th>Status</th>
            <th>Joined</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($users)) : ?>
            <?php $i = 1; foreach ($users as $user) : ?>
              <tr>
                <td><?= $i++; ?></td>
                <td><?= $user['name']; ?></td>
                <td><?= $user['email']; ?></td>
                <td>
                  <?php if ($user['status'] == 'active') : ?>
                    <span class="badge bg-success">Active</span>
                  <?php elseif ($user['status'] == 'unpaid') : ?>
                    <span class="badge bg-warning text-dark">Unpaid</span>
                  <?php elseif ($user['status'] == 'new') : ?>
                    <span class="badge bg-info text-dark">New</span>
                  <?php else : ?>
                    <span class="badge bg-secondary"><?= ucfirst($user['status']); ?></span>
                  <?php endif; ?>
                </td>
                <td><?= $user['created_at']; ?></td>
              </tr>
            <?php endforeach; ?>
          <?php else : ?>
            <tr>
              <td colspan="5" class="text-center">No users found.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
