<h3 class="fw-bold mb-3">Waste Banks & Collection Points</h3>
<p class="text-muted">Find nearby waste collection centers and agencies in your area</p>

<div class="d-flex gap-2 mb-3">
  <input type="text" class="form-control" placeholder="Search by name or location">
  <button class="btn btn-dark">All</button>
  <button class="btn btn-outline-secondary">Plastic</button>
  <button class="btn btn-outline-secondary">Paper</button>
  <button class="btn btn-outline-secondary">Metal</button>
  <button class="btn btn-outline-secondary">Electronic</button>
  <button class="btn btn-outline-secondary">Organic</button>
</div>

<div class="row">
  <?php foreach($centers as $c): ?>
    <div class="col-md-4 mb-3">
      <div class="card shadow-sm">
        <img src="https://picsum.photos/400/200?random=<?=$c['id']?>" class="card-img-top" alt="<?=$c['name']?>">
        <div class="card-body">
          <h5 class="card-title fw-semibold"><?=$c['name']?></h5>
          <p class="card-text text-muted">Distance: <?=$c['distance']?> <br> Type: <?=$c['type']?></p>
          <button class="btn btn-outline-primary btn-sm">Details</button>
          <?php if($c['favorite']): ?>
            <span class="float-end text-danger">❤️</span>
          <?php endif; ?>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
</div>
