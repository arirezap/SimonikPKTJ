<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\Sasaran as SasaranModel;
use App\Models\Indikator as IndikatorModel;
use App\Models\Satuan as SatuanModel;

class MasterDataController extends BaseController
{
    // --- FUNGSI UNTUK SASARAN PROGRAM ---
    public function sasaran()
    {
        $sasaranModel = new SasaranModel();
        $data = [
            'page_title' => 'Master Sasaran Program',
            'items' => $sasaranModel->findAll(),
            'validation' => \Config\Services::validation()
        ];
        return view('admin/master/sasaran', $data);
    }

    public function storeSasaran()
    {
        $rules = ['nama_sasaran' => 'required|is_unique[sasaran.nama_sasaran]'];
        if (!$this->validate($rules)) {
            session()->setFlashdata('show_modal', 'addModal');
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }
        $sasaranModel = new SasaranModel();
        $sasaranModel->save(['nama_sasaran' => $this->request->getPost('nama_sasaran')]);
        return redirect()->to('/admin/master-data/sasaran')->with('success', 'Data sasaran berhasil ditambahkan.');
    }

    public function updateSasaran($id)
    {
        $rules = ['nama_sasaran' => 'required|is_unique[sasaran.nama_sasaran,id,' . $id . ']'];
        if (!$this->validate($rules)) {
            session()->setFlashdata('show_modal', 'editModal-' . $id);
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }
        $sasaranModel = new SasaranModel();
        $sasaranModel->update($id, ['nama_sasaran' => $this->request->getPost('nama_sasaran')]);
        return redirect()->to('/admin/master-data/sasaran')->with('success', 'Data sasaran berhasil diperbarui.');
    }

    public function deleteSasaran($id)
    {
        $sasaranModel = new SasaranModel();
        $sasaranModel->delete($id);
        return redirect()->to('/admin/master-data/sasaran')->with('success', 'Data sasaran berhasil dihapus.');
    }


    // --- FUNGSI UNTUK INDIKATOR KINERJA ---
    public function indikator()
    {
        $indikatorModel = new IndikatorModel();
        $data = [
            'page_title' => 'Master Indikator Kinerja',
            'items' => $indikatorModel->findAll(),
            'validation' => \Config\Services::validation()
        ];
        return view('admin/master/indikator', $data);
    }

    public function storeIndikator()
    {
        $rules = ['nama_indikator' => 'required|is_unique[indikator.nama_indikator]'];
        if (!$this->validate($rules)) {
            session()->setFlashdata('show_modal', 'addModal');
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }
        $indikatorModel = new IndikatorModel();
        $indikatorModel->save(['nama_indikator' => $this->request->getPost('nama_indikator')]);
        return redirect()->to('/admin/master-data/indikator')->with('success', 'Data indikator berhasil ditambahkan.');
    }

    public function updateIndikator($id)
    {
        $rules = ['nama_indikator' => 'required|is_unique[indikator.nama_indikator,id,' . $id . ']'];
        if (!$this->validate($rules)) {
            session()->setFlashdata('show_modal', 'editModal-' . $id);
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }
        $indikatorModel = new IndikatorModel();
        $indikatorModel->update($id, ['nama_indikator' => $this->request->getPost('nama_indikator')]);
        return redirect()->to('/admin/master-data/indikator')->with('success', 'Data indikator berhasil diperbarui.');
    }

    public function deleteIndikator($id)
    {
        $indikatorModel = new IndikatorModel();
        $indikatorModel->delete($id);
        return redirect()->to('/admin/master-data/indikator')->with('success', 'Data indikator berhasil dihapus.');
    }


    // --- FUNGSI UNTUK SATUAN ---
    public function satuan()
    {
        $satuanModel = new SatuanModel();
        $data = [
            'page_title' => 'Master Satuan',
            'items' => $satuanModel->findAll(),
            'validation' => \Config\Services::validation()
        ];
        return view('admin/master/satuan', $data);
    }

    public function storeSatuan()
    {
        $rules = ['nama_satuan' => 'required|is_unique[satuan.nama_satuan]'];
        if (!$this->validate($rules)) {
            session()->setFlashdata('show_modal', 'addModal');
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }
        $satuanModel = new SatuanModel();
        $satuanModel->save(['nama_satuan' => $this->request->getPost('nama_satuan')]);
        return redirect()->to('/admin/master-data/satuan')->with('success', 'Data satuan berhasil ditambahkan.');
    }

    public function updateSatuan($id)
    {
        $rules = ['nama_satuan' => 'required|is_unique[satuan.nama_satuan,id,' . $id . ']'];
        if (!$this->validate($rules)) {
            session()->setFlashdata('show_modal', 'editModal-' . $id);
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }
        $satuanModel = new SatuanModel();
        $satuanModel->update($id, ['nama_satuan' => $this->request->getPost('nama_satuan')]);
        return redirect()->to('/admin/master-data/satuan')->with('success', 'Data satuan berhasil diperbarui.');
    }

    public function deleteSatuan($id)
    {
        $satuanModel = new SatuanModel();
        $satuanModel->delete($id);
        return redirect()->to('/admin/master-data/satuan')->with('success', 'Data satuan berhasil dihapus.');
    }
}
