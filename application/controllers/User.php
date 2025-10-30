<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model');

        // Pengecekan sesi: pastikan pengguna sudah login dan rolenya adalah 'user'
        if (!$this->session->userdata('logged_in') || $this->session->userdata('role') !== 'user') {
            $this->session->set_flashdata('error', 'Anda harus login sebagai User untuk mengakses halaman ini.');
            redirect(base_url());
        }
    }

    public function index()
    {
        redirect('user/dashboard');
    }

    public function dashboard()
    {
        $user_id = $this->session->userdata('user_id');
        
        // 1. Mengambil data utama
        $data['balance'] = $this->User_model->get_user_balance($user_id);
        $data['total_waste'] = $this->User_model->get_total_waste_by_user($user_id);
        $data['transactions'] = $this->User_model->get_user_transactions($user_id, 5); // Ambil 5 transaksi terakhir

        // 2. Mengambil data untuk grafik komposisi sampah
        $waste_summary = $this->User_model->get_waste_summary_by_user($user_id);
        
        // Memformat data agar siap digunakan oleh Chart.js
        $data['waste_labels'] = json_encode(array_column($waste_summary, 'waste_type'));
        $data['waste_data'] = json_encode(array_column($waste_summary, 'total_weight'));

        // 3. Logika untuk status iuran (contoh statis)
        // Di aplikasi nyata, Anda akan memeriksa status pembayaran dari database.
        // Untuk saat ini, kita buat contoh statis.
        $data['iuran'] = [
            'status' => 'paid', // Ganti menjadi 'unpaid' untuk melihat perbedaannya
            'amount' => 20000,
            'due_date' => '30 Oktober 2025'
        ];

        $data['view_name'] = 'user/dashboard'; 
        $this->load->view('user/layout', $data);
    }

    public function transactions()
    {
        $user_id = $this->session->userdata('user_id');

        // Mengambil data untuk kartu rekapitulasi
        $data['total_transactions'] = $this->User_model->count_user_transactions($user_id);
        $data['total_income'] = $this->User_model->get_total_income_by_user($user_id);
        $data['total_waste'] = $this->User_model->get_total_waste_by_user($user_id);

        // Mengambil daftar semua transaksi untuk ditampilkan di tabel
        $data['transactions'] = $this->User_model->get_user_transactions($user_id);
        
        $data['view_name'] = 'user/transactions';
        $this->load->view('user/layout', $data);
    }

    public function waste_banks()
    {
        $user_id = $this->session->userdata('user_id');
        $user_profile = $this->User_model->get_user_profile($user_id);

        // Ambil ID agent pilihan user (jika ada) untuk dikirim ke view
        $data['selected_agent_id'] = $user_profile['id_agent_pilihan'] ?? null;

        // Cek apakah pengguna sudah punya lokasi ATAU sudah memilih agent
        if (empty($user_profile['latitude']) || empty($user_profile['longitude'])) {
             // Jika BELUM punya lokasi DAN BELUM memilih agent -> Tampilkan semua agent
             if (!$data['selected_agent_id']) {
                  $this->session->set_flashdata('info', 'Atur lokasi Anda di profil untuk melihat bank sampah terdekat. Menampilkan semua bank sampah.');
                  $data['agents'] = $this->User_model->get_all_active_agents();
             }
             // Jika BELUM punya lokasi TAPI SUDAH memilih agent -> Tampilkan agent pilihan
             else {
                 $data['agents'] = $this->User_model->get_one_agent($data['selected_agent_id']);
                 // Handle jika agent pilihan tidak valid/aktif
                 if (empty($data['agents'])) {
                     $this->session->set_flashdata('error', 'Bank sampah pilihan Anda tidak ditemukan/aktif. Pilihan direset.');
                     $this->User_model->set_chosen_agent($user_id, null);
                     $data['selected_agent_id'] = null;
                     $data['agents'] = $this->User_model->get_all_active_agents(); // Tampilkan semua sebagai fallback
                 }
             }
        }
        // Jika SUDAH punya lokasi
        else {
            $data['user_location'] = ['lat' => $user_profile['latitude'], 'lng' => $user_profile['longitude']];

            // Jika SUDAH memilih agent -> Tampilkan agent pilihan
            if ($data['selected_agent_id']) {
                 $data['agents'] = $this->User_model->get_one_agent($data['selected_agent_id']);
                 // Handle jika agent pilihan tidak valid/aktif
                 if (empty($data['agents'])) {
                     $this->session->set_flashdata('error', 'Bank sampah pilihan Anda tidak ditemukan/aktif. Pilihan direset.');
                     $this->User_model->set_chosen_agent($user_id, null);
                     $data['selected_agent_id'] = null;
                      // Tampilkan yang terdekat sebagai fallback
                     $data['agents'] = $this->User_model->get_nearest_agents($user_profile['latitude'], $user_profile['longitude']);
                      if(empty($data['agents'])) { // Jika terdekat juga kosong
                           $data['agents'] = $this->User_model->get_all_active_agents();
                      }
                 }
            }
            // Jika BELUM memilih agent -> Cari yang terdekat
            else {
                $data['agents'] = $this->User_model->get_nearest_agents($user_profile['latitude'], $user_profile['longitude']);
                if (empty($data['agents'])) {
                    $this->session->set_flashdata('info', 'Tidak ada bank sampah dalam radius 10km. Menampilkan semua.');
                    $data['agents'] = $this->User_model->get_all_active_agents();
                }
            }
        }

        $data['view_name'] = 'user/waste_banks';
        $this->load->view('user/layout', $data);
    }


    public function select_agent($agent_id)
    {
        // Bisa pakai AJAX (lebih baik) atau redirect biasa
        // if (!$this->input->is_ajax_request()) { show_404(); } // Aktifkan jika pakai AJAX

        $user_id = $this->session->userdata('user_id');
        $agent_id = (int) $agent_id;

        $agent_exists = $this->db->get_where('agent', ['id_agent' => $agent_id, 'status' => 'aktif'])->num_rows() > 0;

        if ($user_id && $agent_id > 0 && $agent_exists) {
            if ($this->User_model->set_chosen_agent($user_id, $agent_id)) {
                // Response untuk AJAX
                 if ($this->input->is_ajax_request()){
                    $this->output->set_content_type('application/json')->set_output(json_encode(['success' => true]));
                    return;
                 }
                 // Redirect jika bukan AJAX
                 $this->session->set_flashdata('success', 'Bank sampah berhasil dipilih.');
                 redirect('user/waste_banks');

            } else {
                 if ($this->input->is_ajax_request()){
                    $this->output->set_status_header(500)->set_content_type('application/json')->set_output(json_encode(['success' => false, 'message' => 'Database error.']));
                    return;
                 }
                 $this->session->set_flashdata('error', 'Gagal memilih bank sampah.');
                 redirect('user/waste_banks');
            }
        } else {
            if ($this->input->is_ajax_request()){
                 $this->output->set_status_header(400)->set_content_type('application/json')->set_output(json_encode(['success' => false, 'message' => 'Agent tidak valid.']));
                 return;
            }
            $this->session->set_flashdata('error', 'Bank sampah tidak valid.');
            redirect('user/waste_banks');
        }
    }

    public function profile()
    {
        $user_id = $this->session->userdata('user_id');

        // Proses update jika ada form POST
        if ($this->input->post()) {
            // Cek apakah ini submit form 'add_nasabah'
            if ($this->input->post('add_nasabah') == '1') {
                $tipe = $this->input->post('tipe_nasabah');
                $jumlah = ($tipe == 'Kelompok') ? $this->input->post('jumlah_nasabah') : 0; // Jumlah 0 untuk perorangan

                // Validasi jumlah jika kelompok
                if ($tipe == 'Kelompok' && (!is_numeric($jumlah) || $jumlah < 2)) {
                    $this->session->set_flashdata('error', 'Jumlah anggota untuk kelompok minimal 2.');
                    redirect('user/profile');
                    return; // Hentikan eksekusi
                }

                $nasabah_data = [
                    'tipe_nasabah' => $tipe,
                    'jumlah_nasabah' => $jumlah
                ];
                if ($this->User_model->update_profile_direct($user_id, $nasabah_data)) { // Gunakan fungsi update langsung
                    $this->session->set_flashdata('success', 'Tipe nasabah berhasil disimpan.');
                } else {
                    $this->session->set_flashdata('error', 'Gagal menyimpan tipe nasabah.');
                }

            } else { // Proses update profil biasa
                $update_data = [
                    'nama'      => $this->input->post('name'),
                    'phone'     => $this->input->post('phone'),
                    'address'   => $this->input->post('address'), // Tetap simpan alamat teks
                    'bio'       => $this->input->post('bio'),
                    'latitude'  => $this->input->post('latitude') ?: NULL,
                    'longitude' => $this->input->post('longitude') ?: NULL,
                ];

                // Hanya update password jika diisi
                if ($this->input->post('password')) {
                    $update_data['password'] = password_hash($this->input->post('password'), PASSWORD_BCRYPT);
                }

                if ($this->User_model->update_profile_direct($user_id, $update_data)) { // Gunakan fungsi update langsung
                    $this->session->set_userdata('name', $update_data['nama']);
                    $this->session->set_flashdata('success', 'Profil berhasil diperbarui.');
                } else {
                    $this->session->set_flashdata('error', 'Gagal memperbarui profil.');
                }
            }
            redirect('user/profile');
        }

        // Menyiapkan data untuk ditampilkan di view
        $user_profile = $this->User_model->get_user_profile($user_id);

        // Logika alamat display
        $address_display = 'Belum diisi';
        if (!empty($user_profile['latitude']) && !empty($user_profile['longitude'])) {
            $this->load->helper('location');
            $address_display = get_address_from_coords($user_profile['latitude'], $user_profile['longitude']);
            // Tambahkan fallback ke alamat teks jika reverse geocoding gagal atau alamat teks ada
            if (($address_display === "Tidak dapat mengambil data lokasi" || $address_display === "Lokasi tidak ditemukan") && !empty($user_profile['address'])) {
                $address_display = $user_profile['address'];
            } elseif(!empty($user_profile['address'])) {
                // Opsional: Gabungkan hasil geocoding dengan alamat teks
                // $address_display .= ' (' . $user_profile['address'] . ')';
                $address_display = $user_profile['address']; // Atau prioritaskan alamat teks jika ada
            }
        } elseif (!empty($user_profile['address'])) {
            $address_display = $user_profile['address'];
        }


        $data['user'] = [
            'name'         => $user_profile['nama'],
            'role'         => ucfirst($user_profile['role']),
            'email'        => $user_profile['email'],
            'phone'        => !empty($user_profile['phone']) ? $user_profile['phone'] : 'Belum diisi',
            'address'      => $address_display,
            'latitude'     => $user_profile['latitude'],
            'longitude'    => $user_profile['longitude'],
            'raw_address'  => $user_profile['address'],
            'bio'          => !empty($user_profile['bio']) ? $user_profile['bio'] : 'Ceritakan tentang diri Anda.',
            'member_since' => date('M Y', strtotime($user_profile['created_at'])),
            // Hapus 'customer_type' dari sini, kita siapkan di $data['nasabah']
        ];

        // PERSIAPKAN DATA NASABAH UNTUK VIEW
        if (!empty($user_profile['tipe_nasabah'])) {
            $data['nasabah'] = [
                'tipe_nasabah' => $user_profile['tipe_nasabah'],
                'jumlah_nasabah' => $user_profile['jumlah_nasabah'] ?? 0 // Ambil jumlah jika ada
            ];
        } else {
            $data['nasabah'] = null; // Kirim null jika belum ada tipe
        }

        $data['stats'] = [
            'collections'     => $this->User_model->count_user_transactions($user_id),
            'points'          => $this->User_model->get_user_points($user_id),
            'waste_collected' => $this->User_model->get_total_waste_by_user($user_id),
        ];

        $data['view_name'] = 'user/profile';
        $this->load->view('user/layout', $data);
    }

    public function logout()
    {
        // Hapus semua data session
        $this->session->sess_destroy();

        // Redirect ke halaman utama (landing page)
        redirect(base_url());
    }

}

