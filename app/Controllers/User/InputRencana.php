<?php

namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Models\RencanaKinerja as RencanaKinerjaModel;
use App\Models\Sasaran as SasaranModel;
use App\Models\Indikator as IndikatorModel;
use App\Models\Satuan as SatuanModel;

class InputRencana extends BaseController
{
    public function index()
    {
        $rencanaModel = new RencanaKinerjaModel();
        $user_id = session()->get('user_id');
        $tahun_terpilih = $this->request->getGet('tahun') ?? date('Y');

        // Ambil data dari tabel master untuk dropdown
        $sasaranModel = new SasaranModel();
        $indikatorModel = new IndikatorModel();
        $satuanModel = new SatuanModel();

        $existing_plans_years = $rencanaModel->select('tahun_anggaran')->where('user_id', $user_id)->distinct()->findAll();
        $years_with_data = array_column($existing_plans_years, 'tahun_anggaran');

        $data = [
            'page_title' => 'Input & Kelola Rencana Kerja',
            'tahun_terpilih' => $tahun_terpilih,
            'existing_years_json' => json_encode($years_with_data),
            'rencana_kinerja' => $rencanaModel->where('user_id', $user_id)->where('tahun_anggaran', $tahun_terpilih)->findAll(),
            // Kirim data master ke view
            'daftar_sasaran' => $sasaranModel->findAll(),
            'daftar_indikator' => $indikatorModel->findAll(),
            'daftar_satuan' => $satuanModel->findAll(),
        ];

        return view('User/rencana/input_tahunan', $data);
    }

    /**
     * Menyimpan atau memperbarui data rencana tahunan.
     */
    public function store()
    {
        // ... (Validasi server-side tetap sama seperti sebelumnya) ...

        $rencanaModel = new RencanaKinerjaModel();
        $user_id = session()->get('user_id');
        $tahun_anggaran = $this->request->getPost('tahun_anggaran');

        // Ambil semua data dari form
        $rencana_ids = $this->request->getPost('rencana_id');
        $sasaran_program_arr = $this->request->getPost('sasaran_program');
        $indikator_kinerja_arr = $this->request->getPost('indikator_kinerja');
        $satuan_arr = $this->request->getPost('satuan');
        $target_utama_arr = $this->request->getPost('target_utama');
        $kegiatan_arr = $this->request->getPost('kegiatan');

        $dataToUpdate = [];
        $dataToInsert = [];

        foreach ($sasaran_program_arr as $index => $sasaran) {
            if (empty($sasaran)) continue; // Lewati baris kosong

            $rowData = [
                'user_id'           => $user_id,
                'tahun_anggaran'    => $tahun_anggaran,
                'sasaran_program'   => $sasaran,
                'indikator_kinerja' => $indikator_kinerja_arr[$index],
                'satuan'            => $satuan_arr[$index],
                'target_utama'      => $target_utama_arr[$index],
                'kegiatan'          => $kegiatan_arr[$index],
            ];

            // Cek apakah ini data lama (ada id) atau data baru (id kosong)
            if (!empty($rencana_ids[$index])) {
                // Data untuk di-update
                $rowData['id'] = $rencana_ids[$index];
                $dataToUpdate[] = $rowData;
            } else {
                // Data baru untuk di-insert
                $rowData['target_bulanan'] = json_encode(array_fill(0, 12, 0));
                $dataToInsert[] = $rowData;
            }
        }

        // Jalankan query
        if (!empty($dataToUpdate)) {
            $rencanaModel->updateBatch($dataToUpdate, 'id');
        }
        if (!empty($dataToInsert)) {
            $rencanaModel->insertBatch($dataToInsert);
        }

        return redirect()->to('/user/alokasi/bulanan?tahun=' . $tahun_anggaran)
            ->with('success', 'Rencana kerja tahun ' . $tahun_anggaran . ' berhasil disimpan!');
    }
}
