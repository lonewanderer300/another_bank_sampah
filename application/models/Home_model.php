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

    // ===============================
    // === 2️⃣ STATISTIK SAMPAH ===
    // ===============================

    // Total per jenis sampah (untuk Pie Chart)
    public function get_waste_by_type() {
        $this->db->select('jenis_sampah.nama_jenis AS name, SUM(detail_setoran.berat) AS amount');
        $this->db->from('detail_setoran');
        $this->db->join('jenis_sampah', 'jenis_sampah.id_jenis = detail_setoran.id_jenis', 'left');
        $this->db->group_by('jenis_sampah.id_jenis');
        $query = $this->db->get();
        return $query->result_array();
    }

    // Total bulanan dari tabel transaksi_setoran (untuk Bar Chart)
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
        ")->result_array();
    }


    // ===============================
    // === 3️⃣ DATA AGEN ===
    // ===============================

    public function get_agents()
    {
        $this->db->select('
            agent.id_agent,
            users.nama AS name,
            agent.wilayah AS area,
            agent.status,
            agent.latitude,
            agent.longitude
        ');
        $this->db->from('agent');
        $this->db->join('users', 'users.id_user = agent.id_user', 'left');
        $query = $this->db->get();

        $agents = $query->result_array();

        // Default Barito Selatan
        $defaultLat = -1.86667;
        $defaultLng = 114.73333;

        foreach ($agents as &$a) {
            if (!isset($a['latitude']) || $a['latitude'] === null) {
                $a['latitude'] = $defaultLat;
            }
            if (!isset($a['longitude']) || $a['longitude'] === null) {
                $a['longitude'] = $defaultLng;
            }
        }

        return $agents;
    }

    // ===============================
    // === 4️⃣ REGISTRASI USER & AGEN ===
    // ===============================
    
    public function insert_user($data)
    {
        $this->db->insert('users', $data);
        return $this->db->insert_id(); // Mengembalikan ID dari user yang baru dibuat
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
}
