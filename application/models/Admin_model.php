<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_model extends CI_Model {

    // --- DASHBOARD ---
    public function get_pending_agents()
    {
        $this->db->select('u.nama, u.email, a.id_agent');
        $this->db->from('agent a');
        $this->db->join('users u', 'a.id_user = u.id_user');
        $this->db->where('a.status', 'pending');
        return $this->db->get()->result_array();
    }

    public function count_unpaid_customers()
    {
        // Logika ini adalah contoh. Anda perlu tabel/kolom 'iuran_status' di tabel 'users'.
        // Untuk saat ini, kita akan mengembalikan angka statis.
        // $this->db->where('iuran_status', 'unpaid');
        // return $this->db->count_all_results('users');
        return 12; // Contoh
    }
    
    // --- HARGA SAMPAH ---
    public function get_all_waste_types()
    {
        $this->db->select('js.*, hh.harga, hh.tanggal_update');
        $this->db->from('jenis_sampah js');
        $this->db->join('(SELECT id_jenis, MAX(id_histori) as max_id FROM harga_histori GROUP BY id_jenis) h_max', 'js.id_jenis = h_max.id_jenis', 'left');
        $this->db->join('harga_histori hh', 'h_max.max_id = hh.id_histori', 'left');
        return $this->db->get()->result_array();
    }

    public function update_waste_price($waste_type_id, $new_price)
    {
        return $this->db->insert('harga_histori', [
            'id_jenis' => $waste_type_id,
            'harga' => $new_price,
            'tanggal_update' => date('Y-m-d')
        ]);
    }

    // --- MANAJEMEN AGEN & NASABAH ---
    public function get_all_agents()
    {
        $this->db->select('u.nama, u.email, u.phone, a.wilayah, a.status, a.id_agent');
        $this->db->from('agent a');
        $this->db->join('users u', 'a.id_user = u.id_user');
        return $this->db->get()->result_array();
    }
    
    public function get_all_customers()
    {
        $this->db->select('nama, email, phone, saldo, poin, created_at');
        $this->db->where('role', 'user');
        return $this->db->get('users')->result_array();
    }
    
    public function approve_agent($agent_id)
    {
        $this->db->where('id_agent', $agent_id);
        return $this->db->update('agent', ['status' => 'aktif']);
    }

    public function reject_agent($agent_id)
    {
        $this->db->where('id_agent', $agent_id);
        return $this->db->update('agent', ['status' => 'nonaktif']);
    }
	public function get_all_nasabah_with_iuran()
{
    $this->db->select('n.id_nasabah, n.tipe_nasabah, n.jumlah_nasabah, i.biaya, i.deadline, i.status_iuran');
    $this->db->from('nasabah n');
    $this->db->join('iuran i', 'i.id_nasabah = n.id_nasabah', 'left');
    return $this->db->get()->result_array();
}


public function add_or_update_iuran($data)
{
    $existing = $this->db->get_where('iuran', ['id_nasabah' => $data['id_nasabah']])->row_array();

    if ($existing) {
        // Update existing iuran
        $this->db->where('id_nasabah', $data['id_nasabah']);
        return $this->db->update('iuran', [
            'biaya' => $data['biaya'],
            'deadline' => $data['deadline'],
            'status_iuran' => 'belum bayar'
        ]);
    } else {
        // Insert new iuran
        return $this->db->insert('iuran', $data);
    }
}

}
