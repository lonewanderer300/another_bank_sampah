<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model');
        $this->load->library(array('session', 'form_validation'));
        $this->load->helper(array('url', 'form'));

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
        $data['balance'] = $this->User_model->get_user_balance($user_id);
        $data['total_waste'] = $this->User_model->get_total_waste_by_user($user_id);
        $data['transactions'] = $this->User_model->get_user_transactions($user_id, 5);

        $waste_summary = $this->User_model->get_waste_summary_by_user($user_id);
        $data['waste_labels'] = json_encode(array_column($waste_summary, 'waste_type'));
        $data['waste_data'] = json_encode(array_column($waste_summary, 'total_weight'));

        $data['iuran'] = array(
            'status' => 'paid',
            'amount' => 20000,
            'due_date' => '30 Oktober 2025'
        );

        $data['view_name'] = 'user/dashboard';
        $this->load->view('user/layout', $data);
    }

    public function transactions()
    {
        $user_id = $this->session->userdata('user_id');
        $data['total_transactions'] = $this->User_model->count_user_transactions($user_id);
        $data['total_income'] = $this->User_model->get_total_income_by_user($user_id);
        $data['total_waste'] = $this->User_model->get_total_waste_by_user($user_id);
        $data['transactions'] = $this->User_model->get_user_transactions($user_id);

        $data['view_name'] = 'user/transactions';
        $this->load->view('user/layout', $data);
    }

    public function waste_banks()
    {
        $user_id = $this->session->userdata('user_id');
        $user_profile = $this->User_model->get_user_profile($user_id);
        $data['selected_agent_id'] = isset($user_profile['id_agent_pilihan']) ? $user_profile['id_agent_pilihan'] : null;

        if (empty($user_profile['latitude']) || empty($user_profile['longitude'])) {
            if (!$data['selected_agent_id']) {
                $this->session->set_flashdata('info', 'Atur lokasi Anda di profil untuk melihat bank sampah terdekat. Menampilkan semua bank sampah.');
                $data['agents'] = $this->User_model->get_all_active_agents();
            } else {
                $data['agents'] = $this->User_model->get_nearest_agents($user_profile['latitude'], $user_profile['longitude'], 10, 4);
                if (empty($data['agents'])) {
                    $this->session->set_flashdata('info', 'Tidak ada bank sampah dalam radius 10km. Menampilkan semua.');
                    $data['agents'] = $this->User_model->get_all_active_agents();
                }
            }
        } else {
            $data['user_location'] = array('lat' => $user_profile['latitude'], 'lng' => $user_profile['longitude']);

            if ($data['selected_agent_id']) {
                $data['agents'] = $this->User_model->get_one_agent($data['selected_agent_id']);
                if (empty($data['agents'])) {
                    $this->session->set_flashdata('error', 'Bank sampah pilihan tidak valid. Pilihan direset.');
                    $this->User_model->set_chosen_agent($user_id, null);
                    $data['selected_agent_id'] = null;
                    $data['agents'] = $this->User_model->get_nearest_agents($user_profile['latitude'], $user_profile['longitude'], 10, 4);
                    if (empty($data['agents'])) {
                        $data['agents'] = $this->User_model->get_all_active_agents();
                    }
                }
            } else {
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
        $user_id = $this->session->userdata('user_id');

        if (!$user_id) {
            if ($this->input->is_ajax_request()) {
                $this->output
                    ->set_status_header(401)
                    ->set_content_type('application/json')
                    ->set_output(json_encode(array('success' => false, 'message' => 'User tidak login atau sesi berakhir.')));
            } else {
                $this->session->set_flashdata('error', 'Sesi Anda berakhir, silakan login lagi.');
                redirect(base_url());
            }
            return;
        }

        $agent_id = (int)$agent_id;
        if ($agent_id === 0) {
            $this->User_model->set_chosen_agent($user_id, null);
            $this->output->set_content_type('application/json')
                ->set_output(json_encode(array('success' => true, 'message' => 'Pilihan agent direset.')));
            return;
        }

        $agent_exists = $this->db->get_where('agent', array('id_agent' => $agent_id, 'status' => 'aktif'))->num_rows() > 0;
        if ($user_id && $agent_id > 0 && $agent_exists) {
            $this->User_model->set_chosen_agent($user_id, $agent_id);
            $this->output->set_content_type('application/json')->set_output(json_encode(array('success' => true)));
        } else {
            $this->output->set_status_header(400)
                ->set_content_type('application/json')
                ->set_output(json_encode(array('success' => false, 'message' => 'Agent tidak valid atau tidak aktif.')));
        }
    }

    public function profile()
    {
        $user_id = $this->session->userdata('user_id');

        if ($this->input->post()) {

           if ($this->input->post('add_nasabah') == '1') {
    $tipe = $this->input->post('tipe_nasabah');
    $jumlah = ($tipe == 'Kelompok') ? $this->input->post('jumlah_nasabah') : 1;

    // Cek apakah user sudah pernah jadi kelompok
    $existing = $this->User_model->get_nasabah_by_user($user_id);
    if ($existing && $existing['tipe_nasabah'] === 'Kelompok' && $tipe === 'Perorangan') {
        $this->session->set_flashdata('error', 'Anda sudah terdaftar sebagai nasabah kelompok dan tidak dapat kembali menjadi perorangan.');
        redirect('user/profile');
        return;
    }

    if ($tipe == 'Kelompok' && (!is_numeric($jumlah) || $jumlah < 2)) {
        $this->session->set_flashdata('error', 'Jumlah anggota untuk kelompok minimal 2.');
        redirect('user/profile');
        return;
    }

    // Simpan atau update data nasabah
    $nasabah_data = [
        'tipe_nasabah' => $tipe,
        'jumlah_nasabah' => $jumlah
    ];

    $nasabah = $this->User_model->get_nasabah_by_user($user_id);
    if ($nasabah) {
        $this->db->where('id_users', $user_id);
        $this->db->update('nasabah', $nasabah_data);
    } else {
        $nasabah_data['id_users'] = $user_id;
        $this->db->insert('nasabah', $nasabah_data);
    }

    // Reset iuran (biaya dan deadline baru)
    $biaya_baru = ($tipe == 'Kelompok') ? 50000 : 20000; // contoh: kelompok lebih mahal
    $deadline_baru = date('Y-m-d', strtotime('+30 days'));
    $this->User_model->reset_iuran($user_id, $biaya_baru, $deadline_baru);

    $this->session->set_flashdata('success', 'Tipe nasabah berhasil diperbarui dan iuran direset.');
    redirect('user/profile');
}


            $update_data = array(
                'nama' => $this->input->post('name'),
                'phone' => $this->input->post('phone'),
                'address' => $this->input->post('address'),
                'bio' => $this->input->post('bio'),
                'latitude' => $this->input->post('latitude') ? $this->input->post('latitude') : null,
                'longitude' => $this->input->post('longitude') ? $this->input->post('longitude') : null
            );

            if ($this->input->post('password')) {
                $update_data['password'] = password_hash($this->input->post('password'), PASSWORD_BCRYPT);
            }

            if ($this->User_model->update_profile_direct($user_id, $update_data)) {
                $this->session->set_userdata('name', $update_data['nama']);
                $this->session->set_flashdata('success', 'Profil berhasil diperbarui.');
            } else {
                $this->session->set_flashdata('error', 'Gagal memperbarui profil.');
            }
            redirect('user/profile');
            return;
        }

        $user_profile = $this->User_model->get_user_profile($user_id);

        $address_display = 'Belum diisi';
        if (!empty($user_profile['latitude']) && !empty($user_profile['longitude'])) {
            $this->load->helper('location');
            $address_display = get_address_from_coords($user_profile['latitude'], $user_profile['longitude']);
            if (($address_display === "Tidak dapat mengambil data lokasi" || $address_display === "Lokasi tidak ditemukan") && !empty($user_profile['address'])) {
                $address_display = $user_profile['address'];
            }
        } elseif (!empty($user_profile['address'])) {
            $address_display = $user_profile['address'];
        }

        $data['user'] = array(
            'name' => $user_profile['nama'],
            'role' => ucfirst($user_profile['role']),
            'email' => $user_profile['email'],
            'phone' => !empty($user_profile['phone']) ? $user_profile['phone'] : 'Belum diisi',
            'address' => $address_display,
            'latitude' => $user_profile['latitude'],
            'longitude' => $user_profile['longitude'],
            'raw_address' => $user_profile['address'],
            'bio' => !empty($user_profile['bio']) ? $user_profile['bio'] : 'Ceritakan tentang diri Anda.',
            'member_since' => date('M Y', strtotime($user_profile['created_at']))
        );

        $data['nasabah'] = !empty($user_profile['tipe_nasabah']) ? array(
            'tipe_nasabah' => $user_profile['tipe_nasabah'],
            'jumlah_nasabah' => isset($user_profile['jumlah_nasabah']) ? $user_profile['jumlah_nasabah'] : 0
        ) : null;

        $data['stats'] = array(
            'collections' => $this->User_model->count_user_transactions($user_id),
            'points' => $this->User_model->get_user_points($user_id),
            'waste_collected' => $this->User_model->get_total_waste_by_user($user_id)
        );

        $data['view_name'] = 'user/profile';
        $this->load->view('user/layout', $data);
    }

    public function rekening()
    {
        $user_id = $this->session->userdata('user_id');

        if ($this->input->post()) {
            $no_rekening = trim($this->input->post('no_rekening'));
            if (empty($no_rekening)) {
                $this->session->set_flashdata('error', 'Nomor rekening tidak boleh kosong.');
                redirect('user/rekening');
            }

            if ($this->User_model->add_or_update_rekening($user_id, $no_rekening)) {
                $this->session->set_flashdata('success', 'Nomor rekening berhasil disimpan.');
            } else {
                $this->session->set_flashdata('error', 'Gagal menyimpan nomor rekening.');
            }
            redirect('user/rekening');
        }

        $data['rekening'] = $this->User_model->get_user_rekening($user_id);
        $data['view_name'] = 'user/rekening';
        $this->load->view('user/layout', $data);
    }

    public function logout()
    {
        $this->session->sess_destroy();
        redirect(base_url());
    }
}
