<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\User as UserModel;
use App\Models\Remunerasi as RemunerasiModel;

class RemunerasiController extends BaseController
{
    /**
     * Menampilkan halaman input remunerasi.
     */
    public function index()
    {
        $userModel = new UserModel();
        $remunModel = new RemunerasiModel();

        // Ambil filter, default ke bulan & tahun sekarang
        $selectedTahun = $this->request->getGet('tahun') ?? date('Y');
        $selectedBulan = $this->request->getGet('bulan') ?? date('n');

        // 1. Dapatkan daftar semua pegawai (User)
        // ANDA BISA SESUAIKAN DAFTAR ROLE INI
        $pegawai = $userModel->whereIn('role', ['aak', 'kuk', 'spm'])
                             ->orderBy('nama_lengkap', 'ASC')
                             ->findAll();

        // 2. Dapatkan data remunerasi yang sudah ada
        $existingData = $remunModel
            ->where('tahun', $selectedTahun)
            ->where('bulan', $selectedBulan)
            ->findAll();
        
        // 3. Ubah data existing menjadi map [user_id => jumlah] agar mudah diakses di view
        $remunMap = array_column($existingData, 'jumlah', 'user_id');

        $data = [
            'page_title'      => 'Input Remunerasi Pegawai',
            'pegawai_list'    => $pegawai,
            'remun_map'       => $remunMap,
            'selectedTahun'   => $selectedTahun,
            'selectedBulan'   => $selectedBulan,
        ];

        return view('Admin/remunerasi_index', $data);
    }

    /**
     * Menyimpan data remunerasi
     */
    public function store()
    {
        $remunModel = new RemunerasiModel();

        $tahun = $this->request->getPost('tahun');
        $bulan = $this->request->getPost('bulan');
        $jumlah_arr = $this->request->getPost('jumlah');
        $creator_id = session()->get('user_id');

        if (empty($tahun) || empty($bulan) || empty($jumlah_arr)) {
            return redirect()->back()->with('error', 'Data tidak lengkap.');
        }

        $dataToSave = [];

        // Loop data dari form
        foreach ($jumlah_arr as $user_id => $jumlah) {
            // Bersihkan nilai (hapus 'Rp.' atau '.')
            $cleaned_jumlah = preg_replace("/[^0-9]/", "", $jumlah);
            
            if (empty($cleaned_jumlah)) {
                $cleaned_jumlah = 0;
            }

            // Cari data yang sudah ada
            $existing = $remunModel
                ->where('user_id', $user_id)
                ->where('tahun', $tahun)
                ->where('bulan', $bulan)
                ->first();

            $data = [
                'user_id' => $user_id,
                'tahun'   => $tahun,
                'bulan'   => $bulan,
                'jumlah'  => $cleaned_jumlah,
                'created_by_user_id' => $creator_id
            ];

            if ($existing) {
                // Jika ada, update (hanya jumlah dan updated_at)
                $remunModel->update($existing['id'], ['jumlah' => $cleaned_jumlah]);
            } else {
                // Jika tidak ada, insert
                $remunModel->insert($data);
            }
        }

        return redirect()->to("admin/remunerasi?tahun=$tahun&bulan=$bulan")
                         ->with('success', 'Data remunerasi berhasil disimpan.');
    }
}