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

        return view('admin/remunerasi_index', $data);
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
        $creator_id = session()->get('id');

        if (empty($tahun) || empty($bulan) || empty($jumlah_arr)) {
            return redirect()->back()->with('error', 'Data tidak lengkap.');
        }

        // Ambil data yang sudah ada SEKALI SAJA untuk efisiensi
        $existingData = $remunModel
            ->where('tahun', $tahun)
            ->where('bulan', 'bulan')
            ->findAll();
        $existingMap = array_column($existingData, 'id', 'user_id'); // Map [user_id => remunerasi_id]

        // Loop data dari form
        foreach ($jumlah_arr as $user_id => $jumlah) {
            // Bersihkan nilai (hapus 'Rp.' atau '.')
            $cleaned_jumlah = preg_replace("/[^0-9]/", "", $jumlah);

            if (empty($cleaned_jumlah)) {
                $cleaned_jumlah = 0;
            }

            $data = [
                'user_id' => $user_id,
                'tahun'   => $tahun,
                'bulan'   => $bulan,
                'jumlah'  => $cleaned_jumlah,
            ];

            // OPTIMISASI: Cek dari map yang sudah diambil, bukan query ulang
            if (array_key_exists($user_id, $existingMap)) {
                // Jika ada, update
                $remunasi_id = $existingMap[$user_id];
                $remunModel->update($remunasi_id, ['jumlah' => $cleaned_jumlah]);
            } else {
                // Jika tidak ada, tambahkan creator_id dan insert
                $data['created_by_user_id'] = $creator_id;
                $remunModel->insert($data);
            }
        }

        return redirect()->to("admin/remunerasi?tahun=$tahun&bulan=$bulan")
                         ->with('success', 'Data remunerasi berhasil disimpan.');
    }
}