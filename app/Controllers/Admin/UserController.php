<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\User;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;

class UserController extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function index()
    {
        $data = [
            'title' => 'Kelola Pengguna',
        ];
        
        $search = $this->request->getGet('search');
        $sortBy = $this->request->getGet('sort_by') ?? 'nama_lengkap'; // Default sort by nama_lengkap
        $sortOrder = $this->request->getGet('sort_order') ?? 'asc'; // Default sort order ascending

        $query = $this->userModel;

        if ($search) {
            $query = $query->like('nama_lengkap', $search);
        }

        $query = $query->orderBy($sortBy, $sortOrder);
        $data['users'] = $query->findAll();
        $data['search'] = $search;
        $data['sortBy'] = $sortBy;
        $data['sortOrder'] = $sortOrder;

        // Ambil data untuk dropdown atasan di modal batch edit
        $data['potential_bosses'] = $this->userModel->orderBy('nama_lengkap', 'ASC')->findAll();

        // Buat peta semua pengguna untuk pencarian yang efisien
        $userMap = [];
        foreach($data['users'] as $u) {
            $userMap[$u['id']] = $u;
        }
        
        foreach($data['users'] as &$u) {
            // 1. Tentukan Nama Atasan
            $u['nama_atasan'] = ($u['atasan_id'] && isset($userMap[$u['atasan_id']])) 
                                ? $userMap[$u['atasan_id']]['nama_lengkap'] 
                                : '-';
            
            // 2. Tentukan Unit Kabag (AAK/KUK) jika rolenya 'user'
            if ($u['role'] === 'user') {
                $u['unit_kabag'] = $this->getKabagUnit($u['id'], $userMap);
            } else {
                $u['unit_kabag'] = null; // Tidak berlaku untuk non-user
            }
        }

        return view('admin/users', $data);
    }

    // --- FITUR BARU: CREATE ---
    public function create()
    {
        // Ambil list semua user untuk dropdown "Pilih Atasan"
        $potentialBosses = $this->userModel->orderBy('nama_lengkap', 'ASC')->findAll();

        $data = [
            'title' => 'Tambah Pengguna Baru',
            'potential_bosses' => $potentialBosses
        ];

        return view('admin/user_create', $data);
    }

    public function store()
    {
        // Validasi input dasar
        // PERBAIKAN: min_length(4) diubah menjadi min_length[4]
        if (!$this->validate([
            'username' => 'required|is_unique[users.username]',
            'email'    => 'required|valid_email',
            'password' => 'required|min_length[4]', 
            'nama_lengkap' => 'required'
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'nama_lengkap' => $this->request->getPost('nama_lengkap'),
            'nip'          => $this->request->getPost('nip'),
            'jabatan'      => $this->request->getPost('jabatan'),
            'pangkat'      => $this->request->getPost('pangkat'),
            'unit'         => $this->request->getPost('unit'),
            'role'         => $this->request->getPost('role'),
            'email'        => $this->request->getPost('email'),
            'username'     => $this->request->getPost('username'),
            'password'     => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
        ];

        // Logic Atasan
        $atasan_id = $this->request->getPost('atasan_id');
        if (!empty($atasan_id)) {
            $data['atasan_id'] = $atasan_id;
        }

        $this->userModel->insert($data);

        return redirect()->to('admin/users')->with('success', 'User baru berhasil ditambahkan.');
    }
    // --- END FITUR BARU ---

    public function edit($id)
    {
        $user = $this->userModel->find($id);

        if (!$user) {
            return redirect()->to('admin/users')->with('error', 'User tidak ditemukan');
        }

        // Kecualikan diri sendiri dari list atasan
        $potentialBosses = $this->userModel->where('id !=', $id)->orderBy('nama_lengkap', 'ASC')->findAll();

        $data = [
            'title' => 'Edit Pengguna & Atasan',
            'user'  => $user,
            'potential_bosses' => $potentialBosses
        ];

        return view('admin/user_edit', $data);
    }

    public function update()
    {
        $id = $this->request->getPost('id');
        
        $data = [
            'nama_lengkap' => $this->request->getPost('nama_lengkap'),
            'nip'          => $this->request->getPost('nip'),
            'jabatan'      => $this->request->getPost('jabatan'),
            'pangkat'      => $this->request->getPost('pangkat'),
            'unit'         => $this->request->getPost('unit'),
            'role'         => $this->request->getPost('role'),
            'email'        => $this->request->getPost('email'),
            'username'     => $this->request->getPost('username'),
        ];

        $atasan_id = $this->request->getPost('atasan_id');
        $data['atasan_id'] = !empty($atasan_id) ? $atasan_id : null;

        $password = $this->request->getPost('password');
        if (!empty($password)) {
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        $this->userModel->update($id, $data);

        return redirect()->to('admin/users')->with('success', 'Data pengguna berhasil diperbarui.');
    }

    public function batch_update()
    {
        $userIds = $this->request->getPost('user_ids');
        $atasanId = $this->request->getPost('atasan_id');

        if (empty($userIds)) {
            return redirect()->to('admin/users')->with('error', 'Tidak ada pengguna yang dipilih untuk diupdate.');
        }

        // Konversi string "1,2,3" menjadi array [1, 2, 3]
        $idArray = explode(',', $userIds);

        // Siapkan data untuk batch update
        $dataToUpdate = [];
        foreach ($idArray as $id) {
            if (is_numeric(trim($id))) {
                $dataToUpdate[] = [
                    'id' => (int)trim($id),
                    'atasan_id' => !empty($atasanId) ? (int)$atasanId : null
                ];
            }
        }

        if (empty($dataToUpdate)) {
            return redirect()->to('admin/users')->with('error', 'Tidak ada data valid untuk diupdate.');
        }

        $this->userModel->updateBatch($dataToUpdate, 'id');

        return redirect()->to('admin/users')->with('success', count($dataToUpdate) . ' data pengguna berhasil diperbarui.');
    }
    
    public function delete($id)
    {
        $this->userModel->delete($id);
        return redirect()->to('admin/users')->with('success', 'User berhasil dihapus');
    }

    /**
     * Mencari unit Kabag (AAK/KUK) dari seorang user berdasarkan hierarki atasan.
     * @param int $userId ID user yang akan dicek
     * @param array $userMap Peta semua user untuk lookup
     * @return string|null 'AAK', 'KUK', atau null jika tidak ditemukan
     */
    private function getKabagUnit($userId, &$userMap)
    {
        if (!isset($userMap[$userId])) {
            return null;
        }

        $currentUserId = $userId;

        // Loop untuk menelusuri ke atas, dengan batas 10 level untuk keamanan
        for ($i = 0; $i < 10; $i++) { 
            // Cek apakah user saat ini punya atasan
            if (empty($userMap[$currentUserId]['atasan_id'])) {
                return null; 
            }

            $atasanId = $userMap[$currentUserId]['atasan_id'];

            // Cek apakah atasan ada di map
            if (!isset($userMap[$atasanId])) {
                return null; // Hierarki terputus
            }

            $atasan = $userMap[$atasanId];

            // Jika atasan adalah kabag yang dicari, kembalikan unitnya
            if ($atasan['role'] === 'kabag_aak') {
                return 'AAK';
            }
            if ($atasan['role'] === 'kabag_kuk') {
                return 'KUK';
            }

            // Jika bukan, lanjutkan pencarian ke atasan dari atasan
            $currentUserId = $atasanId;
        }

        return null; // Tidak ditemukan setelah 10 level
    }

    /**
     * Menghasilkan file CSV sebagai template atau backup data pengguna.
     */
    public function exportExcel()
    {
        $fileName = 'Template_Import_Pengguna_' . date('Y-m-d') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . urlencode($fileName) . '"');
        
        $output = fopen('php://output', 'w');
        fputcsv($output, [
            'username (wajib, unik)',
            'email (wajib, unik)',
            'password (wajib, min: 4 karakter)',
            'nama_lengkap (wajib)',
            'role (wajib: admin, aak, kuk, spm, dll.)',
            'nip',
            'jabatan',
            'pangkat',
            'unit',
            'atasan_id (opsional, ID dari user atasan)'
        ]);
        fclose($output);
        exit();
    }

    /**
     * Mengimpor data pengguna dari file CSV.
     */
    public function importExcel()
    {
        $file = $this->request->getFile('file_excel');

        if (!$file || !$file->isValid() || $file->getExtension() !== 'csv') {
            return redirect()->to('admin/users')->with('error', 'File tidak valid. Harap unggah file .csv');
        }

        $dataToInsert = [];
        $insertedCount = 0;
        $skippedCount = 0;
        $errors = [];

        // Ambil semua username dan email yang ada untuk validasi duplikat
        $existingUsers = $this->userModel->select('username, email')->findAll();
        $existingUsernames = array_column($existingUsers, 'username');
        $existingEmails = array_column($existingUsers, 'email');

        if (($handle = fopen($file->getTempName(), 'r')) !== false) {
            $header = fgetcsv($handle, 1000, ',');
            $rowIndex = 1;
            
            while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                $rowIndex++;
                $username     = trim($row[0] ?? '');
                $email        = trim($row[1] ?? '');
                $password     = trim($row[2] ?? '');
                $nama_lengkap = trim($row[3] ?? '');
                $role         = trim($row[4] ?? '');

                // Validasi dasar: Lewati baris jika data wajib kosong
                if (empty($username) || empty($email) || empty($password) || empty($nama_lengkap) || empty($role)) {
                    $skippedCount++;
                    continue;
                }

                // Validasi duplikasi
                if (in_array($username, $existingUsernames)) {
                    $errors[] = "Baris {$rowIndex}: Username '{$username}' sudah ada di database.";
                    $skippedCount++;
                    continue;
                }
                if (in_array($email, $existingEmails)) {
                    $errors[] = "Baris {$rowIndex}: Email '{$email}' sudah ada di database.";
                    $skippedCount++;
                    continue;
                }

                $dataToInsert[] = [
                    'username'     => $username,
                    'email'        => $email,
                    'password'     => md5($password), // Telah menggunakan MD5
                    // nama_lengkap dengan tanda kutip akan tersimpan dengan aman karena insertBatch CI4 mengemasnya lewat prepared statement
                    'nama_lengkap' => $nama_lengkap,
                    'role'         => $role,
                    'nip'          => trim($row[5] ?? ''),
                    'jabatan'      => trim($row[6] ?? ''),
                    'pangkat'      => trim($row[7] ?? ''),
                    'unit'         => trim($row[8] ?? ''),
                    'atasan_id'    => !empty(trim($row[9] ?? '')) ? (int)trim($row[9]) : null,
                ];

                // Tambahkan username & email baru ke daftar pengecekan
                $existingUsernames[] = $username;
                $existingEmails[] = $email;
            }
            fclose($handle);
        }

        if (!empty($dataToInsert)) {
            $this->userModel->insertBatch($dataToInsert);
            $insertedCount = count($dataToInsert);
        }

        if ($insertedCount > 0) {
            $message = "Import berhasil: {$insertedCount} pengguna baru berhasil ditambahkan.";
            if ($skippedCount > 0) {
                $message .= " {$skippedCount} baris dilewati karena data tidak lengkap atau duplikat.";
            }
            if (!empty($errors)) {
                session()->setFlashdata('import_errors', $errors);
            }
            return redirect()->to('admin/users')->with('success', $message);
        }

        return redirect()->to('admin/users')->with('error', 'Tidak ada data pengguna baru yang dapat diimpor dari file yang diunggah.');
    }
}