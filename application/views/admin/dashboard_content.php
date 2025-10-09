<div class="bg-white p-6 rounded-lg shadow-lg">
    <h2 class="text-2xl font-bold mb-4">Persetujuan Agen</h2>

    <?php if($this->session->flashdata('success')): ?>
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
        <?= $this->session->flashdata('success'); ?>
    </div>
    <?php endif; ?>

    <table class="w-full">
        <thead>
            <tr class="bg-gray-200 text-left">
                <th class="py-2 px-4">Nama</th>
                <th class="py-2 px-4">Email</th>
                <th class="py-2 px-4">Wilayah</th>
                <th class="py-2 px-4">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if(empty($pending_agents)): ?>
                <tr>
                    <td colspan="4" class="text-center py-4">Tidak ada pendaftaran agen baru.</td>
                </tr>
            <?php else: ?>
                <?php foreach($pending_agents as $agent): ?>
                <tr class="border-b">
                    <td class="py-2 px-4"><?= $agent['name']; ?></td>
                    <td class="py-2 px-4"><?= $agent['email']; ?></td>
                    <td class="py-2 px-4"><?= $agent['wilayah']; ?></td>
                    <td class="py-2 px-4">
                        <a href="<?= base_url('admin/approve_agent/' . $agent['id_agent']) ?>" class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600">Setujui</a>
                        <a href="<?= base_url('admin/reject_agent/' . $agent['id_agent']) ?>" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">Tolak</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>