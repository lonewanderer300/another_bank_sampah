<div class="bg-white p-6 rounded-lg shadow-lg">
    <h2 class="text-2xl font-bold mb-6">Profil Saya</h2>

    <?php if($this->session->flashdata('success')): ?>
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
            <p><?= $this->session->flashdata('success'); ?></p>
        </div>
    <?php endif; ?>
    <?php if($this->session->flashdata('error')): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
            <p><?= $this->session->flashdata('error'); ?></p>
        </div>
    <?php endif; ?>

    <form action="<?= base_url('agent/profile') ?>" method="POST">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h3 class="text-lg font-semibold border-b pb-2 mb-4">Informasi Akun</h3>
                <div class="mb-4">
                    <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Nama Lengkap:</label>
                    <input type="text" name="name" id="name" value="<?= htmlspecialchars($profile['name']); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                </div>
                <div class="mb-4">
                    <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email:</label>
                    <input type="email" name="email" id="email" value="<?= htmlspecialchars($profile['email']); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                </div>
                <div class="mb-4">
                    <label for="phone" class="block text-gray-700 text-sm font-bold mb-2">No. Telepon:</label>
                    <input type="text" name="phone" id="phone" value="<?= htmlspecialchars($profile['phone']); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                </div>
                 <div class="mb-4">
                    <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Password Baru (Opsional):</label>
                    <input type="password" name="password" id="password" placeholder="Kosongkan jika tidak ingin diubah" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                </div>
            </div>

            <div>
                <h3 class="text-lg font-semibold border-b pb-2 mb-4">Informasi Bank Sampah</h3>
                <div class="mb-4">
                    <label for="wilayah" class="block text-gray-700 text-sm font-bold mb-2">Wilayah:</label>
                    <input type="text" name="wilayah" id="wilayah" value="<?= htmlspecialchars($profile['wilayah']); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                </div>
                <div class="mb-4">
                    <label for="latitude" class="block text-gray-700 text-sm font-bold mb-2">Latitude:</label>
                    <input type="text" name="latitude" id="latitude" value="<?= htmlspecialchars($profile['latitude']); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                </div>
                <div class="mb-4">
                    <label for="longitude" class="block text-gray-700 text-sm font-bold mb-2">Longitude:</label>
                    <input type="text" name="longitude" id="longitude" value="<?= htmlspecialchars($profile['longitude']); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                </div>
                <p class="text-xs text-gray-500">Anda bisa mendapatkan Latitude & Longitude dari Google Maps dengan klik kanan pada lokasi Anda.</p>
            </div>
        </div>

        <div class="mt-8 flex justify-end">
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>