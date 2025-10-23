<h4 class="fw-bold mb-4">Dashboard Agen</h4>

<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-body d-flex align-items-center">
                <div class="bg-success text-white rounded-3 p-3 me-3"><i class="bi bi-people-fill fs-4"></i></div>
                <div>
                    <h6 class="text-muted">Total Nasabah</h6>
                    <h4 class="fw-bold mb-0"><?= $total_customers; ?> Orang</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-body d-flex align-items-center">
                <div class="bg-primary text-white rounded-3 p-3 me-3"><i class="bi bi-receipt fs-4"></i></div>
                <div>
                    <h6 class="text-muted">Total Transaksi</h6>
                    <h4 class="fw-bold mb-0"><?= $total_transactions; ?> Kali</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-body d-flex align-items-center">
                <div class="bg-info text-white rounded-3 p-3 me-3"><i class="bi bi-trash3-fill fs-4"></i></div>
                <div>
                    <h6 class="text-muted">Sampah Terkumpul</h6>
                    <h4 class="fw-bold mb-0"><?= number_format($total_waste, 2); ?> kg</h4>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body">
        <h5 class="card-title fw-bold mb-3">Transaksi Terbaru</h5>
        </div>
</div>