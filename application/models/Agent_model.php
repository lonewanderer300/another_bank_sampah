<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Agent_model extends CI_Model {

    // --- FUNGSI BARU UNTUK DASHBOARD & REKAPITULASI ---

    public function count_agent_transactions($agent_id)
    {
        $this->db->where('id_agent', $agent_id);
        return $this->db->count_all_results('transaksi_setoran');
    }

    public function count_agent_customers($agent_id)
    {
        $this->db->select('COUNT(DISTINCT id_user) as total_customers');
        $this->db->where('id_agent', $agent_id);
        $query = $this->db->get('transaksi_setoran');
        return $query->row()->total_customers ?? 0;
    }

    public function get_total_waste_by_agent($agent_id)
    {
        $this->db->select_sum('total_berat');
        $this->db->where('id_agent', $agent_id);
        $query = $this->db->get('transaksi_setoran');
        return $query->row()->total_berat ?? 0;
    }

    public function get_total_income_by_agent($agent_id)
    {
        $this->db->select('SUM(ds.subtotal_poin) as total_income');
        $this->db->from('transaksi_setoran ts');
        $this->db->join('detail_setoran ds', 'ts.id_setoran = ds.id_setoran');
        $this->db->where('ts.id_agent', $agent_id);
        $query = $this->db->get();
        $row = $query->row();
        return $row->total_income ?? 0;
    }

    public function get_agent_transactions($agent_id)
    {
        $this->db->select('ts.*, u.nama as customer_name, (SELECT SUM(ds.subtotal_poin) FROM detail_setoran ds WHERE ds.id_setoran = ts.id_setoran) as transaction_value');
        $this->db->from('transaksi_setoran ts');
        $this->db->join('users u', 'u.id_user = ts.id_user');
        $this->db->where('ts.id_agent', $agent_id);
        $this->db->order_by('ts.tanggal_setor', 'DESC');
        return $this->db->get()->result_array();
    }

    public function get_agent_customers_list($agent_id)
    {
        $this->db->select('u.nama, u.email, u.phone, MAX(ts.tanggal_setor) as last_transaction');
        $this->db->from('transaksi_setoran ts');
        $this->db->join('users u', 'u.id_user = ts.id_user');
        $this->db->where('ts.id_agent', $agent_id);
        $this->db->group_by('u.id_user');
        $this->db->order_by('last_transaction', 'DESC');
        return $this->db->get()->result_array();
    }
    
    public function get_agent_profile($user_id)
    {
        $this->db->select('u.*, a.wilayah');
        $this->db->from('users u');
        $this->db->join('agent a', 'u.id_user = a.id_user');
        $this->db->where('u.id_user', $user_id);
        return $this->db->get()->row_array();
    }

    public function update_profile($user_id, $agent_id, $data_user, $data_agent)
    {
        $this->db->trans_start();

        // Update tabel users
        $this->db->where('id_user', $user_id);
        $this->db->update('users', $data_user);

        // Update tabel agent
        $this->db->where('id_agent', $agent_id);
        $this->db->update('agent', $data_agent);

        $this->db->trans_complete();

        return $this->db->trans_status();
    }
}