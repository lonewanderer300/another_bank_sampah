<div class="bg-white shadow-md rounded-lg overflow-hidden">
    <table class="min-w-full leading-normal">
        <thead>
            <tr>
                <th class="px-4 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama</th>
                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Email</th>
                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Telepon</th>
                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Bank Sampah</th>
                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($users)): foreach($users as $user): ?>
                <tr>
                    <td class="px-4 py-3 border-b border-gray-200 bg-white text-sm"><?= htmlspecialchars($user['nama']); ?></td>
                    <td class="px-5 py-3 border-b border-gray-200 bg-white text-sm"><?= htmlspecialchars($user['email']); ?></td>
                    <td class="px-5 py-3 border-b border-gray-200 bg-white text-sm"><?= htmlspecialchars($user['phone']); ?></td>
                    
                    <td class="px-5 py-3 border-b border-gray-200 bg-white text-sm">
                        <?php 
                        // Cek apakah 'nama_agent' ada dan tidak kosong (hasil dari JOIN)
                        if (!empty($user['nama_agent'])) {
                            
                            // Tampilkan Nama Agent
                            echo htmlspecialchars($user['nama_agent']);

                        } else {
                            // Jika tidak ada nama_agent (misal: user adalah Admin, atau belum memilih)
                            // Tampilkan Role aslinya sebagai fallback
                            $role = $user['role'];
                            $roleText = ucfirst(htmlspecialchars($role));
                            $colorClass = 'bg-green-100 text-green-700'; // Default (User)
                            
                            if ($role == 'admin') {
                                $colorClass = 'bg-red-100 text-red-700';
                            } else if ($role == 'agent') {
                                $colorClass = 'bg-blue-100 text-blue-700';
                            } else if ($role == 'user') {
                                // User biasa yang belum milih agent
                                $roleText = 'Belum Mendaftar';
                                $colorClass = 'bg-gray-100 text-gray-700';
                            }

                            echo '<span class="px-2 py-1 font-semibold leading-tight rounded-full ' . $colorClass . '">';
                            echo $roleText;
                            echo '</span>';
                        }
                        ?>
                    </td> 

                    <td class="px-5 py-2 border-b border-gray-200 bg-white text-sm">
                        <a href="<?= base_url('admin/edit_user/' . $user['id_user']); ?>" class="text-blue-600 hover:text-blue-900">Edit</a>
                        <a href="<?= base_url('admin/delete_user/' . $user['id_user']); ?>" class="text-red-600 hover:text-red-900 ml-3" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
                    </td>
                </tr>
            <?php endforeach; else: ?>
                <tr>
                    <td colspan="5" class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-center">Data tidak ditemukan.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>