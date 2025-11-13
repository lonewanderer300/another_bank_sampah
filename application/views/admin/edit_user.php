<?php
// Definisikan variabel User
$user_nama = isset($user['nama']) ? htmlspecialchars($user['nama']) : '';
$user_id = isset($user['id_user']) ? $user['id_user'] : '0';
$user_email = isset($user['email']) ? htmlspecialchars($user['email']) : '';
$user_phone = isset($user['phone']) ? htmlspecialchars($user['phone']) : '';
$user_role = isset($user['role']) ? $user['role'] : 'user';

// Definisikan variabel agent DENGAN HATI-HATI
// Ini memeriksa apakah $agent_data ada, apakah itu array, 
// DAN apakah 'wilayah' ada di dalamnya.
$agent_wilayah = ''; // Default-nya kosong
if (isset($agent_data) && is_array($agent_data) && isset($agent_data['wilayah'])) {
    $agent_wilayah = htmlspecialchars($agent_data['wilayah']);
}

// Definisikan wilayah_options DENGAN HATI-HATI
// Ini menggantikan '??' untuk keamanan
if (!isset($wilayah_options) || !is_array($wilayah_options)) {
    $wilayah_options = []; // Default-nya array kosong
}
?>

<link rel="stylesheet" href="<?= base_url('assets/css/form-custom.css'); ?>">


<div class="form-container-custom">
    
    <div class="form-header">
        <h2>Manajemen User</h2>
        <p>Edit detail untuk <?= htmlspecialchars($user_nama); ?></p>
    </div>

    <form action="<?= base_url('admin/edit_user/' . $user_id); ?>" method="POST">
        
        <div class="form-body">
            
            <div class="form-group">
                <label for="nama">Nama</label>
                <input type="text" name="nama" id="nama" value="<?= $user_nama; ?>" required>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" value="<?= $user_email; ?>" required>
            </div>

            <div class="form-group">
                <label for="phone">Telepon</label>
                <input type="text" name="phone" id="phone" value="<?= $user_phone; ?>">
            </div>

            <div class="form-group">
                <label for="role">Role</label>
                <select name="role" id="role">
                    <option value="user" <?= $user_role == 'user' ? 'selected' : ''; ?>>User (Nasabah)</option>
                    <option value="agent" <?= $user_role == 'agent' ? 'selected' : ''; ?>>Agent</option>
                    <option value="admin" <?= $user_role == 'admin' ? 'selected' : ''; ?>>Admin</option>
                </select>
            </div>

            <div class="form-group">
                <label for="password">Ganti Password</label>
                <input type="password" name="password" id="password" placeholder="●●●●●●●●">
                <p class="helper-text">Kosongkan jika tidak ingin diubah</p>
            </div>

            <div class="form-group" id="wilayah-field">
                <label for="wilayah">Wilayah</label>
                
                <select name="wilayah" id="wilayah">
                    <option value="">-- Pilih Wilayah --</option>
                    <?php foreach ($wilayah_options as $option): ?>
                        <?php 
                            // Tentukan apakah ini adalah opsi yang sedang dipilih
                            $selected = ($agent_wilayah == $option) ? 'selected' : ''; 
                        ?>
                        <option value="<?= htmlspecialchars($option); ?>" <?= $selected; ?>>
                            <?= htmlspecialchars($option); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                
                <p class="helper-text">Hanya berlaku untuk Role: Agent</p>
            </div>
            </div>
        
        <div class="form-footer">
            <a href="<?= base_url('admin/' . ($user_role == 'agent' ? 'manage_agents' : 'manage_users')); ?>" 
               class="btn btn-batal">
                BATAL
            </a>
            <button type="submit" class="btn btn-simpan">
                SIMPAN
            </button>
        </div>

    </form>
</div>

<script>
    const roleSelect = document.getElementById('role');
    const wilayahField = document.getElementById('wilayah-field');
    
    function toggleWilayahField() {
        // Fungsi ini mengecek nilai dropdown
        // Jika nilainya BUKAN 'agent', maka akan disembunyikan
        wilayahField.style.display = (roleSelect.value === 'agent') ? 'block' : 'none';
    }

    // Skrip ini dijalankan SAAT HALAMAN SELESAI DIMUAT
    document.addEventListener('DOMContentLoaded', toggleWilayahField); 
    
    // Skrip ini dijalankan SAAT DROPDOWN DIUBAH
    roleSelect.addEventListener('change', toggleWilayahField);
</script>