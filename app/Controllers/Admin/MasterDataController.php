<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\Sasaran as SasaranModel;
use App\Models\Indikator as IndikatorModel;
use App\Models\Satuan as SatuanModel;

class MasterDataController extends BaseController
{
    // --- SASARAN ---
    public function sasaran()
    {
        $model = new SasaranModel();
        $data = [
            'page_title' => 'Master Sasaran Program',
            'items' => $model->findAll(),
            'validation' => \Config\Services::validation()
        ];
        return view('admin/master/sasaran', $data);
    }

    public function storeSasaran()
    {
        if (!$this->validate(['nama_sasaran' => 'required|is_unique[sasaran.nama_sasaran]'])) {
            return redirect()->to('/admin/master-data/sasaran')->withInput()->with('show_modal', 'addModal');
        }
        $model = new SasaranModel();
        $model->save(['nama_sasaran' => $this->request->getPost('nama_sasaran')]);
        return redirect()->to('/admin/master-data/sasaran')->with('success', 'Sasaran baru berhasil ditambahkan.');
    }

    public function updateSasaran($id)
    {
        if (!$this->validate(['nama_sasaran' => "required|is_unique[sasaran.nama_sasaran,id,{$id}]"])) {
            return redirect()->to('/admin/master-data/sasaran')->withInput()->with('show_modal', 'editModal-' . $id);
        }
        $model = new SasaranModel();
        $model->update($id, ['nama_sasaran' => $this->request->getPost('nama_sasaran')]);
        return redirect()->to('/admin/master-data/sasaran')->with('success', 'Sasaran berhasil diperbarui.');
    }

    public function deleteSasaran($id)
    {
        $model = new SasaranModel();
        $model->delete($id);
        return redirect()->to('/admin/master-data/sasaran')->with('success', 'Sasaran berhasil dihapus.');
    }

    // --- INDIKATOR ---
    public function indikator()
    {
        $model = new IndikatorModel();
        $data = [
            'page_title' => 'Master Indikator Kinerja',
            'items' => $model->findAll(),
            'validation' => \Config\Services::validation()
        ];
        return view('admin/master/indikator', $data);
    }

    public function storeIndikator()
    {
        if (!$this->validate(['nama_indikator' => 'required|is_unique[indikator.nama_indikator]'])) {
            return redirect()->to('/admin/master-data/indikator')->withInput();
        }
        $model = new IndikatorModel();
        $model->save(['nama_indikator' => $this->request->getPost('nama_indikator')]);
        return redirect()->to('/admin/master-data/indikator')->with('success', 'Indikator baru berhasil ditambahkan.');
    }

    // --- SATUAN ---
    public function satuan()
    {
        $model = new SatuanModel();
        $data = [
            'page_title' => 'Master Satuan',
            'items' => $model->findAll(),
            'validation' => \Config\Services::validation()
        ];
        return view('admin/master/satuan', $data);
    }

    public function storeSatuan()
    {
        if (!$this->validate(['nama_satuan' => 'required|is_unique[satuan.nama_satuan]'])) {
            return redirect()->to('/admin/master-data/satuan')->withInput();
        }
        $model = new SatuanModel();
        $model->save(['nama_satuan' => $this->request->getPost('nama_satuan')]);
        return redirect()->to('/admin/master-data/satuan')->with('success', 'Satuan baru berhasil ditambahkan.');
    }
}
