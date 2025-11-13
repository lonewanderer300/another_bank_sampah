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
        // Logika ini diperbarui untuk menghitung langsung dari tabel 'iuran'
        $this->db->where('status_iuran', 'belum bayar');
        
        // Opsional: Jika Anda hanya ingin menghitung yang deadline-nya sudah lewat (menunggak)
        // $this->db->where('deadline <', date('Y-m-d')); 
        
        return $this->db->count_all_results('iuran');
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
        // Pastikan 'users.id_user' ada di dalam select
        $this->db->select('agent.id_agent, users.id_user, users.nama, users.email, agent.wilayah, agent.status');
        $this->db->from('agent');
        $this->db->join('users', 'users.id_user = agent.id_user');
        $this->db->where('users.role', 'agent');
        return $this->db->get()->result_array();
    }
    
    public function get_all_customers()
    {
        $this->db->select('u.*, agent_user.nama as nama_agent');
        $this->db->from('users u'); // 'u' adalah alias untuk tabel users

        // GABUNG ke tabel 'agent' untuk dapat 'id_user' si agent
        // berdasarkan 'id_agent_pilihan' milik si user
        $this->db->join('agent a', 'u.id_agent_pilihan = a.id_agent', 'left');

        // GABUNG LAGI ke tabel 'users' (sebagai 'agent_user') untuk dapat NAMA si agent
        $this->db->join('users agent_user', 'a.id_user = agent_user.id_user', 'left');
        
        /*
        * PENTING:
        * Fungsi Anda namanya 'get_all_customers', jadi saya asumsikan
        * Anda hanya ingin menampilkan user dengan role 'user' (nasabah).
        * * Jika Anda ingin menampilkan SEMUA user (termasuk admin/agent lain)
        * di tabel ini, hapus baris '$this->db->where()' di bawah ini.
        */
        $this->db->where('u.role', 'user'); 
        
        // Mengurutkan berdasarkan nama (opsional)
        $this->db->order_by('u.nama', 'ASC'); 

        // Mengambil hasil kueri
        return $this->db->get()->result_array();
    }

    // public function get_all_users_nasabah()
    // {
    //     // Pastikan 'users.role' ada di dalam select
    //     $this->db->select('users.id_user, users.nama, users.email, users.phone, users.role');
    //     $this->db->from('users');
    //     $this->db->where('role', 'user');
    //     return $this->db->get()->result_array();

    //     $this->db->select('users.id_user, users.nama, users.email, users.phone, users.role');
    //     $this->db->from('users');
    //     $this->db->where('role', 'user');
    //     return $this->db->get()->result_array();
    // }
    
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
            // ✅ Update only biaya and status, keep the existing deadline
            $this->db->where('id_nasabah', $data['id_nasabah']);
            return $this->db->update('iuran', [
                'biaya' => $data['biaya'],
                'status_iuran' => 'belum bayar'
            ]);
        } else {
            // Insert new iuran if not exists
            return $this->db->insert('iuran', $data);
        }
    }

    // FUNGSI BARU UNTUK EDIT/DELETE
    
    public function get_user_by_id($id_user)
    {
        return $this->db->get_where('users', ['id_user' => $id_user])->row_array();
    }

    public function get_agent_by_user_id($id_user)
    {
        return $this->db->get_where('agent', ['id_user' => $id_user])->row_array();
    }

    public function update_user($id_user, $data)
    {
        $this->db->where('id_user', $id_user);
        return $this->db->update('users', $data);
    }

    public function update_agent_by_user_id($id_user, $data)
    {
        // Cek dulu apakah data agent sudah ada
        $exists = $this->db->get_where('agent', ['id_user' => $id_user])->num_rows() > 0;
        
        if ($exists) {
            $this->db->where('id_user', $id_user);
            return $this->db->update('agent', $data);
        } else {
            // Jika user diubah jadi agent, tapi data agent blm ada, buat baru
            $data['id_user'] = $id_user;
            return $this->db->insert('agent', $data);
        }
    }

    public function delete_user_and_related_data($id_user)
    {
        // Model ini akan menghapus user dan data terkait (agent/nasabah)
        // karena ada foreign key constraint (ON DELETE CASCADE) di database.
        // Jika tidak ada CASCADE, Anda harus hapus manual dari tabel agent/nasabah dulu.
        
        // Asumsi ON DELETE CASCADE sudah diset di database
        $this->db->where('id_user', $id_user);
        return $this->db->delete('users');
        
        /* // Jika TIDAK ADA CASCADE, lakukan ini:
        $this->db->trans_start();
        $this->db->delete('agent', ['id_user' => $id_user]);
        $this->db->delete('nasabah', ['id_users' => $id_user]); // pastikan nama kolom 'id_users'
        $this->db->delete('users', ['id_user' => $id_user]);
        $this->db->trans_complete();
        return $this->db->trans_status();
        */
    }

    public function get_wilayah_enum_values()
    {
        // Jalankan kueri SQL 'SHOW COLUMNS'
        $query = $this->db->query("SHOW COLUMNS FROM agent LIKE 'wilayah'");
        
        if (!$query || $query->num_rows() == 0) {
            return []; // Gagal atau kolom tidak ditemukan
        }

        $row = $query->row_array();
        
        // Hasilnya akan seperti: "enum('Dusun Selatan','Gunung Bintang Awai',...)"
        $type = $row['Type']; 

        // Gunakan regex untuk mengekstrak semua nilai di dalam tanda kutip
        preg_match_all("/'([^']*)'/", $type, $matches);

        // $matches[1] akan berisi array: ['Dusun Selatan', 'Gunung Bintang Awai', ...]
        return $matches[1] ?? []; 
    }
	public function get_laporan_transaksi_admin($bulan = null, $tahun = null)
{
    $this->db->select("
        ts.tanggal_setor,
        ru.no_rekening,
        u.nama AS nama_nasabah,
        agt.id_agent,
        ua.nama AS nama_agent,
        ts.id_setoran,
        ts.total_poin AS pendapatan,
        js.kode,
        js.nama_jenis,
        js.id_jenis,
        ks.nama_kategori,
        ts.total_berat,
        ds.berat,
        th.jumlah AS tarik_tunai,
        u.saldo AS saldo_akhir,
        tps.nama_tipe AS tipe_sampah,
        p.nama_petugas,
        hh.harga,
        ds.berat AS jumlah_kg,
        0 AS jumlah_botol
    ");
    $this->db->from('transaksi_setoran ts');
    $this->db->join('detail_setoran ds', 'ds.id_setoran = ts.id_setoran', 'left');
    $this->db->join('jenis_sampah js', 'js.id_jenis = ds.id_jenis', 'left');
    $this->db->join('kategori_sampah ks', 'ks.id_kategori = js.id_kategori', 'left');
    $this->db->join('harga_histori hh', 'hh.id_jenis = js.id_jenis', 'left');
    $this->db->join('users u', 'u.id_user = ts.id_user', 'left');
    $this->db->join('rekening_user ru', 'ru.id_user = u.id_user', 'left');
    $this->db->join('tipe_sampah tps', 'tps.id_tipe_sampah = js.id_tipe_sampah', 'left');
    $this->db->join('transaksi_penarikan th', 'th.id_user = u.id_user', 'left');
    $this->db->join('agent agt', 'agt.id_agent = ts.id_agent', 'left');
    $this->db->join('users ua', 'ua.id_user = agt.id_user', 'left'); // untuk nama agent
    $this->db->join('petugas p', 'p.id_agent = ts.id_agent', 'left');

    if ($bulan && $tahun) {
        $this->db->where('MONTH(ts.tanggal_setor)', $bulan);
        $this->db->where('YEAR(ts.tanggal_setor)', $tahun);
    }

    $this->db->order_by('ts.tanggal_setor', 'ASC');
    $query = $this->db->get();
    return $query->result_array();
}


}
