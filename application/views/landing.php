<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Garbage Bank</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
  <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>
</head>
<body class="bg-gray-100">

  <?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

  <div x-data="{ loginModal: false, registerModal: false, registerRole: '' }">

    <section class="relative bg-gray-900 text-white" style="
      background: url('https://images.unsplash.com/photo-1501594907352-04cda38ebc29') no-repeat center center;
      background-size: cover;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
    ">
      <div class="absolute inset-0 bg-black opacity-50"></div>
      <div class="relative z-10 text-center py-24 px-4">

        <?php if($this->session->flashdata('success')): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative max-w-md mx-auto mb-4" role="alert">
            <span class="block sm:inline"><?= $this->session->flashdata('success'); ?></span>
        </div>
        <?php endif; ?>
        
        <?php if($this->session->flashdata('error')): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative max-w-md mx-auto mb-4">
            <?= $this->session->flashdata('error'); ?>
        </div>
        <?php endif; ?>

        <h1 class="text-4xl md:text-5xl font-bold mb-4">Garbage Bank</h1>
        <p class="mb-6 max-w-2xl mx-auto">Transform your waste into value. Join our community of collectors and agents working together for a cleaner city.</p>
        <div class="flex flex-col sm:flex-row justify-center gap-4">
          <button @click="registerModal = true; registerRole = 'user'" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-semibold">Register as User</button>
          <button @click="registerModal = true; registerRole = 'agent'" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold">Register as Agent</button>
        </div>
        <p class="mt-4">
          Already have an account?
          <button @click="loginModal = true" class="underline font-semibold hover:text-green-300">Login</button>
        </p>
        <p class="mt-2">
          <a href="#impact" class="underline hover:text-green-300">View Statistics</a>
        </p>
      </div>
    </section>
    
    <?php $this->load->view('partials/modal_auth'); ?>
  </div>

  <!-- Impact Section -->
  <section id="impact" class="py-16 text-center">
    <h2 class="text-3xl font-bold mb-4">Our Impact So Far</h2>
    <p class="text-gray-600 mb-10">See how our community is making a difference in waste management and environmental sustainability.</p>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-4xl mx-auto">
      <div class="bg-white p-6 rounded-xl shadow text-center">
        <div class="text-3xl">♻️</div>
        <h3 class="text-xl font-bold"><?= number_format($summary['total_waste'], 2); ?> kg</h3>
        <p class="text-gray-600">Total Waste Collected</p>
        <button onclick="toggleDetail('wasteDetail')" class="mt-4 text-blue-600 underline">View Detail</button>
      </div>
      <div class="bg-white p-6 rounded-xl shadow text-center">
        <div class="text-3xl">👥</div>
        <h3 class="text-xl font-bold"><?= $summary['active_agents']; ?></h3>
        <p class="text-gray-600">Active Agents</p>
        <button onclick="toggleDetail('agentDetail')" class="mt-4 text-blue-600 underline">View Detail</button>
      </div>
    </div>
  </section>

  <!-- Waste Detail -->
  <div id="wasteDetail" class="hidden max-w-5xl mx-auto bg-white p-6 mt-6 rounded-xl shadow">
    <h3 class="text-xl font-bold mb-4">Waste Statistics</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div class="flex justify-center items-center">
        <div class="w-64 h-64">
          <canvas id="wastePieChart"></canvas>
        </div>
      </div>
      <canvas id="wasteBarChart"></canvas>
    </div>
  </div>

  <!-- Agent Detail -->
  <div id="agentDetail" class="hidden max-w-5xl mx-auto bg-white p-6 mt-6 rounded-xl shadow">
    <h3 class="text-xl font-bold mb-4">Agent Distribution</h3>
    <canvas id="agentBarChart"></canvas>
  </div>

  <!-- List Agen -->
  <section id="statistics" class="max-w-6xl mx-auto mt-10 bg-white p-6 rounded-xl shadow">
    <h3 class="text-2xl font-bold mb-4">Daftar Bank Sampah / Agen</h3>
    <table class="w-full border border-gray-200 rounded-lg overflow-hidden">
      <thead class="bg-gray-100">
        <tr>
          <th class="border px-4 py-2 text-left">Nama</th>
          <th class="border px-4 py-2 text-left">Wilayah</th>
          <th class="border px-4 py-2 text-center">Status</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($agents as $a): ?>
          <tr>
            <td class="border px-4 py-2"><?= $a['name']; ?></td>
            <td class="border px-4 py-2"><?= $a['area']; ?></td>
            <td class="border px-4 py-2 text-center"><?= ucfirst($a['status']); ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </section>

  <!-- Map -->
  <section class="max-w-6xl mx-auto mt-6 bg-white p-6 rounded-xl shadow">
    <h3 class="text-2xl font-bold mb-4">Peta Persebaran Agen</h3>
    <div id="map" class="w-full h-96 rounded-lg"></div>
  </section>

  <script>
    document.addEventListener('DOMContentLoaded', () => {

      // === TOMBOL DETAIL ===
      function toggleDetail(id) {
        const wasteDetail = document.getElementById("wasteDetail");
        const agentDetail = document.getElementById("agentDetail");

        if (id === "wasteDetail") {
          wasteDetail.classList.toggle("hidden");
          if (!wasteDetail.classList.contains("hidden")) wasteDetail.classList.add("block");
          agentDetail.classList.add("hidden");
        } else {
          agentDetail.classList.toggle("hidden");
          if (!agentDetail.classList.contains("hidden")) agentDetail.classList.add("block");
          wasteDetail.classList.add("hidden");
        }
      }
      window.toggleDetail = toggleDetail;

      // ==== Chart Data dari PHP ====
      const wasteLabels = <?= json_encode(array_column($waste_stats, 'name')); ?>;
      const wasteData = <?= json_encode(array_column($waste_stats, 'amount')); ?>;
      const months = <?= json_encode(array_column($monthly_stats, 'month')); ?>;
      const monthData = <?= json_encode(array_column($monthly_stats, 'amount')); ?>;

      // Chart Pie
      new Chart(document.getElementById("wastePieChart"), {
        type: "pie",
        data: {
          labels: wasteLabels,
          datasets: [{
            data: wasteData,
            backgroundColor: ["#f87171", "#60a5fa", "#34d399", "#fbbf24", "#d971f8ff"]
          }]
        },
        options: { responsive: true, maintainAspectRatio: false }
      });

      // Chart Bar
      new Chart(document.getElementById("wasteBarChart"), {
        type: "bar",
        data: {
          labels: months,
          datasets: [{
            label: "Total Waste (kg)",
            data: monthData,
            backgroundColor: "#34d399"
          }]
        }
      });

      // === MAP ===
      const defaultLat = <?= isset($agents[0]['latitude']) ? $agents[0]['latitude'] : -1.86667 ?>;
      const defaultLng = <?= isset($agents[0]['longitude']) ? $agents[0]['longitude'] : 114.73333 ?>;
      const map = L.map("map").setView([defaultLat, defaultLng], 10);
      L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", { maxZoom: 19 }).addTo(map);

      <?php foreach($agents as $a): ?>
        L.marker([<?= $a['lat']; ?>, <?= $a['lng']; ?>])
          .addTo(map)
          .bindPopup("<?= addslashes($a['name']); ?> - <?= addslashes($a['area']); ?>");
      <?php endforeach; ?>

      // Pastikan map muncul sempurna
      setTimeout(() => { map.invalidateSize(); }, 500);
    });
  </script>
</body>
</html>
