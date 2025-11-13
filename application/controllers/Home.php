<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Home_model');
    }

    public function index()
    {
        // 🔹 Ambil data summary (DENGAN TOTAL CUSTOMERS)
        $data['summary'] = [
            'total_waste'     => $this->Home_model->get_total_waste(),
            'active_agents'   => $this->Home_model->get_active_agents(),
            'total_customers' => $this->Home_model->get_total_customers() // <-- DIKEMBALIKAN
        ];

        $data['list_wilayah'] = $this->Home_model->get_distinct_wilayah();

        // 🔹 Ambil statistik per jenis sampah (pie chart)
        $waste_stats = $this->Home_model->get_waste_by_type();
        $data['waste_stats'] = [];
        foreach ($waste_stats as $ws) {
            $data['waste_stats'][] = [
                'name'   => $ws['name'],
                'amount' => $ws['amount'] ? (float)$ws['amount'] : 0
            ];
        }

        // 🔹 Ambil statistik bulanan (bar chart)
        $monthly_stats = $this->Home_model->get_monthly_waste();
        $data['monthly_stats'] = [];
        foreach ($monthly_stats as $ms) {
            $data['monthly_stats'][] = [
                'month'  => $ms['month'],
                'amount' => $ms['amount'] ? (float)$ms['amount'] : 0
            ];
        }
        
        // 🔹 Ambil data persebaran agen untuk grafik
        $agent_distribution = $this->Home_model->get_agent_distribution_by_area();
        $data['agent_distribution_labels'] = json_encode(array_column($agent_distribution, 'wilayah'));
        $data['agent_distribution_data'] = json_encode(array_column($agent_distribution, 'total'));

        // 🔹 Ambil daftar agen untuk tabel dan peta
        $agents = $this->Home_model->get_agents();
        $data['agents'] = [];
        foreach ($agents as $a) {
            $agent_data = [
                'name'   => $a['name'],
                'area'   => $a['area'],
                'status' => $a['status'],
            ];

            if (!empty($a['latitude']) && !empty($a['longitude'])) {
                $agent_data['lat'] = (float)$a['latitude'];
                $agent_data['lng'] = (float)$a['longitude'];
            }
    
            $data['agents'][] = $agent_data;
        }

        // 🔹 DIKEMBALIKAN: Ambil data persebaran nasabah
        $customer_distribution = $this->Home_model->get_customer_distribution_by_area();
        $data['customer_distribution_labels'] = json_encode(array_column($customer_distribution, 'wilayah'));
        $data['customer_distribution_data'] = json_encode(array_column($customer_distribution, 'total'));

        // 🔹 BARU: Ambil data untuk fitur harga (dari file upload)
        $data['latest_prices'] = $this->Home_model->get_latest_prices_summary();
        $data['waste_categories'] = $this->Home_model->get_waste_categories();
        
        // Ambil data chart histori harga (unfiltered) untuk render awal
        $price_history = $this->Home_model->get_price_history_filtered(null, null, null);
        
        // Kirim data mentah $price_history ke view
        $data['price_history'] = $price_history; 

        // 🔹 Tambahan data untuk layout
        $data['title']   = "Garbage Bank - Home";
        $data['content'] = 'landing';

        // 🔹 Muat layout utama
        $this->load->view('layout', $data);
    }

    public function register()
    {
        $role = $this->input->post('role');
        
        $userData = [
            'nama' => $this->input->post('name'),
            'email' => $this->input->post('email'),
            'password' => password_hash($this->input->post('password'), PASSWORD_BCRYPT),
            'phone' => $this->input->post('phone'),
            'role' => $role
        ];

        $userId = $this->Home_model->insert_user($userData);

        if ($role === 'agent' && $userId) {
            // =========================
            // Jika role = agent
            // =========================
            $agentData = [
                'id_user' => $userId,
                'wilayah' => $this->input->post('wilayah'), // Ini akan tetap berfungsi
            ];
            $this->Home_model->insert_agent($agentData);
            
            $this->session->set_flashdata('success', 'Registrasi sebagai Agent berhasil! Akun Anda sedang menunggu persetujuan dari Admin.');
        
        } elseif ($role === 'user' && $userId) {
            // =========================
            // Jika role = user (nasabah perorangan)
            // =========================
            $nasabahData = [
                'id_users' => $userId,
                'tipe_nasabah' => 'Perorangan',
                'jumlah_nasabah' => 1
            ];
            $id_nasabah = $this->Home_model->insert_nasabah($nasabahData);

            // Tambahkan biaya iuran default (misalnya 18000)
            if ($id_nasabah) {
                $iuranData = [
                    'id_nasabah' => $id_nasabah,
                    'biaya' => 18000, // biaya iuran default
                    'deadline' => date('Y-m-d', strtotime('+1 month')), // contoh deadline 1 bulan dari sekarang
                    'status_iuran' => 'belum bayar'
                ];
                $this->Home_model->insert_iuran($iuranData);
            }

            $this->session->set_flashdata('success', 'Registrasi berhasil! Anda telah terdaftar sebagai Nasabah Perorangan.');
        
        } else {
            // Ini untuk 'admin' atau role lain jika ada
            $this->session->set_flashdata('success', 'Registrasi berhasil! Silakan login.');
        }

        redirect(base_url());
    }

    
    public function login()
    {
        $email = $this->input->post('email');
        $password = $this->input->post('password');
        $user = $this->Home_model->get_user_by_email($email);

        if ($user && password_verify($password, $user['password'])) {

            // --- Variabel untuk menyimpan agent_id ---
            $agent_id_to_session = null;

            if ($user['role'] == 'agent') {
                $agent_data = $this->Home_model->get_agent_status($user['id_user']); // Anda perlu memastikan fungsi ini mengembalikan 'id_agent'

                if (!$agent_data) {
                    $this->session->set_flashdata('error', 'Data agen tidak ditemukan.');
                    redirect(base_url());
                    return;
                }

                if ($agent_data['status'] != 'aktif') {
                    $message = ($agent_data['status'] == 'pending')
                        ? 'Akun Anda masih menunggu persetujuan Admin.'
                        : 'Akun Anda telah dinonaktifkan.';
                    $this->session->set_flashdata('error', $message);
                    redirect(base_url());
                    return;
                }

                if (isset($agent_data['id_agent'])) {
                    $agent_id_to_session = $agent_data['id_agent'];
                } else {
                    $this->session->set_flashdata('error', 'Kesalahan: ID Agen tidak ditemukan. Hubungi admin.');
                    redirect(base_url());
                    return;
                }

            }

            // --- Susun Session Data ---
            $session_data = [
                'user_id' => $user['id_user'],
                'name'    => $user['name'],
                'email'   => $user['email'],
                'role'    => $user['role'],
                'logged_in' => TRUE
            ];

            if ($user['role'] == 'agent' && $agent_id_to_session !== null) {
                $session_data['agent_id'] = $agent_id_to_session;
            }

            $this->session->set_userdata($session_data);

            // --- Redirect ---
            switch ($user['role']) {
                case 'admin':
                    redirect('admin/dashboard');
                    break;
                case 'agent':
                    redirect('agent/dashboard');
                    break;
                default: // default ke user
                    redirect('user/dashboard');
                    break;
            }

        } else {
            $this->session->set_flashdata('error', 'Email atau Password salah!');
            redirect(base_url());
        }
    }
    
    public function seed_agent_locations()
    {
        // ... (Fungsi ini tidak berubah) ...
        $agents_to_update = $this->db->where('latitude IS NULL')->get('agent')->result_array();

        $locations = [
            ['lat' => -1.6751, 'lng' => 114.8552], 
            ['lat' => -1.6923, 'lng' => 114.8711], 
            ['lat' => -1.6815, 'lng' => 114.8645], 
        ];

        if (empty($agents_to_update)) {
            echo "Tidak ada agen yang perlu diupdate. Semua sudah memiliki koordinat.";
            return;
        }

        echo "<h3>Memulai proses pengisian data koordinat...</h3>";
        $location_index = 0;

        foreach ($agents_to_update as $agent) {
            if (!isset($locations[$location_index])) {
                $location_index = 0;
            }

            $data_to_update = [
                'latitude' => $locations[$location_index]['lat'],
                'longitude' => $locations[$location_index]['lng']
            ];
            
            $this->db->where('id_agent', $agent['id_agent']);
            $this->db->update('agent', $data_to_update);
            
            echo "Agen ID #" . $agent['id_agent'] . " berhasil diupdate dengan koordinat: " . $data_to_update['latitude'] . ", " . $data_to_update['longitude'] . "<br>";
            
            $location_index++;
        }

        echo "<br><b>Proses selesai!</b> Anda sekarang bisa kembali ke halaman utama. Jangan lupa hapus fungsi ini dari controller.";
    }

	// FUNGSI BARU UNTUK FILTER HARGA (dari file upload)
	public function filter_price_chart()
	{
	    $category_id = $this->input->post('category_id');
	    $month = $this->input->post('month');
	    $year = $this->input->post('year');

	    $this->load->model('Home_model'); 
	    
	    $data = $this->Home_model->get_price_history_filtered($category_id, $month, $year);

	    $this->output
	        ->set_content_type('application/json')
	        ->set_output(json_encode($data));
	}

}
