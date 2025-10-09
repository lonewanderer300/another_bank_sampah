<h3 class="fw-bold">Welcome back, <?= $user['name']; ?>!</h3>
<p class="text-muted">Here's your waste collection summary and recent activity.</p>

<!-- Stats -->
<div class="row g-3 mb-4">
  <div class="col-md-3">
    <div class="card p-3 shadow-sm">
      <h6>Total Collections</h6>
      <h4 class="fw-bold"><?= $stats['total_collections']; ?></h4>
      <small class="text-success">+3 this month</small>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card p-3 shadow-sm">
      <h6>Points Earned</h6>
      <h4 class="fw-bold"><?= $stats['points']; ?></h4>
      <small class="text-success">+180 this week</small>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card p-3 shadow-sm">
      <h6>Active Requests</h6>
      <h4 class="fw-bold"><?= $stats['active_requests']; ?></h4>
      <small class="text-muted">Pending pickup</small>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card p-3 shadow-sm">
      <h6>Monthly Goal</h6>
      <h4 class="fw-bold"><?= $stats['monthly_goal']; ?>%</h4>
      <small class="text-muted"><?= 100 - $stats['monthly_goal']; ?>% remaining</small>
    </div>
  </div>
</div>

<!-- Recent Activity -->
<div class="card shadow-sm mb-4">
  <div class="card-body">
    <h5 class="fw-bold">Recent Activity</h5>
    <p class="text-muted mb-3">Your latest waste collections</p>
    <div class="list-group">
      <?php foreach($recent_activity as $act): ?>
        <div class="list-group-item d-flex justify-content-between align-items-center">
          <div>
            <strong>Collection</strong> - <?= $act['date']; ?>
          </div>
          <span><?= $act['amount']; ?> <small class="text-muted">by <?= $act['by']; ?></small></span>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<!-- Quick Actions -->
<div class="card shadow-sm">
  <div class="card-body">
    <h5 class="fw-bold">Quick Actions</h5>
    <p class="text-muted">Manage your waste collection</p>
    <div class="d-flex gap-3">
      <a href="#" class="btn btn-dark">Schedule Pickup</a>
      <a href="#" class="btn btn-outline-success">Find Waste Bank</a>
      <a href="#" class="btn btn-outline-warning">Redeem Points</a>
    </div>
  </div>
</div>
