<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

    <div class="flex h-screen bg-gray-200">
        <div class="w-64 bg-gray-800 text-white">
            <div class="p-4 text-xl font-bold">Admin Panel</div>
            <nav class="mt-10">
                <a href="<?= base_url('admin/dashboard') ?>" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700">Dashboard</a>
                <a href="<?= base_url('home/logout') ?>" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700">Logout</a>
            </nav>
        </div>

        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="bg-white shadow p-4">
                <h1 class="text-xl font-bold">Dashboard</h1>
            </header>
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-200 p-6">
                <?php $this->load->view($view_name); // Ini akan memuat konten dinamis ?>
            </main>
        </div>
    </div>

</body>
</html>
