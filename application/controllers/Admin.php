<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        // Nanti kita akan tambahkan pengecekan session di sini
        $this->load->model('Admin_model');
    }

    public function index()
    {
        // Arahkan ke dashboard
        redirect('admin/dashboard');
    }

    public function dashboard()
    {
        $data['pending_agents'] = $this->Admin_model->get_pending_agents();
        $data['view_name'] = 'admin/dashboard_content'; // view konten
        $this->load->view('admin/layout', $data); // panggil layout utama
    }
    
    public function approve_agent($id_agent)
    {
        $this->Admin_model->update_agent_status($id_agent, 'aktif');
        $this->session->set_flashdata('success', 'Agen berhasil diaktifkan.');
        redirect('admin/dashboard');
    }

    public function reject_agent($id_agent)
    {
        $this->Admin_model->update_agent_status($id_agent, 'nonaktif');
        $this->session->set_flashdata('success', 'Agen telah dinonaktifkan.');
        redirect('admin/dashboard');
    }
}