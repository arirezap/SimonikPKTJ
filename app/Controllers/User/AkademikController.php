<?php

namespace App\Controllers\User;

use App\Controllers\BaseController;

class AkademikController extends BaseController
{
    /**
     * Menampilkan halaman rangkuman (dashboard) akademik.
     */
    public function index()
    {
        // Data dummy untuk rangkuman
        $data = [
            'page_title' => 'Rangkuman Akademik',
            'total_matkul' => 120,
            'total_dosen' => 75,
            'total_ruangan' => 25,
            'chart_labels' => ['RSTJ', 'TRO', 'TO'],
            'chart_data' => [450, 550, 250] // Contoh jumlah mahasiswa per prodi
        ];

        return view('user/akademik/index', $data);
    }

    /**
     * Menampilkan halaman kelola jadwal kuliah.
     */
    public function jadwal()
    {
        // Data dummy untuk tabel jadwal
        $data = [
            'page_title' => 'Kelola Jadwal Kuliah',
            'jadwal_kuliah' => [
                ['id' => 1, 'prodi' => 'RSTJ', 'matkul' => 'Manajemen Lalu Lintas', 'dosen' => 'Dr. Ir. Budi Hartono', 'hari' => 'Senin', 'jam' => '08:00 - 10:00', 'ruangan' => 'A-101'],
                ['id' => 2, 'prodi' => 'TRO', 'matkul' => 'Sistem Rem Otomotif', 'dosen' => 'Andi Wijaya, S.T., M.T.', 'hari' => 'Selasa', 'jam' => '10:00 - 12:00', 'ruangan' => 'B-203'],
                ['id' => 3, 'prodi' => 'TO', 'matkul' => 'Dasar-dasar Mesin', 'dosen' => 'Siti Aminah, S.Pd., M.Eng.', 'hari' => 'Rabu', 'jam' => '13:00 - 15:00', 'ruangan' => 'C-105'],
            ],
            'validation' => \Config\Services::validation()
        ];

        return view('user/akademik/kelola_jadwal', $data);
    }

    /**
     * Menyimpan jadwal baru.
     * (Ini adalah kerangka, logika penyimpanan ke DB belum diimplementasikan)
     */
    public function storeJadwal()
    {
        // Logika validasi dan penyimpanan data baru
        return redirect()->to('/akademik/jadwal')->with('success', 'Jadwal baru berhasil ditambahkan.');
    }
}
