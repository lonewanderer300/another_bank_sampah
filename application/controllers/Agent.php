<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Agent extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Agent_model');

        if (!$this->session->userdata('logged_in') || $this->session->userdata('role') !== 'agent') {
            $this->session->set_flashdata('error', 'Anda harus login sebagai Agen untuk mengakses halaman ini.');
            redirect(base_url());
        }
    }

    public function index()
    {
        redirect('agent/dashboard');
    }

    public function dashboard()
    {
        $agent_id = $this->session->userdata('agent_id'); // Pastikan Anda menyimpan agent_id di session saat login
        
        $data['total_customers'] = $this->Agent_model->count_agent_customers($agent_id);
        $data['total_transactions'] = $this->Agent_model->count_agent_transactions($agent_id);
        $data['total_waste'] = $this->Agent_model->get_total_waste_by_agent($agent_id);
        $data['recent_transactions'] = $this->Agent_model->get_agent_transactions($agent_id, 5); // Limit 5

        $data['view_name'] = 'agent/dashboard'; 
        $this->load->view('agent/layout', $data);
    }

    public function my_user()
    {
        $agent_id = $this->session->userdata('agent_id');
        $data['customers'] = $this->Agent_model->get_agent_customers_list($agent_id);

        $data['view_name'] = 'agent/my_user';
        $this->load->view('agent/layout', $data);
    }

    public function transactions()
    {
        $agent_id = $this->session->userdata('agent_id');

        $data['total_transactions'] = $this->Agent_model->count_agent_transactions($agent_id);
        $data['total_income'] = $this->Agent_model->get_total_income_by_agent($agent_id);
        $data['total_waste'] = $this->Agent_model->get_total_waste_by_agent($agent_id);
        $data['transactions'] = $this->Agent_model->get_agent_transactions($agent_id);

        $data['view_name'] = 'agent/transactions';
        $this->load->view('agent/layout', $data);
    }

    public function profile()
    {
        $user_id = $this->session->userdata('user_id');
        $agent_id = $this->session->userdata('agent_id');

        // Proses update jika ada form POST
        if ($this->input->post()) {
            // Data untuk tabel 'users'
            $data_user = [
                'nama'      => $this->input->post('name'),
                'phone'     => $this->input->post('phone'),
                'address'   => $this->input->post('address'),
                'bio'       => $this->input->post('bio'),
            ];

            // Data untuk tabel 'agent'
            $data_agent = [
                'wilayah' => $this->input->post('wilayah')
            ];

            // Hanya update password jika diisi
            if ($this->input->post('password')) {
                $data_user['password'] = password_hash($this->input->post('password'), PASSWORD_BCRYPT);
            }

            if ($this->Agent_model->update_profile($user_id, $agent_id, $data_user, $data_agent)) {
                $this->session->set_userdata('name', $data_user['nama']);
                $this->session->set_flashdata('success', 'Profil berhasil diperbarui.');
            } else {
                $this->session->set_flashdata('error', 'Gagal memperbarui profil.');
            }
            redirect('agent/profile');
        }

        // Menyiapkan data untuk ditampilkan di view
        $agent_profile = $this->Agent_model->get_agent_profile($user_id);

        $data['agent'] = [
            'name'         => $agent_profile['nama'],
            'role'         => ucfirst($agent_profile['role']),
            'email'        => $agent_profile['email'],
            'phone'        => $agent_profile['phone'] ?? 'Belum diisi',
            'address'      => $agent_profile['address'] ?? 'Belum diisi',
            'bio'          => $agent_profile['bio'] ?? 'Ceritakan tentang bank sampah Anda.',
            'wilayah'      => $agent_profile['wilayah'],
            'member_since' => date('M Y', strtotime($agent_profile['created_at'])),
        ];
        
        // Data untuk kartu statistik
        $data['stats'] = [
            'customers'     => $this->Agent_model->count_agent_customers($agent_id),
            'transactions'  => $this->Agent_model->count_agent_transactions($agent_id),
            'waste_collected' => $this->Agent_model->get_total_waste_by_agent($agent_id),
        ];

        // Opsi untuk dropdown wilayah
        $data['wilayah_options'] = ['Dusun Selatan','Dusun Hilir','Dusun Utara','Gunung Bintang Awai','Jenamas','Karau Kuala'];

        $data['view_name'] = 'agent/profile';
        $this->load->view('agent/layout', $data);
    }
}