<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->helper(['url', 'form']);
        $this->load->library('session');
        $this->load->database();
        $this->load->model('User_model');
    }

    // ================== DASHBOARD ==================
    public function dashboard()
    {
        // 🔒 Require login as user
        if (!$this->session->userdata('logged_in') || $this->session->userdata('role') !== 'user') {
            redirect(base_url());
        }

        // 🧠 Get session user info
        $user = [
            'name'   => $this->session->userdata('nama'),
            'role'   => 'Regular User',
            'email'  => $this->session->userdata('email'),
            'avatar' => 'https://ui-avatars.com/api/?name=' . urlencode($this->session->userdata('nama')) . '&background=random'
        ];

        // 📊 Dummy stats (you can replace with real model calls later)
        $data['stats'] = [
            'total_collections' => 23,
            'points'            => 1240,
            'active_requests'   => 2,
            'monthly_goal'      => 78
        ];

        // 📅 Dummy activity log (you can pull from DB later)
        $data['recent_activity'] = [
            ['date' => '2025-10-01', 'amount' => '5.2 kg', 'by' => 'Maria Garcia'],
            ['date' => '2025-09-27', 'amount' => '3.8 kg', 'by' => 'John Smith'],
            ['date' => '2025-09-25', 'amount' => '7.1 kg', 'by' => 'David Chen'],
        ];

        // ✅ Keep your current dashboard front-end style
        $data['page']  = 'dashboard';
        $data['user']  = $user;
        $data['content'] = 'user/dashboard';
        $this->load->view('user/layout', $data);
    }

    // ================== WASTE BANKS ==================
    public function waste_banks()
    {
        if (!$this->session->userdata('logged_in') || $this->session->userdata('role') !== 'user') {
            redirect(base_url());
        }

        $data['page'] = 'waste_banks';
        $data['user'] = [
            'name'   => $this->session->userdata('nama'),
            'role'   => 'Regular User',
            'email'  => $this->session->userdata('email')
        ];

        // Dummy waste banks (you can later pull from DB)
        $data['centers'] = [
            ['id'=>1, 'name'=>'Center A', 'distance'=>'1.2 km', 'type'=>'Plastic', 'favorite'=>true],
            ['id'=>2, 'name'=>'Center B', 'distance'=>'2.5 km', 'type'=>'Paper', 'favorite'=>false],
            ['id'=>3, 'name'=>'Center C', 'distance'=>'3.1 km', 'type'=>'Metal', 'favorite'=>true],
        ];

        $data['content'] = 'user/waste_banks';
        $this->load->view('user/layout', $data);
    }

    // ================== TRANSACTIONS ==================
    public function transactions()
    {
        if (!$this->session->userdata('logged_in') || $this->session->userdata('role') !== 'user') {
            redirect(base_url());
        }

        $data['page'] = 'transactions';
        $data['user'] = [
            'name'   => $this->session->userdata('nama'),
            'role'   => 'Regular User',
            'email'  => $this->session->userdata('email')
        ];

        // Dummy transactions
        $data['transactions'] = [
            ['id'=>'TX001', 'date'=>'2025-09-30', 'waste_type'=>'Plastic', 'agent'=>'Agent A', 'weight'=>'2.5kg', 'location'=>'Center A', 'points'=>20, 'earnings'=>5.5, 'status'=>'Completed'],
            ['id'=>'TX002', 'date'=>'2025-09-29', 'waste_type'=>'Paper', 'agent'=>'Agent B', 'weight'=>'1.2kg', 'location'=>'Center B', 'points'=>10, 'earnings'=>2.3, 'status'=>'Pending'],
        ];

        $data['content'] = 'user/transactions';
        $this->load->view('user/layout', $data);
    }

    // ================== PROFILE ==================
    public function profile()
    {
        if (!$this->session->userdata('logged_in') || $this->session->userdata('role') !== 'user') {
            redirect(base_url());
        }

        $data['page']  = "profile"; 
        $data['user'] = [
            'name'    => $this->session->userdata('nama'),
            'role'    => 'Regular User',
            'email'   => $this->session->userdata('email'),
            'phone'   => '+62 812-3456-7890',
            'address' => 'Jl. Kebersihan No. 45, Bandung',
            'bio'     => 'Environmental enthusiast committed to sustainable waste management',
            'member_since' => 'Jan 2024'
        ];

        $data['stats'] = [
            'collections'     => 23,
            'points'          => 1240,
            'member_since'    => 'Jan 2024',
            'waste_collected' => '156 kg'
        ];

        $data['content'] = 'user/profile';
        $this->load->view('user/layout', $data);
    }
}
