<div class="container mt-4">
  <!-- Header -->
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold">Profile Settings</h4>
    <a href="#" class="btn btn-dark btn-sm">
      <i class="bi bi-pencil-square"></i> Edit Profile
    </a>
  </div>
  <p class="text-muted">Manage your account information and preferences</p>

  <!-- Profile Card -->
  <div class="card shadow-sm mb-4">
    <div class="card-body text-center">
      <div class="rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center"
           style="width: 100px; height: 100px; background: linear-gradient(135deg,#0f9,#09f); color: #fff; font-size: 32px; font-weight: bold;">
        <?= strtoupper(substr($user['name'],0,2)); ?>
      </div>
      <h5 class="mb-0"><?= $user['name']; ?></h5>
      <p class="text-muted"><?= $user['role']; ?></p>
      <span class="badge bg-success">Verified User</span>
      <div class="mt-3">
        <p class="mb-1"><i class="bi bi-envelope"></i> <?= $user['email']; ?></p>
        <p class="mb-1"><i class="bi bi-telephone"></i> <?= $user['phone']; ?></p>
        <p class="mb-1"><i class="bi bi-geo-alt"></i> <?= $user['address']; ?></p>
      </div>
      <hr>
      <p class="text-muted"><?= $user['bio']; ?></p>
    </div>
  </div>

  <!-- Statistics -->
  <div class="row text-center mt-4">
    <div class="col-md-3 mb-3">
      <div class="card shadow-sm">
        <div class="card-body">
          <h5 class="fw-bold"><?= $stats['collections']; ?></h5>
          <p class="text-muted">Collections</p>
        </div>
      </div>
    </div>
    <div class="col-md-3 mb-3">
      <div class="card shadow-sm">
        <div class="card-body">
          <h5 class="fw-bold"><?= $stats['points']; ?></h5>
          <p class="text-muted">Total Points</p>
        </div>
      </div>
    </div>
    <div class="col-md-3 mb-3">
      <div class="card shadow-sm">
        <div class="card-body">
          <h5 class="fw-bold"><?= $user['member_since']; ?></h5>
          <p class="text-muted">Member Since</p>
        </div>
      </div>
    </div>
    <div class="col-md-3 mb-3">
      <div class="card shadow-sm">
        <div class="card-body">
          <h5 class="fw-bold"><?= $stats['waste_collected']; ?>kg</h5>
          <p class="text-muted">Waste Collected</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Tabs Navigation -->
  <ul class="nav nav-tabs mb-3" id="profileTabs" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link active" id="personal-tab" data-bs-toggle="tab" data-bs-target="#personal" type="button" role="tab">
        Personal Info
      </button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="achievements-tab" data-bs-toggle="tab" data-bs-target="#achievements" type="button" role="tab">
        Achievements
      </button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="settings-tab" data-bs-toggle="tab" data-bs-target="#settings" type="button" role="tab">
        Settings
      </button>
    </li>
  </ul>

  <!-- Tabs Content -->
  <div class="tab-content" id="profileTabsContent">
    <!-- Personal Info -->
    <div class="tab-pane fade show active" id="personal" role="tabpanel">
      <div class="card shadow-sm">
        <div class="card-body">
          <h6 class="fw-bold mb-3">Personal Information</h6>
          <p class="text-muted">Update your personal details and contact information</p>

          <div class="mb-2">
              <label class="form-label">Full Name</label>
              <input type="text" class="form-control" value="<?= $user['name']; ?>" readonly>
          </div>

          <div class="mb-2">
            <label class="form-label">Email Address</label>
            <input type="email" class="form-control" value="<?= $user['email']; ?>" readonly>
          </div>

          <div class="mb-2">
            <label class="form-label">Phone Number</label>
            <input type="text" class="form-control" value="<?= $user['phone']; ?>" readonly>
          </div>

          <div class="mb-2">
            <label class="form-label">Address</label>
            <input type="text" class="form-control" value="<?= $user['address']; ?>" readonly>
          </div>

          <div class="mb-2">
            <label class="form-label">Bio</label>
            <textarea class="form-control" rows="2" readonly><?= $user['bio']; ?></textarea>
          </div>
        </div>
      </div>
    </div>

    <!-- Achievements -->
    <div class="tab-pane fade" id="achievements" role="tabpanel">
      <div class="card shadow-sm">
        <div class="card-body">
          <h6 class="fw-bold mb-3">Achievements</h6>
          <p class="text-muted">Your rewards, milestones, and certifications</p>
          <!-- Dummy Example -->
          <ul>
            <li>Top Collector - March 2024</li>
            <li>Eco-Friendly Badge</li>
            <li>100kg Waste Milestone</li>
          </ul>
        </div>
      </div>
    </div>

    <!-- Settings -->
    <div class="tab-pane fade" id="settings" role="tabpanel">
      <div class="card shadow-sm">
        <div class="card-body">
          <h6 class="fw-bold mb-3">Account Settings</h6>
          <p class="text-muted">Manage your preferences and security</p>
          <button class="btn btn-outline-danger btn-sm">
            <i class="bi bi-box-arrow-right"></i> Logout
          </button>
        </div>
      </div>
    </div>
  </div>

</div>
