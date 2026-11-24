<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\User as UserModel;
use App\Models\Remunerasi as RemunerasiModel;

class RemunerasiController extends BaseController
{
    private const ALLOWED_ROLES = ['admin', 'kepegawaian'];

    /**
     * Menampilkan halaman input remunerasi.
     */
    public function index()
    {
        if (!hasAnyRole(self::ALLOWED_ROLES)) {
            return redirect()->to('dashboard')->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        $userModel = new UserModel();
        $remunModel = new RemunerasiModel();

        // Ambil filter, default ke bulan & tahun sekarang
        $selectedTahun = (int) ($this->request->getGet('tahun') ?? date('Y'));
        $selectedBulan = (int) ($this->request->getGet('bulan') ?? date('n'));

        // 1. Dapatkan daftar semua pegawai (User) dengan kolom selektif
        $pegawai = $userModel->select('id, nama_lengkap, nip, username, jabatan, unit, role')
                             ->whereIn('role', ['aak', 'kuk', 'spm'])
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
        if (!hasAnyRole(self::ALLOWED_ROLES)) {
            return redirect()->to('dashboard')->with('error', 'Anda tidak memiliki akses untuk mengubah data ini.');
        }

        $remunModel = new RemunerasiModel();

        $tahun = (int) $this->request->getPost('tahun');
        $bulan = (int) $this->request->getPost('bulan');
        $jumlah_arr = $this->request->getPost('jumlah');
        $creator_id = (int) session()->get('id');

        if ($tahun < 2020 || $tahun > 2100 || $bulan < 1 || $bulan > 12 || !is_array($jumlah_arr) || empty($jumlah_arr)) {
            return redirect()->back()->with('error', 'Data tidak lengkap.');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Ambil data yang sudah ada SEKALI SAJA untuk efisiensi
            $existingData = $remunModel
                ->where('tahun', $tahun)
                ->where('bulan', $bulan)
                ->findAll();
            $existingMap = array_column($existingData, 'id', 'user_id');

            // Loop data dari form
            foreach ($jumlah_arr as $user_id => $jumlah) {
                $targetUserId = (int) $user_id;
                if ($targetUserId <= 0) {
                    continue;
                }

                // Bersihkan nilai (hapus 'Rp.' atau '.')
                $cleaned_jumlah = preg_replace('/[^0-9]/', '', (string) $jumlah);
                if ($cleaned_jumlah === '' || $cleaned_jumlah === null) {
                    $cleaned_jumlah = 0;
                }

                $data = [
                    'user_id' => $targetUserId,
                    'tahun'   => $tahun,
                    'bulan'   => $bulan,
                    'jumlah'  => $cleaned_jumlah,
                ];

                if (array_key_exists($targetUserId, $existingMap)) {
                    $remunModel->update($existingMap[$targetUserId], ['jumlah' => $cleaned_jumlah]);
                } else {
                    $data['created_by_user_id'] = $creator_id;
                    $remunModel->insert($data);
                }
            }

            $db->transComplete();
            if ($db->transStatus() === false) {
                throw new \RuntimeException('Transaksi remunerasi gagal.');
            }

            log_audit('UPDATE', 'remunerasi', "{$tahun}-{$bulan}", null, [
                'tahun'        => $tahun,
                'bulan'        => $bulan,
                'total_record' => count($jumlah_arr),
            ]);
        } catch (\Throwable $e) {
            $db->transRollback();
            log_message('error', 'RemunerasiController::store error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menyimpan data remunerasi. Silakan coba lagi.');
        }

        return redirect()->to("remunerasi?tahun={$tahun}&bulan={$bulan}")
                         ->with('success', 'Data remunerasi berhasil disimpan.');
    }
}