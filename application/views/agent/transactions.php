<div class="bg-white p-6 rounded-lg shadow-lg">
    <h2 class="text-2xl font-bold mb-4">Riwayat Transaksi Setoran</h2>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-200 text-left text-sm">
                    <th class="py-2 px-4">Tanggal</th>
                    <th class="py-2 px-4">Nama Nasabah</th>
                    <th class="py-2 px-4">Total</th>
                    <th class="py-2 px-4">Status</th>
                    <th class="py-2 px-4">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($transactions)): ?>
                    <tr>
                        <td colspan="5" class="text-center py-4 text-gray-500">Belum ada transaksi.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach($transactions as $trx): ?>
                    <tr class="border-b">
                        <td class="py-2 px-4"><?= date('d M Y', strtotime($trx['tanggal_setor'])); ?></td>
                        <td class="py-2 px-4"><?= htmlspecialchars($trx['user_name']); ?></td>
                        <td class="py-2 px-4">Rp <?= number_format($trx['total_setoran'], 0, ',', '.'); ?></td>
                        <td class="py-2 px-4">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                Selesai
                            </span>
                        </td>
                        <td class="py-2 px-4">
                            <a href="#" class="text-blue-500 hover:underline">Detail</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>