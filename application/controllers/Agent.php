<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Agent extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Agent_model');
        // Load library & helper yang dibutuhkan
        $this->load->library(['session', 'form_validation']);
        $this->load->helper(['url', 'form']);

        if (!$this->session->userdata('logged_in') || $this->session->userdata('role') !== 'agent') {
            $this->session->set_flashdata('error', 'Anda harus login sebagai Agen.');
            redirect(base_url());
        }
         // Pastikan agent_id ada di session setelah perbaikan login
         if (!$this->session->userdata('agent_id')) {
             show_error('Sesi Agent tidak valid. Silakan login kembali.');
             // Atau redirect('home/logout');
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
        $agent_id = $this->session->userdata('agent_id'); // Pastikan ini ada di session
        if (!$agent_id) {
             // Handle jika agent_id tidak ada di session, mungkin redirect ke login
             show_error('Agent ID tidak ditemukan di session.');
             return;
        }
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
    $data['customers'] = $this->Agent_model->get_registered_customers_for_dropdown($agent_id);
    $data['categories'] = $this->Agent_model->get_all_categories();
    $data['petugas'] = $this->Agent_model->get_petugas_by_agent($agent_id);

    $data['view_name'] = 'agent/transactions';
    $this->load->view('agent/layout', $data);
}


    /**
     * Fungsi untuk memproses penambahan transaksi baru
     */
    public function add_transaction()
    {
        if ($this->input->server('REQUEST_METHOD') !== 'POST') {
             redirect('agent/transactions');
        }

        $agent_id = $this->session->userdata('agent_id');

        // Aturan validasi
        $this->form_validation->set_rules('customer_id', 'Nasabah', 'required|numeric');
        $this->form_validation->set_rules('transaction_date', 'Tanggal Transaksi', 'required');
        // Validasi untuk array waste_items (minimal satu item harus diisi dan > 0)
        // $this->form_validation->set_rules('waste_items', 'Detail Sampah', 'callback_validate_waste_items');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error_form', validation_errors());
            $this->transactions(); // Panggil ulang transactions untuk memuat data view
        } else {
            $customer_id = $this->input->post('customer_id');
            // Format tanggal sesuai kebutuhan database (misal: YYYY-MM-DD HH:MM:SS)
            $transaction_date = date('Y-m-d H:i:s', strtotime($this->input->post('transaction_date')));
            $waste_items_input = $this->input->post('waste_items'); // Ini adalah array [id_jenis => berat]
			if (isset($waste_items_input['id_jenis']) && isset($waste_items_input['berat'])) {
    $waste_items_input = [
        (int)$waste_items_input['id_jenis'] => (float)$waste_items_input['berat']
    ];
}
            // Filter item yang beratnya valid (lebih dari 0)
            $valid_waste_items = [];
            if (is_array($waste_items_input)) {
                foreach ($waste_items_input as $id_jenis => $berat) {
                    if (is_numeric($berat) && $berat > 0) {
                        $valid_waste_items[$id_jenis] = (float)$berat;
                    }
                }
            }

            // Jika tidak ada item yang valid setelah difilter
            if (empty($valid_waste_items)) {
                  $this->session->set_flashdata('error_form', 'Masukkan berat minimal untuk satu jenis sampah.');
             }

            // Panggil model untuk menyimpan
            $result = $this->Agent_model->save_transaction($agent_id, $customer_id, $transaction_date, $valid_waste_items);

            if ($result['success']) {
                $this->session->set_flashdata('success', $result['message']);
            } else {
                $this->session->set_flashdata('error_form', $result['message']);
            }
            redirect('agent/transactions');
        }
    }

    /**
     * Callback function untuk validasi input waste_items
     */
    public function validate_waste_items($waste_items)
    {
        if (!is_array($waste_items) || empty($waste_items)) {
            $this->form_validation->set_message('validate_waste_items', 'Input detail sampah tidak boleh kosong.');
            return FALSE;
        }

        $has_valid_item = false;
        foreach ($waste_items as $berat) {
            if (!empty($berat) && is_numeric($berat) && $berat > 0) {
                $has_valid_item = true;
                break; // Cukup satu item valid
            }
        }

        if (!$has_valid_item) {
            $this->form_validation->set_message('validate_waste_items', 'Masukkan berat (lebih dari 0) untuk minimal satu jenis sampah.');
            return FALSE;
        }

        return TRUE;
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
                // TAMBAHKAN LATITUDE & LONGITUDE DARI FORM
                'latitude'  => $this->input->post('latitude') ?: NULL,
                'longitude' => $this->input->post('longitude') ?: NULL,
            ];

            // Data untuk tabel 'agent'
            $data_agent = [
                'wilayah' => $this->input->post('wilayah')
            ];

            // Hanya update password jika diisi
            if ($this->input->post('password')) {
                $data_user['password'] = password_hash($this->input->post('password'), PASSWORD_BCRYPT);
            }

            // Panggil model untuk update
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

        // Siapkan data alamat display (gunakan helper jika ada)
        $address_display = 'Belum diisi';
        if (!empty($agent_profile['latitude']) && !empty($agent_profile['longitude'])) {
            if (function_exists('get_address_from_coords')) { // Cek jika helper ada
                $address_display = get_address_from_coords($agent_profile['latitude'], $agent_profile['longitude']);
                if (($address_display === "Tidak dapat mengambil data lokasi" || $address_display === "Lokasi tidak ditemukan") && !empty($agent_profile['address'])) {
                        $address_display = $agent_profile['address'];
                } elseif(!empty($agent_profile['address'])) {
                        $address_display = $agent_profile['address']; // Prioritaskan alamat teks jika ada
                }
            } elseif (!empty($agent_profile['address'])) {
                $address_display = $agent_profile['address']; // Fallback jika helper tidak ada
            } else {
                $address_display = "Lat: {$agent_profile['latitude']}, Lon: {$agent_profile['longitude']}";
            }
        } elseif (!empty($agent_profile['address'])) {
            $address_display = $agent_profile['address'];
        }


        $data['agent'] = [
            'name'         => $agent_profile['nama'],
            'role'         => ucfirst($agent_profile['role']),
            'email'        => $agent_profile['email'],
            'phone'        => $agent_profile['phone'] ?? 'Belum diisi',
            // 'address'      => $agent_profile['address'] ?? 'Belum diisi', // Ganti dengan address_display
            'address'      => $address_display, // Tampilkan alamat hasil geocoding/teks
            'bio'          => $agent_profile['bio'] ?? 'Ceritakan tentang bank sampah Anda.',
            'wilayah'      => $agent_profile['wilayah'],
            'member_since' => date('M Y', strtotime($agent_profile['created_at'])),
            // TAMBAHKAN LATITUDE & LONGITUDE UNTUK VIEW
            'latitude'     => $agent_profile['latitude'],
            'longitude'    => $agent_profile['longitude'],
            'raw_address'  => $agent_profile['address'], // Kirim alamat asli untuk form
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
	public function petugas()
{
    $agent_id = $this->session->userdata('agent_id');
    $data['petugas'] = $this->Agent_model->get_petugas_by_agent($agent_id);
    $data['view_name'] = 'agent/petugas';
    $this->load->view('agent/layout', $data);
}

public function register_petugas()
{
    $agent_id = $this->session->userdata('agent_id');
    $nama_petugas = $this->input->post('nama_petugas');

    if (empty($nama_petugas)) {
        $this->session->set_flashdata('error_form', 'Nama petugas tidak boleh kosong.');
    } else {
        if ($this->Agent_model->add_petugas($agent_id, $nama_petugas)) {
            $this->session->set_flashdata('success', 'Petugas berhasil ditambahkan.');
        } else {
            $this->session->set_flashdata('error_form', 'Gagal menambahkan petugas.');
        }
    }
    redirect('agent/petugas');
}
public function delete_petugas($id_petugas)
{
    $agent_id = $this->session->userdata('agent_id');

    if ($this->Agent_model->delete_petugas($id_petugas, $agent_id)) {
        $this->session->set_flashdata('success', 'Petugas berhasil dihapus.');
    } else {
        $this->session->set_flashdata('error_form', 'Gagal menghapus petugas.');
    }

    redirect('agent/petugas');
}
public function get_jenis_by_kategori()
{
    $id_kategori = $this->input->get('id_kategori');
    $result = $this->Agent_model->get_jenis_by_kategori($id_kategori);
    echo json_encode($result);
}
	public function laporan_transaksi()
{
    $agent_id = $this->session->userdata('agent_id');
    $bulan = $this->input->get('bulan');
    $tahun = $this->input->get('tahun');

    $data['bulan'] = $bulan;
    $data['tahun'] = $tahun;
    $data['laporan'] = $this->Agent_model->get_laporan_transaksi($agent_id, $bulan, $tahun);

    $data['view_name'] = 'agent/laporan_transaksi';
    $this->load->view('agent/layout', $data);
}

public function export_excel()
{
    $agent_id = $this->session->userdata('agent_id');
    $bulan = $this->input->get('bulan');
    $tahun = $this->input->get('tahun');

    $laporan = $this->Agent_model->get_laporan_transaksi($agent_id, $bulan, $tahun);

    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=Laporan_Transaksi_Agent_{$bulan}_{$tahun}.xls");

    echo "<table border='1'>
        <tr>
            <th>NO</th>
            <th>TANGGAL</th>
            <th>NO REKENING</th>
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



    public function logout()
    {
        // Hapus semua data session
        $this->session->sess_destroy();

        // Redirect ke halaman utama (landing page)
        redirect(base_url());
    }
}
