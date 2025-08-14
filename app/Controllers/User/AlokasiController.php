<?php

namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Models\RencanaKinerja as RencanaKinerjaModel;

class AlokasiController extends BaseController
{
    /**
     * Menampilkan halaman untuk mengelola alokasi dan realisasi bulanan.
     */
    public function index()
    {
        $rencanaModel = new RencanaKinerjaModel();
        $user_id = session()->get('user_id');
        
        $tahun_terpilih = $this->request->getGet('tahun');

        if (!$tahun_terpilih) {
            return redirect()->to('/user/kinerja/update')
                ->with('error', 'Silakan pilih tahun rencana terlebih dahulu.');
        }

        $data = [
            'page_title'      => 'Kelola Target & Realisasi Bulanan',
            'tahun_terpilih'  => $tahun_terpilih,
            'rencana_kinerja' => $rencanaModel->where('user_id', $user_id)
                                             ->where('tahun_anggaran', $tahun_terpilih)
                                             ->findAll(),
        ];

        return view('user/rencana/alokasi_bulanan', $data);
    }

    /**
     * Memproses pembaruan data dari form alokasi dan realisasi.
     */
    public function update()
    {
        $rencanaModel = new RencanaKinerjaModel();
        $user_id = session()->get('user_id');
        
        $tahun = $this->request->getPost('tahun');
        $indikator_arr = $this->request->getPost('indikator_kinerja');
        $target_utama_arr = $this->request->getPost('target_utama');
        $target_bulanan_arr = $this->request->getPost('target_bulanan');
        // Ambil data realisasi yang baru
        $realisasi_bulanan_arr = $this->request->getPost('realisasi_bulanan');

        $rencanaModel->db->transStart();

        if (!empty($indikator_arr)) {
            foreach($indikator_arr as $id => $indikator) {
                $data = [
                    'indikator_kinerja' => $indikator,
                    'target_utama'      => $target_utama_arr[$id],
                    'target_bulanan'    => json_encode(array_values($target_bulanan_arr[$id] ?? [])),
                    // Tambahkan realisasi ke data update
                    'realisasi_bulanan' => json_encode(array_values($realisasi_bulanan_arr[$id] ?? [])),
                ];
                $rencanaModel->where('id', $id)->where('user_id', $user_id)->set($data)->update();
            }
        }

        $rencanaModel->db->transComplete();

        if ($rencanaModel->db->transStatus() === false) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan data.');
        }

        return redirect()->to('/user/alokasi/bulanan?tahun=' . $tahun)
            ->with('success', 'Data target dan realisasi berhasil diperbarui!');
    }
}
