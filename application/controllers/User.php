<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
		$this->load->model('User_model');
        $this->load->helper(['url', 'form']);
        $this->load->library('session');
    }

    public function dashboard()
{
    // Make sure user is logged in
    if (!$this->session->userdata('logged_in')) {
        redirect(base_url());
    }

    // Get user data from session
    $user = [
        'name'  => $this->session->userdata('nama'),
        'role'  => $this->session->userdata('role'),
        'email' => $this->session->userdata('email')
    ];

    // Dummy dashboard stats (replace with real data later)
    $stats = [
        'total_collections' => 23,
        'points'            => 1240,
        'active_requests'   => 2,
        'monthly_goal'      => 78
    ];

    $recent_activity = [
        ['date' => '2025-10-03', 'amount' => '5.2 kg', 'by' => 'Agent A'],
        ['date' => '2025-10-01', 'amount' => '3.1 kg', 'by' => 'Agent B'],
        ['date' => '2025-09-29', 'amount' => '7.0 kg', 'by' => 'Agent C'],
    ];

    // Pass everything into layout
    $data = [
        'page'            => 'dashboard', // layout will include user/dashboard.php
        'user'            => $user,
        'stats'           => $stats,
        'recent_activity' => $recent_activity
    ];

    // Load layout (this keeps your dashboard design intact)
    $this->load->view('user/layout', $data);
}


    
    public function waste_banks() {
        $data['page'] = 'waste_banks';

        // Dummy data user (sementara, nanti bisa ambil dari session)
        $data['user'] = [
            'name' => 'John Doe',
            'role' => 'Regular User'
        ];

        // Dummy data centers
        $data['centers'] = [
            ['id'=>1, 'name'=>'Center A', 'distance'=>'1.2 km', 'type'=>'Plastic', 'favorite'=>true],
            ['id'=>2, 'name'=>'Center B', 'distance'=>'2.5 km', 'type'=>'Paper', 'favorite'=>false],
            ['id'=>3, 'name'=>'Center C', 'distance'=>'3.1 km', 'type'=>'Metal', 'favorite'=>true],
        ];

        $this->load->view('user/layout', $data);
    }

    public function transactions() {
        $data['page'] = 'transactions';

        // Dummy data user
        $data['user'] = [
            'name' => 'John Doe',
            'role' => 'Regular User'
        ];

        // Dummy data transaksi
        $data['transactions'] = [
            ['id'=>'TX001', 'date'=>'2025-09-30', 'waste_type'=>'Plastic', 'agent'=>'Agent A', 'weight'=>'2.5kg', 'location'=>'Center A', 'points'=>20, 'earnings'=>5.5, 'status'=>'Completed'],
            ['id'=>'TX002', 'date'=>'2025-09-29', 'waste_type'=>'Paper', 'agent'=>'Agent B', 'weight'=>'1.2kg', 'location'=>'Center B', 'points'=>10, 'earnings'=>2.3, 'status'=>'Pending'],
        ];

        $this->load->view('user/layout', $data);
    }


    public function profile() {
        $data['page']  = "profile"; 

        // Dummy user data
        $data['user'] = [
            'name'    => 'John Doe',
            'role'    => 'Community Member',
            'email'   => 'john.doe@example.com',
            'phone'   => '+1-555-0123',
            'address' => '123 Main Street, Downtown District',
            'bio'     => 'Environmental enthusiast committed to sustainable waste management',
            'member_since' => 'Jan 2024'
        ];

        // Dummy stats
        $data['stats'] = [
            'collections'     => 23,
            'points'          => 1240,
            'member_since'    => 'Jan 2024',
            'waste_collected' => '156 '
        ];

        // Load profile view via layout
        $this->load->view('user/layout', [
            'content' => $this->load->view('user/profile', $data, TRUE)
        ]);
    }
	public function register()
    {
        $role = $this->input->post('role', true);
        $name = $this->input->post('name', true);
        $email = $this->input->post('email', true);
        $password = $this->input->post('password', true);
        $phone = $this->input->post('phone', true);

        // Check if email already exists
        // Check if email already exists (regardless of role)
		if ($this->User_model->check_email_exists($email)) {
    		$this->session->set_flashdata('error', 'Email sudah digunakan untuk akun lain.');
    		redirect(base_url());
		}


        // Base user data
        $data_user = [
            'nama' => $name,
            'email' => $email,
            'username' => explode('@', $email)[0],
            'password' => password_hash($password, PASSWORD_BCRYPT),
            'role' => $role,
            'avatar' => 'https://ui-avatars.com/api/?name=' . urlencode($name),
            'poin' => 0,
            'saldo' => 0
        ];

        // If agent, prepare agent data
        $data_agent = null;
        if ($role === 'agent') {
            $data_agent = [
                'wilayah' => $this->input->post('wilayah', true),
                'status' => 'aktif'
            ];
        }

        // Save to DB
        $user_id = $this->User_model->register($data_user, $data_agent);

        // Set session
        $this->session->set_userdata([
            'id_user' => $user_id,
            'nama' => $name,
            'email' => $email,
            'role' => $role,
            'logged_in' => true
        ]);

        // Redirect by role
        if ($role === 'agent') {
            redirect('agent/dashboard');
        } else {
            redirect('user/dashboard');
        }
    }
}
