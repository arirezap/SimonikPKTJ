<?php

namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Models\RencanaKinerja as RencanaKinerjaModel;

class DaftarRencana extends BaseController
{
    public function index()
    {
        $rencanaModel = new RencanaKinerjaModel();
        $user_id = session()->get('user_id');
        
        $tahun_terpilih = $this->request->getGet('tahun') ?? date('Y');

        $data = [
            'page_title' => 'Kelola Rencana Kinerja',
            'tahun_terpilih' => $tahun_terpilih,
            'daftar_tahun' => $rencanaModel->select('tahun_anggaran')
                                         ->where('user_id', $user_id)
                                         ->distinct()
                                         ->orderBy('tahun_anggaran', 'DESC')
                                         ->findAll(),
            'rencana_kinerja' => $rencanaModel->where('user_id', $user_id)
                                               ->where('tahun_anggaran', $tahun_terpilih)
                                               ->findAll()
        ];

        return view('user/rencana/daftar_rencana', $data);
    }

    /**
     * NEW FUNCTION: Processes updates from the edit modal.
     */
    public function update($id)
    {
        // Server-side validation
        $rules = [
            'sasaran_program'   => 'required',
            'indikator_kinerja' => 'required',
            'satuan'            => 'required',
            'target_utama'      => 'required|numeric',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', $this->validator->listErrors());
        }

        $rencanaModel = new RencanaKinerjaModel();
        $user_id = session()->get('user_id');

        // Collect data from the form
        $data = [
            'sasaran_program'   => $this->request->getPost('sasaran_program'),
            'indikator_kinerja' => $this->request->getPost('indikator_kinerja'),
            'satuan'            => $this->request->getPost('satuan'),
            'target_utama'      => $this->request->getPost('target_utama'),
            'kegiatan'          => $this->request->getPost('kegiatan'),
        ];

        // Update the data, ensuring only the correct user can update
        $rencanaModel->where('id', $id)->where('user_id', $user_id)->set($data)->update();

        return redirect()->back()->with('success', 'Rencana Kinerja berhasil diperbarui.');
    }

    public function delete($id)
    {
        $rencanaModel = new RencanaKinerjaModel();
        $user_id = session()->get('user_id');

        $data = $rencanaModel->where('id', $id)->where('user_id', $user_id)->first();

        if ($data) {
            $rencanaModel->delete($id);
            return redirect()->back()->with('success', 'Satu Rencana Kinerja berhasil dihapus.');
        }
        
        return redirect()->back()->with('error', 'Data tidak ditemukan atau Anda tidak memiliki izin.');
    }
}
