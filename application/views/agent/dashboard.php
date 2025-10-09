<div class="container-fluid">
  <h3 class="fw-bold mb-3">Welcome back, <?= $agent['name']; ?>!</h3>
  <p class="text-muted">Here's your collection overview and today's route information.</p>

  <!-- Stats Cards -->
  <div class="row mb-4">
    <div class="col-md-3">
      <div class="card shadow-sm text-center p-3">
        <h5><?= $stats['regular_users']; ?></h5>
        <p class="text-muted">Regular Users</p>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card shadow-sm text-center p-3">
        <h5><?= $stats['unpaid_fees']; ?></h5>
        <p class="text-muted">Unpaid Fees</p>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card shadow-sm text-center p-3">
        <h5><?= $stats['service_rating']; ?></h5>
        <p class="text-muted">Service Rating (<?= $stats['reviews']; ?> reviews)</p>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card shadow-sm text-center p-3">
        <h5>$<?= $stats['monthly_earnings']; ?></h5>
        <p class="text-muted">Monthly Earnings<br><small>+ $<?= $stats['earnings_this_week']; ?> this week</small></p>
      </div>
    </div>
  </div>

  <!-- Grafik Dummy -->
  <div class="card mb-4">
    <div class="card-header">
        Waste Collection Trends
    </div>
    <div class="card-body">
        <canvas id="wasteTrendChart" height="100"></canvas>
    </div>
</div>  

  <!-- Recent Activity -->
  <div class="card shadow-sm">
    <div class="card-header bg-light fw-bold">Recent Activity</div>
    <div class="card-body">
      <p>Pickup - 5.2kg from Alice Johnson (Downtown) - 2024-01-15</p>
    </div>
  </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('wasteTrendChart').getContext('2d');
    const wasteTrendChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($waste_trends['months']); ?>,
            datasets: [
                {
                    label: 'Plastik',
                    data: <?php echo json_encode($waste_trends['plastik']); ?>,
                    borderColor: 'rgba(255, 99, 132, 1)',
                    fill: false
                },
                {
                    label: 'Kertas',
                    data: <?php echo json_encode($waste_trends['kertas']); ?>,
                    borderColor: 'rgba(54, 162, 235, 1)',
                    fill: false
                },
                {
                    label: 'Kaca',
                    data: <?php echo json_encode($waste_trends['kaca']); ?>,
                    borderColor: 'rgba(255, 206, 86, 1)',
                    fill: false
                },
                {
                    label: 'Logam',
                    data: <?php echo json_encode($waste_trends['logam']); ?>,
                    borderColor: 'rgba(75, 192, 192, 1)',
                    fill: false
                },
                {
                    label: 'Organik',
                    data: <?php echo json_encode($waste_trends['organik']); ?>,
                    borderColor: 'rgba(153, 102, 255, 1)',
                    fill: false
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Kg Sampah'
                    }
                }
            }
        }
    });
</script>
