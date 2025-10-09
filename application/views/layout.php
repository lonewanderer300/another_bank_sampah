<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= isset($title) ? $title : "Garbage Bank" ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
</head>
<body class="bg-gray-100 flex flex-col min-h-screen">
  <!-- Main content -->
  <main class="flex-grow">
    <?php $this->load->view($content); ?>
  </main>

  <!-- Footer -->
  <?php if (file_exists(APPPATH.'views/partials/footer.php')) $this->load->view('partials/footer'); ?>
</body>
</html>
