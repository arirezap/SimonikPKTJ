<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\Sasaran;
use App\Models\Indikator;
use App\Models\Satuan;
use App\Models\LedCriteria;
use App\Models\LedCategory; // Pastikan ini di-import
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
    // FUNGSI-FUNGSI UNTUK KRITERIA LED (DENGAN KATEGORI)
    // ==========================================================

    public function led()
    {
        $ledModel = new LedCriteria();
        $kategoriModel = new LedCategory(); // Ambil model Kategori

        $all_items = $ledModel->findAll();

        // Urutkan data menggunakan natural sort di PHP
        usort($all_items, function($a, $b) {
            return strnatcmp($a['nomor_kriteria'], $b['nomor_kriteria']);
        });

        $data = [
            'page_title' => 'Master Kriteria LED',
            'items'      => $all_items,
            // Kirim daftar kategori dari tabel baru ke view
            'kategori_list' => $kategoriModel->orderBy('nama_kategori', 'ASC')->findAll(), 
            'validation' => \Config\Services::validation()
        ];
        return view('Admin/master/led', $data);
    }

    public function storeLed()
    {
        $ledModel = new LedCriteria();
        $data = [
            'nomor_kriteria' => $this->request->getPost('nomor_kriteria'),
            'nama_kriteria'  => $this->request->getPost('nama_kriteria'),
            'kategori'       => $this->request->getPost('kategori'),
            'role_assignment'=> $this->request->getPost('role_assignment'),
        ];

        if (!$ledModel->save($data)) {
            return redirect()->to('admin/master-data/led')->withInput()
                ->with('error', 'Gagal menyimpan data. Silakan periksa error di bawah.')
                ->with('show_modal', 'addModal');
        }

        return redirect()->to('admin/master-data/led')->with('success', 'Kriteria LED baru berhasil ditambahkan.');
    }

    public function updateLed($id)
    {
        $ledModel = new LedCriteria();
        $data = [
            'nomor_kriteria' => $this->request->getPost('nomor_kriteria'),
            'nama_kriteria'  => $this->request->getPost('nama_kriteria'),
            'kategori'       => $this->request->getPost('kategori'),
            'role_assignment'=> $this->request->getPost('role_assignment'),
        ];

        if (!$ledModel->update($id, $data)) {
            return redirect()->to('admin/master-data/led')->withInput()
                ->with('error', 'Gagal memperbarui data. Silakan periksa error di bawah.')
                ->with('show_modal', 'editModal-' . $id);
        }

        return redirect()->to('admin/master-data/led')->with('success', 'Kriteria LED berhasil diperbarui.');
    }
    
    public function deleteLed($id)
    {
        $ledModel = new LedCriteria();
        if ($ledModel->delete($id)) {
            return redirect()->to('admin/master-data/led')->with('success', 'Kriteria LED berhasil dihapus.');
        }
        return redirect()->to('admin/master-data/led')->with('error', 'Gagal menghapus kriteria.');
    }

    public function deleteLedBatch()
    {
        $ledModel = new LedCriteria();
        $ids = $this->request->getPost('ids');
        if (!empty($ids)) {
            $ledModel->delete($ids); // Model CI4 bisa menangani array ID untuk delete
            return redirect()->to('admin/master-data/led')->with('success', 'Data kriteria yang terpilih berhasil dihapus.');
        }
        return redirect()->to('admin/master-data/led')->with('error', 'Tidak ada data yang dipilih untuk dihapus.');
    }

    public function importLed()
    {
        $file = $this->request->getFile('file_excel');
        if (!$file->isValid() || !in_array($file->getMimeType(), ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel'])) {
             return redirect()->to('admin/master-data/led')->with('error', 'File tidak valid. Harap unggah file .xlsx');
        }

        $reader = new Xlsx();
        $spreadsheet = $reader->load($file->getTempName());
        $sheet = $spreadsheet->getActiveSheet()->toArray();

        $dataToInsert = [];
        // Loop dari baris kedua (index 1) untuk melewati header
        foreach (array_slice($sheet, 1) as $row) {
            // Asumsikan Kolom A = nomor, Kolom B = nama, Kolom C = kategori, Kolom D = role_assignment
            if (!empty($row[0]) && !empty($row[1])) {
                $dataToInsert[] = [
                    'nomor_kriteria' => $row[0],
                    'nama_kriteria'  => $row[1],
                    'kategori'       => $row[2] ?? null,
                    'role_assignment'=> $row[3] ?? null
                ];
            }
        }

        if (!empty($dataToInsert)) {
            (new LedCriteria())->insertBatch($dataToInsert);
            return redirect()->to('admin/master-data/led')->with('success', 'Data berhasil diimpor.');
        }
        return redirect()->to('admin/master-data/led')->with('error', 'Gagal mengimpor data atau file kosong.');
    }

    // ==========================================================
    // FUNGSI-FUNGSI BARU UNTUK KATEGORI LED
    // ==========================================================

    public function led_kategori()
    {
        $kategoriModel = new LedCategory();
        $data = [
            'page_title' => 'Master Kategori LED',
            'items'      => $kategoriModel->orderBy('nama_kategori', 'ASC')->findAll(),
            'validation' => \Config\Services::validation()
        ];
        return view('Admin/master/led_kategori', $data);
    }

    public function storeKategori()
    {
        $kategoriModel = new LedCategory();
        $data = ['nama_kategori' => $this->request->getPost('nama_kategori')];

        if (!$kategoriModel->save($data)) {
            return redirect()->to('admin/master-data/led-kategori')->withInput()
                ->with('error', 'Gagal menyimpan data. Pastikan nama kategori unik.');
        }
        return redirect()->to('admin/master-data/led-kategori')->with('success', 'Kategori baru berhasil ditambahkan.');
    }

    public function updateKategori($id)
    {
        $kategoriModel = new LedCategory();
        $data = ['nama_kategori' => $this->request->getPost('nama_kategori')];

        if (!$kategoriModel->update($id, $data)) {
            return redirect()->to('admin/master-data/led-kategori')->withInput()
                ->with('error', 'Gagal memperbarui data. Pastikan nama kategori unik.');
        }
        return redirect()->to('admin/master-data/led-kategori')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function deleteKategori($id)
    {
        $kategoriModel = new LedCategory();
        if ($kategoriModel->delete($id)) {
            return redirect()->to('admin/master-data/led-kategori')->with('success', 'Kategori berhasil dihapus.');
        }
        return redirect()->to('admin/master-data/led-kategori')->with('error', 'Gagal menghapus data.');
    }
}
