<div class="container mx-auto p-6">
    <h2 class="text-2xl font-bold mb-4">Manajemen Agen</h2>
    
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
                    <th class="px-4 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Email</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Wilayah</th>
                    <th class="px-4 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                    <th class="px-4 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($agents)): foreach($agents as $agent): ?>
                    <tr>
                        <td class="px-4 py-2 border-b border-gray-200 bg-white text-sm"><?= htmlspecialchars($agent['nama']); ?></td>
                        <td class="px-5 py-2 border-b border-gray-200 bg-white text-sm"><?= htmlspecialchars($agent['email']); ?></td>
                        <td class="px-5 py-2 border-b border-gray-200 bg-white text-sm"><?= htmlspecialchars($agent['wilayah']); ?></td>
                        <td class="px-4 py-2 border-b border-gray-200 bg-white text-sm">
                            <form action="<?= base_url('admin/update_agent_status'); ?>" method="POST">
                                <input type="hidden" name="id_agent" value="<?= $agent['id_agent']; ?>">
                                <select name="status" onchange="this.form.submit()" class="p-2 rounded border <?= $agent['status'] == 'aktif' ? 'bg-green-100' : 'bg-yellow-100'; ?>">
                                    <option value="aktif" <?= $agent['status'] == 'aktif' ? 'selected' : ''; ?>>Aktif</option>
                                    <option value="pending" <?= $agent['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="nonaktif" <?= $agent['status'] == 'nonaktif' ? 'selected' : ''; ?>>Nonaktif</option>
                                </select>
                            </form>
                        </td>
                        <td class="px-4 py-2 border-b border-gray-200 bg-white text-sm">
                            <a href="<?= base_url('admin/edit_user/' . $agent['id_user']); ?>" class="text-blue-600 hover:text-blue-900 mr-3">Edit</a>
                            <a href="<?= base_url('admin/delete_user/' . $agent['id_user']); ?>" class="text-red-600 hover:text-red-900" onclick="return confirm('Peringatan: Menghapus data user juga akan menghapus data agen/nasabah terkait. Yakin ingin menghapus user ini?');">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; else: ?>
                    <tr>
                        <td colspan="6" class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-center">Tidak ada data agen.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>