<h3 class="fw-bold mb-3">Transaction History</h3>
<p class="text-muted">Track your waste collections, points earned, and redemptions</p>

<div class="row mb-4">
  <div class="col-md-4">
    <div class="card shadow-sm p-3 text-center">
      <h6>Total Earnings</h6>
      <h4 class="fw-bold">$141.60</h4>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card shadow-sm p-3 text-center">
      <h6>Points Balance</h6>
      <h4 class="fw-bold">322</h4>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card shadow-sm p-3 text-center">
      <h6>Total Weight</h6>
      <h4 class="fw-bold">19.3 kg</h4>
    </div>
  </div>
</div>

<input type="text" class="form-control mb-3" placeholder="Search transactions by ID, agent, or waste type...">

<div class="btn-group mb-3">
  <button class="btn btn-dark">All</button>
  <button class="btn btn-outline-secondary">Completed</button>
  <button class="btn btn-outline-secondary">Pending</button>
</div>

<div class="list-group">
  <?php foreach($transactions as $t): ?>
    <div class="list-group-item d-flex justify-content-between align-items-center">
      <div>
        <h6 class="fw-bold mb-1"><?=$t['id']?> <small class="text-muted"><?=$t['date']?></small></h6>
        <p class="mb-0 text-muted">Type & Agent: <?=$t['waste_type']?> - <?=$t['agent']?></p>
        <small>Weight: <?=$t['weight']?> | Location: <?=$t['location']?></small>
      </div>
      <div class="text-end">
        <p class="mb-1 fw-semibold">+<?=$t['points']?> pts <br> $<?=$t['earnings']?></p>
        <span class="badge <?=($t['status']=='Completed'?'bg-success':'bg-warning text-dark')?>"><?=$t['status']?></span>
      </div>
    </div>
  <?php endforeach; ?>
</div>
