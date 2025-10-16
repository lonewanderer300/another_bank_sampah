<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Agent extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->helper(['url', 'form']);
        $this->load->library('session');
        $this->load->database();
        $this->load->model('Agent_model');
    }

    // ================== DASHBOARD ==================
    public function dashboard()
    {
        // 🔒 Require login
        if (!$this->session->userdata('logged_in') || $this->session->userdata('role') !== 'agent') {
            redirect(base_url());
        }

        // 🧠 Get logged-in agent info from session
        $agent = [
            'name'   => $this->session->userdata('nama'),
            'role'   => 'Collection Agent',
            'email'  => $this->session->userdata('email'),
            'avatar' => 'https://ui-avatars.com/api/?name=' . urlencode($this->session->userdata('nama')) . '&background=random'
        ];

        // 🧩 Find agent ID from table `agent` (linked to users.id_user)
        $agent_row = $this->db->get_where('agent', [
            'id_user' => $this->session->userdata('id_user')
        ])->row();

        if (!$agent_row) {
            $this->session->set_flashdata('error', 'Data agen tidak ditemukan di database.');
            redirect(base_url());
        }

        $id_agent = $agent_row->id_agent;

        // 📊 Fetch data from model
        $total_setoran = $this->Agent_model->get_total_setoran($id_agent);
        $total_waste   = $this->Agent_model->get_total_waste($id_agent);
        $my_users      = $this->Agent_model->get_my_users($id_agent);
        $waste_trends  = $this->Agent_model->get_waste_trends($id_agent);

        // 🔹 Build dashboard stats
        $data['stats'] = [
            'regular_users'      => count($my_users),
            'unpaid_fees'        => rand(0, 5), // (placeholder)
            'service_rating'     => number_format(rand(40, 50) / 10, 1),
            'reviews'            => rand(5, 30),
            'monthly_earnings'   => number_format($total_waste * 1.2, 2),
            'earnings_this_week' => number_format($total_waste * 0.25, 2)
        ];

        // 🔹 Chart Data
        $months = [];
        $weights = [];
        foreach ($waste_trends as $trend) {
            $monthNum = (int)$trend['bulan'];
            $months[] = date("M", mktime(0, 0, 0, $monthNum, 1));
            $weights[] = (float)$trend['total_berat'];
        }

        $data['waste_trends'] = [
            'months'  => $months,
            'plastik' => $weights,
            'kertas'  => $weights,
            'kaca'    => $weights,
            'logam'   => $weights,
            'organik' => $weights
        ];

        // ✅ Same as before — maintain your layout design
        $data['page']    = 'dashboard';
        $data['agent']   = $agent;
        $data['content'] = 'agent/dashboard';

        $this->load->view('agent/layout', $data);
    }

    // ================== MY USER ==================
    public function my_user()
    {
        if (!$this->session->userdata('logged_in') || $this->session->userdata('role') !== 'agent') {
            redirect(base_url());
        }

        $data['page'] = 'my_user';
        $data['agent'] = [
            'name'   => $this->session->userdata('nama'),
            'role'   => 'Collection Agent',
            'email'  => $this->session->userdata('email'),
            'avatar' => 'https://ui-avatars.com/api/?name=' . urlencode($this->session->userdata('nama')) . '&background=random'
        ];

        $id_agent = $this->db->get_where('agent', [
            'id_user' => $this->session->userdata('id_user')
        ])->row('id_agent');

        $data['users'] = $this->Agent_model->get_my_users($id_agent);

        $data['stats'] = [
            'total_users'  => count($data['users']),
            'active_users' => rand(3, 8),
            'unpaid_users' => rand(0, 3),
            'new_users'    => rand(0, 2)
        ];

        $data['content'] = 'agent/my_user';
        $this->load->view('agent/layout', $data);
    }

    // ================== TRANSACTIONS ==================
    public function transactions()
    {
        if (!$this->session->userdata('logged_in') || $this->session->userdata('role') !== 'agent') {
            redirect(base_url());
        }

        $data['page'] = 'transactions';
        $data['agent'] = [
            'name'   => $this->session->userdata('nama'),
            'role'   => 'Collection Agent',
            'email'  => $this->session->userdata('email'),
            'avatar' => 'https://ui-avatars.com/api/?name=' . urlencode($this->session->userdata('nama')) . '&background=random'
        ];

        $data['transactions'] = [
            ['id'=>1, 'user'=>'Andi', 'amount'=>50, 'date'=>'2025-09-12'],
            ['id'=>2, 'user'=>'Budi', 'amount'=>30, 'date'=>'2025-09-15'],
        ];

        $data['content'] = 'agent/transactions';
        $this->load->view('agent/layout', $data);
    }

    // ================== PROFILE ==================
    public function profile()
    {
        if (!$this->session->userdata('logged_in') || $this->session->userdata('role') !== 'agent') {
            redirect(base_url());
        }

        $data['page'] = 'profile';
        $data['agent'] = [
            'name'   => $this->session->userdata('nama'),
            'role'   => 'Collection Agent',
            'email'  => $this->session->userdata('email'),
            'avatar' => 'https://ui-avatars.com/api/?name=' . urlencode($this->session->userdata('nama')) . '&background=random'
        ];

        $data['content'] = 'agent/profile';
        $this->load->view('agent/layout', $data);
    }
}
