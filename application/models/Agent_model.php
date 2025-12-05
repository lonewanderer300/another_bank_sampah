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
        // PERBAIKAN: Hitung user yang memilih agent ini sebagai agent pilihan mereka (id_agent_pilihan)
        $this->db->where('id_agent_pilihan', $agent_id);
        $this->db->where('role', 'user'); 
        return $this->db->count_all_results('users');
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

    public function get_agent_transactions($agent_id, $limit = null) 
    {
        $this->db->select('ts.*, u.nama as customer_name, ts.total_poin, ts.total_berat'); 
        $this->db->from('transaksi_setoran ts');
        $this->db->join('users u', 'u.id_user = ts.id_user', 'left');
        $this->db->where('ts.id_agent', $agent_id);
        $this->db->order_by('ts.tanggal_setor', 'DESC');
        
        if ($limit !== null) {
            $this->db->limit($limit);
        }
        
        return $this->db->get()->result_array();
    }

    /**
     * Mengambil daftar nasabah (role user) YANG SUDAH MEMILIH agent ini
     */
    public function get_registered_customers_for_dropdown($agent_id) // Tambahkan parameter $agent_id
    {
        $this->db->select('id_user, nama');
        $this->db->from('users'); // Perlu from() jika pakai where()
        $this->db->where('role', 'user');
        $this->db->where('id_agent_pilihan', $agent_id); // Filter berdasarkan agent yang dipilih
        $this->db->order_by('nama', 'ASC');
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Mengambil daftar jenis sampah beserta harga terkini
     */
    public function get_all_waste_types_with_prices()
    {
        // Query untuk mendapatkan harga terkini per jenis sampah
        // Beri alias 'latest_price' pada subquery
        $subQuery = "(SELECT id_jenis, harga FROM harga_histori WHERE id_histori IN (SELECT MAX(id_histori) FROM harga_histori GROUP BY id_jenis)) as latest_price";
        // PERBAIKAN: Gunakan alias 'latest_price' bukan 'lp'
        $this->db->select('js.id_jenis, js.nama_jenis, latest_price.harga');
        $this->db->from('jenis_sampah js');
        // Join dengan subquery harga terkini
        $this->db->join($subQuery, 'js.id_jenis = latest_price.id_jenis', 'left');
        $this->db->order_by('js.nama_jenis', 'ASC');
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Menyimpan transaksi setoran baru dengan multiple detail sampah
     * @param int $agent_id ID agent yang login
     * @param int $customer_id ID user nasabah
     * @param string $transaction_date Tanggal transaksi (Y-m-d H:i:s)
     * @param array $waste_items Array asosiatif [id_jenis => berat]
     * @return array Status penyimpanan ['success' => bool, 'message' => string]
     */
    public function save_transaction($agent_id, $customer_id, $transaction_date, $waste_items)
    {
        $total_berat_transaksi = 0;
        $total_poin_transaksi = 0;
        $detail_batch_data = []; // Untuk batch insert detail
        // Ambil semua harga terkini sekaligus untuk efisiensi
        $prices = [];
        $waste_type_ids = array_keys($waste_items); // Ambil ID jenis sampah dari input
        if (empty($waste_type_ids)) {
            return ['success' => false, 'message' => 'Tidak ada detail sampah yang dimasukkan.'];
        }

        $this->db->select('id_jenis, harga');
        $this->db->from('harga_histori');
        $this->db->where_in('id_jenis', $waste_type_ids);
        $this->db->where_in('id_histori', "(SELECT MAX(id_histori) FROM harga_histori GROUP BY id_jenis)", FALSE); // Ambil harga terbaru
        $price_query = $this->db->get();
        foreach ($price_query->result() as $row) {
            $prices[$row->id_jenis] = $row->harga;
        }

        // Validasi dan hitung subtotal untuk setiap item
        foreach ($waste_items as $id_jenis => $berat) {
            // Pastikan berat valid
            if (!is_numeric($berat) || $berat <= 0) {
                continue; // Lewati item ini jika berat tidak valid
            }
            // Pastikan harga ada
            if (!isset($prices[$id_jenis]) || $prices[$id_jenis] <= 0) {
                return ['success' => false, 'message' => 'Harga belum diatur untuk id_jenis: ' . $id_jenis . '. Transaksi dibatalkan.'];



            }
            $harga_satuan = $prices[$id_jenis];
            $subtotal_poin_item = $berat * $harga_satuan;
            // Tambahkan ke total transaksi
            $total_berat_transaksi += $berat;
            $total_poin_transaksi += $subtotal_poin_item;

            // Siapkan data untuk batch insert detail
            $detail_batch_data[] = [
                // id_setoran akan diisi nanti setelah insert utama
                'id_jenis'      => $id_jenis,
                'berat'         => $berat,
                'subtotal_poin' => $subtotal_poin_item
            ];
        }
        // Jika tidak ada detail yang valid setelah divalidasi
        if (empty($detail_batch_data)) {
            return ['success' => false, 'message' => 'Tidak ada detail sampah yang valid untuk disimpan.'];
        }
        // Mulai Database Transaction
        $this->db->trans_start();

        // Insert ke tabel transaksi_setoran (master)
        $setoran_data = [
            'id_user'       => $customer_id,
            'id_agent'      => $agent_id,
            'tanggal_setor' => $transaction_date,
            'status'        => 'selesai',
            'total_berat'   => $total_berat_transaksi,
            'total_poin'    => $total_poin_transaksi
        ];
        log_message('debug', 'DEBUG id_jenis value: ' . print_r($id_jenis, true));

        $this->db->insert('transaksi_setoran', $setoran_data);
        $id_setoran = $this->db->insert_id();
        // Update id_setoran untuk setiap item di batch data detail
        foreach ($detail_batch_data as &$detail_item) { // Gunakan reference (&) untuk update langsung
            $detail_item['id_setoran'] = $id_setoran;
        }
        unset($detail_item); // Hapus reference setelah loop    
        // Insert multiple detail ke tabel detail_setoran menggunakan Batch Insert
        $this->db->insert_batch('detail_setoran', $detail_batch_data);

        // (Opsional) Update saldo/poin user
        $this->db->set('poin', 'poin + ' . $total_poin_transaksi, FALSE);
        $this->db->set('saldo', 'saldo + ' . $total_poin_transaksi, FALSE); // Asumsi poin = nilai rupiah
        $this->db->where('id_user', $customer_id);
        $this->db->update('users');
        // Selesaikan Database Transaction
        $this->db->trans_complete();

        // Kembalikan status
        if ($this->db->trans_status() === FALSE) {
            return ['success' => false, 'message' => 'Gagal menyimpan transaksi ke database.'];
        } else {
             return ['success' => true, 'message' => 'Transaksi berhasil disimpan.'];
        }
    }

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

    public function get_current_waste_details($id_setoran)
    {
        // 1. Ambil detail yang tersimpan
        $this->db->select('ds.id_detail, ds.id_jenis, ds.berat, ds.subtotal_poin, js.nama_jenis, ks.id_kategori, ks.nama_kategori');
        $this->db->from('detail_setoran ds');
        $this->db->join('jenis_sampah js', 'js.id_jenis = ds.id_jenis');
        $this->db->join('kategori_sampah ks', 'ks.id_kategori = js.id_kategori');
        $this->db->where('ds.id_setoran', $id_setoran);
        $details = $this->db->get()->result_array();

        // 2. Ambil harga terbaru saat ini untuk setiap jenis sampah
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

    public function update_transaction_and_user_balance($id_setoran, $old_total_poin, $customer_id, $transaction_date, $valid_waste_items)
    {
        $total_berat_transaksi = 0;
        $total_poin_transaksi = 0;
        $detail_batch_data = [];

        // --- 1. Ambil harga terbaru saat ini untuk perhitungan ---
        $prices = [];
        $waste_type_ids = array_keys($valid_waste_items);
        if (empty($waste_type_ids)) {
            return ['success' => false, 'message' => 'Tidak ada detail sampah yang valid untuk disimpan.'];
        }

        $this->db->select('id_jenis, harga');
        $this->db->from('harga_histori');
        $this->db->where_in('id_jenis', $waste_type_ids);
        $this->db->where_in('id_histori', "(SELECT MAX(id_histori) FROM harga_histori GROUP BY id_jenis)", FALSE);
        $price_query = $this->db->get();
        foreach ($price_query->result() as $row) {
            $prices[$row->id_jenis] = $row->harga;
        }

        // --- 2. Validasi dan hitung total baru ---
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
        
        // --- 3. Database Transaction ---
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
            'id_user'       => $customer_id, // Perbarui nasabah jika diganti
            'tanggal_setor' => $transaction_date,
            'total_berat'   => $total_berat_transaksi,
            'total_poin'    => $total_poin_transaksi
        ];
        $this->db->where('id_setoran', $id_setoran);
        $this->db->update('transaksi_setoran', $setoran_data);

        // E. Add new points/saldo (tambahkan saldo baru)
        $this->db->set('poin', 'poin + ' . $total_poin_transaksi, FALSE);
        $this->db->set('saldo', 'saldo + ' . $total_poin_transaksi, FALSE); 
        $this->db->where('id_user', $customer_id);
        $this->db->update('users');

        $this->db->trans_complete();

        // --- 4. Return Status ---
        if ($this->db->trans_status() === FALSE) {
            return ['success' => false, 'message' => 'Gagal memperbarui transaksi ke database.'];
        } else {
            return ['success' => true, 'message' => 'Transaksi berhasil diperbarui.'];
        }
    }
    
    /**
     * Mengambil daftar nasabah yang TELAH MEMILIH agent ini
     */
    public function get_agent_customers_list($agent_id)
    {
        $this->db->select('u.nama, u.email, u.phone, u.created_at as join_date');
        $this->db->from('users u');
        $this->db->where('u.role', 'user');
        $this->db->where('u.id_agent_pilihan', $agent_id); // Filter yang benar
        $this->db->order_by('u.nama', 'ASC');
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
    // --- PETUGAS (staff agent) ---

    public function get_petugas_by_agent($agent_id)
    {
        $this->db->where('id_agent', $agent_id);
        $query = $this->db->get('petugas');
        return $query->result_array();
    }

    public function add_petugas($agent_id, $nama_petugas)
    {
        $data = [
            'id_agent' => $agent_id,
            'nama_petugas' => $nama_petugas
        ];
        return $this->db->insert('petugas', $data);
    }
    public function delete_petugas($id_petugas, $agent_id)
    {
     // Pastikan hanya bisa menghapus petugas milik agent yang login
        $this->db->where('id_petugas', $id_petugas);
        $this->db->where('id_agent', $agent_id);
        return $this->db->delete('petugas');
    }
    public function get_all_categories()
    {
        return $this->db->get('kategori_sampah')->result_array();
    }

    public function get_jenis_by_kategori($id_kategori)
    {
        $this->db->select('js.id_jenis, js.nama_jenis, hh.harga');
        $this->db->from('jenis_sampah js');
        $this->db->join('(SELECT id_jenis, MAX(id_histori) as latest FROM harga_histori GROUP BY id_jenis) as sub', 'sub.id_jenis = js.id_jenis', 'inner');
        $this->db->join('harga_histori hh', 'hh.id_histori = sub.latest', 'inner');
        $this->db->where('js.id_kategori', $id_kategori);
        return $this->db->get()->result_array();
    }

    public function get_laporan_transaksi($agent_id, $bulan = null, $tahun = null)
    {
        $this->db->select("
            ts.tanggal_setor,
            ru.no_rekening,
            u.nama AS nama_nasabah,
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
        $this->db->join('petugas p', 'p.id_agent = ts.id_agent', 'left');
        $this->db->where('ts.id_agent', $agent_id);

        if ($bulan && $tahun) {
            $this->db->where('MONTH(ts.tanggal_setor)', $bulan);
            $this->db->where('YEAR(ts.tanggal_setor)', $tahun);
        }

        $this->db->order_by('ts.tanggal_setor', 'ASC');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_pending_iuran_by_user($user_id)
    {
        $this->db->select('i.id_iuran, i.biaya, i.deadline, i.tanggal_bayar'); 
        $this->db->from('iuran i');
        $this->db->join('nasabah n', 'n.id_nasabah = i.id_nasabah');
        $this->db->where('n.id_users', $user_id);
        $this->db->where('i.status_iuran', 'belum bayar');
        $this->db->limit(1); 
        return $this->db->get()->row_array();
    }

    public function get_iuran_history_by_user($user_id)
    {
        $this->db->select('i.*, n.tipe_nasabah');
        $this->db->from('iuran i');
        $this->db->join('nasabah n', 'n.id_nasabah = i.id_nasabah');
        $this->db->where('n.id_users', $user_id);
        $this->db->order_by('i.deadline', 'DESC');
        return $this->db->get()->result_array();
    }

    public function update_iuran_to_paid($id_iuran)
    {
        $data = [
            'status_iuran' => 'sudah bayar',
            'tanggal_bayar' => date('Y-m-d') // Mengisi tanggal hari ini saat dibayar
        ];
        $this->db->where('id_iuran', $id_iuran);
        return $this->db->update('iuran', $data);
    }

    public function get_all_users_by_agent($agent_id)
    {
        $this->db->select('u.id_user, u.nama, u.email, u.phone, u.address, n.tipe_nasabah, n.jumlah_nasabah'); // 🚨 PERBAIKAN: TAMBAHKAN u.email DI SINI
        $this->db->from('users u');
        // Filter user yang memilih agent ini sebagai agent pilihan mereka
        $this->db->where('u.id_agent_pilihan', $agent_id);
        // Pastikan user adalah role 'user'
        $this->db->where('u.role', 'user'); 
        // Join dengan tabel nasabah (optional, tapi baik untuk data di view)
        $this->db->join('nasabah n', 'n.id_users = u.id_user', 'left'); 
        $this->db->order_by('u.nama', 'ASC');
        return $this->db->get()->result_array();
    }

    public function count_customers_by_agent($agent_id)
    {
        // Asumsi nasabah memilih agent dengan kolom 'id_agent_pilihan' di tabel 'users'
        $this->db->where('id_agent_pilihan', $agent_id);
        $this->db->where('role', 'user');
        return $this->db->count_all_results('users');
    }

    public function count_unpaid_customers_by_agent($agent_id)
    {
        // Mengambil user (nasabah) yang memilih agent ini
        $this->db->select('COUNT(DISTINCT u.id_user) as total');
        $this->db->from('users u');
        $this->db->join('nasabah n', 'n.id_users = u.id_user', 'inner');
        $this->db->join('iuran i', 'i.id_nasabah = n.id_nasabah', 'inner');
        
        // Filter berdasarkan agent yang dipilih oleh nasabah
        $this->db->where('u.id_agent_pilihan', $agent_id);
        // Filter berdasarkan status iuran
        $this->db->where('i.status_iuran', 'belum bayar');
        
        $query = $this->db->get();
        return $query->row() ? $query->row()->total : 0;
    }
}