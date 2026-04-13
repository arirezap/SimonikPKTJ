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
        return view('admin/master/indikator', $data);
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
        return view('admin/master/satuan', $data);
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
            return redirect()->to('admin/master-data/unit-kerja')->withInput()
                ->with('errors', $unitKerjaModel->errors());
        }
        return redirect()->to('admin/master-data/unit-kerja')->with('success', 'Unit Kerja baru berhasil ditambahkan.');
    }

    public function updateUnitKerja($id)
    {
        $unitKerjaModel = new UnitKerja();
        $data = [
            'nama_unit' => $this->request->getPost('nama_unit'),
            'parent_unit' => $this->request->getPost('parent_unit') ?: null,
        ];

        if (!$unitKerjaModel->update($id, $data)) {
            return redirect()->to('admin/master-data/unit-kerja')->withInput()
                ->with('errors', $unitKerjaModel->errors());
        }
        return redirect()->to('admin/master-data/unit-kerja')->with('success', 'Unit Kerja berhasil diperbarui.');
    }

    public function deleteUnitKerja($id)
    {
        $unitKerjaModel = new UnitKerja();
        if ($unitKerjaModel->delete($id)) {
            return redirect()->to('admin/master-data/unit-kerja')->with('success', 'Unit Kerja berhasil dihapus.');
        }
        return redirect()->to('admin/master-data/unit-kerja')->with('error', 'Gagal menghapus data.');
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
        
        $data = [
            'prodi'           => $this->request->getPost('prodi'),
            'nama_kriteria'   => $this->request->getPost('nama_kriteria'),
            'id_standar'     => $this->request->getPost('id_standar') ?: null,
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

    public function batchUpdateLed()
    {
        $ledModel = new \App\Models\LedCriteria();
        $prodi = $this->request->getPost('prodi_filter') ?? config('Simonik')->prodiList[0];
        $ids = $this->request->getPost('ids');
        
        $standarId = $this->request->getPost('id_standar');
        $role = $this->request->getPost('role_assignment');

        if (empty($ids)) {
            return redirect()->to('admin/master-data/led?prodi=' . $prodi)->with('error', 'Tidak ada data yang dipilih untuk diubah.');
        }

        $dataToUpdate = [];

        if ($standarId !== null && $standarId !== '') {
            $dataToUpdate['id_standar'] = ($standarId === 'null') ? null : $standarId;
        }

        if ($role !== null && $role !== '') {
            $dataToUpdate['role_assignment'] = ($role === 'null') ? null : $role;
        }

        if (empty($dataToUpdate)) {
            return redirect()->to('admin/master-data/led?prodi=' . $prodi)->with('error', 'Tidak ada perubahan yang dipilih (Standar atau Role).');
        }

        if ($ledModel->whereIn('id', $ids)->set($dataToUpdate)->update()) {
            return redirect()->to('admin/master-data/led?prodi=' . $prodi)->with('success', 'Data kriteria yang terpilih berhasil diperbarui.');
        }

        return redirect()->to('admin/master-data/led?prodi=' . $prodi)->with('error', 'Gagal memperbarui data kriteria.');
    }

    public function exportLed()
    {
        $ledModel = new LedCriteria();
        
        $selectedProdi = $this->request->getGet('prodi');

        if (empty($selectedProdi)) {
            return redirect()->to('admin/master-data/led')->with('error', 'Silakan pilih prodi terlebih dahulu.');
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
            return redirect()->to('admin/master-data/led')->with('error', 'Harap pilih Program Studi tujuan import.');
        }
        
        if (!$file->isValid() || !in_array($file->getMimeType(), ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel'])) {
             return redirect()->to('admin/master-data/led?prodi=' . $prodi)->with('error', 'File tidak valid. Harap unggah file .xlsx');
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
            return redirect()->to('admin/master-data/led?prodi=' . $prodi)->with('success', $message);
        }

        return redirect()->to('admin/master-data/led?prodi=' . $prodi)->with('error', 'Gagal mengimpor data atau file kosong (tidak ada baris yang diproses).');
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
        $selectedProdi = $this->request->getGet('prodi') ?? config('Simonik')->prodiList[0];

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
        // Priority 1: Direct role assignment
        if (in_array($currentRole, ['aak', 'kabag_aak'])) {
            $userUnitKabag = 'aak';
        } elseif (in_array($currentRole, ['kuk', 'kabag_kuk'])) {
            $userUnitKabag = 'kuk';
        }
        // Priority 2: For 'user' role, determine from superior's role
        elseif ($currentRole === 'user' && !empty($currentUser['atasan_id'])) {
            $atasan = $db->table('users')->where('id', $currentUser['atasan_id'])->get()->getRowArray();
            if ($atasan) {
                // PERBAIKAN: Gunakan strtolower() untuk membuat pengecekan role tidak case-sensitive
                $atasanRole = strtolower($atasan['role']);
                if (in_array($atasanRole, ['aak', 'kabag_aak'])) {
                    $userUnitKabag = 'aak';
                } elseif (in_array($atasanRole, ['kuk', 'kabag_kuk'])) {
                    $userUnitKabag = 'kuk';
                }
            }
        }
        // Priority 3: Fallback for other roles (or 'user' without a valid superior) - look up from their assigned unit
        elseif (!empty($currentUser['unit'])) {
            $unitName = trim($currentUser['unit']);
            $unitKerja = $unitKerjaModel->where('nama_unit', $unitName)->first();
            if ($unitKerja) {
                $userUnitKabag = $unitKerja['parent_unit'];
            }
        }

        // 5. Filter criteria based on role
        $unfilteredRoles = ['admin', 'spm', 'direktur', 'manajemen']; // Roles that see everything

        if (in_array($currentRole, $unfilteredRoles)) {
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
            'prodiList'         => config('Simonik')->prodiList,
            'all_criteria'      => $all_criteria,
            'filtered_criteria' => array_values($filtered_criteria),
            'submitted_data'    => $submitted_data,
            'currentRole'       => $currentRole,
            'is_staf'           => in_array($currentRole, ['aak', 'kuk', 'user']),
            'is_kabag'          => in_array($currentRole, ['kabag_aak', 'kabag_kuk']),
            'is_wadir'          => in_array($currentRole, ['manajemen', 'direktur', 'admin', 'spm']),
        ];

        return view('ecc/led_index', $data);
    }
}