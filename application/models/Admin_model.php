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
        $this->db->select('agent.id_agent, users.id_user, users.nama, users.email, agent.wilayah, agent.status');
        $this->db->from('agent');
        $this->db->join('users', 'users.id_user = agent.id_user');
        $this->db->where('users.role', 'agent');
        return $this->db->get()->result_array();
    }
    
    public function get_all_customers()
    {
        $this->db->select('u.*, agent_user.nama as nama_agent');
        $this->db->from('users u');
        $this->db->join('agent a', 'u.id_agent_pilihan = a.id_agent', 'left');
        $this->db->join('users agent_user', 'a.id_user = agent_user.id_user', 'left');
        $this->db->where('u.role', 'user'); 
        $this->db->order_by('u.nama', 'ASC'); 
        return $this->db->get()->result_array();
    }

    public function get_all_nasabah_with_iuran()
    {
        
	// 	    $this->db->select('
    //     iuran.id_nasabah,
    //     iuran.biaya,
    //     iuran.deadline,
    //     iuran.status_iuran,
    //     nasabah.tipe_nasabah,
    //     nasabah.jumlah_nasabah,
    //     users.nama AS nama_user
    // ');
    // $this->db->from('iuran');
    // $this->db->join('nasabah', 'nasabah.id_nasabah = iuran.id_nasabah', 'left');
    // $this->db->join('users', 'users.id_user = nasabah.id_users', 'left');

    // return $this->db->get()->result_array();
	    $this->db->select('
        iuran.*,
        nasabah.tipe_nasabah,
        nasabah.jumlah_nasabah,
        users.nama AS nama_user,
        agent_users.nama AS nama_agent
    ');
    $this->db->from('iuran');
    $this->db->join('nasabah', 'nasabah.id_nasabah = iuran.id_nasabah', 'left');
    $this->db->join('users', 'users.id_user = nasabah.id_users', 'left'); // pemilik akun
    $this->db->join('agent', 'agent.id_agent = users.id_agent_pilihan', 'left'); // agent terpilih
    $this->db->join('users AS agent_users', 'agent_users.id_user = agent.id_user', 'left'); // nama bank sampah

    return $this->db->get()->result_array();
    }

    public function add_or_update_iuran($data)
    {
        $existing = $this->db->get_where('iuran', ['id_nasabah' => $data['id_nasabah']])->row_array();

        if ($existing) {
            $this->db->where('id_nasabah', $data['id_nasabah']);
            return $this->db->update('iuran', [
                'biaya' => $data['biaya'],
                'status_iuran' => 'belum bayar'
            ]);
        } else {
            $data['deadline'] = $data['deadline'] ?? date('Y-m-d', strtotime('+30 days'));
            return $this->db->insert('iuran', $data);
        }
    }

    // FUNGSI BARU UNTUK EDIT/DELETE USER
    
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
        $exists = $this->db->get_where('agent', ['id_user' => $id_user])->num_rows() > 0;
        
        if ($exists) {
            $this->db->where('id_user', $id_user);
            return $this->db->update('agent', $data);
        } else {
            $data['id_user'] = $id_user;
            return $this->db->insert('agent', $data);
        }
    }

    public function delete_user_and_related_data($id_user)
    {
        $this->db->where('id_user', $id_user);
        return $this->db->delete('users');
    }

    public function get_wilayah_enum_values()
    {
        $query = $this->db->query("SHOW COLUMNS FROM agent LIKE 'wilayah'");
        
        if (!$query || $query->num_rows() == 0) {
            return [];
        }

        $row = $query->row_array();
        $type = $row['Type']; 
        preg_match_all("/'([^']*)'/", $type, $matches);

        return $matches[1] ?? []; 
    }
    
    // =============================================================
    // --- MANAJEMEN TRANSAKSI OLEH ADMIN ---
    // =============================================================

    /**
     * FUNGSI BARU: Mengambil daftar semua user untuk dropdown (Admin)
     */
    public function get_all_users_for_dropdown()
    {
        $this->db->select('id_user, nama, role');
        $this->db->from('users');
        $this->db->where('role', 'user'); 
        $this->db->or_where('role', 'agent'); // Admin juga bisa memilih Agen sebagai nasabah jika diperlukan
        $this->db->order_by('nama', 'ASC');
        return $this->db->get()->result_array();
    }

    /**
     * FUNGSI BARU: Mengambil daftar semua agen untuk dropdown (Admin)
     */
    public function get_all_agents_for_dropdown()
    {
        $this->db->select('a.id_agent, u.nama');
        $this->db->from('agent a');
        $this->db->join('users u', 'u.id_user = a.id_user');
        $this->db->where('a.status', 'aktif');
        $this->db->order_by('u.nama', 'ASC');
        return $this->db->get()->result_array();
    }
    
    /**
     * FUNGSI BARU: Mengambil semua kategori sampah (Helper)
     */
    public function get_all_categories()
    {
        return $this->db->get('kategori_sampah')->result_array();
    }
    
    /**
     * FUNGSI BARU: Mengambil petugas berdasarkan Agent ID (Helper)
     */
    public function get_petugas_by_agent($agent_id)
    {
        if (empty($agent_id)) {
            return [];
        }
        $this->db->where('id_agent', $agent_id);
        $query = $this->db->get('petugas');
        return $query->result_array();
    }

    /**
     * FUNGSI BARU: Mengambil jenis sampah berdasarkan kategori (Helper)
     */
    public function get_jenis_by_kategori($id_kategori)
    {
        $this->db->select('js.id_jenis, js.nama_jenis, hh.harga');
        $this->db->from('jenis_sampah js');
        $this->db->join('(SELECT id_jenis, MAX(id_histori) as latest FROM harga_histori GROUP BY id_jenis) as sub', 'sub.id_jenis = js.id_jenis', 'inner');
        $this->db->join('harga_histori hh', 'hh.id_histori = sub.latest', 'inner');
        $this->db->where('js.id_kategori', $id_kategori);
        return $this->db->get()->result_array();
    }

    /**
     * Mengambil semua transaksi setoran dengan nama Nasabah dan Agen.
     */
    public function get_all_transactions()
    {
        $this->db->select('ts.id_setoran, ts.tanggal_setor, ts.total_berat, ts.total_poin, u_nasabah.nama as nasabah_name, u_agent.nama as agent_name');
        $this->db->from('transaksi_setoran ts');
        // JOIN ke users untuk nama Nasabah
        $this->db->join('users u_nasabah', 'u_nasabah.id_user = ts.id_user', 'left');
        // JOIN ke agent untuk mendapatkan id_user dari agent
        $this->db->join('agent a', 'a.id_agent = ts.id_agent', 'left');
        // JOIN ke users lagi untuk nama Agent (Bank Sampah)
        $this->db->join('users u_agent', 'u_agent.id_user = a.id_user', 'left');
        $this->db->order_by('ts.tanggal_setor', 'DESC');
        return $this->db->get()->result_array();
    }

    /**
     * Menghapus transaksi dan mengoreksi saldo nasabah.
     */
    public function delete_transaction($id_setoran)
    {
        $this->db->trans_start();

        $transaction = $this->db->select('id_user, total_poin')->where('id_setoran', $id_setoran)->get('transaksi_setoran')->row();

        if ($transaction) {
            $user_id = $transaction->id_user;
            $total_poin = $transaction->total_poin;

            // Koreksi saldo/poin user (mengurangi nilai yang dihapus)
            $this->db->set('poin', 'poin - ' . $total_poin, FALSE);
            $this->db->set('saldo', 'saldo - ' . $total_poin, FALSE);
            $this->db->where('id_user', $user_id);
            $this->db->update('users');
            
            // Hapus detail transaksi (perintah ini akan menghapus detail yang terikat)
            $this->db->where('id_setoran', $id_setoran)->delete('detail_setoran');

            // Hapus transaksi master record
            $this->db->where('id_setoran', $id_setoran)->delete('transaksi_setoran');
        }

        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    /**
     * Mengambil data transaksi master, nasabah, dan agen yang terlibat.
     * PERBAIKAN: Memastikan nama Agent (Bank Sampah) ikut terambil.
     */
    public function get_transaction_data($id_setoran)
    {
        $this->db->select('
            ts.*, 
            u_nasabah.nama as customer_name, 
            a.id_agent, 
            u_agent.nama as agent_name
        ');
        $this->db->from('transaksi_setoran ts');
        // Join ke users untuk nama Nasabah
        $this->db->join('users u_nasabah', 'u_nasabah.id_user = ts.id_user', 'left');
        // Join ke agent untuk mendapatkan id_user dari agent
        $this->db->join('agent a', 'a.id_agent = ts.id_agent', 'left');
        // Join ke users lagi untuk nama Agent (Bank Sampah)
        $this->db->join('users u_agent', 'u_agent.id_user = a.id_user', 'left');
        $this->db->where('ts.id_setoran', $id_setoran);
        return $this->db->get()->row_array();
    }

    /**
     * Mengambil detail item sampah, kategori, dan harga saat ini (untuk pre-fill form edit).
     */
    public function get_current_waste_details($id_setoran)
    {
        $this->db->select('ds.id_detail, ds.id_jenis, ds.berat, ds.subtotal_poin, js.nama_jenis, ks.id_kategori, ks.nama_kategori');
        $this->db->from('detail_setoran ds');
        $this->db->join('jenis_sampah js', 'js.id_jenis = ds.id_jenis');
        $this->db->join('kategori_sampah ks', 'ks.id_kategori = js.id_kategori');
        $this->db->where('ds.id_setoran', $id_setoran);
        $details = $this->db->get()->result_array();

        foreach ($details as &$detail) {
            $this->db->select('harga');
            $this->db->from('harga_histori');
            $this->db->where('id_jenis', $detail['id_jenis']);
            $this->db->order_by('tanggal_update', 'DESC');
            $this->db->limit(1);
            $current_price_row = $this->db->get()->row();
            $detail['current_price'] = $current_price_row ? $current_price_row->harga : 0;
        }
        unset($detail);

        return $details;
    }

    /**
     * Mengupdate transaksi: koreksi saldo lama, hapus detail lama, simpan detail baru, koreksi saldo baru.
     */
    public function update_transaction_and_user_balance($id_setoran, $old_total_poin, $customer_id, $transaction_date, $valid_waste_items, $id_agent_baru = null, $id_petugas = null)
    {
        $total_berat_transaksi = 0;
        $total_poin_transaksi = 0;
        $detail_batch_data = [];

        $prices = [];
        $waste_type_ids = array_keys($valid_waste_items);

        $this->db->select('id_jenis, harga');
        $this->db->from('harga_histori');
        $this->db->where_in('id_jenis', $waste_type_ids);
        $this->db->where_in('id_histori', "(SELECT MAX(id_histori) FROM harga_histori GROUP BY id_jenis)", FALSE);
        $price_query = $this->db->get();
        foreach ($price_query->result() as $row) {
            $prices[$row->id_jenis] = $row->harga;
        }

        foreach ($valid_waste_items as $id_jenis => $berat) {
            if (!isset($prices[$id_jenis]) || $prices[$id_jenis] <= 0) {
                return ['success' => false, 'message' => 'Harga belum diatur untuk id_jenis: ' . $id_jenis . '. Transaksi dibatalkan.'];
            }

            $harga_satuan = $prices[$id_jenis];
            $subtotal_poin_item = $berat * $harga_satuan;

            $total_berat_transaksi += $berat;
            $total_poin_transaksi += $subtotal_poin_item;

            $detail_batch_data[] = [
                'id_setoran'    => $id_setoran,
                'id_jenis'      => $id_jenis,
                'berat'         => $berat,
                'subtotal_poin' => $subtotal_poin_item
            ];
        }
        
        $this->db->trans_start();

        // A. Revert old points/saldo (kurangi saldo lama)
        $this->db->set('poin', 'poin - ' . $old_total_poin, FALSE);
        $this->db->set('saldo', 'saldo - ' . $old_total_poin, FALSE);
        $this->db->where('id_user', $customer_id);
        $this->db->update('users');

        // B. Hapus detail transaksi lama
        $this->db->where('id_setoran', $id_setoran);
        $this->db->delete('detail_setoran');

        // C. Insert detail transaksi baru
        $this->db->insert_batch('detail_setoran', $detail_batch_data);

        // D. Update transaksi master
        $setoran_data = [
            'id_user'       => $customer_id, 
            'tanggal_setor' => $transaction_date,
            'total_berat'   => $total_berat_transaksi,
            'total_poin'    => $total_poin_transaksi
        ];
        
        // Khusus Admin: Perbarui Agen jika ada input baru (id_agent_baru)
        if (!empty($id_agent_baru)) {
            $setoran_data['id_agent'] = $id_agent_baru;
        }
        
        // Perbarui id_petugas jika kolomnya ada di transaksi setoran (meskipun SQL Anda tidak menunjukkannya)
        // Jika Anda ingin menyimpan id_petugas, Anda harus menambahkan kolom 'id_petugas' ke tabel 'transaksi_setoran' dulu.
        // Untuk saat ini, kita biarkan kosong di sini.
        // if (!empty($id_petugas) && KONDISI_KOLOM_PETUGAS_ADA) { $setoran_data['id_petugas'] = $id_petugas; }

        $this->db->where('id_setoran', $id_setoran);
        $this->db->update('transaksi_setoran', $setoran_data);

        // E. Add new points/saldo (tambahkan saldo baru)
        $this->db->set('poin', 'poin + ' . $total_poin_transaksi, FALSE);
        $this->db->set('saldo', 'saldo + ' . $total_poin_transaksi, FALSE); 
        $this->db->where('id_user', $customer_id);
        $this->db->update('users');

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            return ['success' => false, 'message' => 'Gagal memperbarui transaksi ke database.'];
        } else {
            return ['success' => true, 'message' => 'Transaksi berhasil diperbarui.'];
        }
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
	public function get_nasabah_detail($id_nasabah)
{
    $this->db->select('n.*, u.nama, u.email, u.phone');
    $this->db->from('nasabah n');
    $this->db->join('users u', 'u.id_user = n.id_users', 'left');
    $this->db->where('n.id_nasabah', $id_nasabah);
    return $this->db->get()->row_array();
}
// Ambil semua master iuran
public function get_all_iuran_master()
{
    return $this->db->get('iuran_master')->result_array();
}

// Update biaya iuran_master
public function update_iuran_master($id_master, $biaya)
{
    return $this->db->update('iuran_master', 
        ['biaya' => $biaya],
        ['id_master' => $id_master]
    );
}

// Tambah row baru
public function add_iuran_master($data)
{
    return $this->db->insert('iuran_master', $data);
}
public function update_iuran_belum_bayar_by_master($tipe, $jumlah, $biaya_baru)
{
    // Cari semua nasabah dengan matching tipe & jumlah
    $this->db->select('id_nasabah');
    $this->db->from('nasabah');
    $this->db->where('tipe_nasabah', $tipe);
    $this->db->where('jumlah_nasabah', $jumlah);
    $nasabah_list = $this->db->get()->result_array();

    if (!empty($nasabah_list)) {
        $ids = array_column($nasabah_list, 'id_nasabah');

        // Update hanya iuran dengan status belum bayar
        $this->db->where_in('id_nasabah', $ids);
        $this->db->where('status_iuran', 'belum bayar');
        return $this->db->update('iuran', [
            'biaya' => $biaya_baru
        ]);
    }

    return true;
}

    
}
