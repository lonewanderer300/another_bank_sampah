<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// PERBAIKAN: Mengubah nama class dari 'User' menjadi 'User_model'
class User_model extends CI_Model {

    // --- FUNGSI UNTUK DASHBOARD ---

    public function get_user_balance($user_id)
    {
        $this->db->select('saldo');
        $this->db->where('id_user', $user_id);
        $query = $this->db->get('users');
        $row = $query->row();
        return $row ? $row->saldo : 0;
    }

    public function get_user_points($user_id)
    {
        $this->db->select('poin');
        $this->db->where('id_user', $user_id);
        $query = $this->db->get('users');
        $row = $query->row();
        return $row ? $row->poin : 0;
    }
    
    public function count_user_transactions($user_id)
    {
        $this->db->where('id_user', $user_id);
        return $this->db->count_all_results('transaksi_setoran');
    }

    public function get_user_transactions($user_id, $limit = null)
    {
        // PERBAIKAN FINAL: Menggunakan 'subtotal_poin' di sub-query
        $this->db->select('ts.*, a.wilayah as agent_area, u_agent.nama as agent_name, (SELECT SUM(ds.subtotal_poin) FROM detail_setoran ds WHERE ds.id_setoran = ts.id_setoran) as transaction_value');
        $this->db->from('transaksi_setoran ts');
        $this->db->join('agent a', 'a.id_agent = ts.id_agent', 'left');
        $this->db->join('users u_agent', 'u_agent.id_user = a.id_user', 'left');
        $this->db->where('ts.id_user', $user_id);
        $this->db->order_by('ts.tanggal_setor', 'DESC');
        if ($limit) {
            $this->db->limit($limit);
        }
        return $this->db->get()->result_array();
    }
    
    public function get_total_waste_by_user($user_id)
    {
        $this->db->select_sum('total_berat');
        $this->db->where('id_user', $user_id);
        $query = $this->db->get('transaksi_setoran');
        $row = $query->row();
        return $row->total_berat ?? 0;
    }

    // --- FUNGSI BARU UNTUK DASHBOARD ---

    public function get_waste_summary_by_user($user_id)
    {
        $this->db->select('js.nama_jenis as waste_type, SUM(ds.berat) as total_weight');
        $this->db->from('transaksi_setoran ts');
        $this->db->join('detail_setoran ds', 'ts.id_setoran = ds.id_setoran');
        $this->db->join('jenis_sampah js', 'ds.id_jenis = js.id_jenis');
        $this->db->where('ts.id_user', $user_id);
        $this->db->group_by('js.nama_jenis');
        $this->db->order_by('total_weight', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    // --- FUNGSI UNTUK HALAMAN BANK SAMPAH ---

    public function get_nearest_agents($latitude, $longitude, $radius = 10, $limit = 5)
    {
        // Rumus Haversine untuk menghitung jarak berdasarkan koordinat
        $this->db->select("
            u.nama as name, 
            u.phone,
            u.address,
            a.wilayah, 
            u.latitude, 
            u.longitude, 
            (
                6371 * acos(
                    cos(radians(" . $latitude . ")) 
                    * cos(radians(u.latitude)) 
                    * cos(radians(u.longitude) - radians(" . $longitude . ")) 
                    + sin(radians(" . $latitude . ")) 
                    * sin(radians(u.latitude))
                )
            ) AS distance
        ");
        $this->db->from('agent a');
        $this->db->join('users u', 'u.id_user = a.id_user');
        $this->db->where('a.status', 'aktif');
        $this->db->having('distance <=', $radius);
        $this->db->order_by('distance', 'ASC');
        $this->db->limit($limit);

        return $this->db->get()->result_array();
    }

    public function get_all_active_agents()
    {
        $this->db->select('users.nama as name, users.phone, users.address, agent.wilayah, users.latitude, users.longitude');
        $this->db->from('agent');
        $this->db->join('users', 'users.id_user = agent.id_user');
        $this->db->where('agent.status', 'aktif');
        return $this->db->get()->result_array();
    }

    // --- FUNGSI BARU UNTUK HALAMAN TRANSAKSI (DIPERBAIKI) ---

    public function get_total_income_by_user($user_id)
    {
        // PERBAIKAN FINAL: Menggunakan nama kolom yang 100% benar 'subtotal_poin'
        $this->db->select('SUM(ds.subtotal_poin) as total_income');
        $this->db->from('transaksi_setoran ts');
        $this->db->join('detail_setoran ds', 'ts.id_setoran = ds.id_setoran');
        $this->db->where('ts.id_user', $user_id);
        $query = $this->db->get();
        $row = $query->row();
        return $row->total_income ?? 0;
    }

    // --- FUNGSI UNTUK HALAMAN PROFIL ---

    public function get_user_profile($user_id)
    {
        $this->db->where('id_user', $user_id);
        return $this->db->get('users')->row_array();
    }

    public function update_profile($user_id, $data)
    {
        $this->db->where('id_user', $user_id);
        return $this->db->update('users', $data);
    }
    
    // --- FUNGSI UNTUK REGISTRASI ---
    
    public function check_email_exists($email)
    {
        $this->db->where('email', $email);
        $query = $this->db->get('users');
        return $query->num_rows() > 0;
    }
    
    public function register($data_user, $data_agent = null)
    {
        $this->db->trans_start();

        $this->db->insert('users', $data_user);
        $user_id = $this->db->insert_id();

        if ($data_agent !== null) {
            $data_agent['id_user'] = $user_id;
            $this->db->insert('agent', $data_agent);
        }

        $this->db->trans_complete();

        return ($this->db->trans_status() === FALSE) ? false : $user_id;
    }
	// --- FUNGSI UNTUK NASABAH ---
public function get_nasabah_by_user($user_id)
{
    $this->db->where('id_users', $user_id);
    return $this->db->get('nasabah')->row_array();
}

public function add_nasabah($data)
{
    return $this->db->insert('nasabah', $data);
}
public function simpan()
{
    $tipe = $this->input->post('tipe_nasabah');
    $jumlah = $this->input->post('jumlah_nasabah');

    if ($tipe === 'perorangan') {
        $jumlah = 1; // force 1 for perorangan
    }

    $data = [
        'tipe_nasabah' => $tipe,
        'jumlah_nasabah' => $jumlah,
        // tambahkan kolom lain sesuai database kamu
    ];

    $this->db->insert('nasabah', $data);
    redirect('nasabah');
}


}

