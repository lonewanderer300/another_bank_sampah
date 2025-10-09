<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_model extends CI_Model {

    public function get_pending_agents()
    {
        $this->db->select('users.name, users.email, agent.*');
        $this->db->from('agent');
        $this->db->join('users', 'users.id_user = agent.id_user');
        $this->db->where('agent.status', 'pending');
        return $this->db->get()->result_array();
    }

    public function update_agent_status($id_agent, $status)
    {
        $this->db->where('id_agent', $id_agent);
        $this->db->update('agent', ['status' => $status]);
    }
}