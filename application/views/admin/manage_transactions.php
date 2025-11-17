<div class="container mx-auto p-6">
    <h2 class="text-2xl font-bold mb-4">Manajemen Transaksi</h2>

    <?php if($this->session->flashdata('success')): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <?= $this->session->flashdata('success'); ?>
        </div>
    <?php endif; ?>
    <?php if($this->session->flashdata('error')): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <?= $this->session->flashdata('error'); ?>
        </div>
    <?php endif; ?>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full leading-normal">
            <thead>
                <tr>
                    <th class="px-4 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tanggal</th>
                    <th class="px-4 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Bank Sampah</th>
                    <th class="px-4 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nasabah</th>
                    <th class="px-4 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Total Sampah (kg)</th>
                    <th class="px-4 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Total Nilai (Rp)</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($transactions)): foreach($transactions as $trans): ?>
                    <tr>
                        <td class="px-4 py-3 border-b border-gray-200 bg-white text-sm"><?= date('d M Y H:i', strtotime($trans['tanggal_setor'])); ?></td>
                        <td class="px-4 py-3 border-b border-gray-200 bg-white text-sm"><?= htmlspecialchars($trans['agent_name'] ?? 'N/A'); ?></td>
                        <td class="px-4 py-3 border-b border-gray-200 bg-white text-sm"><?= htmlspecialchars($trans['nasabah_name']); ?></td>
                        <td class="px-4 py-3 border-b border-gray-200 bg-white text-sm text-right"><?= number_format($trans['total_berat'], 2, ',', '.'); ?></td>
                        <td class="px-4 py-3 border-b border-gray-200 bg-white text-sm text-right font-bold text-green-600"><?= number_format($trans['total_poin'], 0, ',', '.'); ?></td>
                        
                        <td class="px-6 py-3 border-b border-gray-200 bg-white text-sm">
                            <button type="button" 
                                    onclick="window.location.href='<?= base_url('admin/edit_transaction/' . $trans['id_setoran']); ?>'" 
                                    class="btn btn-primary btn-sm me-2">
                                Edit
                            </button>
                            
                            <button type="button" 
                                    onclick="if(confirm('Yakin ingin menghapus transaksi ini? Saldo nasabah akan dikoreksi!')) { window.location.href='<?= base_url('admin/delete_transaction/' . $trans['id_setoran']); ?>'; }"
                                    class="btn btn-danger btn-sm">
                                Hapus
                            </button>
                        </td>
                    </tr>
                <?php endforeach; else: ?>
                    <tr>
                        <td colspan="6" class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-center">Tidak ada data transaksi.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>