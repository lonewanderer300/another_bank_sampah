<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('get_address_from_coords')) {
    function get_address_from_coords($latitude, $longitude)
    {
        if (empty($latitude) || empty($longitude)) {
            return null;
        }

        // URL API Nominatim
        $url = "https://nominatim.openstreetmap.org/reverse?format=json&lat={$latitude}&lon={$longitude}&zoom=18&addressdetails=1";

        // Penting: Set User-Agent agar tidak diblokir
        $options = [
            'http' => [
                'header' => "User-Agent: MyApp/1.0 (contact@myapp.com)\r\n"
            ]
        ];
        $context = stream_context_create($options);

        // Panggil API
        $response = @file_get_contents($url, false, $context);

        if ($response === FALSE) {
            return "Tidak dapat mengambil data lokasi";
        }

        $data = json_decode($response, true);

        // Jika ada nama jalan, tampilkan, jika tidak, tampilkan nama display
        if (isset($data['display_name'])) {
            return $data['display_name'];
        }

        return "Lokasi tidak ditemukan";
    }
}