<?php

namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Models\RencanaKinerja as RencanaKinerjaModel;

class AlokasiController extends BaseController
{
    public function index()
    {
        $rencanaModel = new RencanaKinerjaModel();
        $user_id = session()->get('id');
        $tahun_terpilih = $this->request->getGet('tahun');

        if (!$tahun_terpilih) {
            return redirect()->to('/user/kinerja/update')->with('error', 'Silakan pilih tahun rencana terlebih dahulu.');
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
     * Update Data via JSON (Batch Update + Disable Callbacks)
     */
    public function update()
    {
        // 1. Konfigurasi Server (Mencegah Timeout)
        ini_set('max_execution_time', 300); 
        ini_set('memory_limit', '512M');

        $rencanaModel = new RencanaKinerjaModel();
        $user_id = session()->get('id');
        
        // 2. Ambil JSON Data
        $jsonData = $this->request->getJSON(true); 

        if (!$jsonData) {
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'Data JSON tidak diterima/valid.']);
        }

        $tahun = $jsonData['tahun'] ?? '';
        $items = $jsonData['items'] ?? [];

        if (empty($items)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Tidak ada data yang dikirim.']);
        }

        // 3. Validasi ID User (Sekali Query untuk performa)
        $validIds = $rencanaModel->select('id')->where('user_id', $user_id)->findColumn('id');
        if (empty($validIds)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Data tidak ditemukan atau bukan milik Anda.']);
        }

        // 4. Siapkan Data Batch
        $updateData = [];
        foreach ($items as $item) {
            // Pastikan ID valid milik user
            if (in_array($item['id'], $validIds)) {
                $updateData[] = [
                    'id'                => $item['id'],
                    // Encode manual di sini sebagai string JSON final
                    'target_bulanan'    => json_encode($item['target_bulanan']),
                    'realisasi_bulanan' => json_encode($item['realisasi_bulanan']),
                ];
            }
        }

        if (empty($updateData)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Tidak ada perubahan data yang valid.']);
        }

        // 5. Eksekusi Simpan
        $rencanaModel->db->transStart();
        
        try {
            // PENTING: Matikan Callbacks Model!
            // Karena kita pakai updateBatch dengan data yang SUDAH di-json_encode manual.
            // Jika callbacks aktif, Model akan mencoba encode lagi (double encode) yang bikin data rusak.
            $rencanaModel->allowCallbacks(false)
                         ->updateBatch($updateData, 'id', 50); // Batch size 50 agar aman
            
            $rencanaModel->db->transComplete();

            if ($rencanaModel->db->transStatus() === false) {
                $error = $rencanaModel->db->error();
                return $this->response->setStatusCode(500)->setJSON(['status' => 'error', 'message' => 'Database Error: ' . $error['message']]);
            }

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Berhasil menyimpan ' . count($updateData) . ' data!',
                'redirect' => site_url('user/alokasi/bulanan?tahun=' . $tahun)
            ]);

        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Exception: ' . $e->getMessage()
            ]);
        }
    }
}