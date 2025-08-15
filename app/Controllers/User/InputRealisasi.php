<?php

namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Models\RencanaKinerja as RencanaKinerjaModel;

class InputRealisasi extends BaseController
{
    /**
     * Menampilkan form untuk input realisasi bulan berjalan.
     */
    public function index()
    {
        $rencanaModel = new RencanaKinerjaModel();
        $user_id = session()->get('user_id');
        
        $tahun_sekarang = date('Y');
        $bulan_sekarang = date('n');

        $data = [
            'page_title' => 'Input Realisasi Bulan Ini',
            'tahun_sekarang' => $tahun_sekarang,
            'bulan_sekarang' => $bulan_sekarang,
            'nama_bulan_sekarang' => date('F'),
            'rencana_kinerja' => $rencanaModel->where('user_id', $user_id)
                                               ->where('tahun_anggaran', $tahun_sekarang)
                                               ->findAll(),
        ];

        return view('user/rencana/input_realisasi', $data);
    }

    /**
     * Menyimpan data realisasi dari form.
     */
    public function store()
    {
        $rencanaModel = new RencanaKinerjaModel();
        $user_id = session()->get('user_id');
        $bulan_sekarang = date('n');

        $realisasi_arr = $this->request->getPost('realisasi');

        $rencanaModel->db->transStart();
        try {
            if (!empty($realisasi_arr)) {
                foreach ($realisasi_arr as $id_rencana => $nilai_realisasi) {
                    // Hanya proses jika nilai realisasi diisi
                    if ($nilai_realisasi !== '') {
                        $rencana = $rencanaModel->find($id_rencana);
                        // Pastikan data milik user yang benar
                        if ($rencana && $rencana['user_id'] == $user_id) {
                            $realisasi_data = json_decode($rencana['realisasi_bulanan'], true) ?? array_fill(0, 12, null);
                            // Update nilai untuk bulan sekarang
                            $realisasi_data[$bulan_sekarang - 1] = $nilai_realisasi;
                            
                            $rencanaModel->update($id_rencana, ['realisasi_bulanan' => json_encode($realisasi_data)]);
                        }
                    }
                }
            }
            $rencanaModel->db->transComplete();
            
            if ($rencanaModel->db->transStatus() === false) {
                 return redirect()->back()->with('error', 'Gagal menyimpan data realisasi.');
            }

            return redirect()->to('/user/realisasi/input')->with('success', 'Data realisasi berhasil disimpan!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
