<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\Sasaran;
use App\Models\Indikator;
use App\Models\Satuan;
use App\Models\User;
use App\Models\UnitKerja;
use App\Models\LedCriteria;
use App\Models\LedStandar;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;

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
        return view('admin/master/sasaran', $data);
    }

    public function storeSasaran()
    {
        $sasaranModel = new Sasaran();
        $namaSasaran = trim($this->request->getPost('nama_sasaran') ?? '');

        if (empty($namaSasaran)) {
            return redirect()->to('master-data/sasaran')->withInput()
                ->with('error', 'Gagal menyimpan. Nama sasaran tidak boleh kosong.');
        }

        // Cek duplikasi nama sasaran
        $existing = $sasaranModel->where('nama_sasaran', $namaSasaran)->first();
        if ($existing) {
            return redirect()->to('master-data/sasaran')->withInput()
                ->with('error', 'Gagal menyimpan. Sasaran dengan nama tersebut sudah ada.');
        }

        $data = ['nama_sasaran' => $namaSasaran];

        if (!$sasaranModel->save($data)) {
            return redirect()->to('master-data/sasaran')->withInput()
                ->with('error', 'Gagal menyimpan data.');
        }
        log_audit('CREATE', 'master_sasaran', $sasaranModel->getInsertID(), null, $data);
        return redirect()->to('master-data/sasaran')->with('success', 'Sasaran baru berhasil ditambahkan.');
    }

    public function updateSasaran($id)
    {
        $sasaranModel = new Sasaran();
        $namaSasaran = trim($this->request->getPost('nama_sasaran') ?? '');

        if (empty($namaSasaran)) {
            return redirect()->to('master-data/sasaran')->withInput()
                ->with('error', 'Gagal memperbarui. Nama sasaran tidak boleh kosong.');
        }

        // Cek duplikasi dengan record lain
        $existing = $sasaranModel->where('nama_sasaran', $namaSasaran)->where('id !=', $id)->first();
        if ($existing) {
            return redirect()->to('master-data/sasaran')->withInput()
                ->with('error', 'Gagal memperbarui. Sasaran dengan nama tersebut sudah ada.');
        }

        $data = ['nama_sasaran' => $namaSasaran];

        if (!$sasaranModel->update($id, $data)) {
            return redirect()->to('master-data/sasaran')->withInput()
                ->with('error', 'Gagal memperbarui data.');
        }
        log_audit('UPDATE', 'master_sasaran', $id, null, $data);
        return redirect()->to('master-data/sasaran')->with('success', 'Sasaran berhasil diperbarui.');
    }

    public function deleteSasaran($id)
    {
        $sasaranModel = new Sasaran();
        if ($sasaranModel->delete($id)) {
            log_audit('DELETE', 'master_sasaran', $id);
            return redirect()->to('master-data/sasaran')->with('success', 'Sasaran berhasil dihapus.');
        }
        return redirect()->to('master-data/sasaran')->with('error', 'Gagal menghapus data.');
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
        return view('admin/master/indikator', $data);
    }

    public function storeIndikator()
    {
        $indikatorModel = new Indikator();
        $namaIndikator = trim($this->request->getPost('nama_indikator') ?? '');

        if (empty($namaIndikator)) {
            return redirect()->to('master-data/indikator')->withInput()
                ->with('error', 'Gagal menyimpan. Nama indikator tidak boleh kosong.');
        }

        // Cek duplikasi nama indikator
        $existing = $indikatorModel->where('nama_indikator', $namaIndikator)->first();
        if ($existing) {
            return redirect()->to('master-data/indikator')->withInput()
                ->with('error', 'Gagal menyimpan. Indikator dengan nama tersebut sudah ada.');
        }

        $data = ['nama_indikator' => $namaIndikator];

        if (!$indikatorModel->save($data)) {
            return redirect()->to('master-data/indikator')->withInput()
                ->with('error', 'Gagal menyimpan data.');
        }
        log_audit('CREATE', 'master_indikator', $indikatorModel->getInsertID(), null, $data);
        return redirect()->to('master-data/indikator')->with('success', 'Indikator baru berhasil ditambahkan.');
    }

    public function updateIndikator($id)
    {
        $indikatorModel = new Indikator();
        $namaIndikator = trim($this->request->getPost('nama_indikator') ?? '');

        if (empty($namaIndikator)) {
            return redirect()->to('master-data/indikator')->withInput()
                ->with('error', 'Gagal memperbarui. Nama indikator tidak boleh kosong.');
        }

        // Cek duplikasi dengan record lain
        $existing = $indikatorModel->where('nama_indikator', $namaIndikator)->where('id !=', $id)->first();
        if ($existing) {
            return redirect()->to('master-data/indikator')->withInput()
                ->with('error', 'Gagal memperbarui. Indikator dengan nama tersebut sudah ada.');
        }

        $data = ['nama_indikator' => $namaIndikator];

        if (!$indikatorModel->update($id, $data)) {
            return redirect()->to('master-data/indikator')->withInput()
                ->with('error', 'Gagal memperbarui data.');
        }
        log_audit('UPDATE', 'master_indikator', $id, null, $data);
        return redirect()->to('master-data/indikator')->with('success', 'Indikator berhasil diperbarui.');
    }

    public function deleteIndikator($id)
    {
        $indikatorModel = new Indikator();
        if ($indikatorModel->delete($id)) {
            log_audit('DELETE', 'master_indikator', $id);
            return redirect()->to('master-data/indikator')->with('success', 'Indikator berhasil dihapus.');
        }
        return redirect()->to('master-data/indikator')->with('error', 'Gagal menghapus data.');
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
        return view('admin/master/satuan', $data);
    }

    public function storeSatuan()
    {
        $satuanModel = new Satuan();
        $namaSatuan = trim($this->request->getPost('nama_satuan') ?? '');

        if (empty($namaSatuan)) {
            return redirect()->to('master-data/satuan')->withInput()
                ->with('error', 'Gagal menyimpan. Nama satuan tidak boleh kosong.');
        }

        // Cek duplikasi nama satuan
        $existing = $satuanModel->where('nama_satuan', $namaSatuan)->first();
        if ($existing) {
            return redirect()->to('master-data/satuan')->withInput()
                ->with('error', 'Gagal menyimpan. Satuan dengan nama tersebut sudah ada.');
        }

        $data = ['nama_satuan' => $namaSatuan];

        if (!$satuanModel->save($data)) {
            return redirect()->to('master-data/satuan')->withInput()
                ->with('error', 'Gagal menyimpan data.');
        }
        log_audit('CREATE', 'master_satuan', $satuanModel->getInsertID(), null, $data);
        return redirect()->to('master-data/satuan')->with('success', 'Satuan baru berhasil ditambahkan.');
    }

    public function updateSatuan($id)
    {
        $satuanModel = new Satuan();
        $namaSatuan = trim($this->request->getPost('nama_satuan') ?? '');

        if (empty($namaSatuan)) {
            return redirect()->to('master-data/satuan')->withInput()
                ->with('error', 'Gagal memperbarui. Nama satuan tidak boleh kosong.');
        }

        // Cek duplikasi dengan record lain
        $existing = $satuanModel->where('nama_satuan', $namaSatuan)->where('id !=', $id)->first();
        if ($existing) {
            return redirect()->to('master-data/satuan')->withInput()
                ->with('error', 'Gagal memperbarui. Satuan dengan nama tersebut sudah ada.');
        }

        $data = ['nama_satuan' => $namaSatuan];

        if (!$satuanModel->update($id, $data)) {
            return redirect()->to('master-data/satuan')->withInput()
                ->with('error', 'Gagal memperbarui data.');
        }
        log_audit('UPDATE', 'master_satuan', $id, null, $data);
        return redirect()->to('master-data/satuan')->with('success', 'Satuan berhasil diperbarui.');
    }

    public function deleteSatuan($id)
    {
        $satuanModel = new Satuan();
        if ($satuanModel->delete($id)) {
            log_audit('DELETE', 'master_satuan', $id);
            return redirect()->to('master-data/satuan')->with('success', 'Satuan berhasil dihapus.');
        }
        return redirect()->to('master-data/satuan')->with('error', 'Gagal menghapus data.');
    }
    
    // ==========================================================
    // FUNGSI-FUNGSI UNTUK UNIT KERJA
    // ==========================================================

    public function unitKerja()
    {
        $unitKerjaModel = new UnitKerja();
        $data = [
            'page_title' => 'Master Unit Kerja',
            'items'      => $unitKerjaModel->orderBy('nama_unit', 'ASC')->findAll(),
            'validation' => \Config\Services::validation()
        ];
        return view('admin/master/unit_kerja', $data);
    }

    public function storeUnitKerja()
    {
        $unitKerjaModel = new UnitKerja();
        $data = [
            'nama_unit' => $this->request->getPost('nama_unit'),
            'parent_unit' => $this->request->getPost('parent_unit') ?: null,
        ];

        if (!$unitKerjaModel->save($data)) {
            return redirect()->to('master-data/unit-kerja')->withInput()
                ->with('errors', $unitKerjaModel->errors());
        }
        log_audit('CREATE', 'master_unit_kerja', $unitKerjaModel->getInsertID(), null, $data);
        return redirect()->to('master-data/unit-kerja')->with('success', 'Unit Kerja baru berhasil ditambahkan.');
    }

    public function updateUnitKerja($id)
    {
        $unitKerjaModel = new UnitKerja();
        $data = [
            'nama_unit' => $this->request->getPost('nama_unit'),
            'parent_unit' => $this->request->getPost('parent_unit') ?: null,
        ];

        if (!$unitKerjaModel->update($id, $data)) {
            return redirect()->to('master-data/unit-kerja')->withInput()
                ->with('errors', $unitKerjaModel->errors());
        }
        log_audit('UPDATE', 'master_unit_kerja', $id, null, $data);
        return redirect()->to('master-data/unit-kerja')->with('success', 'Unit Kerja berhasil diperbarui.');
    }

    public function deleteUnitKerja($id)
    {
        $unitKerjaModel = new UnitKerja();
        if ($unitKerjaModel->delete($id)) {
            log_audit('DELETE', 'master_unit_kerja', $id);
            return redirect()->to('master-data/unit-kerja')->with('success', 'Unit Kerja berhasil dihapus.');
        }
        return redirect()->to('master-data/unit-kerja')->with('error', 'Gagal menghapus data.');
    }

    // ==========================================================
    // FUNGSI-FUNGSI UNTUK KRITERIA LED (DENGAN STANDAR)
    // ==========================================================

    public function led()
    {
        $ledModel = new LedCriteria();
        $standarModel = new LedStandar(); 

        $selectedProdi = $this->request->getGet('prodi') ?? config('Ecc')->prodiList[0];

        $all_items = $ledModel
            ->select('led_criteria.*, led_standar.nama_standar') 
            ->join('led_standar', 'led_standar.id = led_criteria.id_standar', 'left') 
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
        return view('admin/master/led', $data);
    }

    public function storeLed()
    {
        $ledModel = new LedCriteria();
        $data = [
            'prodi'           => $this->request->getPost('prodi'),
            'nama_kriteria'   => $this->request->getPost('nama_kriteria'),
            'id_standar'     => $this->request->getPost('id_standar') ?: null,
            'role_assignment' => $this->request->getPost('role_assignment'),
        ];

        if (!$ledModel->save($data)) {
            return redirect()->to('master-data/led?prodi=' . $data['prodi'])->withInput()
                ->with('error', 'Gagal menyimpan data. Silakan periksa error di bawah.')
                ->with('show_modal', 'addModal');
        }

        $newId = $ledModel->getInsertID();
        log_audit('CREATE', 'master_led_criteria', $newId, null, $data);

        return redirect()->to('master-data/led?prodi=' . $data['prodi'] . '#kriteria-' . $newId)
                         ->with('success', 'Kriteria LED baru berhasil ditambahkan.');
    }

    public function updateLed($id)
    {
        $ledModel = new LedCriteria();
        
        $data = [
            'prodi'           => $this->request->getPost('prodi'),
            'nama_kriteria'   => $this->request->getPost('nama_kriteria'),
            'id_standar'     => $this->request->getPost('id_standar') ?: null,
            'role_assignment' => $this->request->getPost('role_assignment'),
        ];

        if (!$ledModel->update($id, $data)) {
            return redirect()->to('master-data/led?prodi=' . $data['prodi'])->withInput()
                ->with('error', 'Gagal memperbarui data. Silakan periksa error di bawah.')
                ->with('show_modal', 'editModal-' . $id);
        }

        log_audit('UPDATE', 'master_led_criteria', $id, null, $data);

        return redirect()->to('master-data/led?prodi=' . $data['prodi'] . '#kriteria-' . $id)
                         ->with('success', 'Kriteria LED berhasil diperbarui.');
    }
    
    public function deleteLed($id)
    {
        $ledModel = new LedCriteria();
        $prodi = $this->request->getGet('prodi') ?? config('Ecc')->prodiList[0];
        
        if ($ledModel->delete($id)) {
            log_audit('DELETE', 'master_led_criteria', $id);
            return redirect()->to('master-data/led?prodi=' . $prodi)->with('success', 'Kriteria LED berhasil dihapus.');
        }
        return redirect()->to('master-data/led?prodi=' . $prodi)->with('error', 'Gagal menghapus kriteria.');
    }

    public function deleteLedBatch()
    {
        $ledModel = new LedCriteria();
        $prodi = $this->request->getPost('prodi_filter') ?? config('Ecc')->prodiList[0];
        $ids = $this->request->getPost('ids');
        
        if (!empty($ids)) {
            $ledModel->delete($ids);
            log_audit('DELETE', 'master_led_criteria', 'batch', null, $ids);
            return redirect()->to('master-data/led?prodi=' . $prodi)->with('success', 'Data kriteria yang terpilih berhasil dihapus.');
        }
        return redirect()->to('master-data/led?prodi=' . $prodi)->with('error', 'Tidak ada data yang dipilih untuk dihapus.');
    }

    public function batchUpdateLed()
    {
        $ledModel = new \App\Models\LedCriteria();
        $prodi = $this->request->getPost('prodi_filter') ?? config('Ecc')->prodiList[0];
        $ids = $this->request->getPost('ids');
        
        $standarId = $this->request->getPost('id_standar');
        $role = $this->request->getPost('role_assignment');

        if (empty($ids)) {
            return redirect()->to('master-data/led?prodi=' . $prodi)->with('error', 'Tidak ada data yang dipilih untuk diubah.');
        }

        $dataToUpdate = [];

        if ($standarId !== null && $standarId !== '') {
            $dataToUpdate['id_standar'] = ($standarId === 'null') ? null : $standarId;
        }

        if ($role !== null && $role !== '') {
            $dataToUpdate['role_assignment'] = ($role === 'null') ? null : $role;
        }

        if (empty($dataToUpdate)) {
            return redirect()->to('master-data/led?prodi=' . $prodi)->with('error', 'Tidak ada perubahan yang dipilih (Standar atau Role).');
        }

        if ($ledModel->whereIn('id', $ids)->set($dataToUpdate)->update()) {
            log_audit('UPDATE', 'master_led_criteria', 'batch', null, $dataToUpdate);
            return redirect()->to('master-data/led?prodi=' . $prodi)->with('success', 'Data kriteria yang terpilih berhasil diperbarui.');
        }

        return redirect()->to('master-data/led?prodi=' . $prodi)->with('error', 'Gagal memperbarui data kriteria.');
    }

    public function exportLed()
    {
        $ledModel = new LedCriteria();
        
        $selectedProdi = $this->request->getGet('prodi');

        if (empty($selectedProdi)) {
            return redirect()->to('master-data/led')->with('error', 'Silakan pilih prodi terlebih dahulu.');
        }

        $items = $ledModel
            ->select('led_criteria.*, led_standar.nama_standar') 
            ->join('led_standar', 'led_standar.id = led_criteria.id_standar', 'left') 
            ->where('led_criteria.prodi', $selectedProdi) 
            ->orderBy('led_criteria.id', 'ASC') 
            ->findAll();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $sheet->setTitle($selectedProdi);

        $sheet->setCellValue('A1', 'ID (JANGAN DIUBAH)');
        $sheet->setCellValue('B1', 'Nama Kriteria/Elemen/Indikator');
        $sheet->setCellValue('C1', 'Standar');
        $sheet->setCellValue('D1', 'Penanggung Jawab (aak, kuk, all)');
        $sheet->getStyle('A1:D1')->getFont()->setBold(true);

        $rowNumber = 2;
        foreach ($items as $item) {
            $sheet->setCellValue('A' . $rowNumber, $item['id']);
            $sheet->setCellValue('B' . $rowNumber, $item['nama_kriteria']);
            $sheet->setCellValue('C' . $rowNumber, $item['nama_standar']);
            $sheet->setCellValue('D' . $rowNumber, $item['role_assignment']);
            $rowNumber++;
        }

        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setWidth(80);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);
        
        $sheet->getStyle('B2:B'.$rowNumber)->getAlignment()->setWrapText(true);

        $writer = new XlsxWriter($spreadsheet);
        $fileName = 'Export_LED_' . $selectedProdi . '_' . date('Y-m-d') . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . urlencode($fileName) . '"');
        $writer->save('php://output');
        exit();
    }

    public function importLed()
    {
        $file = $this->request->getFile('file_excel');
        $prodi = $this->request->getPost('prodi');
        $ledModel = new LedCriteria();

        if (empty($prodi)) {
            return redirect()->to('master-data/led')->with('error', 'Harap pilih Program Studi tujuan import.');
        }
        
        if (!$file->isValid() || !in_array($file->getMimeType(), ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel'])) {
             return redirect()->to('master-data/led?prodi=' . $prodi)->with('error', 'File tidak valid. Harap unggah file .xlsx');
        }

        $reader = new XlsxReader();
        $spreadsheet = $reader->load($file->getTempName());
        $sheet = $spreadsheet->getActiveSheet()->toArray(null, true, true, true); 

        $standarModel = new LedStandar(); 
        $standarList = $standarModel->findAll(); 
        $standarMap = array_column($standarList, 'id', 'nama_standar'); 

        $dataToInsert = [];
        $dataToUpdate = [];
        $updatedCount = 0;
        $insertedCount = 0;
        $skippedCount = 0;
        $excelRowNames = []; 

        $allCriteriaForProdi = $ledModel->where('prodi', $prodi)
                                        ->select('id, nama_kriteria')
                                        ->findAll();
        $criteriaNameMap = array_column($allCriteriaForProdi, 'id', 'nama_kriteria');
        $criteriaIdMap = array_column($allCriteriaForProdi, 'id', 'id');

        foreach (array_slice($sheet, 1, null, true) as $rowIndex => $row) {
            
            $id = trim($row['A'] ?? '');
            $namaKriteria = trim($row['B'] ?? '');
            $namaStandar = trim($row['C'] ?? '');
            $roleAssignment = trim($row['D'] ?? '');

            if (empty($namaKriteria)) {
                continue; 
            }

            if (isset($excelRowNames[$namaKriteria])) {
                $skippedCount++;
                continue;
            }
            $excelRowNames[$namaKriteria] = true;

            $idStandar = ($namaStandar && isset($standarMap[$namaStandar])) ? $standarMap[$namaStandar] : null; 

            $rowData = [
                'prodi'           => $prodi, 
                'nama_kriteria'   => $namaKriteria,
                'id_standar'     => $idStandar, 
                'role_assignment' => $roleAssignment
            ];

            $foundById = false;
            if (!empty($id) && is_numeric($id)) {
                if (isset($criteriaIdMap[$id])) {
                    $rowData['id'] = $id;
                    $dataToUpdate[] = $rowData;
                    $foundById = true;
                }
            }

            if (!$foundById) {
                if (isset($criteriaNameMap[$namaKriteria])) {
                    $rowData['id'] = $criteriaNameMap[$namaKriteria];
                    $dataToUpdate[] = $rowData;
                } else {
                    $dataToInsert[] = $rowData;
                }
            }
        }

        if (!empty($dataToUpdate)) {
            $ledModel->updateBatch($dataToUpdate, 'id');
            $updatedCount = count($dataToUpdate);
        }
        if (!empty($dataToInsert)) {
            $ledModel->insertBatch($dataToInsert);
            $insertedCount = count($dataToInsert);
        }

        if ($updatedCount > 0 || $insertedCount > 0 || $skippedCount > 0) {
            $message = "Import berhasil: {$insertedCount} data baru ditambahkan, {$updatedCount} data diperbarui.";
            if ($skippedCount > 0) {
                $message .= " {$skippedCount} baris duplikat (berdasarkan nama) di file Excel dilewati.";
            }
            return redirect()->to('master-data/led?prodi=' . $prodi)->with('success', $message);
        }

        return redirect()->to('master-data/led?prodi=' . $prodi)->with('error', 'Gagal mengimpor data atau file kosong (tidak ada baris yang diproses).');
    }

    // ==========================================================
    // FUNGSI-FUNGSI BARU UNTUK STANDAR LED
    // ==========================================================

    public function ledStandar()
    {
        $standarModel = new LedStandar();
        $data = [
            'page_title' => 'Master Standar LED',
            'items'      => $standarModel->orderBy('nama_standar', 'ASC')->findAll(),
            'validation' => \Config\Services::validation()
        ];
        return view('admin/master/led_standar', $data);
    }

    public function storeStandar()
    {
        $standarModel = new LedStandar();
        $namaStandar = trim($this->request->getPost('nama_standar') ?? '');

        if (empty($namaStandar)) {
            return redirect()->to('master-data/led-standar')->withInput()
                ->with('error', 'Gagal menyimpan. Nama standar tidak boleh kosong.');
        }

        // Cek duplikasi
        $existing = $standarModel->where('nama_standar', $namaStandar)->first();
        if ($existing) {
            return redirect()->to('master-data/led-standar')->withInput()
                ->with('error', 'Gagal menyimpan. Standar dengan nama tersebut sudah ada.');
        }

        $data = ['nama_standar' => $namaStandar];

        if (!$standarModel->save($data)) {
            return redirect()->to('master-data/led-standar')->withInput()
                ->with('error', 'Gagal menyimpan data.');
        }
        log_audit('CREATE', 'master_led_standar', $standarModel->getInsertID(), null, $data);
        return redirect()->to('master-data/led-standar')->with('success', 'Standar baru berhasil ditambahkan.');
    }

    public function updateStandar($id)
    {
        $standarModel = new LedStandar();
        $namaStandar = trim($this->request->getPost('nama_standar') ?? '');

        if (empty($namaStandar)) {
            return redirect()->to('master-data/led-standar')->withInput()
                ->with('error', 'Gagal memperbarui. Nama standar tidak boleh kosong.');
        }

        // Cek duplikasi dengan record lain
        $existing = $standarModel->where('nama_standar', $namaStandar)->where('id !=', $id)->first();
        if ($existing) {
            return redirect()->to('master-data/led-standar')->withInput()
                ->with('error', 'Gagal memperbarui. Standar dengan nama tersebut sudah ada.');
        }

        $data = ['nama_standar' => $namaStandar];

        if (!$standarModel->update($id, $data)) {
            return redirect()->to('master-data/led-standar')->withInput()
                ->with('error', 'Gagal memperbarui data.');
        }
        log_audit('UPDATE', 'master_led_standar', $id, null, $data);
        return redirect()->to('master-data/led-standar')->with('success', 'Standar berhasil diperbarui.');
    }

    public function deleteStandar($id)
    {
        $standarModel = new LedStandar();
        
        if ($standarModel->delete($id)) {
            log_audit('DELETE', 'master_led_standar', $id);
            return redirect()->to('master-data/led-standar')->with('success', 'Standar berhasil dihapus. Kriteria terkait kini tidak dikategorikan.');
        }
        return redirect()->to('master-data/led-standar')->with('error', 'Gagal menghapus data.');
    }

    public function eccLed()
    {
        // Models
        $ledModel = new LedCriteria();
        $submissionModel = new \App\Models\LedSubmission(); // Asumsi model ini ada
        $userModel = new User();
        $unitKerjaModel = new UnitKerja();

        // Gunakan query builder untuk memastikan semua kolom terbaca, menghindari masalah pada Model.
        $db = \Config\Database::connect();

        // 1. Get filters from URL
        $selectedTahun = $this->request->getGet('tahun') ?? date('Y');
        $selectedProdi = $this->request->getGet('prodi') ?? config('Ecc')->prodiList[0];

        // 2. Get current user info
        $user_id = session()->get('id');
        $currentRole = session()->get('role');
        $currentUser = $db->table('users')->where('id', $user_id)->get()->getRowArray();
        if (!$currentUser) {
            return redirect()->to('/login')->with('error', 'Sesi tidak valid, silakan login kembali.');
        }

        // 3. Get all criteria for the selected prodi
        $all_criteria = $ledModel
            ->select('led_criteria.*, led_standar.nama_standar')
            ->join('led_standar', 'led_standar.id = led_criteria.id_standar', 'left')
            ->where('led_criteria.prodi', $selectedProdi)
            ->orderBy('led_criteria.id', 'ASC')
            ->findAll();

        // 4. Determine the user's "Unit Kabag" (aak/kuk) for filtering
        $userUnitKabag = null;

        // Coba cari dari unit kerja pengguna terlebih dahulu (paling akurat)
        if (!empty($currentUser['unit'])) {
            $unitName = trim($currentUser['unit']);
            $unitKerja = $unitKerjaModel->where('nama_unit', $unitName)->first();
            if ($unitKerja && !empty($unitKerja['parent_unit'])) {
                $userUnitKabag = strtolower(trim($unitKerja['parent_unit']));
            }
        }

        // Jika tidak ketemu dari unit, cek dari role langsung
        if (empty($userUnitKabag)) {
            if (in_array($currentRole, ['aak', 'kabag_aak'])) {
                $userUnitKabag = 'aak';
            } elseif (in_array($currentRole, ['kuk', 'kabag_kuk'])) {
                $userUnitKabag = 'kuk';
            }
        }

        // Jika masih tidak ketemu, telusuri atasan (hingga 2 level ke atas)
        if (empty($userUnitKabag) && !empty($currentUser['atasan_id'])) {
            $atasan = $db->table('users')->where('id', $currentUser['atasan_id'])->get()->getRowArray();
            if ($atasan) {
                if (in_array(strtolower($atasan['role']), ['aak', 'kabag_aak'])) {
                    $userUnitKabag = 'aak';
                } elseif (in_array(strtolower($atasan['role']), ['kuk', 'kabag_kuk'])) {
                    $userUnitKabag = 'kuk';
                } elseif (!empty($atasan['atasan_id'])) {
                    // Level 2 (misal dari Kanit -> Kabag)
                    $atasan2 = $db->table('users')->where('id', $atasan['atasan_id'])->get()->getRowArray();
                    if ($atasan2) {
                        if (in_array(strtolower($atasan2['role']), ['aak', 'kabag_aak'])) {
                            $userUnitKabag = 'aak';
                        } elseif (in_array(strtolower($atasan2['role']), ['kuk', 'kabag_kuk'])) {
                            $userUnitKabag = 'kuk';
                        }
                    }
                }
            }
        }

        // 5. Filter criteria based on role
        $unfilteredRoles = ['admin', 'spm', 'direktur', 'wadir', 'manajemen']; // Roles that see everything

        if (hasAnyRole($unfilteredRoles)) {
            // For super-viewer roles, show all criteria
            $filtered_criteria = $all_criteria;
        } else {
            // For all other roles, filter based on their determined unit
            $filtered_criteria = array_filter($all_criteria, function($criteria) use ($userUnitKabag) {
                return in_array($criteria['role_assignment'], [$userUnitKabag, 'all', null, '']);
            });
        }

        // 6. Get submitted data
        $submitted_data = [];
        if (!empty($filtered_criteria)) {
            $criteria_ids = array_column($filtered_criteria, 'id');
            $submitted_data_raw = $submissionModel->where('tahun', $selectedTahun)->where('prodi', $selectedProdi)->whereIn('led_criteria_id', $criteria_ids)->findAll();
            $submitted_data = array_column($submitted_data_raw, null, 'led_criteria_id');
        }

        // 7. Define role groups for the view
        $data = [
            'page_title'        => 'Laporan Evaluasi Diri (LED)',
            'selectedTahun'     => $selectedTahun,
            'selectedProdi'     => $selectedProdi,
            'prodiList'         => config('Ecc')->prodiList,
            'all_criteria'      => $all_criteria,
            'filtered_criteria' => array_values($filtered_criteria),
            'submitted_data'    => $submitted_data,
            'currentRole'       => $currentRole,
            'is_staf'           => !hasAnyRole(['manajemen', 'direktur', 'wadir', 'admin', 'spm', 'kabag_aak', 'kabag_kuk']),
            'is_kabag'          => !hasAnyRole(['manajemen', 'direktur', 'wadir', 'admin', 'spm']) && hasAnyRole(['kabag_aak', 'kabag_kuk']),
            'is_wadir'          => hasAnyRole(['manajemen', 'direktur', 'wadir', 'admin', 'spm']),
        ];

        return view('ecc/led_index', $data);
    }
    
    // ==========================================================
    // FUNGSI-FUNGSI UNTUK HARI LIBUR (HOLIDAYS)
    // ==========================================================
    
    public function holidays()
    {
        $holidayModel = new \App\Models\HolidayModel();
        $data = [
            'page_title' => 'Master Hari Libur',
            'items'      => $holidayModel->orderBy('holiday_date', 'ASC')->findAll(),
            'validation' => \Config\Services::validation()
        ];
        return view('admin/master/holidays', $data);
    }
    
    public function syncHolidays()
    {
        $year = date('Y');
        $holidayModel = new \App\Models\HolidayModel();
        
        $urls = [
            "https://api-hari-libur.vercel.app/api?year={$year}",
            "https://libur.deno.dev/api?year={$year}"
        ];
        
        $holidays = [];
        $client = \Config\Services::curlrequest();
        
        foreach ($urls as $url) {
            try {
                $response = $client->request('GET', $url, [
                    'headers' => [
                        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko)',
                        'Accept'     => 'application/json',
                    ],
                    'timeout' => 10,
                    'http_errors' => false
                ]);
                
                if ($response->getStatusCode() === 200) {
                    $body = $response->getBody();
                    $parsed = json_decode($body, true);
                    
                    if (isset($parsed['data']) && is_array($parsed['data'])) {
                        $holidays = $parsed['data'];
                        break;
                    } elseif (is_array($parsed) && !empty($parsed)) {
                        $holidays = $parsed;
                        break;
                    }
                }
            } catch (\Exception $e) {
                continue;
            }
        }
        
        if (!empty($holidays)) {
            $countAdded = 0;
            foreach ($holidays as $h) {
                $date = $h['date'] ?? $h['holiday_date'] ?? null;
                $name = $h['description'] ?? $h['holiday_name'] ?? $h['name'] ?? null;
                
                if (!$date || !$name) {
                    continue;
                }
                
                // Tentukan apakah libur nasional atau cuti bersama
                $isNational = 1;
                if (isset($h['is_national_holiday'])) {
                    $isNational = $h['is_national_holiday'] ? 1 : 0;
                } elseif (stripos($name, 'cuti bersama') !== false) {
                    $isNational = 0;
                }
                
                // Cek apakah tanggal sudah ada
                if (!$holidayModel->where('holiday_date', $date)->first()) {
                    $holidayModel->insert([
                        'holiday_date' => $date,
                        'holiday_name' => $name,
                        'is_national'  => $isNational
                    ]);
                    $countAdded++;
                }
            }
            
            log_audit('SYNC', 'master_holidays', null, null, ['year' => $year, 'added' => $countAdded]);
            return redirect()->to('master-data/holidays')->with('success', "Sinkronisasi berhasil! {$countAdded} hari libur & cuti bersama tahun {$year} berhasil ditambahkan/disinkronkan.");
        }
        
        return redirect()->to('master-data/holidays')->with('error', 'Gagal menghubungi server API Hari Libur Nasional. Silakan periksa koneksi internet atau gunakan form Tambah Libur Manual.');
    }
    
    public function storeHoliday()
    {
        $holidayModel = new \App\Models\HolidayModel();
        $date = trim($this->request->getPost('holiday_date') ?? '');
        $name = trim($this->request->getPost('holiday_name') ?? '');
        $isNational = $this->request->getPost('is_national') ? 1 : 0;

        if (empty($date) || empty($name)) {
            return redirect()->to('master-data/holidays')->withInput()
                ->with('error', 'Gagal menyimpan. Tanggal dan keterangan hari libur wajib diisi.');
        }

        // Cek duplikasi tanggal
        $existing = $holidayModel->where('holiday_date', $date)->first();
        if ($existing) {
            return redirect()->to('master-data/holidays')->withInput()
                ->with('error', 'Gagal menyimpan. Hari libur pada tanggal ' . $date . ' sudah terdaftar (' . $existing['holiday_name'] . ').');
        }

        $data = [
            'holiday_date' => $date,
            'holiday_name' => $name,
            'is_national'  => $isNational
        ];

        if (!$holidayModel->save($data)) {
            return redirect()->to('master-data/holidays')->withInput()->with('error', 'Gagal menyimpan data.');
        }
        log_audit('CREATE', 'master_holidays', $holidayModel->getInsertID(), null, $data);
        return redirect()->to('master-data/holidays')->with('success', 'Hari libur baru berhasil ditambahkan.');
    }
    
    public function deleteHoliday($id)
    {
        $holidayModel = new \App\Models\HolidayModel();
        if ($holidayModel->delete($id)) {
            log_audit('DELETE', 'master_holidays', $id);
            return redirect()->to('master-data/holidays')->with('success', 'Hari libur berhasil dihapus.');
        }
        return redirect()->to('master-data/holidays')->with('error', 'Gagal menghapus data.');
    }
}