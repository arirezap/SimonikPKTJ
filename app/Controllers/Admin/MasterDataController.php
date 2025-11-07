<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\Sasaran;
use App\Models\Indikator;
use App\Models\Satuan;
use App\Models\LedCriteria;
use App\Models\LedStandar; // Pastikan ini LedStandar
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class MasterDataController extends BaseController
{
    // ==========================================================
    // FUNGSI-FUNGSI UNTUK SASARAN PROGRAM
    // ==========================================================

    public function sasaran()
    {
        $sasaranModel = new Sasaran();
        $data = [
            'page_title' => 'Master Sasaran Program',
            'items'      => $sasaranModel->orderBy('nama_sasaran', 'ASC')->findAll(),
            'validation' => \Config\Services::validation()
        ];
        return view('Admin/master/sasaran', $data);
    }

    public function storeSasaran()
    {
        $sasaranModel = new Sasaran();
        $data = ['nama_sasaran' => $this->request->getPost('nama_sasaran')];

        if (!$sasaranModel->save($data)) {
            return redirect()->to('admin/master-data/sasaran')->withInput()
                ->with('error', 'Gagal menyimpan data.');
        }
        return redirect()->to('admin/master-data/sasaran')->with('success', 'Sasaran baru berhasil ditambahkan.');
    }

    public function updateSasaran($id)
    {
        $sasaranModel = new Sasaran();
        $data = ['nama_sasaran' => $this->request->getPost('nama_sasaran')];

        if (!$sasaranModel->update($id, $data)) {
            return redirect()->to('admin/master-data/sasaran')->withInput()
                ->with('error', 'Gagal memperbarui data.');
        }
        return redirect()->to('admin/master-data/sasaran')->with('success', 'Sasaran berhasil diperbarui.');
    }

    public function deleteSasaran($id)
    {
        $sasaranModel = new Sasaran();
        if ($sasaranModel->delete($id)) {
            return redirect()->to('admin/master-data/sasaran')->with('success', 'Sasaran berhasil dihapus.');
        }
        return redirect()->to('admin/master-data/sasaran')->with('error', 'Gagal menghapus data.');
    }

    // ==========================================================
    // FUNGSI-FUNGSI UNTUK INDIKATOR KINERJA
    // ==========================================================

    public function indikator()
    {
        $indikatorModel = new Indikator();
        $data = [
            'page_title' => 'Master Indikator Kinerja',
            'items'      => $indikatorModel->orderBy('nama_indikator', 'ASC')->findAll(),
            'validation' => \Config\Services::validation()
        ];
        return view('Admin/master/indikator', $data);
    }

    public function storeIndikator()
    {
        $indikatorModel = new Indikator();
        $data = ['nama_indikator' => $this->request->getPost('nama_indikator')];

        if (!$indikatorModel->save($data)) {
            return redirect()->to('admin/master-data/indikator')->withInput()
                ->with('error', 'Gagal menyimpan data.');
        }
        return redirect()->to('admin/master-data/indikator')->with('success', 'Indikator baru berhasil ditambahkan.');
    }

    public function updateIndikator($id)
    {
        $indikatorModel = new Indikator();
        $data = ['nama_indikator' => $this->request->getPost('nama_indikator')];

        if (!$indikatorModel->update($id, $data)) {
            return redirect()->to('admin/master-data/indikator')->withInput()
                ->with('error', 'Gagal memperbarui data.');
        }
        return redirect()->to('admin/master-data/indikator')->with('success', 'Indikator berhasil diperbarui.');
    }

    public function deleteIndikator($id)
    {
        $indikatorModel = new Indikator();
        if ($indikatorModel->delete($id)) {
            return redirect()->to('admin/master-data/indikator')->with('success', 'Indikator berhasil dihapus.');
        }
        return redirect()->to('admin/master-data/indikator')->with('error', 'Gagal menghapus data.');
    }
    
    // ==========================================================
    // FUNGSI-FUNGSI UNTUK SATUAN
    // ==========================================================
    
    public function satuan()
    {
        $satuanModel = new Satuan();
        $data = [
            'page_title' => 'Master Satuan',
            'items'      => $satuanModel->orderBy('nama_satuan', 'ASC')->findAll(),
            'validation' => \Config\Services::validation()
        ];
        return view('Admin/master/satuan', $data);
    }

    public function storeSatuan()
    {
        $satuanModel = new Satuan();
        $data = ['nama_satuan' => $this->request->getPost('nama_satuan')];

        if (!$satuanModel->save($data)) {
            return redirect()->to('admin/master-data/satuan')->withInput()
                ->with('error', 'Gagal menyimpan data.');
        }
        return redirect()->to('admin/master-data/satuan')->with('success', 'Satuan baru berhasil ditambahkan.');
    }

    public function updateSatuan($id)
    {
        $satuanModel = new Satuan();
        $data = ['nama_satuan' => $this->request->getPost('nama_satuan')];

        if (!$satuanModel->update($id, $data)) {
            return redirect()->to('admin/master-data/satuan')->withInput()
                ->with('error', 'Gagal memperbarui data.');
        }
        return redirect()->to('admin/master-data/satuan')->with('success', 'Satuan berhasil diperbarui.');
    }

    public function deleteSatuan($id)
    {
        $satuanModel = new Satuan();
        if ($satuanModel->delete($id)) {
            return redirect()->to('admin/master-data/satuan')->with('success', 'Satuan berhasil dihapus.');
        }
        return redirect()->to('admin/master-data/satuan')->with('error', 'Gagal menghapus data.');
    }
    
    // ==========================================================
    // FUNGSI-FUNGSI UNTUK KRITERIA LED (DENGAN STANDAR)
    // ==========================================================

    public function led()
    {
        $ledModel = new LedCriteria();
        $standarModel = new LedStandar(); 

        $selectedProdi = $this->request->getGet('prodi') ?? config('Simonik')->prodiList[0];

        $all_items = $ledModel
            ->select('led_criteria.*, led_standar.nama_standar') 
            ->join('led_standar', 'led_standar.id = led_criteria.id_kategori', 'left') 
            ->where('led_criteria.prodi', $selectedProdi) 
            ->orderBy('led_criteria.id', 'ASC') 
            ->findAll();

        $data = [
            'page_title' => 'Master Kriteria LED',
            'items'      => $all_items,
            'standar_list' => $standarModel->orderBy('nama_standar', 'ASC')->findAll(), 
            'validation' => \Config\Services::validation(),
            'selectedProdi' => $selectedProdi
        ];
        return view('Admin/master/led', $data);
    }

    public function storeLed()
    {
        $ledModel = new LedCriteria();
        $data = [
            'prodi'           => $this->request->getPost('prodi'),
            'nama_kriteria'   => $this->request->getPost('nama_kriteria'),
            'id_kategori'     => $this->request->getPost('id_kategori') ?: null, // Ini sudah benar
            'role_assignment' => $this->request->getPost('role_assignment'),
        ];

        if (!$ledModel->save($data)) {
            return redirect()->to('admin/master-data/led?prodi=' . $data['prodi'])->withInput()
                ->with('error', 'Gagal menyimpan data. Silakan periksa error di bawah.')
                ->with('show_modal', 'addModal');
        }

        $newId = $ledModel->getInsertID();

        return redirect()->to('admin/master-data/led?prodi=' . $data['prodi'] . '#kriteria-' . $newId)
                         ->with('success', 'Kriteria LED baru berhasil ditambahkan.');
    }

    public function updateLed($id)
    {
        $ledModel = new LedCriteria();
        
        // INI BAGIAN YANG DIPERBAIKI:
        $data = [
            'prodi'           => $this->request->getPost('prodi'),
            'nama_kriteria'   => $this->request->getPost('nama_kriteria'),
            'id_kategori'     => $this->request->getPost('id_kategori') ?: null, // Mengambil 'id_kategori'
            'role_assignment' => $this->request->getPost('role_assignment'),
        ];

        if (!$ledModel->update($id, $data)) {
            return redirect()->to('admin/master-data/led?prodi=' . $data['prodi'])->withInput()
                ->with('error', 'Gagal memperbarui data. Silakan periksa error di bawah.')
                ->with('show_modal', 'editModal-' . $id);
        }

        return redirect()->to('admin/master-data/led?prodi=' . $data['prodi'] . '#kriteria-' . $id)
                         ->with('success', 'Kriteria LED berhasil diperbarui.');
    }
    
    public function deleteLed($id)
    {
        $ledModel = new LedCriteria();
        $prodi = $this->request->getGet('prodi') ?? config('Simonik')->prodiList[0];
        
        if ($ledModel->delete($id)) {
            return redirect()->to('admin/master-data/led?prodi=' . $prodi)->with('success', 'Kriteria LED berhasil dihapus.');
        }
        return redirect()->to('admin/master-data/led?prodi=' . $prodi)->with('error', 'Gagal menghapus kriteria.');
    }

    public function deleteLedBatch()
    {
        $ledModel = new LedCriteria();
        $prodi = $this->request->getPost('prodi_filter') ?? config('Simonik')->prodiList[0];
        $ids = $this->request->getPost('ids');
        
        if (!empty($ids)) {
            $ledModel->delete($ids);
            return redirect()->to('admin/master-data/led?prodi=' . $prodi)->with('success', 'Data kriteria yang terpilih berhasil dihapus.');
        }
        return redirect()->to('admin/master-data/led?prodi=' . $prodi)->with('error', 'Tidak ada data yang dipilih untuk dihapus.');
    }

    public function importLed()
    {
        $file = $this->request->getFile('file_excel');
        $prodi = $this->request->getPost('prodi');

        if (empty($prodi)) {
            return redirect()->to('admin/master-data/led')->with('error', 'Harap pilih Program Studi tujuan import.');
        }
        
        if (!$file->isValid() || !in_array($file->getMimeType(), ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel'])) {
             return redirect()->to('admin/master-data/led?prodi=' . $prodi)->with('error', 'File tidak valid. Harap unggah file .xlsx');
        }

        $reader = new Xlsx();
        $spreadsheet = $reader->load($file->getTempName());
        $sheet = $spreadsheet->getActiveSheet()->toArray();

        $standarModel = new LedStandar(); 
        $standarList = $standarModel->findAll(); 
        $standarMap = array_column($standarList, 'id', 'nama_standar'); 

        $dataToInsert = [];
        foreach (array_slice($sheet, 1) as $row) {
            if (!empty($row[0])) { 
                $namaStandar = $row[1] ?? null; 
                $idStandar = ($namaStandar && isset($standarMap[$namaStandar])) ? $standarMap[$namaStandar] : null; 

                $dataToInsert[] = [
                    'prodi'           => $prodi, 
                    'nama_kriteria'   => $row[0], 
                    'id_kategori'     => $idStandar,
                    'role_assignment' => $row[2] ?? null
                ];
            }
        }

        if (!empty($dataToInsert)) {
            (new LedCriteria())->insertBatch($dataToInsert);
            return redirect()->to('admin/master-data/led?prodi=' . $prodi)->with('success', 'Data berhasil diimpor untuk prodi ' . $prodi);
        }
        return redirect()->to('admin/master-data/led?prodi=' . $prodi)->with('error', 'Gagal mengimpor data atau file kosong.');
    }

    // ==========================================================
    // FUNGSI-FUNGSI BARU UNTUK STANDAR LED
    // ==========================================================

    public function led_standar()
    {
        $standarModel = new LedStandar();
        $data = [
            'page_title' => 'Master Standar LED',
            'items'      => $standarModel->orderBy('nama_standar', 'ASC')->findAll(),
            'validation' => \Config\Services::validation()
        ];
        return view('Admin/master/led_standar', $data);
    }

    public function storeStandar()
    {
        $standarModel = new LedStandar();
        $data = ['nama_standar' => $this->request->getPost('nama_standar')];

        if (!$standarModel->save($data)) {
            return redirect()->to('admin/master-data/led-standar')->withInput()
                ->with('error', 'Gagal menyimpan data. Pastikan nama standar unik.');
        }
        return redirect()->to('admin/master-data/led-standar')->with('success', 'Standar baru berhasil ditambahkan.');
    }

    public function updateStandar($id)
    {
        $standarModel = new LedStandar();
        $data = ['nama_standar' => $this->request->getPost('nama_standar')];

        if (!$standarModel->update($id, $data)) {
            return redirect()->to('admin/master-data/led-standar')->withInput()
                ->with('error', 'Gagal memperbarui data. Pastikan nama standar unik.');
        }
        return redirect()->to('admin/master-data/led-standar')->with('success', 'Standar berhasil diperbarui.');
    }

    public function deleteStandar($id)
    {
        $standarModel = new LedStandar();
        
        if ($standarModel->delete($id)) {
            return redirect()->to('admin/master-data/led-standar')->with('success', 'Standar berhasil dihapus. Kriteria terkait kini tidak dikategorikan.');
        }
        return redirect()->to('admin/master-data/led-standar')->with('error', 'Gagal menghapus data.');
    }
}