<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        // Pastikan database dan model dimuat
        $this->load->database();
        $this->load->model('Home_model');
		$this->load->model('User_model');

		$this->load->helper(['url', 'form']);
        $this->load->library('session');
    }

    public function index()
    {
        // 🔹 Ambil data summary
        $data['summary'] = [
            'total_waste'   => $this->Home_model->get_total_waste(),
            'active_agents' => $this->Home_model->get_active_agents()
        ];

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

        // 🔹 Ambil daftar agen untuk tabel dan peta
        $agents = $this->Home_model->get_agents();
        $data['agents'] = [];
        foreach ($agents as $a) {
            $data['agents'][] = [
                'name'   => $a['name'],
                'area'   => $a['area'],
                'status' => $a['status'],
                'lat'    => isset($a['lat']) ? (float)$a['lat'] : -6.2,
                'lng'    => isset($a['lng']) ? (float)$a['lng'] : 106.816666
            ];
        }

        // 🔹 Tambahan data untuk layout
        $data['title']   = "Garbage Bank - Home";
        $data['content'] = 'landing';

        // 🔹 Muat layout utama
        $this->load->view('layout', $data);
    }

    public function register()
    {
        // 1. Ambil data dari form
        $role = $this->input->post('role');
        
        // Data untuk tabel 'users'
        $userData = [
            'name' => $this->input->post('name'),
            'email' => $this->input->post('email'),
            'password' => password_hash($this->input->post('password'), PASSWORD_BCRYPT),
            'phone' => $this->input->post('phone'),
            'role' => $role
        ];

        // 2. Simpan data user dan dapatkan ID-nya
        $userId = $this->Home_model->insert_user($userData);

        // 3. Jika registrasi sebagai agent, simpan data ke tabel 'agent'
        if ($role === 'agent' && $userId) {
            $agentData = [
                'id_user' => $userId,
                'wilayah' => $this->input->post('wilayah'),
                // Status akan otomatis 'pending' karena default di database
            ];
            $this->Home_model->insert_agent($agentData);
            
            // Beri pesan bahwa pendaftaran agent perlu persetujuan
            $this->session->set_flashdata('success', 'Registrasi sebagai Agent berhasil! Akun Anda sedang menunggu persetujuan dari Admin.');
        } else {
            // Pesan untuk user biasa
            $this->session->set_flashdata('success', 'Registrasi berhasil! Silakan login.');
        }

        redirect(base_url());
    }

    public function login()
    {
        $email = $this->input->post('email', true);
        $password = $this->input->post('password', true);

        // Check user by email
        $user = $this->User_model->get_user_by_email($email);

        if ($user) {
            // Verify password (since register uses password_hash)
            if (password_verify($password, $user->password)) {
                // Set session
                $this->session->set_userdata([
                    'id_user'   => $user->id_user,
                    'nama'      => $user->nama,
                    'email'     => $user->email,
                    'role'      => $user->role,
                    'logged_in' => true
                ]);

                // Redirect based on role
                switch ($user->role) {
                    case 'user':
                        redirect('user/dashboard');
                        break;

                    case 'agent':
                        redirect('agent/dashboard');
                        break;

                    case 'admin':
                        // Admin page under development
                        $this->session->set_flashdata('info', 'Dashboard admin masih dalam pengembangan.');
                        redirect(base_url());
                        break;

                    default:
                        $this->session->set_flashdata('error', 'Role tidak dikenali.');
                        redirect(base_url());
                        break;
                }
            } else {
                $this->session->set_flashdata('error', 'Password salah.');
                redirect(base_url());
            }
        } else {
            $this->session->set_flashdata('error', 'Akun tidak ditemukan.');
            redirect(base_url());
        }
    }

    public function logout()
    {
        $this->session->sess_destroy();
        redirect(base_url());
    }
}
