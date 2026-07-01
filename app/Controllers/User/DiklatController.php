<?php

namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Models\JadwalDiklat as JadwalDiklatModel;

class DiklatController extends BaseController
{
    public function index()
    {
        $diklatModel = new JadwalDiklatModel();
        $data = [
            'page_title' => 'Data Diklat',
            'daftar_diklat' => $diklatModel->orderBy('periode', 'DESC')->findAll(),
            'validation' => \Config\Services::validation()
        ];
        return view('user/data/diklat', $data);
    }

    public function store()
    {
        $rules = [
            'nama_diklat' => 'required',
            'periode' => 'required',
            'jumlah_peserta' => 'required|numeric',
            'status' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->to('/diklat')->withInput()->with('error', 'Terdapat kesalahan input.')->with('show_modal', 'addDiklatModal');
        }

        $diklatModel = new JadwalDiklatModel();
        $diklatModel->save([
            'user_id' => session()->get('user_id'),
            'nama_diklat' => $this->request->getPost('nama_diklat'),
            'periode' => $this->request->getPost('periode'),
            'jumlah_peserta' => $this->request->getPost('jumlah_peserta'),
            'status' => $this->request->getPost('status'),
        ]);

        return redirect()->to('/diklat')->with('success', 'Program Diklat baru berhasil ditambahkan.');
    }

    public function update($id)
    {
        $rules = [
            'nama_diklat' => 'required',
            'periode' => 'required',
            'jumlah_peserta' => 'required|numeric',
            'status' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->to('/diklat')->withInput()->with('error', 'Terdapat kesalahan input.')->with('show_modal', 'editDiklatModal-' . $id);
        }

        $diklatModel = new JadwalDiklatModel();
        $diklatModel->update($id, [
            'nama_diklat' => $this->request->getPost('nama_diklat'),
            'periode' => $this->request->getPost('periode'),
            'jumlah_peserta' => $this->request->getPost('jumlah_peserta'),
            'status' => $this->request->getPost('status'),
        ]);

        return redirect()->to('/diklat')->with('success', 'Data Diklat berhasil diperbarui.');
    }

    public function delete($id)
    {
        $diklatModel = new JadwalDiklatModel();
        $diklatModel->delete($id);
        return redirect()->to('/diklat')->with('success', 'Data Diklat berhasil dihapus.');
    }
}
