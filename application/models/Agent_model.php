<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Agent_model extends CI_Model {

    // Total setoran diterima oleh agent
    public function get_total_setoran($id_agent) {
        return $this->db->where('id_agent', $id_agent)
                        ->count_all_results('transaksi_setoran');
    }

    // Total berat dari semua setoran ke agent
    public function get_total_waste($id_agent) {
        $query = $this->db->select_sum('ds.berat')
                          ->from('detail_setoran ds')
                          ->join('transaksi_setoran ts', 'ts.id_setoran = ds.id_setoran', 'left')
                          ->where('ts.id_agent', $id_agent)
                          ->get()
                          ->row();
        return $query && $query->berat ? $query->berat : 0;
    }

    // Daftar user yang setor ke agent ini
    public function get_my_users($id_agent) {
        $query = $this->db->select('u.id_user, u.nama, u.email, u.username, COUNT(ts.id_setoran) as total_setoran, SUM(ts.total_poin) as total_poin')
                          ->from('transaksi_setoran ts')
                          ->join('users u', 'u.id_user = ts.id_user', 'left')
                          ->where('ts.id_agent', $id_agent)
                          ->group_by('u.id_user')
                          ->get();
        return $query->result_array();
    }

    // Statistik bulanan sampah agent
    public function get_waste_trends($id_agent) {
        $query = $this->db->select('MONTH(ts.tanggal_setor) as bulan, SUM(ds.berat) as total_berat')
                          ->from('detail_setoran ds')
                          ->join('transaksi_setoran ts', 'ts.id_setoran = ds.id_setoran', 'left')
                          ->where('ts.id_agent', $id_agent)
                          ->group_by('MONTH(ts.tanggal_setor)')
                          ->order_by('bulan', 'ASC')
                          ->get();
        return $query->result_array();
    }
}
