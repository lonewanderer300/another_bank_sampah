<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="container-fluid">
    <h4 class="fw-bold mb-4">Dashboard Agen</h4>
    
    <div class="row mb-4">
        
        <div class="col-lg-3 col-md-4 mb-4">
            <div class="card shadow-sm border-0 text-white bg-primary h-100">
                <div class="card-body text-center">
                    <i class="bi bi-people-fill fs-1"></i>
                    <h1 class="display-4 fw-bold my-2"><?= $total_customers ?? 0; ?></h1>
                    <p class="lead">Total Nasabah Anda</p>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-4 mb-4">
            <div class="card shadow-sm border-0 text-white bg-warning h-100">
                <div class="card-body text-center">
                    <i class="bi bi-cash-stack fs-1"></i>
                    <h1 class="display-4 fw-bold my-2"><?= $unpaid_customers_agent ?? 0; ?></h1>
                    <p class="lead">Nasabah Belum Bayar Iuran</p>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-4 mb-4">
            <div class="card shadow-sm border-0 text-white bg-info h-100">
                <div class="card-body text-center">
                    <i class="bi bi-arrow-down-up fs-1"></i>
                    <h1 class="display-4 fw-bold my-2"><?= $total_transactions ?? 0; ?></h1>
                    <p class="lead">Total Transaksi</p>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-4 mb-4">
            <div class="card shadow-sm border-0 text-white bg-success h-100">
                <div class="card-body text-center">
                    <i class="bi bi-recycle fs-1"></i>
                    <h1 class="display-4 fw-bold my-2"><?= number_format($total_waste ?? 0, 2); ?> kg</h1>
                    <p class="lead">Total Sampah Terkumpul</p>
                </div>
            </div>
        </div>
    </div>
    
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h5 class="card-title fw-bold mb-3">5 Transaksi Terbaru</h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Nasabah</th>
                                <th>Berat Total</th>
                                <th>Poin/Saldo</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($recent_transactions)): ?>
                                <?php foreach ($recent_transactions as $t): ?>
                                <tr>
                                    <td><?= date('d M Y', strtotime($t['tanggal_setor'])); ?></td>
                                    <td><?= html_escape($t['customer_name']); ?></td>
                                    <td><?= number_format($t['total_berat'] ?? 0, 2); ?> kg</td>
                                    <td>Rp <?= number_format($t['total_poin'] ?? 0); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-3">Belum ada transaksi setoran.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>