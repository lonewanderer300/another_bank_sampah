<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Admin_model');

        $method = $this->uri->segment(2);

        // Jika user BELUM login DAN method yang diakses BUKAN 'login', paksa ke halaman login.
        if (!$this->session->userdata('logged_in') && $method != 'login') {
            $this->session->set_flashdata('error', 'Silakan login terlebih dahulu.');
            redirect('admin/login');
        }

        // Jika user SUDAH login TAPI BUKAN admin, tendang ke halaman utama.
        if ($this->session->userdata('logged_in') && $this->session->userdata('role') !== 'admin' && $method != 'login') {
            $this->session->set_flashdata('error', 'Anda tidak memiliki hak akses ke halaman ini.');
            redirect(base_url());
        }
    }

    public function login()
    {
        // Jika sudah login sebagai admin, langsung redirect ke dashboard
        if ($this->session->userdata('logged_in') && $this->session->userdata('role') === 'admin') {
            redirect('admin/dashboard');
        }

        // Jika ada data post (form disubmit)
        if ($this->input->post()) {
            $email = $this->input->post('email');
            $password = $this->input->post('password');

            // Kita pakai fungsi get_user_by_email dari Home_model
            $this->load->model('Home_model');
            $user = $this->Home_model->get_user_by_email($email);

            // Validasi: user ada, password cocok, dan role adalah 'admin'
            if ($user && password_verify($password, $user['password']) && $user['role'] === 'admin') {
                // Set session
                $session_data = [
                    'user_id'   => $user['id_user'],
                    'name'      => $user['name'],
                    'email'     => $user['email'],
                    'role'      => $user['role'],
                    'logged_in' => TRUE
                ];
                $this->session->set_userdata($session_data);
                redirect('admin/dashboard');
            } else {
                // Jika gagal
                $this->session->set_flashdata('error', 'Email atau password salah.');
                redirect('admin/login');
            }
        } else {
            // Jika tidak ada data post, tampilkan view login
            $this->load->view('admin/login');
        }
    }

    public function index()
    {
        redirect('admin/dashboard');
    }
    
    // ... (Semua fungsi dashboard dan manajemen lainnya tetap sama) ...
    public function dashboard()
    {
        $data['pending_agents'] = $this->Admin_model->get_pending_agents();
        $data['unpaid_customers'] = $this->Admin_model->count_unpaid_customers();
        
        $data['view_name'] = 'admin/dashboard';
        $this->load->view('admin/layout', $data);
    }

    public function waste_prices()
    {
        if ($this->input->post('update_price')) {
            $waste_id = $this->input->post('id_jenis');
            $new_price = $this->input->post('harga');
            $this->Admin_model->update_waste_price($waste_id, $new_price);
            $this->session->set_flashdata('success', 'Harga sampah berhasil diperbarui.');
            redirect('admin/waste_prices');
        }

        $data['waste_types'] = $this->Admin_model->get_all_waste_types();
        $data['view_name'] = 'admin/waste_prices';
        $this->load->view('admin/layout', $data);
    }

    public function manage_agents()
    {
        $data['agents'] = $this->Admin_model->get_all_agents();
        $data['view_name'] = 'admin/manage_agents';
        $this->load->view('admin/layout', $data);
    }

    public function manage_users()
    {
        $data['users'] = $this->Admin_model->get_all_customers();
        $data['view_name'] = 'admin/manage_users';
        $this->load->view('admin/layout', $data);
    }

    // --- AKSI ---
    public function approve_agent($agent_id)
    {
        if ($this->Admin_model->approve_agent($agent_id)) {
            $this->session->set_flashdata('success', 'Agen berhasil diaktifkan.');
        } else {
            $this->session->set_flashdata('error', 'Gagal mengaktifkan agen.');
        }
        redirect('admin/dashboard');
    }

    public function reject_agent($agent_id)
    {
        if ($this->Admin_model->reject_agent($agent_id)) {
            $this->session->set_flashdata('success', 'Agen berhasil ditolak.');
        } else {
            $this->session->set_flashdata('error', 'Gagal menolak agen.');
        }
        redirect('admin/dashboard');
    }

	public function manage_iuran()
    {
        //$this->load->model('Admin_model');

        // If admin updates biaya
        if ($this->input->post('update_iuran')) {
            $id_nasabah = $this->input->post('id_nasabah');
            $biaya = $this->input->post('biaya');

            $data = [
                'id_nasabah' => $id_nasabah,
                'biaya' => $biaya,
                'deadline' => date('Y-m-d', strtotime('+30 days')),
                'status_iuran' => 'belum bayar'
            ];

            $this->Admin_model->add_or_update_iuran($data);

            $this->session->set_flashdata('success', 'Iuran berhasil diperbarui.');
            redirect('admin/manage_iuran');
        }

        // Ambil semua nasabah + iuran jika ada
        $data['nasabah_list'] = $this->Admin_model->get_all_nasabah_with_iuran();
        $data['view_name'] = 'admin/manage_iuran';
        $this->load->view('admin/layout', $data);
    }

    public function logout()
    {
        // Hapus semua data session
        $this->session->sess_destroy();

        // Redirect ke halaman utama (landing page)
        redirect('admin/login');
    }
}
