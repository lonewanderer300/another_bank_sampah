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
    
    public function dashboard()
    {
        $data['pending_agents'] = $this->Admin_model->get_pending_agents();
        $data['unpaid_customers'] = $this->Admin_model->count_unpaid_customers();
        
        $data['view_name'] = 'admin/dashboard'; // Menggunakan view_name
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
        
        // Cek jika ini adalah request untuk menambah jenis sampah baru
        if ($this->input->post('add_waste_type')) {
             $data = [
                'id_kategori' => $this->input->post('id_kategori'),
                'nama_jenis' => $this->input->post('nama_jenis'),
                'harga' => $this->input->post('harga'),
                'satuan' => $this->input->post('satuan')
            ];
            // Asumsi Anda punya model 'Admin_model' dengan method 'add_waste_type'
            // $this->Admin_model->add_waste_type($data); 
            $this->session->set_flashdata('success', 'Jenis sampah baru berhasil ditambahkan (Logika Model belum ada).');
            redirect('admin/waste_prices');
        }

        $data['waste_types'] = $this->Admin_model->get_all_waste_types();
        $data['view_name'] = 'admin/waste_prices'; // Menggunakan view_name
        $this->load->view('admin/layout', $data);
    }

    public function manage_agents()
    {
        $data['agents'] = $this->Admin_model->get_all_agents();
        $data['view_name'] = 'admin/manage_agents'; // Menggunakan view_name
        $this->load->view('admin/layout', $data);
    }

    public function update_agent_status()
    {
        $id_agent = $this->input->post('id_agent');
        $status = $this->input->post('status');
        
        // Asumsi ada fungsi ini di model, dari kode Anda yang terduplikat
        // $this->Admin_model->update_agent_status($id_agent, $status);
        
        $this->session->set_flashdata('success', 'Status agen berhasil diperbarui (Logika Model belum ada).');
        redirect('admin/manage_agents');
    }

    public function manage_users()
    {
        $data['users'] = $this->Admin_model->get_all_customers();
        $data['view_name'] = 'admin/manage_users'; // Menggunakan view_name
        $this->load->view('admin/layout', $data);
    }

    public function edit_user($id_user)
    {
        if (empty($id_user)) {
            redirect('admin/manage_users');
        }

        // Proses form submission
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $data_user = [
                'nama'  => $this->input->post('nama'),
                'email' => $this->input->post('email'),
                'phone' => $this->input->post('phone'),
                'role'  => $this->input->post('role')
            ];

            $password = $this->input->post('password');
            if (!empty($password)) {
                $data_user['password'] = password_hash($password, PASSWORD_BCRYPT);
            }

            $this->Admin_model->update_user($id_user, $data_user);

            if ($data_user['role'] == 'agent') {
                $data_agent = [
                    'wilayah' => $this->input->post('wilayah')
                ];
                $this->Admin_model->update_agent_by_user_id($id_user, $data_agent);
            }

            $this->session->set_flashdata('success', 'Data user berhasil diperbarui.');
            redirect('admin/' . ($data_user['role'] == 'agent' ? 'manage_agents' : 'manage_users'));

        } else {
            // Tampilkan form (GET)
            $data['title'] = "Edit User";
            $data['user'] = $this->Admin_model->get_user_by_id($id_user);
            $data['wilayah_options'] = $this->Admin_model->get_wilayah_enum_values();
 
            // === INI HARUS ADA DI SINI ===
            $data['agent_data'] = []; 
            // ============================

            if (!$data['user']) {
                $this->session->set_flashdata('error', 'User tidak ditemukan.');
                redirect('admin/manage_users');
            }

            // Blok ini SEKARANG AMAN
            if ($data['user']['role'] == 'agent') {
                $data['agent_data'] = $this->Admin_model->get_agent_by_user_id($id_user);
            }

            $data['view_name'] = 'admin/edit_user';
            $this->load->view('admin/layout', $data);
        }
    }

    public function delete_user($id_user)
    {
        if (empty($id_user)) {
            redirect('admin/dashboard');
        }

        $user = $this->Admin_model->get_user_by_id($id_user);
        if ($user) {
            $this->Admin_model->delete_user_and_related_data($id_user);
            $this->session->set_flashdata('success', 'User dan data terkait (agent/nasabah) berhasil dihapus.');
            redirect('admin/' . ($user['role'] == 'agent' ? 'manage_agents' : 'manage_users'));
        } else {
            $this->session->set_flashdata('error', 'User tidak ditemukan.');
            redirect('admin/dashboard');
        }
    }

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

        $data['nasabah_list'] = $this->Admin_model->get_all_nasabah_with_iuran();
        $data['view_name'] = 'admin/manage_iuran'; // Menggunakan view_name
        $this->load->view('admin/layout', $data);
    }

	public function laporan_transaksi()
    {
        $this->load->model('Admin_model');

        $bulan = $this->input->get('bulan');
        $tahun = $this->input->get('tahun');

        // Admin melihat semua agent
        $data['bulan'] = $bulan;
        $data['tahun'] = $tahun;
        $data['laporan'] = $this->Admin_model->get_laporan_transaksi_admin($bulan, $tahun);

        $data['view_name'] = 'admin/laporan_transaksi';
        $this->load->view('admin/layout', $data);
    }

    public function export_excel()
    {
        $this->load->model('Admin_model');

        $bulan = $this->input->get('bulan');
        $tahun = $this->input->get('tahun');

        $laporan = $this->Admin_model->get_laporan_transaksi_admin($bulan, $tahun);

        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=Laporan_Transaksi_Admin_{$bulan}_{$tahun}.xls");

        echo "<table border='1'>
            <tr>
                <th>NO</th>
                <th>TANGGAL</th>
                <th>NO REKENING</th>
                <th>AGEN</th>
                <th>NAMA NASABAH</th>
                <th>TIPE SAMPAH</th>
                <th>JENIS</th>
                <th>KODE</th>
                <th>URAIAN BARANG</th>
                <th>JUMLAH (Kg)</th>
                <th>JUMLAH BOTOL (Biji)</th>
                <th>HARGA SATUAN (Rp)</th>
                <th>PENDAPATAN (Rp)</th>
                <th>TARIK TUNAI (Rp)</th>
                <th>SALDO AKHIR (Rp)</th>
                <th>PETUGAS</th>
            </tr>";

        $no = 1;
        foreach ($laporan as $row) {
            echo "<tr>
                <td>{$no}</td>
                <td>{$row['tanggal_setor']}</td>
                <td>{$row['no_rekening']}</td>
                <td>{$row['nama_agent']}</td>
                <td>{$row['nama_nasabah']}</td>
                <td>{$row['tipe_sampah']}</td>
                <td>{$row['nama_kategori']}</td>
                <td>{$row['kode']}</td>
                <td>{$row['nama_jenis']}</td>
                <td>{$row['berat']}</td>
                <td>{$row['jumlah_botol']}</td>
                <td>{$row['harga']}</td>
                <td>{$row['pendapatan']}</td>
                <td>{$row['tarik_tunai']}</td>
                <td>{$row['saldo_akhir']}</td>
                <td>{$row['nama_petugas']}</td>
            </tr>";
            $no++;
        }

        echo "</table>";
    }

    /**
     * Menampilkan daftar semua transaksi.
     */
    public function manage_transactions()
    {
        $data['title'] = "Manajemen Transaksi";
        $data['transactions'] = $this->Admin_model->get_all_transactions();

        $data['view_name'] = 'admin/manage_transactions';
        $this->load->view('admin/layout', $data);
    }

    /**
     * Menghapus transaksi dan mengoreksi saldo nasabah.
     */
    public function delete_transaction($id_setoran)
    {
        if ($this->Admin_model->delete_transaction($id_setoran)) {
            $this->session->set_flashdata('success', 'Transaksi berhasil dihapus dan saldo nasabah telah dikoreksi.');
        } else {
            $this->session->set_flashdata('error', 'Gagal menghapus transaksi. Cek log database.');
        }

        redirect('admin/manage_transactions');
    }

    /**
     * Memuat dan memproses form edit transaksi setoran.
     */
    public function edit_transaction($id_setoran)
    {
        $id_setoran = (int) $id_setoran;

        // 1. Ambil data transaksi master lama
        $transaction = $this->Admin_model->get_transaction_data($id_setoran);

        if (!$transaction) {
            $this->session->set_flashdata('error', 'Transaksi tidak ditemukan.');
            redirect('admin/manage_transactions');
            return;
        }
        
        // --- Proses POST Request (UPDATE) ---
        if ($this->input->server('REQUEST_METHOD') === 'POST') {
            
            // Validasi dasar
            $this->form_validation->set_rules('customer_id', 'Nasabah', 'required|numeric');
            $this->form_validation->set_rules('transaction_date', 'Tanggal Transaksi', 'required');

            if ($this->form_validation->run() == FALSE) {
                $this->session->set_flashdata('error_form', validation_errors());
            } else {
                $customer_id = $this->input->post('customer_id');
                $transaction_date = date('Y-m-d H:i:s', strtotime($this->input->post('transaction_date')));
                $waste_items_input = $this->input->post('waste_items');
                
                $valid_waste_items = [];
                // Parsing waste items input
                if (is_array($waste_items_input)) {
                    foreach ($waste_items_input as $item) {
                        if (
                            isset($item['id_jenis'], $item['berat']) &&
                            is_numeric($item['berat']) &&
                            $item['berat'] > 0
                        ) {
                            $valid_waste_items[(int)$item['id_jenis']] = (float)$item['berat'];
                        }
                    }
                }

                if (empty($valid_waste_items)) {
                    $this->session->set_flashdata('error_form', 'Masukkan minimal satu jenis sampah dengan berat > 0.');
                } else {
                    $old_total_poin = $transaction['total_poin'];
                    
                    $result = $this->Admin_model->update_transaction_and_user_balance(
                        $id_setoran, 
                        $old_total_poin, 
                        $customer_id, 
                        $transaction_date, 
                        $valid_waste_items
                    );

                    if ($result['success']) {
                        $this->session->set_flashdata('success', $result['message']);
                        redirect('admin/manage_transactions');
                        return;
                    } else {
                        $this->session->set_flashdata('error_form', $result['message']);
                    }
                }
            }
        }
        
        // --- Memuat Data untuk Form (GET Request atau POST Request Gagal) ---
        
        $data['title'] = "Edit Transaksi";
        $data['transaction'] = $transaction;
        
        // Admin needs ALL customers/users and ALL active agents
        $data['all_customers'] = $this->Admin_model->get_all_users_for_dropdown();
        $data['all_agents'] = $this->Admin_model->get_all_agents_for_dropdown(); // MENGAMBIL SEMUA AGEN
        
        // Petugas: Ambil Petugas berdasarkan Agent ID dari transaksi yang sedang diedit
        $data['petugas'] = $this->Admin_model->get_petugas_by_agent($transaction['id_agent']); 
        
        $data['categories'] = $this->Admin_model->get_all_categories();
        // Data Petugas ID yang tersimpan di transaksi (asumsi ada kolom ts.id_petugas, jika tidak, nilainya NULL)
        $data['current_petugas_id'] = $transaction['id_petugas'] ?? NULL; 
        
        $data['details'] = $this->Admin_model->get_current_waste_details($id_setoran);
        
        $data['view_name'] = 'admin/edit_transaction'; 
        $this->load->view('admin/layout', $data);
    }
	public function export_users_excel()
{
    $this->load->model('Admin_model');
    $users = $this->Admin_model->get_all_customers();

    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=Daftar_Nasabah.xls");

    echo "<table border='1'>
            <tr>
                <th>Nama</th>
                <th>Email</th>
                <th>Telepon</th>
                <th>Bank Sampah</th>
            </tr>";

    foreach ($users as $u) {

        $bank = !empty($u['nama_agent']) ? $u['nama_agent'] : 'Belum Mendaftar';

        echo "<tr>
                <td>{$u['nama']}</td>
                <td>{$u['email']}</td>
                <td>{$u['phone']}</td>
                <td>{$bank}</td>
            </tr>";
    }

    echo "</table>";
}
public function export_agents()
{
    $this->load->model('Admin_model');

    // Ambil semua data agen
    $agents = $this->Admin_model->get_all_agents();

    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=Daftar_Agen.xls");

    echo "<table border='1'>
            <tr>
                <th>Nama</th>
                <th>Email</th>
                <th>Wilayah</th>
                <th>Status</th>
            </tr>";

    foreach ($agents as $agent) {
        echo "<tr>
                <td>".htmlspecialchars($agent['nama'])."</td>
                <td>".htmlspecialchars($agent['email'])."</td>
                <td>".htmlspecialchars($agent['wilayah'])."</td>
                <td>".htmlspecialchars(ucfirst($agent['status']))."</td>
             </tr>";
    }

    echo "</table>";
}



    public function logout()
    {
        $this->session->sess_destroy();
        redirect('admin/login');
    }
	
}
