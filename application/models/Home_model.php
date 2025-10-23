<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home_model extends CI_Model {

    // ===============================
    // === 1️⃣ TOTAL & RINGKASAN ===
    // ===============================

    public function get_total_waste() {
        $this->db->select_sum('berat');
        $query = $this->db->get('detail_setoran');
        $row = $query->row();
        return $row ? $row->berat : 0;
    }

    public function get_active_agents() {
        $this->db->where('status', 'aktif');
        return $this->db->count_all_results('agent');
    }

    public function get_agent_distribution_by_area()
    {
        $this->db->select('wilayah, COUNT(id_agent) as total');
        $this->db->from('agent');
        $this->db->where('status', 'aktif');
        $this->db->where('wilayah IS NOT NULL');
        $this->db->group_by('wilayah');
        $this->db->order_by('total', 'DESC');
        return $this->db->get()->result_array();
    }

    // ===============================
    // === 2️⃣ STATISTIK SAMPAH ===
    // ===============================

    public function get_waste_by_type() {
        $this->db->select('jenis_sampah.nama_jenis AS name, SUM(detail_setoran.berat) AS amount');
        $this->db->from('detail_setoran');
        $this->db->join('jenis_sampah', 'jenis_sampah.id_jenis = detail_setoran.id_jenis', 'left');
        $this->db->group_by('jenis_sampah.id_jenis');
        return $this->db->get()->result_array();
    }

    /**
     * FUNGSI DIPERBAIKI
     * Menggunakan MIN(tanggal_setor) untuk SELECT dan ORDER BY agar
     * kompatibel dengan sql_mode=only_full_group_by.
     */
    public function get_monthly_waste()
    {
        return $this->db->query("
            SELECT 
                DATE_FORMAT(MIN(tanggal_setor), '%b') AS month,
                SUM(total_berat) AS amount
            FROM transaksi_setoran
            WHERE status = 'selesai'
            GROUP BY YEAR(tanggal_setor), MONTH(tanggal_setor)
            ORDER BY MIN(tanggal_setor) ASC
            LIMIT 6
        ")->result_array();
    }

    // ===============================
    // === 3️⃣ DATA AGEN (UNTUK PETA & TABEL) ===
    // ===============================

    public function get_agents()
    {
        $this->db->select('
            users.nama AS name,
            agent.wilayah AS area,
            agent.status,
            users.latitude,
            users.longitude
        ');
        $this->db->from('agent');
        $this->db->join('users', 'users.id_user = agent.id_user', 'left');
        return $this->db->get()->result_array();
    }

    // ===============================
    // === 4️⃣ FITUR HARGA ===
    // ===============================
    
    public function get_latest_prices_summary($limit = 3)
    {
        $query = $this->db->query("
            SELECT js.nama_jenis, hh.harga
            FROM harga_histori hh
            JOIN jenis_sampah js ON hh.id_jenis = js.id_jenis
            WHERE hh.id_histori IN (
                SELECT MAX(id_histori) FROM harga_histori GROUP BY id_jenis
            )
            ORDER BY hh.id_histori DESC
            LIMIT ?
        ", [$limit]);
        return $query->result_array();
    }

    public function get_waste_categories()
    {
        return $this->db->get('kategori_sampah')->result_array();
    }

    public function get_price_history_by_category($category_id)
    {
        $dates = $this->db->select('tanggal_update')->distinct()->from('harga_histori')->order_by('tanggal_update', 'DESC')->limit(2)->get()->result_array();
        $latest_date = isset($dates[0]) ? $dates[0]['tanggal_update'] : null;
        $previous_date = isset($dates[1]) ? $dates[1]['tanggal_update'] : null;

        if (!$latest_date) return [];

        $this->db->select('js.nama_jenis');
        $this->db->select("(SELECT harga FROM harga_histori WHERE id_jenis = js.id_jenis AND tanggal_update = '{$latest_date}') as harga_sekarang");
        $this->db->select($previous_date ? "(SELECT harga FROM harga_histori WHERE id_jenis = js.id_jenis AND tanggal_update = '{$previous_date}') as harga_sebelumnya" : "0 as harga_sebelumnya");
        $this->db->from('jenis_sampah js');
        $this->db->where('js.id_kategori', $category_id);
        
        return $this->db->get()->result_array();
    }

    // ===============================
    // === 5️⃣ LOGIN & REGISTER ===
    // ===============================
    
    public function insert_user($data)
    {
        $this->db->insert('users', $data);
        return $this->db->insert_id();
    }

    public function insert_agent($data)
    {
        return $this->db->insert('agent', $data);
    }

    public function get_agent_status($user_id)
    {
        $this->db->select('status');
        $this->db->where('id_user', $user_id);
        return $this->db->get('agent')->row_array();
    }

    public function get_user_by_email($email)
    {
        $this->db->where('email', $email);
        return $this->db->get('users')->row_array();
    }
}

