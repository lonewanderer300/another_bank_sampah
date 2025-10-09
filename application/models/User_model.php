<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model {

    public function get_total_setoran($id_user) {
        return $this->db->where('id_user', $id_user)
                        ->count_all_results('transaksi_setoran');
    }

    public function get_total_poin($id_user) {
        $query = $this->db->select_sum('total_poin')
                          ->where('id_user', $id_user)
                          ->get('transaksi_setoran')
                          ->row();
        return $query && $query->total_poin ? $query->total_poin : 0;
    }

    public function get_recent_setoran($id_user) {
        $query = $this->db->select('ts.*, SUM(ds.berat) as total_berat')
                          ->from('transaksi_setoran ts')
                          ->join('detail_setoran ds', 'ts.id_setoran = ds.id_setoran', 'left')
                          ->where('ts.id_user', $id_user)
                          ->group_by('ts.id_setoran')
                          ->order_by('ts.tanggal_setor', 'DESC')
                          ->limit(5)
                          ->get();
        return $query->result_array();
    }
    
	 public function register($data_user, $data_agent = null)
    {
        // Insert user first
        $this->db->insert('users', $data_user);
        $user_id = $this->db->insert_id();

        // If role is agent, insert into agent table
        if ($data_user['role'] === 'agent' && $data_agent !== null) {
            $data_agent['id_user'] = $user_id;
            $this->db->insert('agent', $data_agent);
        }

        return $user_id;
    }

    public function check_email_exists($email, $role = null)
{
    $this->db->where('email', $email);

    if ($role !== null) {
        // Optional: check within specific role
        $this->db->where('role', $role);
    }

    return $this->db->get('users')->num_rows() > 0;
}
	public function get_user_by_email($email)
{
    return $this->db->where('email', $email)
                    ->get('users')
                    ->row();
}

}
