<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class Ecc2SimulasiSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        $now = date('Y-m-d H:i:s');

        // 1. Program Studi
        $db->table('ref_program_studi')->truncate();
        $prodis = [
            ['kode_prodi' => 'RSTJ', 'nama_prodi' => 'D-IV Rekayasa Sistem Transportasi Jalan', 'jenjang' => 'D-IV', 'is_aktif' => 1, 'created_at' => $now],
            ['kode_prodi' => 'TRO',  'nama_prodi' => 'D-IV Teknologi Rekayasa Otomotif',        'jenjang' => 'D-IV', 'is_aktif' => 1, 'created_at' => $now],
            ['kode_prodi' => 'TO',   'nama_prodi' => 'D-III Teknologi Otomotif',                'jenjang' => 'D-III', 'is_aktif' => 1, 'created_at' => $now],
        ];
        $db->table('ref_program_studi')->insertBatch($prodis);
        $prodiMap = [];
        foreach ($db->table('ref_program_studi')->get()->getResultArray() as $p) {
            $prodiMap[$p['kode_prodi']] = (int) $p['id'];
        }

        // 2. Angkatan
        $db->table('ref_angkatan')->truncate();
        $angkatans = [
            ['tahun_masuk' => 2022, 'perkiraan_lulus' => 2026, 'is_aktif' => 1, 'created_at' => $now],
            ['tahun_masuk' => 2023, 'perkiraan_lulus' => 2027, 'is_aktif' => 1, 'created_at' => $now],
            ['tahun_masuk' => 2024, 'perkiraan_lulus' => 2028, 'is_aktif' => 1, 'created_at' => $now],
            ['tahun_masuk' => 2025, 'perkiraan_lulus' => 2029, 'is_aktif' => 1, 'created_at' => $now],
            ['tahun_masuk' => 2026, 'perkiraan_lulus' => 2030, 'is_aktif' => 1, 'created_at' => $now],
        ];
        $db->table('ref_angkatan')->insertBatch($angkatans);
        $angkatanMap = [];
        foreach ($db->table('ref_angkatan')->get()->getResultArray() as $a) {
            $angkatanMap[(int) $a['tahun_masuk']] = (int) $a['id'];
        }

        // 3. Tahun Akademik
        $db->table('ref_tahun_akademik')->truncate();
        $tahunAkademikRows = [
            ['kode_tahun' => '2024/2025 Ganjil', 'tahun' => 2024, 'semester' => 'ganjil', 'tanggal_mulai' => '2024-09-01', 'tanggal_selesai' => '2025-01-31', 'is_aktif' => 0, 'created_at' => $now],
            ['kode_tahun' => '2024/2025 Genap',  'tahun' => 2024, 'semester' => 'genap',  'tanggal_mulai' => '2025-02-01', 'tanggal_selesai' => '2025-08-31', 'is_aktif' => 0, 'created_at' => $now],
            ['kode_tahun' => '2025/2026 Ganjil', 'tahun' => 2025, 'semester' => 'ganjil', 'tanggal_mulai' => '2025-09-01', 'tanggal_selesai' => '2026-01-31', 'is_aktif' => 0, 'created_at' => $now],
            ['kode_tahun' => '2025/2026 Genap',  'tahun' => 2025, 'semester' => 'genap',  'tanggal_mulai' => '2026-02-01', 'tanggal_selesai' => '2026-08-31', 'is_aktif' => 0, 'created_at' => $now],
            ['kode_tahun' => '2026/2027 Ganjil', 'tahun' => 2026, 'semester' => 'ganjil', 'tanggal_mulai' => '2026-09-01', 'tanggal_selesai' => '2027-01-31', 'is_aktif' => 1, 'created_at' => $now],
        ];
        $db->table('ref_tahun_akademik')->insertBatch($tahunAkademikRows);
        $taMap = [];
        foreach ($db->table('ref_tahun_akademik')->get()->getResultArray() as $ta) {
            $taMap[$ta['kode_tahun']] = (int) $ta['id'];
        }
        $tahunAkademikId = $taMap['2026/2027 Ganjil'];

        // 4. Kelas Referensi (Multi-Prodi & Multi-Kelas untuk pembuktian UAT-09)
        $db->table('ref_kelas')->truncate();
        $kelasBatch = [];
        foreach (['RSTJ', 'TRO', 'TO'] as $kdProdi) {
            foreach (['A', 'B', 'C'] as $namaKls) {
                $kelasBatch[] = [
                    'program_studi_id' => $prodiMap[$kdProdi],
                    'angkatan_id'      => $angkatanMap[2024],
                    'tingkat'          => 3,
                    'nama_kelas'       => $namaKls,
                    'is_aktif'         => 1,
                    'created_at'       => $now,
                ];
            }
        }
        $db->table('ref_kelas')->insertBatch($kelasBatch);
        $kelasMap = [];
        foreach ($db->table('ref_kelas')->get()->getResultArray() as $k) {
            $kelasMap[(int) $k['program_studi_id'] . '_' . $k['nama_kelas']] = (int) $k['id'];
        }
        $kelasAId = $kelasMap[$prodiMap['RSTJ'] . '_A'] ?? null;

        // 5. Status Taruna
        $db->table('ref_status_taruna')->truncate();
        $statuses = [
            ['kode_status' => 'aktif',             'nama_status' => 'Aktif',             'kelompok_status' => 'dalam_pendidikan',           'badge_color' => 'primary'],
            ['kode_status' => 'skorsing',          'nama_status' => 'Skorsing',          'kelompok_status' => 'dalam_pendidikan_sementara', 'badge_color' => 'warning'],
            ['kode_status' => 'cuti',              'nama_status' => 'Cuti Akademik',     'kelompok_status' => 'dalam_pendidikan_sementara', 'badge_color' => 'success'],
            ['kode_status' => 'mengundurkan_diri', 'nama_status' => 'Mengundurkan Diri', 'kelompok_status' => 'berhenti',                  'badge_color' => 'orange'],
            ['kode_status' => 'drop_out',          'nama_status' => 'Drop Out',          'kelompok_status' => 'berhenti',                  'badge_color' => 'danger'],
            ['kode_status' => 'lulus',             'nama_status' => 'Lulus',             'kelompok_status' => 'lulusan',                   'badge_color' => 'purple'],
        ];
        $db->table('ref_status_taruna')->insertBatch($statuses);

        // 6. Truncate tabel data
        if ($db->tableExists('taruna_capaian')) {
            $db->table('taruna_capaian')->truncate();
        }
        if ($db->tableExists('alumni_riwayat_perubahan')) {
            $db->table('alumni_riwayat_perubahan')->truncate();
        }
        $db->table('alumni_profil')->truncate();
        $db->table('taruna_peserta_prestasi')->truncate();
        $db->table('taruna_prestasi')->truncate();
        if ($db->tableExists('taruna_pelanggaran_tindak_lanjut')) {
            $db->table('taruna_pelanggaran_tindak_lanjut')->truncate();
        }
        if ($db->tableExists('taruna_pelanggaran_pihak')) {
            $db->table('taruna_pelanggaran_pihak')->truncate();
        }
        $db->table('taruna_pelanggaran')->truncate();
        if ($db->tableExists('taruna_beasiswa_hasil_peringkat')) {
            $db->table('taruna_beasiswa_hasil_peringkat')->truncate();
        }
        if ($db->tableExists('taruna_beasiswa_pemeringkatan')) {
            $db->table('taruna_beasiswa_pemeringkatan')->truncate();
        }
        if ($db->tableExists('taruna_nilai_ip')) {
            $db->table('taruna_nilai_ip')->truncate();
        }
        if ($db->tableExists('ref_wilayah_3tp')) {
            $db->table('ref_wilayah_3tp')->truncate();
        }
        $db->table('taruna_penerima_beasiswa')->truncate();
        $db->table('taruna_program_beasiswa')->truncate();
        if ($db->tableExists('lowongan_dokumen')) {
            $db->table('lowongan_dokumen')->truncate();
        }
        $db->table('lowongan_sasaran_prodi')->truncate();
        $db->table('lowongan_pekerjaan')->truncate();
        $db->table('perusahaan_mitra')->truncate();
        $db->table('riwayat_pendidikan_taruna')->truncate();
        $db->table('riwayat_status_taruna')->truncate();
        $db->table('pendidikan_taruna')->truncate();
        $db->table('master_taruna')->truncate();

        // 7. Generate 650 Taruna Presisi Sesuai Mock-up:
        // RSTJ: Aktif 180, Skorsing 2, Cuti 3, Mundur 2, DO 1, Lulus 60 = 248
        // TRO:  Aktif 160, Skorsing 1, Cuti 3, Mundur 2, DO 1, Lulus 50 = 217
        // TO:   Aktif 140, Skorsing 1, Cuti 2, Mundur 2, DO 0, Lulus 40 = 185
        // TOTAL = 650
        $distribution = [
            'RSTJ' => ['aktif' => 180, 'skorsing' => 2, 'cuti' => 3, 'mengundurkan_diri' => 2, 'drop_out' => 1, 'lulus' => 60],
            'TRO'  => ['aktif' => 160, 'skorsing' => 1, 'cuti' => 3, 'mengundurkan_diri' => 2, 'drop_out' => 1, 'lulus' => 50],
            'TO'   => ['aktif' => 140, 'skorsing' => 1, 'cuti' => 2, 'mengundurkan_diri' => 2, 'drop_out' => 0, 'lulus' => 40],
        ];

        $firstNames = ['Aditya', 'Bima', 'Bagus', 'Cahyo', 'Dimas', 'Eko', 'Fajar', 'Galang', 'Gilang', 'Hadi', 'Ilham', 'Joko', 'Kurniawan', 'Muhammad', 'Nugroho', 'Pandu', 'Pratama', 'Rafi', 'Reza', 'Rian', 'Satria', 'Tri', 'Wahyu', 'Yoga', 'Anisa', 'Citra', 'Dewi', 'Dian', 'Fitri', 'Indah', 'Lestari', 'Maya', 'Nabila', 'Putri', 'Rani', 'Siti', 'Triana', 'Wulandari', 'Yuliana'];
        $lastNames = ['Pratama', 'Saputra', 'Wijaya', 'Kusuma', 'Santoso', 'Utomo', 'Hidayat', 'Wibowo', 'Nugraha', 'Setiawan', 'Ramadhan', 'Firmansyah', 'Kurnia', 'Pangestu', 'Suryanto', 'Purnomo', 'Mahendra', 'Gunawan', 'Subekti', 'Hakim'];

        $tarunaBatch = [];
        $pendidikanBatch = [];
        $riwayatStatusBatch = [];
        $riwayatPendidikanBatch = [];
        $alumniBatch = [];
        $alumniRiwayatBatch = [];

        $counter = 1;
        $cutiCounter = 0;
        $tahunMasukList = [2023, 2024, 2025, 2026];

        foreach ($distribution as $kodeProdi => $counts) {
            $prodiId = $prodiMap[$kodeProdi];

            foreach ($counts as $statusCode => $total) {
                for ($i = 0; $i < $total; $i++) {
                    $isAditya = ($counter === 1);
                    $nomorTaruna = $isAditya ? '2401001' : sprintf('%08d', 20230000 + $counter);

                    $fn = $isAditya ? 'Aditya' : $firstNames[($counter + $i) % count($firstNames)];
                    $ln = $isAditya ? 'Pratama' : $lastNames[($counter * 3 + $i) % count($lastNames)];
                    $namaLengkap = $fn . ' ' . $ln;
                    $jk = $isAditya ? 'L' : (in_array($fn, ['Anisa', 'Citra', 'Dewi', 'Dian', 'Fitri', 'Indah', 'Lestari', 'Maya', 'Nabila', 'Putri', 'Rani', 'Siti', 'Triana', 'Wulandari', 'Yuliana'], true) ? 'P' : 'L');

                    $thnMasuk = $isAditya ? 2024 : (($statusCode === 'lulus') ? 2022 : $tahunMasukList[$counter % count($tahunMasukList)]);
                    $angkatanId = $angkatanMap[$thnMasuk] ?? $angkatanMap[2023];

                    // 52 dari 650 rekaman diset nama_wali kosong agar kelengkapan profil terhitung 92% (598/650)
                    $namaWali = ($counter > 598) ? '' : ($isAditya ? 'Budi Pratama' : 'Wali dari ' . $fn);

                    $tarunaBatch[] = [
                        'nomor_taruna'    => $nomorTaruna,
                        'nama_lengkap'    => $namaLengkap,
                        'nik'             => '332801' . sprintf('%010d', 1000000000 + $counter),
                        'nama_panggilan'  => $fn,
                        'jenis_kelamin'   => $jk,
                        'tempat_lahir'    => 'Tegal',
                        'tanggal_lahir'   => $isAditya ? '2005-05-15' : '2004-05-12',
                        'agama'           => 'Islam',
                        'foto'            => null,
                        'nomor_hp'        => '0812' . sprintf('%08d', 10000000 + $counter),
                        'email'           => $isAditya ? 'aditya@taruna.pktj.ac.id' : strtolower($fn . '.' . $nomorTaruna) . '@taruna.pktj.ac.id',
                        'alamat_domisili' => 'Asrama Taruna PKTJ Tegal',
                        'alamat_asal'     => 'Jl. Gajah Mada No. ' . ($counter % 100 + 1) . ', Tegal',
                        'nama_wali'       => $namaWali,
                        'hubungan_wali'   => 'Ayah',
                        'nomor_hp_wali'   => '0813' . sprintf('%08d', 20000000 + $counter),
                        'status_terkini'  => $statusCode,
                        'created_at'      => $now,
                        'updated_at'      => $now,
                    ];

                    $tingkatTaruna  = $isAditya ? 3 : (($statusCode === 'lulus') ? 4 : max(1, min(4, 2026 - $thnMasuk + 1)));
                    $semesterTaruna = $isAditya ? 5 : (($statusCode === 'lulus') ? 8 : max(1, min(8, (2026 - $thnMasuk) * 2 + 1)));

                    $kelasNamaTaruna = 'A';
                    if ($kodeProdi === 'RSTJ') {
                        if ($counter >= 31 && $counter <= 60) {
                            $kelasNamaTaruna = 'B';
                        } elseif ($counter > 60) {
                            $kelasNamaTaruna = 'C';
                        }
                    } elseif ($i >= 30 && $i < 60) {
                        $kelasNamaTaruna = 'B';
                    } elseif ($i >= 60) {
                        $kelasNamaTaruna = 'C';
                    }
                    $kelasTarunaId = $kelasMap[$prodiId . '_' . $kelasNamaTaruna] ?? $kelasAId;

                    $pendidikanBatch[] = [
                        'nomor_taruna'      => $nomorTaruna,
                        'program_studi_id'  => $prodiId,
                        'angkatan_id'       => $angkatanId,
                        'kelas_id'          => $kelasTarunaId,
                        'tingkat'           => $tingkatTaruna,
                        'semester_aktif'    => $semesterTaruna,
                        'jalur_penerimaan'  => 'Pola Pembibitan',
                        'asal_formasi'      => $isAditya ? 'Pemerintah Daerah' : 'Kemenhub RI',
                        'tahun_akademik_id' => $tahunAkademikId,
                        'created_at'        => $now,
                        'updated_at'        => $now,
                    ];

                    if ($isAditya) {
                        // Riwayat status Aditya Pratama (2401001): Aktif -> Cuti Akademik -> Aktif
                        $riwayatStatusBatch[] = [
                            'nomor_taruna'      => '2401001',
                            'kode_status'       => 'aktif',
                            'mulai_berlaku'     => '2024-09-01',
                            'akhir_berlaku'     => '2026-02-01',
                            'rencana_kembali'   => null,
                            'alasan_perubahan'  => 'Penerimaan taruna baru angkatan 2024',
                            'nomor_keputusan'   => 'SK/001/2024',
                            'tanggal_keputusan' => '2024-08-25',
                            'bukti_dokumen'     => 'sk_001_2024.pdf',
                            'status_validasi'   => 'tervalidasi',
                            'created_at'        => $now,
                            'updated_at'        => $now,
                        ];
                        $riwayatStatusBatch[] = [
                            'nomor_taruna'      => '2401001',
                            'kode_status'       => 'cuti',
                            'mulai_berlaku'     => '2026-02-01',
                            'akhir_berlaku'     => '2026-09-01',
                            'rencana_kembali'   => '2026-09-01',
                            'alasan_perubahan'  => 'Izin cuti akademik semester genap',
                            'nomor_keputusan'   => 'SK/005/2026',
                            'tanggal_keputusan' => '2026-01-25',
                            'bukti_dokumen'     => 'sk_005_2026.pdf',
                            'status_validasi'   => 'tervalidasi',
                            'created_at'        => $now,
                            'updated_at'        => $now,
                        ];
                        $riwayatStatusBatch[] = [
                            'nomor_taruna'      => '2401001',
                            'kode_status'       => 'aktif',
                            'mulai_berlaku'     => '2026-09-01',
                            'akhir_berlaku'     => null,
                            'rencana_kembali'   => null,
                            'alasan_perubahan'  => 'Aktif kembali mengikuti pendidikan semester ganjil',
                            'nomor_keputusan'   => 'SK/024/2026',
                            'tanggal_keputusan' => '2026-08-28',
                            'bukti_dokumen'     => 'sk_024_2026.pdf',
                            'status_validasi'   => 'tervalidasi',
                            'created_at'        => $now,
                            'updated_at'        => $now,
                        ];

                        // Riwayat pendidikan Aditya Pratama (2401001)
                        $riwayatPendidikanBatch[] = [
                            'nomor_taruna'      => '2401001',
                            'program_studi_id'  => $prodiId,
                            'kelas_id'          => $kelasAId,
                            'tingkat'           => 1,
                            'semester'          => 1,
                            'tahun_akademik_id' => $taMap['2024/2025 Ganjil'],
                            'mulai_berlaku'     => '2024-09-01',
                            'akhir_berlaku'     => '2025-02-01',
                            'created_at'        => $now,
                        ];
                        $riwayatPendidikanBatch[] = [
                            'nomor_taruna'      => '2401001',
                            'program_studi_id'  => $prodiId,
                            'kelas_id'          => $kelasAId,
                            'tingkat'           => 1,
                            'semester'          => 2,
                            'tahun_akademik_id' => $taMap['2024/2025 Genap'],
                            'mulai_berlaku'     => '2025-02-01',
                            'akhir_berlaku'     => '2025-09-01',
                            'created_at'        => $now,
                        ];
                        $riwayatPendidikanBatch[] = [
                            'nomor_taruna'      => '2401001',
                            'program_studi_id'  => $prodiId,
                            'kelas_id'          => $kelasAId,
                            'tingkat'           => 2,
                            'semester'          => 3,
                            'tahun_akademik_id' => $taMap['2025/2026 Ganjil'],
                            'mulai_berlaku'     => '2025-09-01',
                            'akhir_berlaku'     => '2026-02-01',
                            'created_at'        => $now,
                        ];
                        $riwayatPendidikanBatch[] = [
                            'nomor_taruna'      => '2401001',
                            'program_studi_id'  => $prodiId,
                            'kelas_id'          => $kelasAId,
                            'tingkat'           => 3,
                            'semester'          => 5,
                            'tahun_akademik_id' => $taMap['2026/2027 Ganjil'],
                            'mulai_berlaku'     => '2026-09-01',
                            'akhir_berlaku'     => null,
                            'created_at'        => $now,
                        ];
                    } else {
                        // Riwayat status taruna lainnya
                        $rencanaKembali = null;
                        if ($statusCode === 'cuti') {
                            $cutiCounter++;
                            // 2 kasus cuti dengan rencana_kembali <= 30 Oktober 2026 (perlu ditinjau)
                            $rencanaKembali = ($cutiCounter <= 2) ? '2026-10-25' : '2027-01-15';
                        } elseif ($statusCode === 'skorsing') {
                            $rencanaKembali = '2026-11-20';
                        }

                        $riwayatStatusBatch[] = [
                            'nomor_taruna'      => $nomorTaruna,
                            'kode_status'       => $statusCode,
                            'mulai_berlaku'     => '2026-09-01',
                            'akhir_berlaku'     => null,
                            'rencana_kembali'   => $rencanaKembali,
                            'alasan_perubahan'  => ($statusCode === 'aktif') ? 'Registrasi semester baru' : 'Keputusan penetapan status ' . $statusCode,
                            'nomor_keputusan'   => 'SK/' . strtoupper($statusCode) . '/2026/' . $counter,
                            'tanggal_keputusan' => '2026-08-25',
                            'bukti_dokumen'     => 'sk_' . $statusCode . '_' . $nomorTaruna . '.pdf',
                            'status_validasi'   => 'tervalidasi',
                            'created_at'        => $now,
                            'updated_at'        => $now,
                        ];

                        $riwayatPendidikanBatch[] = [
                            'nomor_taruna'      => $nomorTaruna,
                            'program_studi_id'  => $prodiId,
                            'kelas_id'          => $kelasTarunaId,
                            'tingkat'           => $tingkatTaruna,
                            'semester'          => $semesterTaruna,
                            'tahun_akademik_id' => $tahunAkademikId,
                            'mulai_berlaku'     => '2026-09-01',
                            'akhir_berlaku'     => null,
                            'created_at'        => $now,
                        ];
                    }

                    if ($statusCode === 'lulus') {
                        $pekerjaanOpsi = [
                            'Penguji Kendaraan Bermotor',
                            'Staf Keselamatan Transportasi Darat',
                            'Analis Lalu Lintas Jalan',
                            'Staff Engineering Armada & Bengkel',
                            'Inspector Keselamatan Jalan',
                            'Pelaksana Administrasi Terminal',
                        ];
                        $instansiOpsi = [
                            'BPTD Kelas II Jawa Tengah',
                            'Dinas Perhubungan Kota Semarang',
                            'Dinas Perhubungan Kabupaten Tegal',
                            'Balai Pengelola Transportasi Darat DKI Jakarta',
                            'PT Transjakarta',
                            'Direktorat Sarana Transportasi Jalan',
                        ];
                        $kotaOpsi = ['Semarang', 'Tegal', 'Jakarta', 'Surabaya', 'Bandung', 'Yogyakarta'];
                        $sumberOpsi = ['mandiri_alumni', 'telepon_pic', 'email_resmi', 'pesan_whatsapp', 'yudisium_akademik'];

                        $jobTitle     = $pekerjaanOpsi[$counter % count($pekerjaanOpsi)];
                        $companyTitle = $instansiOpsi[$counter % count($instansiOpsi)];
                        $kotaNama     = $kotaOpsi[$counter % count($kotaOpsi)];
                        $hpAlumni     = '0812' . sprintf('%08d', 10000000 + $counter);
                        $emailAlumni  = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $fn) . '.' . $nomorTaruna) . '@alumni.pktj.ac.id';
                        $sumberKontak = $sumberOpsi[$counter % count($sumberOpsi)];
                        $tglLulus     = '2026-08-20';
                        $skLulus      = 'SK.LULUS/PKTJ/2026/' . $counter;
                        $tglUpdate    = ($counter % 3 === 0) ? '2026-10-12' : '2026-09-25';

                        $alumniBatch[] = [
                            'nomor_taruna'             => $nomorTaruna,
                            'tanggal_lulus'            => $tglLulus,
                            'nomor_keputusan_lulus'    => $skLulus,
                            'tahun_lulus'              => 2026,
                            'pekerjaan_saat_ini'       => $jobTitle,
                            'instansi_saat_ini'        => $companyTitle,
                            'kontak_terakhir'          => $hpAlumni,
                            'email_alumni'             => $emailAlumni,
                            'telepon_alumni'           => $hpAlumni,
                            'kota_domisili'            => $kotaNama,
                            'alamat_domisili'          => 'Jl. Transportasi No. ' . (($counter % 80) + 1) . ', ' . $kotaNama,
                            'status_profil'            => 'aktif',
                            'sumber_konfirmasi'        => $sumberKontak,
                            'tanggal_pembaruan_kontak' => $tglUpdate,
                            'dicatat_oleh'             => 'PIC Karir & Alumni',
                            'divalidasi_oleh'          => 'Ketua Bagian AAK',
                            'tanggal_validasi'         => $tglLulus,
                            'catatan_profil'           => 'Profil dasar alumni terbentuk otomatis dari kelulusan tervalidasi.',
                            'created_at'               => $now,
                            'updated_at'               => $now,
                        ];

                        $alumniRiwayatBatch[] = [
                            'nomor_taruna'            => $nomorTaruna,
                            'jenis_kejadian'          => 'kelulusan_awal',
                            'judul_ringkas'           => 'Penetapan profil dasar alumni dari status lulus tervalidasi',
                            'data_sebelum_json'       => json_encode(['status_taruna' => 'aktif'], JSON_UNESCAPED_UNICODE),
                            'data_sesudah_json'       => json_encode([
                                'tanggal_lulus'         => $tglLulus,
                                'nomor_keputusan_lulus' => $skLulus,
                                'status_profil'         => 'aktif',
                            ], JSON_UNESCAPED_UNICODE),
                            'tanggal_lulus_baru'      => $tglLulus,
                            'nomor_keputusan_baru'    => $skLulus,
                            'status_taruna_pengganti' => null,
                            'sumber_konfirmasi'       => 'yudisium_akademik',
                            'tanggal_kejadian'        => $tglLulus,
                            'alasan_perubahan'        => 'Kelulusan yudisium divalidasi resmi.',
                            'dampak_ringkasan'        => 'Terbentuk 1 profil dasar alumni aktif terhubung ke Nomor Taruna.',
                            'status_validasi'         => 'tervalidasi',
                            'catatan_validator'       => 'Sesuai SK yudisium resmi.',
                            'diajukan_oleh'           => 'Staf Ketarunaan',
                            'peran_pencatat'          => 'ketarunaan',
                            'divalidasi_oleh'         => 'Ketua Bagian AAK',
                            'tanggal_validasi'        => $tglLulus,
                            'created_at'              => $now,
                            'updated_at'              => $now,
                        ];

                        if (count($alumniBatch) <= 25) {
                            $alumniRiwayatBatch[] = [
                                'nomor_taruna'            => $nomorTaruna,
                                'jenis_kejadian'          => 'pembaruan_kontak',
                                'judul_ringkas'           => 'Konfirmasi pekerjaan dan kontak alumni (' . $companyTitle . ')',
                                'data_sebelum_json'       => json_encode(['pekerjaan_saat_ini' => '-', 'instansi_saat_ini' => '-'], JSON_UNESCAPED_UNICODE),
                                'data_sesudah_json'       => json_encode([
                                    'pekerjaan_saat_ini' => $jobTitle,
                                    'instansi_saat_ini'  => $companyTitle,
                                    'email_alumni'       => $emailAlumni,
                                    'telepon_alumni'     => $hpAlumni,
                                    'kota_domisili'      => $kotaNama,
                                ], JSON_UNESCAPED_UNICODE),
                                'tanggal_lulus_baru'      => null,
                                'nomor_keputusan_baru'    => null,
                                'status_taruna_pengganti' => null,
                                'sumber_konfirmasi'       => $sumberKontak,
                                'tanggal_kejadian'        => $tglUpdate,
                                'alasan_perubahan'        => 'Pembaruan data kontak dan penempatan kerja terkini.',
                                'dampak_ringkasan'        => 'Kontak aktif diperbarui dengan sumber ' . $sumberKontak . '.',
                                'status_validasi'         => 'tervalidasi',
                                'catatan_validator'       => null,
                                'diajukan_oleh'           => 'PIC Karir & Alumni',
                                'peran_pencatat'          => 'karir_alumni',
                                'divalidasi_oleh'         => 'PIC Karir & Alumni',
                                'tanggal_validasi'        => $tglUpdate,
                                'created_at'              => $now,
                                'updated_at'              => $now,
                            ];
                        }

                        if (count($alumniBatch) === 1) {
                            $alumniRiwayatBatch[] = [
                                'nomor_taruna'            => $nomorTaruna,
                                'jenis_kejadian'          => 'ralat_sk',
                                'judul_ringkas'           => 'Penyesuaian format penomoran SK kelulusan',
                                'data_sebelum_json'       => json_encode(['nomor_keputusan_lulus' => 'SK.LULUS/PKTJ/2026/DRAFT-' . $counter], JSON_UNESCAPED_UNICODE),
                                'data_sesudah_json'       => json_encode(['nomor_keputusan_lulus' => $skLulus, 'tanggal_lulus' => $tglLulus], JSON_UNESCAPED_UNICODE),
                                'tanggal_lulus_baru'      => $tglLulus,
                                'nomor_keputusan_baru'    => $skLulus,
                                'status_taruna_pengganti' => null,
                                'sumber_konfirmasi'       => 'yudisium_akademik',
                                'tanggal_kejadian'        => '2026-08-25',
                                'alasan_perubahan'        => 'Penyelarasan kode arsip SK kelulusan resmi.',
                                'dampak_ringkasan'        => 'Jumlah alumni tetap; nomor SK diperbarui tanpa menghapus profil.',
                                'status_validasi'         => 'tervalidasi',
                                'catatan_validator'       => 'Sudah sesuai dengan salinan SK yudisium.',
                                'diajukan_oleh'           => 'Staf Ketarunaan',
                                'peran_pencatat'          => 'ketarunaan',
                                'divalidasi_oleh'         => 'Ketua Bagian AAK',
                                'tanggal_validasi'        => '2026-08-25',
                                'created_at'              => $now,
                                'updated_at'              => $now,
                            ];
                        }
                    }

                    $counter++;
                }
            }
        }

        // Tambah 6 pengajuan status (diajukan) & 2 perlu_perbaikan (tidak mengubah status_terkini)
        for ($q = 2; $q <= 7; $q++) {
            $riwayatStatusBatch[] = [
                'nomor_taruna'      => $tarunaBatch[$q]['nomor_taruna'],
                'kode_status'       => 'cuti',
                'mulai_berlaku'     => '2026-11-01',
                'akhir_berlaku'     => null,
                'rencana_kembali'   => '2027-02-01',
                'alasan_perubahan'  => 'Pengajuan cuti akademik keperluan keluarga',
                'nomor_keputusan'   => 'SK/DRAFT/2026/' . $q,
                'tanggal_keputusan' => '2026-10-29',
                'bukti_dokumen'     => 'permohonan_cuti_' . $q . '.pdf',
                'status_validasi'   => 'diajukan',
                'created_at'        => $now,
                'updated_at'        => $now,
            ];
        }
        for ($q = 8; $q <= 9; $q++) {
            $riwayatStatusBatch[] = [
                'nomor_taruna'      => $tarunaBatch[$q]['nomor_taruna'],
                'kode_status'       => 'aktif',
                'mulai_berlaku'     => '2026-11-01',
                'akhir_berlaku'     => null,
                'rencana_kembali'   => null,
                'alasan_perubahan'  => 'Koreksi lampiran identitas & berkas keputusan',
                'nomor_keputusan'   => 'SK/REV/2026/' . $q,
                'tanggal_keputusan' => '2026-10-28',
                'bukti_dokumen'     => null,
                'status_validasi'   => 'perlu_perbaikan',
                'created_at'        => $now,
                'updated_at'        => $now,
            ];
        }

        // Chunk insert taruna
        foreach (array_chunk($tarunaBatch, 100) as $chunk) {
            $db->table('master_taruna')->insertBatch($chunk);
        }
        foreach (array_chunk($pendidikanBatch, 100) as $chunk) {
            $db->table('pendidikan_taruna')->insertBatch($chunk);
        }
        foreach (array_chunk($riwayatStatusBatch, 100) as $chunk) {
            $db->table('riwayat_status_taruna')->insertBatch($chunk);
        }
        foreach (array_chunk($riwayatPendidikanBatch, 100) as $chunk) {
            $db->table('riwayat_pendidikan_taruna')->insertBatch($chunk);
        }
        if (!empty($alumniBatch)) {
            foreach (array_chunk($alumniBatch, 100) as $chunk) {
                $db->table('alumni_profil')->insertBatch($chunk);
            }
        }

        // 8. Prestasi Tervalidasi (32 Prestasi Tervalidasi: 10 Tim, 22 Individu, 24 Taruna Berprestasi Unik)
        $prestasiTimTemplates = [
            ['nama_prestasi' => 'Lomba Inovasi Transportasi', 'jenis' => 'akademik', 'bidang' => 'Inovasi Keselamatan Jalan', 'tingkat' => 'nasional', 'peringkat' => 'Juara II', 'penyelenggara' => 'Forum Keselamatan Transportasi', 'lokasi' => 'Jakarta', 'nama_tim' => 'Tim Inovasi RSTJ', 'tgl' => '2025-11-15', 'tgl_mulai' => '2025-11-14'],
            ['nama_prestasi' => 'Kejuaraan Futsal Antar Kampus', 'jenis' => 'olahraga', 'bidang' => 'Olahraga & Kedinasan', 'tingkat' => 'daerah', 'peringkat' => 'Juara I', 'penyelenggara' => 'BAPOMI Jawa Tengah', 'lokasi' => 'Semarang', 'nama_tim' => 'Tim Futsal PKTJ', 'tgl' => '2026-09-20', 'tgl_mulai' => '2026-09-18'],
            ['nama_prestasi' => 'Juara 1 Lomba Inovasi RSTJ Nasional 2026', 'jenis' => 'akademik', 'bidang' => 'Keselamatan & Transportasi', 'tingkat' => 'nasional', 'peringkat' => 'Juara I', 'penyelenggara' => 'Kementerian Perhubungan RI', 'lokasi' => 'Jakarta', 'nama_tim' => 'Tim Rekayasa RSTJ', 'tgl' => '2026-08-14', 'tgl_mulai' => '2026-08-10'],
            ['nama_prestasi' => 'Juara 2 Kategori Desain Otomotif Ramah Lingkungan', 'jenis' => 'akademik', 'bidang' => 'Teknologi Otomotif', 'tingkat' => 'nasional', 'peringkat' => 'Juara II', 'penyelenggara' => 'Kementerian Perhubungan RI', 'lokasi' => 'Bandung', 'nama_tim' => 'OtoGreen PKTJ', 'tgl' => '2026-07-22', 'tgl_mulai' => '2026-07-20'],
        ];
        $prestasiIndividuTemplates = [
            ['nama_prestasi' => 'Juara 1 Karya Tulis Ilmiah Keselamatan Jalan BPTD', 'jenis' => 'akademik', 'bidang' => 'Karya Tulis Ilmiah', 'tingkat' => 'daerah', 'peringkat' => 'Juara I', 'penyelenggara' => 'BPTD Kelas II Jateng', 'lokasi' => 'Tegal'],
            ['nama_prestasi' => 'Medali Emas Kejuaraan Pencak Silat Perguruan Kedinasan', 'jenis' => 'olahraga', 'bidang' => 'Olahraga & Kedinasan', 'tingkat' => 'nasional', 'peringkat' => 'Medali Emas', 'penyelenggara' => 'FORKEDINAS RI', 'lokasi' => 'Jakarta'],
            ['nama_prestasi' => 'Kompetisi Audit Keselamatan Ruas Jalan Nasional', 'jenis' => 'akademik', 'bidang' => 'Inovasi Keselamatan Jalan', 'tingkat' => 'nasional', 'peringkat' => 'Juara II', 'penyelenggara' => 'Ditjen Hubdat Kemenhub', 'lokasi' => 'Yogyakarta'],
            ['nama_prestasi' => 'Festival Paduan Suara & Musik Kedinasan', 'jenis' => 'seni', 'bidang' => 'Seni & Budaya', 'tingkat' => 'daerah', 'peringkat' => 'Juara III', 'penyelenggara' => 'Disdikbud Jateng', 'lokasi' => 'Semarang'],
        ];

        $tarunaAktif = $db->table('master_taruna')
            ->select('nomor_taruna')
            ->where('status_terkini', 'aktif')
            ->where('nomor_taruna !=', '2401001')
            ->limit(23)
            ->get()
            ->getResultArray();
        $taruna24 = array_merge(['2401001'], array_column($tarunaAktif, 'nomor_taruna'));

        $riwayatContoh = json_encode([
            ['tanggal' => '2026-10-30', 'waktu' => '2026-10-30 10:00', 'pelaku' => 'Validator Unit', 'aksi' => 'Menyetujui', 'status' => 'tervalidasi', 'catatan' => 'Data dan bukti sesuai.'],
            ['tanggal' => '2026-10-30', 'waktu' => '2026-10-30 08:30', 'pelaku' => 'PIC Ketarunaan', 'aksi' => 'Mengajukan ulang', 'status' => 'diajukan', 'catatan' => 'Bukti piagam dilengkapi.'],
            ['tanggal' => '2026-10-29', 'waktu' => '2026-10-29 14:00', 'pelaku' => 'Validator Unit', 'aksi' => 'Mengembalikan', 'status' => 'perlu_perbaikan', 'catatan' => 'Lengkapi bukti piagam.'],
            ['tanggal' => '2026-10-29', 'waktu' => '2026-10-29 09:00', 'pelaku' => 'PIC Ketarunaan', 'aksi' => 'Mengajukan', 'status' => 'diajukan', 'catatan' => 'Pengajuan awal.'],
            ['tanggal' => '2026-10-28', 'waktu' => '2026-10-28 15:00', 'pelaku' => 'PIC Ketarunaan', 'aksi' => 'Menyimpan Draf', 'status' => 'draf', 'catatan' => 'Pencatatan awal.'],
        ], JSON_UNESCAPED_UNICODE);

        for ($p = 1; $p <= 32; $p++) {
            $isTim = ($p <= 10);

            if ($isTim) {
                $tpl = $prestasiTimTemplates[($p - 1) % count($prestasiTimTemplates)];
                $isMainInovasi = ($p === 1);
                $namaPrestasi  = ($p <= 2) ? $tpl['nama_prestasi'] : ($tpl['nama_prestasi'] . ' #' . $p);
                $tglMulai      = $tpl['tgl_mulai'];
                $tglSelesai    = $tpl['tgl'];
                $namaTim       = $tpl['nama_tim'];
                $lokasi        = $tpl['lokasi'];
                $noSert        = $isMainInovasi ? 'PIAGAM/115/2025' : ('SERT/PRESTASI/2026/' . sprintf('%03d', $p));
                $buktiBerkas   = $isMainInovasi ? 'Piagam_Prestasi.pdf' : ('sertifikat_prestasi_' . $p . '.pdf');
            } else {
                $tpl = $prestasiIndividuTemplates[($p - 11) % count($prestasiIndividuTemplates)];
                $namaPrestasi  = ($p > 14) ? ($tpl['nama_prestasi'] . ' #' . $p) : $tpl['nama_prestasi'];
                $tglMulai      = '2026-08-' . sprintf('%02d', (($p % 20) + 5));
                $tglSelesai    = $tglMulai;
                $namaTim       = null;
                $lokasi        = $tpl['lokasi'];
                $noSert        = 'SERT/PRESTASI/2026/' . sprintf('%03d', $p);
                $buktiBerkas   = 'sertifikat_prestasi_' . $p . '.pdf';
            }

            $db->table('taruna_prestasi')->insert([
                'nama_prestasi'         => $namaPrestasi,
                'jenis_prestasi'        => $tpl['jenis'],
                'bidang_prestasi'       => $tpl['bidang'],
                'tingkat_prestasi'      => $tpl['tingkat'],
                'peringkat'             => $tpl['peringkat'],
                'penyelenggara'         => $tpl['penyelenggara'],
                'lokasi_kegiatan'       => $lokasi,
                'tanggal_mulai'         => $tglMulai,
                'tanggal_selesai'       => $tglSelesai,
                'jenis_keikutsertaan'   => $isTim ? 'tim' : 'individu',
                'nama_tim'              => $namaTim,
                'pembimbing'            => 'Pembina Ketarunaan',
                'unit_pemilik'          => 'Ketarunaan',
                'sumber_data'           => 'Piagam Penghargaan',
                'nomor_sertifikat'      => $noSert,
                'bukti_berkas'          => $buktiBerkas,
                'ukuran_berkas_kb'      => ($p === 1) ? 420 : 680,
                'dokumentasi_json'      => ($p === 1) ? json_encode(['Foto_Kegiatan_01.jpg', 'Foto_Kegiatan_02.jpg']) : null,
                'status_validasi'       => 'tervalidasi',
                'diajukan_oleh'         => 'PIC Ketarunaan',
                'divalidasi_oleh'       => 'Validator Unit',
                'tanggal_validasi'      => '2026-10-30',
                'riwayat_validasi_json' => ($p === 1) ? $riwayatContoh : json_encode([
                    ['tanggal' => '2026-10-30', 'waktu' => '2026-10-30 09:00', 'pelaku' => 'Validator Unit', 'aksi' => 'Menyetujui', 'status' => 'tervalidasi', 'catatan' => 'Data dan bukti terverifikasi.'],
                    ['tanggal' => '2026-10-29', 'waktu' => '2026-10-29 10:00', 'pelaku' => 'PIC Ketarunaan', 'aksi' => 'Mengajukan', 'status' => 'diajukan', 'catatan' => 'Pengajuan prestasi.'],
                ], JSON_UNESCAPED_UNICODE),
                'created_at'            => $now,
                'updated_at'            => $now,
            ]);
            $prestasiId = $db->insertID();

            if ($isTim) {
                if ($p === 1) {
                    // Aditya Pratama (2401001) Ketua Tim + 2 anggota
                    $memberIndices = [0, 1, 2];
                } elseif ($p === 2) {
                    // Kejuaraan Futsal Antar Kampus: 10 anggota unik (index 3..12)
                    $memberIndices = range(3, 12);
                } else {
                    // Tim 3..10: 3 anggota tiap tim dari index 1..23 (tanpa index 0)
                    $base = (($p - 3) * 3) % 23 + 1;
                    $memberIndices = [
                        (($base - 1) % 23) + 1,
                        ($base % 23) + 1,
                        (($base + 1) % 23) + 1,
                    ];
                }

                $memberIndices = array_values(array_unique($memberIndices));
                foreach ($memberIndices as $idxOrder => $mIdx) {
                    $db->table('taruna_peserta_prestasi')->insert([
                        'prestasi_id'  => $prestasiId,
                        'nomor_taruna' => $taruna24[$mIdx],
                        'peran'        => ($idxOrder === 0) ? 'Ketua Tim' : 'Anggota',
                        'created_at'   => $now,
                    ]);
                }
            } else {
                // p=11 milik Aditya Pratama (total 2 prestasi tervalidasi), p=12..32 bergilir index 1..23
                $targetIdx = ($p === 11) ? 0 : ((($p - 12) % 23) + 1);
                $db->table('taruna_peserta_prestasi')->insert([
                    'prestasi_id'  => $prestasiId,
                    'nomor_taruna' => $taruna24[$targetIdx],
                    'peran'        => 'Peserta Utama',
                    'created_at'   => $now,
                ]);
            }
        }

        // Tambah 5 prestasi berstatus 'diajukan' & 2 'perlu_perbaikan' untuk antrean validasi
        for ($pd = 1; $pd <= 7; $pd++) {
            $statusAntrean = ($pd <= 5) ? 'diajukan' : 'perlu_perbaikan';
            $namaAntrean = match ($pd) {
                1       => 'Lomba Karya Tulis Keselamatan',
                6       => 'Kompetisi Poster Keselamatan',
                default => 'Usulan Prestasi Kompetisi Transportasi #' . $pd,
            };
            $peringkatAntrean = match ($pd) {
                1       => 'Juara III',
                6       => 'Finalis',
                default => 'Juara Harapan',
            };
            $tingkatAntrean = ($pd === 6) ? 'internal' : 'nasional';
            $tglAntrean     = ($pd === 1) ? '2026-10-12' : (($pd === 6) ? '2026-10-18' : '2026-10-15');

            $db->table('taruna_prestasi')->insert([
                'nama_prestasi'         => $namaAntrean,
                'jenis_prestasi'        => ($pd === 6) ? 'seni' : 'akademik',
                'bidang_prestasi'       => 'Keselamatan Jalan',
                'tingkat_prestasi'      => $tingkatAntrean,
                'peringkat'             => $peringkatAntrean,
                'penyelenggara'         => 'BPTD',
                'lokasi_kegiatan'       => 'Tegal',
                'tanggal_mulai'         => $tglAntrean,
                'tanggal_selesai'       => $tglAntrean,
                'jenis_keikutsertaan'   => 'individu',
                'pembimbing'            => 'Pembina Ketarunaan',
                'unit_pemilik'          => 'Ketarunaan',
                'sumber_data'           => 'Sertifikat',
                'nomor_sertifikat'      => 'SERT/USUL/2026/' . $pd,
                'bukti_berkas'          => 'usulan_prestasi_' . $pd . '.pdf',
                'ukuran_berkas_kb'      => 310,
                'status_validasi'       => $statusAntrean,
                'catatan_validasi'      => ($statusAntrean === 'perlu_perbaikan') ? 'Lengkapi lampiran piagam atau sertifikat resmi.' : null,
                'diajukan_oleh'         => 'PIC Ketarunaan',
                'divalidasi_oleh'       => ($statusAntrean === 'perlu_perbaikan') ? 'Validator Unit' : null,
                'riwayat_validasi_json' => json_encode([
                    ['tanggal' => '2026-10-29', 'waktu' => '2026-10-29 09:00', 'pelaku' => 'PIC Ketarunaan', 'aksi' => 'Mengajukan', 'status' => $statusAntrean, 'catatan' => 'Pengajuan prestasi.'],
                ], JSON_UNESCAPED_UNICODE),
                'created_at'            => $now,
                'updated_at'            => $now,
            ]);
            $usulanId = $db->insertID();

            $db->table('taruna_peserta_prestasi')->insert([
                'prestasi_id'  => $usulanId,
                'nomor_taruna' => $taruna24[$pd],
                'peran'        => 'Peserta Utama',
                'created_at'   => $now,
            ]);
        }

        // 9. Pelanggaran Taruna (12 Terbukti: 9 Selesai & 3 Proses, 4 Dugaan, 1 Tidak Terbukti, 1 Dibatalkan)
        $hasPihakTable  = $db->tableExists('taruna_pelanggaran_pihak');
        $hasTindakTable = $db->tableExists('taruna_pelanggaran_tindak_lanjut');

        for ($k = 1; $k <= 12; $k++) {
            $isSelesai  = ($k <= 9);
            $tglKejadian = '2026-09-' . sprintf('%02d', ($k % 20 + 5));
            $tglPeriksa  = '2026-09-' . sprintf('%02d', ($k % 20 + 6));
            $tglPutus    = '2026-09-' . sprintf('%02d', ($k % 20 + 8));
            $noPerkara   = 'PKR/2026/' . sprintf('%04d', $k);
            $noSk        = 'SK.SANKSI/2026/' . sprintf('%04d', $k);
            $tarunaRow   = $tarunaBatch[$k];

            $dokJson = [
                [
                    'nama_berkas'    => 'Laporan_Awal_' . sprintf('%02d', $k) . '.pdf',
                    'jenis_dokumen'  => 'Laporan',
                    'klasifikasi'    => 'terbatas',
                    'tanggal_unggah' => $tglKejadian,
                    'ukuran_kb'      => 420,
                ],
                [
                    'nama_berkas'    => 'BAP_Kasus_' . sprintf('%02d', $k) . '.pdf',
                    'jenis_dokumen'  => 'Pemeriksaan',
                    'klasifikasi'    => 'sangat_terbatas',
                    'tanggal_unggah' => $tglPeriksa,
                    'ukuran_kb'      => 1180,
                ],
                [
                    'nama_berkas'    => 'ba_kasus_' . $k . '.pdf',
                    'jenis_dokumen'  => 'Penetapan',
                    'klasifikasi'    => 'sangat_terbatas',
                    'tanggal_unggah' => $tglPutus,
                    'ukuran_kb'      => 740,
                ],
            ];

            $riwayatValidasi = [
                [
                    'tanggal' => $tglPutus,
                    'waktu'   => $tglPutus . ' 10:00',
                    'pelaku'  => 'Validator Unit',
                    'aksi'    => 'Menyetujui',
                    'status'  => 'tervalidasi',
                    'catatan' => 'Dokumen pemeriksaan dan penetapan sesuai ketentuan.',
                ],
                [
                    'tanggal' => $tglPeriksa,
                    'waktu'   => $tglPeriksa . ' 14:30',
                    'pelaku'  => 'PIC Ketarunaan',
                    'aksi'    => 'Mengajukan',
                    'status'  => 'diajukan',
                    'catatan' => 'Pemeriksaan dan penetapan dicatat.',
                ],
                [
                    'tanggal' => $tglKejadian,
                    'waktu'   => $tglKejadian . ' 09:00',
                    'pelaku'  => 'PIC Ketarunaan',
                    'aksi'    => 'Menyimpan Draf',
                    'status'  => 'draf',
                    'catatan' => 'Laporan awal dicatat.',
                ],
            ];

            $db->table('taruna_pelanggaran')->insert([
                'nomor_perkara'         => $noPerkara,
                'nomor_taruna'          => $tarunaRow['nomor_taruna'],
                'tanggal_kejadian'      => $tglKejadian . ' 06:30:00',
                'lokasi'                => 'Kampus I PKTJ Tegal',
                'uraian'                => 'Keterlambatan apel pagi pembinaan karakter taruna.',
                'jenis_pelanggaran'     => ($k % 3 === 0) ? 'Tata Kehidupan' : 'Kedisiplinan',
                'tingkat_pelanggaran'   => ($k % 4 === 0) ? 'sedang' : 'ringan',
                'pelapor'               => 'Pusbangkar',
                'unit_pemilik'          => 'Ketarunaan',
                'sumber_data'           => 'Laporan petugas',
                'tingkat_kerahasiaan'   => 'terbatas',
                'tanggal_pemeriksaan'   => $tglPeriksa,
                'pemeriksa'             => 'Tim Pemeriksa Unit',
                'dasar_peraturan'       => 'Pedoman Tata Tertib Taruna PKTJ',
                'ringkasan_klarifikasi' => 'Taruna memberikan keterangan klarifikasi dan mengakui keterlambatan saat apel.',
                'hasil_pemeriksaan'     => 'Terbukti melanggar ketentuan tata tertib apel pagi.',
                'berkas_bap'            => 'BAP_Kasus_' . sprintf('%02d', $k) . '.pdf',
                'status_penetapan'      => 'terbukti',
                'status_penyelesaian'   => $isSelesai ? 'selesai' : 'proses',
                'sanksi'                => 'Pembinaan fisik terukur dan apel tambahan selama 3 hari.',
                'jenis_sanksi'          => 'Pembinaan',
                'nomor_keputusan'       => $noSk,
                'tanggal_keputusan'     => $tglPutus,
                'tindak_lanjut'         => 'Pembinaan sesuai ketentuan yang berlaku dan pemantauan perkembangan oleh Unit Ketarunaan.',
                'tanggal_selesai'       => $isSelesai ? '2026-10-' . sprintf('%02d', ($k % 15 + 1)) : null,
                'pic_tindak_lanjut'     => 'Unit Ketarunaan',
                'status_pemantauan'     => $isSelesai ? 'selesai' : 'proses',
                'catatan_pemantauan'    => $isSelesai ? 'Pembinaan telah diselesaikan dengan baik.' : 'Dalam proses pelaksanaan pembinaan.',
                'bukti_berkas'          => 'ba_kasus_' . $k . '.pdf',
                'dokumen_bukti_json'    => json_encode($dokJson, JSON_UNESCAPED_UNICODE),
                'status_validasi'       => 'tervalidasi',
                'diajukan_oleh'         => 'PIC Ketarunaan',
                'divalidasi_oleh'       => 'Validator Unit',
                'tanggal_validasi'      => $tglPutus,
                'riwayat_validasi_json' => json_encode($riwayatValidasi, JSON_UNESCAPED_UNICODE),
                'created_at'            => $now,
                'updated_at'            => $now,
            ]);
            $pelId = $db->insertID();

            if ($hasPihakTable) {
                $db->table('taruna_pelanggaran_pihak')->insert([
                    'pelanggaran_id' => $pelId,
                    'nomor_taruna'   => $tarunaRow['nomor_taruna'],
                    'nama_pihak'     => $tarunaRow['nama_lengkap'],
                    'jenis_pihak'    => 'taruna',
                    'peran'          => 'Terlapor',
                    'keterangan'     => 'Taruna bersangkutan pada laporan kasus.',
                    'created_at'     => $now,
                    'updated_at'     => $now,
                ]);
                if ($k <= 3) {
                    $db->table('taruna_pelanggaran_pihak')->insert([
                        'pelanggaran_id' => $pelId,
                        'nomor_taruna'   => null,
                        'nama_pihak'     => 'Petugas Piket Pusbangkar',
                        'jenis_pihak'    => 'eksternal',
                        'peran'          => 'Pelapor / Saksi',
                        'keterangan'     => 'Petugas piket pencatat laporan lapangan.',
                        'created_at'     => $now,
                        'updated_at'     => $now,
                    ]);
                }
            }

            if ($hasTindakTable) {
                $db->table('taruna_pelanggaran_tindak_lanjut')->insert([
                    'pelanggaran_id'              => $pelId,
                    'tanggal_tindakan'            => $tglPutus,
                    'jenis_tindakan'              => 'penetapan',
                    'status_penetapan_saat_itu'   => 'terbukti',
                    'status_penyelesaian_saat_itu'=> 'proses',
                    'nomor_dasar'                 => $noSk,
                    'sanksi_tindakan'             => 'Pembinaan fisik terukur dan apel tambahan selama 3 hari.',
                    'pic'                         => 'Unit Ketarunaan',
                    'target_selesai'              => null,
                    'berkas_pendukung'            => 'ba_kasus_' . $k . '.pdf',
                    'dicatat_oleh'                => 'Tim Pusbangkar',
                    'created_at'                  => $now,
                    'updated_at'                  => $now,
                ]);
                if ($isSelesai) {
                    $db->table('taruna_pelanggaran_tindak_lanjut')->insert([
                        'pelanggaran_id'              => $pelId,
                        'tanggal_tindakan'            => '2026-10-' . sprintf('%02d', ($k % 15 + 1)),
                        'jenis_tindakan'              => 'selesai',
                        'status_penetapan_saat_itu'   => 'terbukti',
                        'status_penyelesaian_saat_itu'=> 'selesai',
                        'nomor_dasar'                 => $noSk,
                        'sanksi_tindakan'             => 'Pembinaan selesai dilaksanakan sesuai laporan pemantauan.',
                        'pic'                         => 'Unit Ketarunaan',
                        'target_selesai'              => '2026-10-' . sprintf('%02d', ($k % 15 + 1)),
                        'berkas_pendukung'            => null,
                        'dicatat_oleh'                => 'Unit Ketarunaan',
                        'created_at'                  => $now,
                        'updated_at'                  => $now,
                    ]);
                }
            }
        }

        // Tambah 4 kasus dugaan (3 diajukan agar antrean Dashboard tetap 19, dan 1 draf agar total dugaan = 4)
        for ($d = 13; $d <= 16; $d++) {
            $statusValidDugaan = ($d <= 15) ? 'diajukan' : 'draf';
            $tglKejDugaan      = ($d === 16) ? '2026-10-29' : '2026-10-28';
            $tarunaRow         = $tarunaBatch[$d];

            $db->table('taruna_pelanggaran')->insert([
                'nomor_perkara'         => 'PKR/2026/' . sprintf('%04d', $d),
                'nomor_taruna'          => $tarunaRow['nomor_taruna'],
                'tanggal_kejadian'      => $tglKejDugaan . ' 19:30:00',
                'lokasi'                => 'Asrama Taruna',
                'uraian'                => 'Laporan awal memerlukan klarifikasi dan pemeriksaan.',
                'jenis_pelanggaran'     => 'Tata Kehidupan',
                'tingkat_pelanggaran'   => 'belum_ditetapkan',
                'pelapor'               => 'Petugas Jaga Pusbangkar',
                'unit_pemilik'          => 'Ketarunaan',
                'sumber_data'           => 'Laporan petugas',
                'tingkat_kerahasiaan'   => 'terbatas',
                'tanggal_pemeriksaan'   => '2026-10-29',
                'pemeriksa'             => 'Tim Pemeriksa Unit',
                'dasar_peraturan'       => 'Pedoman Tata Tertib Taruna PKTJ',
                'ringkasan_klarifikasi' => 'Keterangan pihak terkait dan bukti sedang diperiksa.',
                'hasil_pemeriksaan'     => 'Belum ada penetapan akhir.',
                'berkas_bap'            => 'BAP_Dugaan_' . $d . '.pdf',
                'status_penetapan'      => 'dugaan',
                'status_penyelesaian'   => 'proses',
                'sanksi'                => null,
                'jenis_sanksi'          => null,
                'nomor_keputusan'       => null,
                'tanggal_keputusan'     => null,
                'tindak_lanjut'         => null,
                'pic_tindak_lanjut'     => 'Unit Ketarunaan',
                'status_pemantauan'     => 'proses',
                'bukti_berkas'          => 'laporan_dugaan_' . $d . '.pdf',
                'dokumen_bukti_json'    => json_encode([
                    ['nama_berkas' => 'laporan_dugaan_' . $d . '.pdf', 'jenis_dokumen' => 'Laporan', 'klasifikasi' => 'terbatas', 'tanggal_unggah' => $tglKejDugaan, 'ukuran_kb' => 380],
                    ['nama_berkas' => 'BAP_Dugaan_' . $d . '.pdf', 'jenis_dokumen' => 'Pemeriksaan', 'klasifikasi' => 'sangat_terbatas', 'tanggal_unggah' => '2026-10-29', 'ukuran_kb' => 1220],
                ], JSON_UNESCAPED_UNICODE),
                'status_validasi'       => $statusValidDugaan,
                'diajukan_oleh'         => 'PIC Ketarunaan',
                'riwayat_validasi_json' => json_encode([
                    ['tanggal' => '2026-10-29', 'waktu' => '2026-10-29 10:00', 'pelaku' => 'PIC Ketarunaan', 'aksi' => ($statusValidDugaan === 'diajukan' ? 'Mengajukan' : 'Menyimpan Draf'), 'status' => $statusValidDugaan, 'catatan' => 'Pencatatan awal dugaan pelanggaran.'],
                ], JSON_UNESCAPED_UNICODE),
                'created_at'            => $now,
                'updated_at'            => $now,
            ]);
        }

        // Tambah 1 kasus Tidak Terbukti & 1 kasus Dibatalkan (tervalidasi) sebagai bukti nyata UAT-07 (tidak masuk 12 Terbukti)
        $kasusNonTerbukti = [
            [
                'nomor_perkara'     => 'PKR/2026/0017',
                'taruna'            => $tarunaBatch[17],
                'tanggal'           => '2026-10-24',
                'kategori'          => 'Kedisiplinan',
                'status_penetapan'  => 'tidak_terbukti',
                'uraian'            => 'Dugaan keterlambatan apel setelah diperiksa terbukti memiliki surat tugas praktikum.',
                'hasil'             => 'Tidak terbukti melakukan pelanggaran; izin kegiatan praktikum sah.',
                'tindak_lanjut'     => 'Kasus ditutup dan nama baik taruna dipulihkan.',
                'no_sk'             => 'BA.KLARIF/2026/0017',
            ],
            [
                'nomor_perkara'     => 'PKR/2026/0018',
                'taruna'            => $tarunaBatch[18],
                'tanggal'           => '2026-10-25',
                'kategori'          => 'Akademik',
                'status_penetapan'  => 'dibatalkan',
                'uraian'            => 'Laporan kesalahan input absensi ujian laboratorium dibatalkan setelah verifikasi presensi.',
                'hasil'             => 'Laporan dibatalkan karena kekeliruan pencatatan awal.',
                'tindak_lanjut'     => 'Pencatatan dibatalkan dan diarsipkan.',
                'no_sk'             => 'BA.BATAL/2026/0018',
            ],
        ];
        foreach ($kasusNonTerbukti as $non) {
            $db->table('taruna_pelanggaran')->insert([
                'nomor_perkara'         => $non['nomor_perkara'],
                'nomor_taruna'          => $non['taruna']['nomor_taruna'],
                'tanggal_kejadian'      => $non['tanggal'] . ' 08:00:00',
                'lokasi'                => 'Kampus I PKTJ Tegal',
                'uraian'                => $non['uraian'],
                'jenis_pelanggaran'     => $non['kategori'],
                'tingkat_pelanggaran'   => 'belum_ditetapkan',
                'pelapor'               => 'Unit Ketarunaan',
                'unit_pemilik'          => 'Ketarunaan',
                'sumber_data'           => 'Laporan petugas',
                'tingkat_kerahasiaan'   => 'terbatas',
                'tanggal_pemeriksaan'   => $non['tanggal'],
                'pemeriksa'             => 'Tim Pemeriksa Unit',
                'dasar_peraturan'       => 'Pedoman Tata Tertib Taruna PKTJ',
                'ringkasan_klarifikasi' => $non['uraian'],
                'hasil_pemeriksaan'     => $non['hasil'],
                'status_penetapan'      => $non['status_penetapan'],
                'status_penyelesaian'   => 'selesai',
                'sanksi'                => 'Tidak ada sanksi.',
                'jenis_sanksi'          => 'Tidak Ada Sanksi / Dibebaskan',
                'nomor_keputusan'       => $non['no_sk'],
                'tanggal_keputusan'     => $non['tanggal'],
                'tindak_lanjut'         => $non['tindak_lanjut'],
                'tanggal_selesai'       => $non['tanggal'],
                'pic_tindak_lanjut'     => 'Unit Ketarunaan',
                'status_pemantauan'     => 'selesai',
                'bukti_berkas'          => 'klarifikasi_' . strtolower(str_replace('/', '_', $non['nomor_perkara'])) . '.pdf',
                'dokumen_bukti_json'    => json_encode([
                    ['nama_berkas' => 'klarifikasi_' . strtolower(str_replace('/', '_', $non['nomor_perkara'])) . '.pdf', 'jenis_dokumen' => 'Penetapan', 'klasifikasi' => 'terbatas', 'tanggal_unggah' => $non['tanggal'], 'ukuran_kb' => 310],
                ], JSON_UNESCAPED_UNICODE),
                'status_validasi'       => 'tervalidasi',
                'diajukan_oleh'         => 'PIC Ketarunaan',
                'divalidasi_oleh'       => 'Validator Unit',
                'tanggal_validasi'      => $non['tanggal'],
                'riwayat_validasi_json' => json_encode([
                    ['tanggal' => $non['tanggal'], 'waktu' => $non['tanggal'] . ' 11:00', 'pelaku' => 'Validator Unit', 'aksi' => 'Menyetujui', 'status' => 'tervalidasi', 'catatan' => 'Hasil klarifikasi sah.'],
                ], JSON_UNESCAPED_UNICODE),
                'created_at'            => $now,
                'updated_at'            => $now,
            ]);
        }

        // 10. Beasiswa (4 Program 4 Jenis: 3 berjalan + 1 seleksi, 45 Penerima Ditetapkan Tervalidasi, Nilai IP, Pemeringkatan 2 Kelas, Referensi 3TP)
        $periodeNilaiId = $taMap['2025/2026 Genap'] ?? $tahunAkademikId;

        if ($db->tableExists('ref_wilayah_3tp')) {
            $wilayah3tpBatch = [
                ['kode_wilayah' => '12.14', 'provinsi' => 'Sumatera Utara',        'kabupaten_kota' => 'Kab. Nias Selatan',        'kategori_3tp' => 'Daerah Tertinggal', 'dasar_acuan' => 'Perpres No. 63 Tahun 2020', 'versi_acuan' => '2020-2024', 'tahun_berlaku' => 2024, 'is_aktif' => 1, 'created_at' => $now],
                ['kode_wilayah' => '13.09', 'provinsi' => 'Sumatera Barat',        'kabupaten_kota' => 'Kab. Kepulauan Mentawai',  'kategori_3tp' => 'Daerah Tertinggal', 'dasar_acuan' => 'Perpres No. 63 Tahun 2020', 'versi_acuan' => '2020-2024', 'tahun_berlaku' => 2024, 'is_aktif' => 1, 'created_at' => $now],
                ['kode_wilayah' => '52.08', 'provinsi' => 'Nusa Tenggara Barat',   'kabupaten_kota' => 'Kab. Lombok Utara',        'kategori_3tp' => 'Daerah Tertinggal', 'dasar_acuan' => 'Perpres No. 63 Tahun 2020', 'versi_acuan' => '2020-2024', 'tahun_berlaku' => 2024, 'is_aktif' => 1, 'created_at' => $now],
                ['kode_wilayah' => '53.14', 'provinsi' => 'Nusa Tenggara Timur',   'kabupaten_kota' => 'Kab. Rote Ndao',           'kategori_3tp' => 'Perbatasan & Terluar', 'dasar_acuan' => 'Perpres No. 63 Tahun 2020', 'versi_acuan' => '2020-2024', 'tahun_berlaku' => 2024, 'is_aktif' => 1, 'created_at' => $now],
                ['kode_wilayah' => '53.17', 'provinsi' => 'Nusa Tenggara Timur',   'kabupaten_kota' => 'Kab. Sumba Tengah',        'kategori_3tp' => 'Daerah Tertinggal', 'dasar_acuan' => 'Perpres No. 63 Tahun 2020', 'versi_acuan' => '2020-2024', 'tahun_berlaku' => 2024, 'is_aktif' => 1, 'created_at' => $now],
                ['kode_wilayah' => '53.20', 'provinsi' => 'Nusa Tenggara Timur',   'kabupaten_kota' => 'Kab. Sabu Raijua',         'kategori_3tp' => 'Daerah Tertinggal', 'dasar_acuan' => 'Perpres No. 63 Tahun 2020', 'versi_acuan' => '2020-2024', 'tahun_berlaku' => 2024, 'is_aktif' => 1, 'created_at' => $now],
                ['kode_wilayah' => '81.08', 'provinsi' => 'Maluku',                'kabupaten_kota' => 'Kab. Maluku Barat Daya',   'kategori_3tp' => 'Perbatasan & Terluar', 'dasar_acuan' => 'Perpres No. 63 Tahun 2020', 'versi_acuan' => '2020-2024', 'tahun_berlaku' => 2024, 'is_aktif' => 1, 'created_at' => $now],
                ['kode_wilayah' => '92.07', 'provinsi' => 'Papua Barat',           'kabupaten_kota' => 'Kab. Teluk Wondama',       'kategori_3tp' => 'Daerah Tertinggal', 'dasar_acuan' => 'Perpres No. 63 Tahun 2020', 'versi_acuan' => '2020-2024', 'tahun_berlaku' => 2024, 'is_aktif' => 1, 'created_at' => $now],
            ];
            $db->table('ref_wilayah_3tp')->insertBatch($wilayah3tpBatch);
        }

        $programs = [
            [
                'nama_program'     => 'Beasiswa Apresiasi IP Tertinggi 2026',
                'jenis'            => 'apresiasi',
                'pemberi'          => 'PKTJ Tegal',
                'kuota'            => 20,
                'nominal'          => 2500000,
                'status_program'   => 'berjalan',
                'persyaratan'      => '5 IP semester tertinggi pada masing-masing kelas periode penilaian sebelumnya.',
                'dasar_penilaian'  => 'ip_semester',
                'periode_nilai_id' => $periodeNilaiId,
                'basis_kelas'      => 'kelas_periode_penilaian',
                'jumlah_peringkat' => 5,
                'aturan_seri'      => 'belum_ditetapkan',
            ],
            [
                'nama_program'     => 'Beasiswa Prestasi Keselamatan Transportasi',
                'jenis'            => 'prestasi',
                'pemberi'          => 'Badan Pengembangan SDM Perhubungan',
                'kuota'            => 15,
                'nominal'          => 3000000,
                'status_program'   => 'berjalan',
                'persyaratan'      => 'Prestasi akademik/nonakademik tervalidasi selama menjadi taruna disertai sertifikat dan dokumentasi.',
                'dasar_penilaian'  => 'klaim_prestasi',
                'periode_nilai_id' => null,
                'basis_kelas'      => 'individu',
                'jumlah_peringkat' => 0,
                'aturan_seri'      => 'belum_ditetapkan',
            ],
            [
                'nama_program'     => 'Bantuan Pendidikan Daerah 3TP',
                'jenis'            => 'daerah_3tp',
                'pemberi'          => 'Kementerian Perhubungan RI',
                'kuota'            => 15,
                'nominal'          => 3500000,
                'status_program'   => 'berjalan',
                'persyaratan'      => 'Berdomisili di wilayah 3TP sesuai daftar acuan resmi dan melampirkan bukti domisili sah.',
                'dasar_penilaian'  => 'klaim_domisili_3tp',
                'periode_nilai_id' => null,
                'basis_kelas'      => 'individu',
                'jumlah_peringkat' => 0,
                'aturan_seri'      => 'belum_ditetapkan',
            ],
            [
                'nama_program'     => 'Beasiswa Bantuan Biaya Pendidikan Tidak Mampu',
                'jenis'            => 'tidak_mampu',
                'pemberi'          => 'PKTJ Tegal & Mitra Industri',
                'kuota'            => 10,
                'nominal'          => 2750000,
                'status_program'   => 'seleksi',
                'persyaratan'      => 'Keterangan kondisi ekonomi keluarga (SKTM/KIP/penghasilan wali) dengan akses berkas terbatas.',
                'dasar_penilaian'  => 'klaim_ekonomi',
                'periode_nilai_id' => null,
                'basis_kelas'      => 'individu',
                'jumlah_peringkat' => 0,
                'aturan_seri'      => 'belum_ditetapkan',
            ],
        ];

        $progIds = [];
        foreach ($programs as $prog) {
            $db->table('taruna_program_beasiswa')->insert([
                'nama_program'     => $prog['nama_program'],
                'jenis_beasiswa'   => $prog['jenis'],
                'pemberi'          => $prog['pemberi'],
                'tahun'            => 2026,
                'tahun_akademik_id'=> $tahunAkademikId,
                'periode_mulai'    => '2026-09-01',
                'periode_selesai'  => '2026-12-31',
                'kuota'            => $prog['kuota'],
                'nominal'          => $prog['nominal'],
                'persyaratan'      => $prog['persyaratan'],
                'penanggung_jawab' => 'Unit Ketarunaan',
                'dasar_penilaian'  => $prog['dasar_penilaian'],
                'periode_nilai_id' => $prog['periode_nilai_id'],
                'basis_kelas'      => $prog['basis_kelas'],
                'jumlah_peringkat' => $prog['jumlah_peringkat'],
                'aturan_seri'      => $prog['aturan_seri'],
                'status_program'   => $prog['status_program'],
                'created_at'       => $now,
                'updated_at'       => $now,
            ]);
            $progIds[] = $db->insertID();
        }

        // 10a. Nilai IP Semester (Genap 2025/2026) untuk 2 kelas RSTJ: Kelas A (30 taruna, tanpa seri) & Kelas B (30 taruna, seri peringkat ke-5 untuk UAT-09 & UAT-10)
        $pemeringkatanAId = null;
        if ($db->tableExists('taruna_nilai_ip')) {
            $rstjProdiId = $prodiMap['RSTJ'];
            $kelasRstjA  = $kelasMap[$rstjProdiId . '_A'];
            $kelasRstjB  = $kelasMap[$rstjProdiId . '_B'];

            // RSTJ Kelas A: index 0..29 (counter 1..30)
            $ipTopA = [3.95, 3.92, 3.89, 3.85, 3.82, 3.78];
            $nilaiIpBatch = [];
            for ($idxA = 0; $idxA < 30; $idxA++) {
                $ipVal = $ipTopA[$idxA] ?? round(3.74 - (($idxA - 6) * 0.03), 2);
                $nilaiIpBatch[] = [
                    'nomor_taruna'      => $tarunaBatch[$idxA]['nomor_taruna'],
                    'tahun_akademik_id' => $periodeNilaiId,
                    'program_studi_id'  => $rstjProdiId,
                    'kelas_id'          => $kelasRstjA,
                    'tingkat'           => 2,
                    'semester'          => 4,
                    'ip_semester'       => $ipVal,
                    'sks_lulus'         => 22,
                    'sumber_data'       => 'Nilai semester tervalidasi',
                    'versi'             => 1,
                    'status_validasi'   => 'tervalidasi',
                    'diimpor_oleh'      => 'Unit Akademik',
                    'created_at'        => $now,
                    'updated_at'        => $now,
                ];
            }

            // RSTJ Kelas B: index 30..59 (counter 31..60) -> peringkat 5 & 6 sama-sama 3.80 (Seri Peringkat Ke-5)
            $ipTopB = [3.91, 3.88, 3.84, 3.83, 3.80, 3.80, 3.75];
            for ($idxB = 0; $idxB < 30; $idxB++) {
                $ipVal = $ipTopB[$idxB] ?? round(3.72 - (($idxB - 7) * 0.03), 2);
                $nilaiIpBatch[] = [
                    'nomor_taruna'      => $tarunaBatch[30 + $idxB]['nomor_taruna'],
                    'tahun_akademik_id' => $periodeNilaiId,
                    'program_studi_id'  => $rstjProdiId,
                    'kelas_id'          => $kelasRstjB,
                    'tingkat'           => 2,
                    'semester'          => 4,
                    'ip_semester'       => $ipVal,
                    'sks_lulus'         => 22,
                    'sumber_data'       => 'Nilai semester tervalidasi',
                    'versi'             => 1,
                    'status_validasi'   => 'tervalidasi',
                    'diimpor_oleh'      => 'Unit Akademik',
                    'created_at'        => $now,
                    'updated_at'        => $now,
                ];
            }
            $db->table('taruna_nilai_ip')->insertBatch($nilaiIpBatch);

            if ($db->tableExists('taruna_beasiswa_pemeringkatan') && $db->tableExists('taruna_beasiswa_hasil_peringkat')) {
                // Versi pemeringkatan Kelas A (siap / tervalidasi)
                $db->table('taruna_beasiswa_pemeringkatan')->insert([
                    'program_beasiswa_id'   => $progIds[0],
                    'tahun_akademik_id'     => $periodeNilaiId,
                    'program_studi_id'      => $rstjProdiId,
                    'kelas_id'              => $kelasRstjA,
                    'kode_versi'            => 'APR-2026-RSTJ-A-V1',
                    'versi'                 => 1,
                    'populasi_kelas'        => 30,
                    'jumlah_kandidat'       => 5,
                    'sumber_nilai'          => 'Nilai semester tervalidasi',
                    'parameter_aturan_json' => json_encode(['kuota_per_kelas' => 5, 'dasar' => 'ip_semester'], JSON_UNESCAPED_UNICODE),
                    'ada_seri_kelima'       => 0,
                    'aturan_seri'           => 'belum_ditetapkan',
                    'status_finalisasi'     => 'tervalidasi',
                    'diproses_oleh'         => 'PIC Ketarunaan',
                    'tanggal_hitung'        => '2026-09-10',
                    'divalidasi_oleh'       => 'Validator Unit',
                    'tanggal_validasi'      => '2026-09-12',
                    'created_at'            => $now,
                    'updated_at'            => $now,
                ]);
                $pemeringkatanAId = $db->insertID();

                $hasilBatch = [];
                for ($idxA = 0; $idxA < 30; $idxA++) {
                    $ipVal = $ipTopA[$idxA] ?? round(3.74 - (($idxA - 6) * 0.03), 2);
                    $rank  = $idxA + 1;
                    $hasilBatch[] = [
                        'pemeringkatan_id' => $pemeringkatanAId,
                        'nomor_taruna'     => $tarunaBatch[$idxA]['nomor_taruna'],
                        'nama_snapshot'    => $tarunaBatch[$idxA]['nama_lengkap'],
                        'kelas_snapshot'   => 'RSTJ Kelas A',
                        'ip_semester'      => $ipVal,
                        'peringkat'        => $rank,
                        'status_kandidat'  => ($rank <= 5) ? 'kandidat' : 'di_luar_5_besar',
                        'is_seri'          => 0,
                        'catatan'          => ($rank <= 5) ? 'Masuk 5 IP tertinggi kelas A' : null,
                        'created_at'       => $now,
                    ];
                }

                // Versi pemeringkatan Kelas B (ditahan karena seri peringkat ke-5 — UAT-10)
                $db->table('taruna_beasiswa_pemeringkatan')->insert([
                    'program_beasiswa_id'   => $progIds[0],
                    'tahun_akademik_id'     => $periodeNilaiId,
                    'program_studi_id'      => $rstjProdiId,
                    'kelas_id'              => $kelasRstjB,
                    'kode_versi'            => 'APR-2026-RSTJ-B-V1',
                    'versi'                 => 1,
                    'populasi_kelas'        => 30,
                    'jumlah_kandidat'       => 4,
                    'sumber_nilai'          => 'Nilai semester tervalidasi',
                    'parameter_aturan_json' => json_encode(['kuota_per_kelas' => 5, 'dasar' => 'ip_semester'], JSON_UNESCAPED_UNICODE),
                    'ada_seri_kelima'       => 1,
                    'aturan_seri'           => 'belum_ditetapkan',
                    'status_finalisasi'     => 'ditahan_seri',
                    'alasan_ditahan'        => 'IP sama (3,80) pada batas peringkat ke-5 memerlukan aturan pemecah seri sebelum finalisasi.',
                    'diproses_oleh'         => 'PIC Ketarunaan',
                    'tanggal_hitung'        => '2026-09-10',
                    'created_at'            => $now,
                    'updated_at'            => $now,
                ]);
                $pemeringkatanBId = $db->insertID();

                for ($idxB = 0; $idxB < 30; $idxB++) {
                    $ipVal = $ipTopB[$idxB] ?? round(3.72 - (($idxB - 7) * 0.03), 2);
                    $rank  = $idxB + 1;
                    $isSeriKelima = ($rank === 5 || $rank === 6);
                    $statusKand   = ($rank <= 4) ? 'kandidat' : ($isSeriKelima ? 'seri_kelima' : 'di_luar_5_besar');
                    $hasilBatch[] = [
                        'pemeringkatan_id' => $pemeringkatanBId,
                        'nomor_taruna'     => $tarunaBatch[30 + $idxB]['nomor_taruna'],
                        'nama_snapshot'    => $tarunaBatch[30 + $idxB]['nama_lengkap'],
                        'kelas_snapshot'   => 'RSTJ Kelas B',
                        'ip_semester'      => $ipVal,
                        'peringkat'        => $isSeriKelima ? 5 : $rank,
                        'status_kandidat'  => $statusKand,
                        'is_seri'          => $isSeriKelima ? 1 : 0,
                        'catatan'          => $isSeriKelima ? 'Seri IP pada batas peringkat ke-5' : null,
                        'created_at'       => $now,
                    ];
                }
                $db->table('taruna_beasiswa_hasil_peringkat')->insertBatch($hasilBatch);
            }
        }

        // Ambil 1 ID prestasi Aditya Pratama untuk ditautkan pada klaim Beasiswa Prestasi
        $adityaPrestasiRow = $db->table('taruna_prestasi tp')
            ->select('tp.id')
            ->join('taruna_peserta_prestasi tpp', 'tpp.prestasi_id = tp.id')
            ->where('tpp.nomor_taruna', '2401001')
            ->where('tp.status_validasi', 'tervalidasi')
            ->orderBy('tp.id', 'ASC')
            ->get()
            ->getRowArray();
        $adityaPrestasiId = $adityaPrestasiRow ? (int) $adityaPrestasiRow['id'] : null;

        // 10b. 45 Penerima Ditetapkan & Tervalidasi (15 per 3 program berjalan)
        $bCounter = 50;
        for ($progIdx = 0; $progIdx < 3; $progIdx++) {
            $pId       = $progIds[$progIdx];
            $jenisProg = $programs[$progIdx]['jenis'];
            for ($b = 0; $b < 15; $b++) {
                $isAdityaBeasiswa = ($progIdx === 1 && $b === 0);
                $targetTaruna     = $isAdityaBeasiswa ? '2401001' : $tarunaBatch[$bCounter]['nomor_taruna'];
                $tglPutus         = $isAdityaBeasiswa ? '2025-10-01' : '2026-09-15';
                $noKep            = $isAdityaBeasiswa ? 'Keputusan_Beasiswa.pdf' : ('SK.BEASISWA/2026/' . $pId . '/' . sprintf('%02d', $b + 1));

                $detailFields = [];
                if ($jenisProg === 'apresiasi') {
                    $detailFields = [
                        'sumber_klaim'    => 'pemeringkatan',
                        'pemeringkatan_id'=> $pemeringkatanAId,
                        'ip_semester'     => round(3.95 - ($b * 0.02), 2),
                        'peringkat_kelas' => ($b % 5) + 1,
                        'kelas_snapshot'  => 'RSTJ Kelas A',
                    ];
                } elseif ($jenisProg === 'prestasi') {
                    $detailFields = [
                        'sumber_klaim'           => $isAdityaBeasiswa ? 'taruna' : 'pic',
                        'prestasi_id'            => $isAdityaBeasiswa ? $adityaPrestasiId : null,
                        'kategori_prestasi'      => 'Akademik',
                        'nama_prestasi'          => $isAdityaBeasiswa ? 'Lomba Inovasi Transportasi Nasional' : 'Kompetisi Keselamatan Transportasi #' . ($b + 1),
                        'tingkat_prestasi'       => 'Nasional',
                        'peringkat_prestasi'     => 'Juara I',
                        'tanggal_prestasi'       => $isAdityaBeasiswa ? '2025-09-12' : '2026-08-18',
                        'penyelenggara_prestasi' => 'Kementerian Perhubungan',
                        'sertifikat_berkas'      => $isAdityaBeasiswa ? 'Piagam_Prestasi.pdf' : ('sertifikat_beasiswa_' . ($b + 1) . '.pdf'),
                        'dokumentasi_berkas'     => 'Dokumentasi_Kegiatan_Beasiswa.jpg',
                    ];
                } else {
                    $detailFields = [
                        'sumber_klaim'           => 'pic',
                        'alamat_domisili'        => 'Desa Contoh No. ' . ($b + 10),
                        'provinsi'               => 'Nusa Tenggara Timur',
                        'kabupaten_kota'         => 'Kab. Sumba Tengah',
                        'kecamatan'              => 'Katikutana',
                        'desa_kelurahan'         => 'Anakalang',
                        'kode_wilayah'           => '53.17',
                        'tanggal_acuan_domisili' => '2026-08-01',
                        'acuan_wilayah_3tp'      => 'Perpres No. 63 Tahun 2020',
                        'versi_acuan_3tp'        => '2020-2024',
                        'status_pemeriksaan_3tp' => 'memenuhi',
                        'dokumen_domisili'       => 'SK_Domisili_3TP_' . ($b + 1) . '.pdf',
                        'jenis_dokumen_domisili' => 'Kartu Keluarga / Surat Domisili',
                    ];
                }

                $riwayatProses = [
                    ['tanggal' => '2026-09-10', 'waktu' => '2026-09-10 09:00', 'pelaku' => 'PIC Ketarunaan', 'aksi' => 'Mengajukan', 'status' => 'diajukan', 'catatan' => 'Berkas pengajuan dikirim untuk pemeriksaan.'],
                    ['tanggal' => '2026-09-12', 'waktu' => '2026-09-12 10:30', 'pelaku' => 'Tim Verifikasi', 'aksi' => 'Memverifikasi', 'status' => 'memenuhi', 'catatan' => 'Memenuhi persyaratan program.'],
                    ['tanggal' => $tglPutus, 'waktu' => $tglPutus . ' 14:00', 'pelaku' => 'Direktur PKTJ', 'aksi' => 'Menetapkan & Memvalidasi', 'status' => 'tervalidasi', 'catatan' => 'Ditetapkan berdasarkan ' . $noKep],
                ];

                $db->table('taruna_penerima_beasiswa')->insert(array_merge([
                    'program_beasiswa_id'  => $pId,
                    'nomor_taruna'         => $targetTaruna,
                    'tahun_akademik_id'    => $tahunAkademikId,
                    'tanggal_pengajuan'    => '2026-09-10',
                    'dicatat_oleh'         => 'PIC Ketarunaan',
                    'status_pengajuan'     => 'diterima',
                    'hasil_verifikasi'     => 'memenuhi',
                    'checklist_verifikasi_json' => json_encode(['identitas' => true, 'kelayakan' => true, 'dokumen' => true]),
                    'catatan_pemeriksa'    => 'Berkas lengkap dan sesuai ketentuan program.',
                    'diperiksa_oleh'       => 'Tim Verifikasi Beasiswa',
                    'tanggal_pemeriksaan'  => '2026-09-12',
                    'status_penetapan'     => 'ditetapkan',
                    'tanggal_penetapan'    => $tglPutus,
                    'nomor_keputusan'      => $noKep,
                    'pejabat_penetap'      => 'Direktur PKTJ',
                    'dokumen_keputusan'    => $isAdityaBeasiswa ? 'Keputusan_Beasiswa.pdf' : ('SK_Penetapan_' . $pId . '_' . sprintf('%02d', $b + 1) . '.pdf'),
                    'catatan_penetapan'    => 'Penetapan penerima resmi periode 2026/2027 Ganjil.',
                    'status_validasi'      => 'tervalidasi',
                    'diajukan_oleh'        => 'PIC Ketarunaan',
                    'divalidasi_oleh'      => 'Validator Unit',
                    'tanggal_validasi'     => $tglPutus,
                    'riwayat_proses_json'  => json_encode($riwayatProses, JSON_UNESCAPED_UNICODE),
                    'created_at'           => $now,
                    'updated_at'           => $now,
                ], $detailFields));
                $bCounter++;
            }
        }

        // 10c. Antrean Tetap: 4 usulan 'diajukan' & 2 'perlu_perbaikan' (status_penetapan = 'kandidat', tidak masuk 45 ditetapkan)
        $antreanBeasiswaRows = [
            [
                'program_beasiswa_id' => $progIds[0],
                'nomor_taruna'        => $tarunaBatch[$bCounter + 1]['nomor_taruna'],
                'sumber_klaim'        => 'pemeringkatan',
                'pemeringkatan_id'    => $pemeringkatanAId,
                'ip_semester'         => 3.85,
                'peringkat_kelas'     => 4,
                'kelas_snapshot'      => 'RSTJ Kelas A',
                'status_pengajuan'    => 'diajukan',
                'hasil_verifikasi'    => 'belum_diperiksa',
                'status_penetapan'    => 'kandidat',
                'status_validasi'     => 'diajukan',
                'uraian_pengajuan'    => 'Kandidat 5 besar IP semester kelas A menunggu pemeriksaan berkas.',
            ],
            [
                // Memenuhi verifikasi namun belum ditetapkan -> uji UAT-14
                'program_beasiswa_id' => $progIds[0],
                'nomor_taruna'        => $tarunaBatch[$bCounter + 2]['nomor_taruna'],
                'sumber_klaim'        => 'pemeringkatan',
                'pemeringkatan_id'    => $pemeringkatanAId,
                'ip_semester'         => 3.82,
                'peringkat_kelas'     => 5,
                'kelas_snapshot'      => 'RSTJ Kelas A',
                'status_pengajuan'    => 'diverifikasi',
                'hasil_verifikasi'    => 'memenuhi',
                'catatan_pemeriksa'   => 'Memenuhi persyaratan pemeringkatan, menunggu keputusan penetapan resmi.',
                'diperiksa_oleh'      => 'Tim Verifikasi Beasiswa',
                'tanggal_pemeriksaan' => '2026-10-28',
                'status_penetapan'    => 'kandidat',
                'status_validasi'     => 'diajukan',
                'uraian_pengajuan'    => 'Kandidat lolos verifikasi menunggu SK penetapan.',
            ],
            [
                'program_beasiswa_id' => $progIds[1],
                'nomor_taruna'        => $tarunaBatch[$bCounter + 3]['nomor_taruna'],
                'sumber_klaim'        => 'taruna',
                'kategori_prestasi'   => 'Nonakademik',
                'nama_prestasi'       => 'Juara II Lomba Karya Keselamatan Jalan',
                'tingkat_prestasi'    => 'Nasional',
                'peringkat_prestasi'  => 'Juara II',
                'tanggal_prestasi'    => '2026-10-05',
                'penyelenggara_prestasi' => 'Dishub Jawa Tengah',
                'sertifikat_berkas'   => 'Sertifikat_Klaim_Prestasi.pdf',
                'dokumentasi_berkas'  => 'Foto_Klaim_Prestasi.jpg',
                'status_pengajuan'    => 'diajukan',
                'hasil_verifikasi'    => 'belum_diperiksa',
                'status_penetapan'    => 'kandidat',
                'status_validasi'     => 'diajukan',
                'uraian_pengajuan'    => 'Mengajukan klaim beasiswa prestasi berdasarkan sertifikat kejuaraan tingkat nasional.',
            ],
            [
                'program_beasiswa_id' => $progIds[2],
                'nomor_taruna'        => $tarunaBatch[$bCounter + 4]['nomor_taruna'],
                'sumber_klaim'        => 'pic',
                'alamat_domisili'     => 'Desa Makatete, Kec. Sumba Tengah',
                'provinsi'            => 'Nusa Tenggara Timur',
                'kabupaten_kota'      => 'Kab. Sumba Tengah',
                'kecamatan'           => 'Katikutana Selatan',
                'desa_kelurahan'      => 'Makatete',
                'kode_wilayah'        => '53.17',
                'tanggal_acuan_domisili' => '2026-09-20',
                'acuan_wilayah_3tp'   => 'Perpres No. 63 Tahun 2020',
                'versi_acuan_3tp'     => '2020-2024',
                'status_pemeriksaan_3tp' => 'menunggu_verifikasi',
                'dokumen_domisili'    => 'KK_Domisili_3TP.pdf',
                'jenis_dokumen_domisili' => 'Kartu Keluarga',
                'status_pengajuan'    => 'diajukan',
                'hasil_verifikasi'    => 'belum_diperiksa',
                'status_penetapan'    => 'kandidat',
                'status_validasi'     => 'diajukan',
                'uraian_pengajuan'    => 'Pengajuan klaim Daerah 3TP melalui PIC Ketarunaan.',
            ],
            [
                // Uji UAT-12: Klaim 3TP wilayah belum terverifikasi pada acuan resmi -> Perlu Perbaikan
                'program_beasiswa_id' => $progIds[2],
                'nomor_taruna'        => $tarunaBatch[$bCounter + 5]['nomor_taruna'],
                'sumber_klaim'        => 'taruna',
                'alamat_domisili'     => 'Jl. Pandanaran No. 14',
                'provinsi'            => 'Jawa Tengah',
                'kabupaten_kota'      => 'Kota Semarang',
                'kecamatan'           => 'Semarang Tengah',
                'desa_kelurahan'      => 'Mugassari',
                'kode_wilayah'        => '33.74',
                'tanggal_acuan_domisili' => '2026-10-01',
                'status_pemeriksaan_3tp' => 'acuan_belum_ada',
                'dokumen_domisili'    => 'Domisili_Belum_Acuan.pdf',
                'jenis_dokumen_domisili' => 'Surat Keterangan Domisili',
                'status_pengajuan'    => 'diajukan',
                'hasil_verifikasi'    => 'perlu_perbaikan',
                'catatan_pemeriksa'   => 'Wilayah domisili tidak tercantum pada daftar acuan wilayah 3TP aktif.',
                'catatan_validasi'    => 'Wilayah domisili belum tercantum pada daftar acuan resmi 3TP.',
                'diperiksa_oleh'      => 'Tim Verifikasi Beasiswa',
                'tanggal_pemeriksaan' => '2026-10-27',
                'status_penetapan'    => 'kandidat',
                'status_validasi'     => 'perlu_perbaikan',
                'uraian_pengajuan'    => 'Pengajuan wilayah belum terverifikasi sebagai daerah 3TP.',
            ],
            [
                // Uji UAT-13: Klaim Tidak Mampu dengan data ekonomi sensitif -> Perlu Perbaikan
                'program_beasiswa_id' => $progIds[3],
                'nomor_taruna'        => $tarunaBatch[$bCounter + 6]['nomor_taruna'],
                'sumber_klaim'        => 'pic',
                'pekerjaan_orang_tua' => 'Buruh Tani',
                'penghasilan_bulanan' => 1850000,
                'jumlah_tanggungan'   => 4,
                'uraian_pengajuan'    => 'Keluarga bekerja sebagai buruh tani dengan 4 tanggungan anak sekolah.',
                'dokumen_ekonomi_json' => json_encode([
                    ['jenis_dokumen' => 'SKTM Kelurahan', 'nomor_dokumen' => 'SKTM/412/2026', 'penerbit' => 'Kelurahan Mintaragen', 'tanggal_terbit' => '2026-10-02', 'nama_berkas' => 'SKTM_Ekonomi_01.pdf'],
                ], JSON_UNESCAPED_UNICODE),
                'status_pengajuan'    => 'diajukan',
                'hasil_verifikasi'    => 'perlu_perbaikan',
                'catatan_pemeriksa'   => 'Mohon lengkapi surat keterangan penghasilan orang tua/wali.',
                'catatan_validasi'    => 'Lampiran surat keterangan penghasilan orang tua perlu ditambahkan.',
                'diperiksa_oleh'      => 'Tim Verifikasi Beasiswa',
                'tanggal_pemeriksaan' => '2026-10-28',
                'status_penetapan'    => 'kandidat',
                'status_validasi'     => 'perlu_perbaikan',
            ],
        ];

        foreach ($antreanBeasiswaRows as $rowAntrean) {
            $stVal = $rowAntrean['status_validasi'];
            $db->table('taruna_penerima_beasiswa')->insert(array_merge([
                'tahun_akademik_id'   => $tahunAkademikId,
                'tanggal_pengajuan'   => '2026-10-25',
                'dicatat_oleh'        => ($rowAntrean['sumber_klaim'] === 'taruna') ? 'Taruna Pengaju' : 'PIC Ketarunaan',
                'diajukan_oleh'       => 'PIC Ketarunaan',
                'riwayat_proses_json' => json_encode([
                    ['tanggal' => '2026-10-25', 'waktu' => '2026-10-25 10:00', 'pelaku' => 'PIC Ketarunaan', 'aksi' => 'Mengajukan', 'status' => $stVal, 'catatan' => $rowAntrean['uraian_pengajuan'] ?? 'Pengajuan beasiswa.'],
                ], JSON_UNESCAPED_UNICODE),
                'created_at'          => $now,
                'updated_at'          => $now,
            ], $rowAntrean));
        }

        // Tambah 1 draf Tidak Mampu (memenuhi verifikasi, belum ditetapkan -> UAT-14) & 1 dibatalkan (tervalidasi) agar tidak menambah antrean maupun 45 ditetapkan
        $db->table('taruna_penerima_beasiswa')->insert([
            'program_beasiswa_id'  => $progIds[3],
            'nomor_taruna'         => $tarunaBatch[$bCounter + 7]['nomor_taruna'],
            'tahun_akademik_id'    => $tahunAkademikId,
            'sumber_klaim'         => 'taruna',
            'tanggal_pengajuan'    => '2026-10-26',
            'dicatat_oleh'         => 'Taruna Pengaju',
            'pekerjaan_orang_tua'  => 'Nelayan Kecil',
            'penghasilan_bulanan'  => 1500000,
            'jumlah_tanggungan'    => 3,
            'uraian_pengajuan'     => 'Kondisi ekonomi keluarga nelayan kecil penghasilan tidak tetap.',
            'dokumen_ekonomi_json' => json_encode([
                ['jenis_dokumen' => 'SKTM Desa', 'nomor_dokumen' => 'SKTM/88/2026', 'penerbit' => 'Kepala Desa Muarareja', 'tanggal_terbit' => '2026-10-08', 'nama_berkas' => 'SKTM_Nelayan_02.pdf'],
                ['jenis_dokumen' => 'Kartu Keluarga', 'nomor_dokumen' => '337601090012', 'penerbit' => 'Disdukcapil', 'tanggal_terbit' => '2025-05-12', 'nama_berkas' => 'KK_Ekonomi_02.pdf'],
            ], JSON_UNESCAPED_UNICODE),
            'status_pengajuan'     => 'diverifikasi',
            'hasil_verifikasi'     => 'memenuhi',
            'catatan_pemeriksa'    => 'Berkas ekonomi lengkap; belum ditetapkan sebagai penerima.',
            'diperiksa_oleh'       => 'Tim Verifikasi Beasiswa',
            'tanggal_pemeriksaan'  => '2026-10-28',
            'status_penetapan'     => 'kandidat',
            'status_validasi'      => 'draf',
            'riwayat_proses_json'  => json_encode([
                ['tanggal' => '2026-10-26', 'waktu' => '2026-10-26 11:00', 'pelaku' => 'PIC Ketarunaan', 'aksi' => 'Menyimpan Draf', 'status' => 'draf', 'catatan' => 'Klaim ekonomi memenuhi syarat, belum ditetapkan.'],
            ], JSON_UNESCAPED_UNICODE),
            'created_at'           => $now,
            'updated_at'           => $now,
        ]);

        $db->table('taruna_penerima_beasiswa')->insert([
            'program_beasiswa_id' => $progIds[1],
            'nomor_taruna'        => $tarunaBatch[$bCounter + 8]['nomor_taruna'],
            'tahun_akademik_id'   => $tahunAkademikId,
            'sumber_klaim'        => 'pic',
            'tanggal_pengajuan'   => '2026-09-05',
            'dicatat_oleh'        => 'PIC Ketarunaan',
            'nama_prestasi'       => 'Usulan Prestasi Dibatalkan',
            'status_pengajuan'    => 'ditolak',
            'hasil_verifikasi'    => 'tidak_memenuhi',
            'status_penetapan'    => 'dibatalkan',
            'tanggal_penetapan'   => '2026-09-18',
            'nomor_keputusan'     => 'SK.BATAL/2026/01',
            'pejabat_penetap'     => 'Direktur PKTJ',
            'status_validasi'     => 'tervalidasi',
            'diajukan_oleh'       => 'PIC Ketarunaan',
            'divalidasi_oleh'     => 'Validator Unit',
            'tanggal_validasi'    => '2026-09-18',
            'riwayat_proses_json' => json_encode([
                ['tanggal' => '2026-09-18', 'waktu' => '2026-09-18 15:00', 'pelaku' => 'Validator Unit', 'aksi' => 'Dibatalkan', 'status' => 'tervalidasi', 'catatan' => 'Keputusan dibatalkan karena peserta mengundurkan diri dari program.'],
            ], JSON_UNESCAPED_UNICODE),
            'created_at'          => $now,
            'updated_at'          => $now,
        ]);

        // 11. Perusahaan Mitra & Lowongan Aktif (8 Lowongan Terbuka Aktif pada 2026-10-30, 2 Segera Berakhir, + skenario UAT-15/16/LOW-04)
        $hasExtendedMitra = $db->fieldExists('kota', 'perusahaan_mitra');
        $hasExtendedLow   = $db->fieldExists('jenjang_pendidikan', 'lowongan_pekerjaan');
        $hasDokTable      = $db->tableExists('lowongan_dokumen');

        $companies = [
            [
                'nama'       => 'PT Kereta Api Indonesia (Persero)',
                'sektor'     => 'Transportasi Perkeretaapian',
                'kota'       => 'Bandung / Jakarta',
                'alamat'     => 'Jl. Perintis Kemerdekaan No. 1, Bandung',
                'web'        => 'https://kai.id',
                'pic'        => 'Rina Kusumawardhani',
                'jabatan'    => 'Manajer Rekrutmen & Seleksi',
                'email'      => 'rekrutmen@kai.id',
                'telepon'    => '022-4230031',
                'verified'   => 1,
                'sumber'     => 'Konfirmasi surat resmi & panggilan telepon HRD',
                'tgl_cek'    => '2026-10-12',
                'catatan'    => 'Kontak HRD dan domain surel resmi telah diverifikasi.',
            ],
            [
                'nama'       => 'PT Transportasi Jakarta (Transjakarta)',
                'sektor'     => 'Transportasi Perkotaan',
                'kota'       => 'DKI Jakarta',
                'alamat'     => 'Jl. Mayjen Sutoyo No. 1, Cawang, Jakarta Timur',
                'web'        => 'https://transjakarta.co.id',
                'pic'        => 'Hendro Wibowo',
                'jabatan'    => 'Kepala Bagian SDM',
                'email'      => 'karir@transjakarta.co.id',
                'telepon'    => '021-80879446',
                'verified'   => 1,
                'sumber'     => 'Portal karir resmi & MoU kampus',
                'tgl_cek'    => '2026-10-14',
                'catatan'    => 'Terdaftar sebagai mitra aktif penyelenggara rekrutmen kampus.',
            ],
            [
                'nama'       => 'Astra Daihatsu Motor',
                'sektor'     => 'Manufaktur Otomotif',
                'kota'       => 'Jakarta Utara',
                'alamat'     => 'Jl. Gaya Motor III No. 5, Sunter II, Jakarta Utara',
                'web'        => 'https://daihatsu.co.id',
                'pic'        => 'Dina Rahmawati',
                'jabatan'    => 'Talent Acquisition Specialist',
                'email'      => 'recruitment@daihatsu.astra.co.id',
                'telepon'    => '021-6510300',
                'verified'   => 1,
                'sumber'     => 'Surel korporat & surat permohonan campus hiring',
                'tgl_cek'    => '2026-10-15',
                'catatan'    => 'Kontak PIC aktif dan korespondensi melalui domain Astra.',
            ],
            [
                'nama'       => 'PT Bluebird Tbk',
                'sektor'     => 'Layanan Taksi & Armada',
                'kota'       => 'Jakarta Selatan',
                'alamat'     => 'Jl. Mampang Prapatan Raya No. 60, Jakarta Selatan',
                'web'        => 'https://bluebirdgroup.com',
                'pic'        => 'Fajar Prasetya',
                'jabatan'    => 'People Partner Fleet Operations',
                'email'      => 'talent@bluebirdgroup.com',
                'telepon'    => '021-7989000',
                'verified'   => 1,
                'sumber'     => 'Konfirmasi WhatsApp bisnis & laman karir perusahaan',
                'tgl_cek'    => '2026-10-16',
                'catatan'    => 'PIC rekrutmen telah dikonfirmasi.',
            ],
            [
                'nama'       => 'PT Hino Motors Manufacturing',
                'sektor'     => 'Kendaraan Niaga',
                'kota'       => 'Purwakarta',
                'alamat'     => 'Kawasan Industri Kota Bukit Indah Blok D1 No. 1, Purwakarta',
                'web'        => 'https://hino.co.id',
                'pic'        => 'Teguh Santoso',
                'jabatan'    => 'Supervisor HR Recruitment',
                'email'      => 'hr.recruitment@hino.co.id',
                'telepon'    => '0264-351911',
                'verified'   => 1,
                'sumber'     => 'Surat undangan seleksi teknisi & konfirmasi telepon',
                'tgl_cek'    => '2026-10-18',
                'catatan'    => 'Rekam jejak kerja sama magang & serapan lulusan aktif.',
            ],
            [
                'nama'       => 'PT Nusantara Logistik Armada',
                'sektor'     => 'Logistik & Manajemen Armada',
                'kota'       => 'Semarang',
                'alamat'     => 'Kawasan Industri Candi Blok B No. 8, Semarang',
                'web'        => 'https://nusantaralogistik.co.id',
                'pic'        => 'Bagus Permadi',
                'jabatan'    => 'Koordinator HR Operasional',
                'email'      => 'hr@nusantaralogistik.co.id',
                'telepon'    => '024-7628190',
                'verified'   => 0,
                'sumber'     => 'Pengajuan awal formulir mitra',
                'tgl_cek'    => null,
                'catatan'    => 'Menunggu konfirmasi panggilan dan pemeriksaan dokumen legalitas kontak.',
            ],
        ];

        $compIds = [];
        foreach ($companies as $comp) {
            $rowMitra = [
                'nama_perusahaan' => $comp['nama'],
                'sektor'          => $comp['sektor'],
                'alamat'          => $comp['alamat'],
                'website'         => $comp['web'],
                'kontak_person'   => $comp['pic'],
                'is_verified'     => $comp['verified'],
                'created_at'      => $now,
                'updated_at'      => $now,
            ];
            if ($hasExtendedMitra) {
                $rowMitra = array_merge($rowMitra, [
                    'kota'                       => $comp['kota'],
                    'pic_jabatan'                => $comp['jabatan'],
                    'email_rekrutmen'            => $comp['email'],
                    'telepon_rekrutmen'          => $comp['telepon'],
                    'sumber_verifikasi'          => $comp['sumber'],
                    'tanggal_pemeriksaan_kontak' => $comp['tgl_cek'],
                    'catatan_verifikasi'         => $comp['catatan'],
                    'diperiksa_oleh'             => $comp['verified'] ? 'Validator Karir PKTJ' : null,
                ]);
            }
            $db->table('perusahaan_mitra')->insert($rowMitra);
            $compIds[] = $db->insertID();
        }

        $defaultChecklist = json_encode([
            'kontak_mitra'      => true,
            'kesesuaian_syarat' => true,
            'kanal_dan_tenggat' => true,
            'dokumen_sumber'    => true,
        ], JSON_UNESCAPED_UNICODE);

        $vacancies = [
            // 1-8: Lowongan aktif terbuka (2 di antaranya <= 7 hari dari 2026-10-30)
            [
                'judul' => 'Transport Safety Officer', 'perusahaan_idx' => 0, 'jenis' => 'Tetap', 'jenjang' => 'D-IV',
                'lokasi' => 'Bandung / Jakarta', 'tenggat' => '2026-11-04 23:59:59', 'prodi' => ['RSTJ'],
                'kebutuhan' => 4, 'pengalaman' => 'Lulusan baru atau pengalaman 1 tahun di bidang keselamatan',
                'sertifikasi' => 'Sertifikat K3 Umum / Keselamatan Transportasi menjadi nilai tambah',
                'metode' => 'tautan_eksternal', 'email' => 'rekrutmen@kai.id',
                'sumber' => 'Surat Resmi Mitra No. 114/SDM-KAI/X/2026', 'tgl_terima' => '2026-10-18',
                'validasi' => 'tervalidasi', 'publikasi' => 'terbit', 'tgl_periksa' => '2026-10-19', 'tgl_terbit' => '2026-10-20',
                'catatan_pemeriksa' => 'Kelengkapan dokumen, program studi sasaran, dan tautan resmi terkonfirmasi.',
                'alasan_tutup' => null, 'tgl_tutup' => null, 'tutup_oleh' => null,
            ],
            [
                'judul' => 'Fleet Maintenance Supervisor', 'perusahaan_idx' => 1, 'jenis' => 'Tetap', 'jenjang' => 'D-III / D-IV',
                'lokasi' => 'DKI Jakarta', 'tenggat' => '2026-11-05 23:59:59', 'prodi' => ['TRO', 'TO'],
                'kebutuhan' => 3, 'pengalaman' => 'Lulusan baru atau minimal 1 tahun inspeksi armada niaga',
                'sertifikasi' => 'Sertifikat Penguji Kendaraan Bermotor diutamakan',
                'metode' => 'keduanya', 'email' => 'karir@transjakarta.co.id',
                'sumber' => 'Portal Resmi Karir Transjakarta', 'tgl_terima' => '2026-10-19',
                'validasi' => 'tervalidasi', 'publikasi' => 'terbit', 'tgl_periksa' => '2026-10-20', 'tgl_terbit' => '2026-10-21',
                'catatan_pemeriksa' => 'Seluruh butir pemeriksaan telah sesuai.',
                'alasan_tutup' => null, 'tgl_tutup' => null, 'tutup_oleh' => null,
            ],
            [
                'judul' => 'Traffic Management Analyst', 'perusahaan_idx' => 1, 'jenis' => 'Kontrak', 'jenjang' => 'D-IV',
                'lokasi' => 'DKI Jakarta', 'tenggat' => '2026-11-30 23:59:59', 'prodi' => ['RSTJ'],
                'kebutuhan' => 2, 'pengalaman' => 'Lulusan baru',
                'sertifikasi' => 'Kemampuan pemodelan lalu lintas dan analisis data rute',
                'metode' => 'tautan_eksternal', 'email' => 'karir@transjakarta.co.id',
                'sumber' => 'Surat Kerja Sama Transjakarta', 'tgl_terima' => '2026-10-20',
                'validasi' => 'tervalidasi', 'publikasi' => 'terbit', 'tgl_periksa' => '2026-10-21', 'tgl_terbit' => '2026-10-22',
                'catatan_pemeriksa' => 'Sesuai kompetensi lulusan RSTJ.',
                'alasan_tutup' => null, 'tgl_tutup' => null, 'tutup_oleh' => null,
            ],
            [
                'judul' => 'Automotive Quality Inspector', 'perusahaan_idx' => 2, 'jenis' => 'Tetap', 'jenjang' => 'D-III / D-IV',
                'lokasi' => 'Karawang / Jakarta Utara', 'tenggat' => '2026-12-15 23:59:59', 'prodi' => ['TRO', 'TO'],
                'kebutuhan' => 6, 'pengalaman' => 'Lulusan baru',
                'sertifikasi' => null,
                'metode' => 'tautan_eksternal', 'email' => 'recruitment@daihatsu.astra.co.id',
                'sumber' => 'Surat Permohonan Campus Hiring ADM', 'tgl_terima' => '2026-10-21',
                'validasi' => 'tervalidasi', 'publikasi' => 'terbit', 'tgl_periksa' => '2026-10-22', 'tgl_terbit' => '2026-10-23',
                'catatan_pemeriksa' => 'Memenuhi seluruh kriteria.',
                'alasan_tutup' => null, 'tgl_tutup' => null, 'tutup_oleh' => null,
            ],
            [
                'judul' => 'Vehicle Roadworthiness Engineer', 'perusahaan_idx' => 4, 'jenis' => 'Tetap', 'jenjang' => 'D-IV',
                'lokasi' => 'Purwakarta, Jawa Barat', 'tenggat' => '2026-12-20 23:59:59', 'prodi' => ['TRO'],
                'kebutuhan' => 3, 'pengalaman' => 'Pengalaman praktik bengkel kendaraan niaga',
                'sertifikasi' => 'Kompetensi Pengujian Kendaraan Bermotor',
                'metode' => 'keduanya', 'email' => 'hr.recruitment@hino.co.id',
                'sumber' => 'Undangan Seleksi Hino Campus Recruitment', 'tgl_terima' => '2026-10-22',
                'validasi' => 'tervalidasi', 'publikasi' => 'terbit', 'tgl_periksa' => '2026-10-23', 'tgl_terbit' => '2026-10-24',
                'catatan_pemeriksa' => 'Siap ditayangkan untuk prodi TRO.',
                'alasan_tutup' => null, 'tgl_tutup' => null, 'tutup_oleh' => null,
            ],
            [
                'judul' => 'Safety & Compliance Specialist', 'perusahaan_idx' => 3, 'jenis' => 'Tetap', 'jenjang' => 'D-IV',
                'lokasi' => 'Jakarta Selatan', 'tenggat' => '2026-12-31 23:59:59', 'prodi' => ['RSTJ'],
                'kebutuhan' => 2, 'pengalaman' => 'Lulusan baru atau 1 tahun audit armada',
                'sertifikasi' => 'Auditor SMKPAU (opsional)',
                'metode' => 'email', 'email' => 'talent@bluebirdgroup.com',
                'sumber' => 'Surel Resmi HRD Bluebird Group', 'tgl_terima' => '2026-10-23',
                'validasi' => 'tervalidasi', 'publikasi' => 'terbit', 'tgl_periksa' => '2026-10-24', 'tgl_terbit' => '2026-10-25',
                'catatan_pemeriksa' => 'Kanal lamaran Alamat Email aktif dan terverifikasi.',
                'alasan_tutup' => null, 'tgl_tutup' => null, 'tutup_oleh' => null,
            ],
            [
                'judul' => 'Maintenance Operations Planner', 'perusahaan_idx' => 0, 'jenis' => 'MT', 'jenjang' => 'D-III / D-IV',
                'lokasi' => 'Bandung, Jawa Barat', 'tenggat' => '2027-01-10 23:59:59', 'prodi' => ['TRO', 'TO'],
                'kebutuhan' => 5, 'pengalaman' => 'Lulusan baru IPK minimal 3,00',
                'sertifikasi' => null,
                'metode' => 'tautan_eksternal', 'email' => 'rekrutmen@kai.id',
                'sumber' => 'Portal Rekrutmen PT KAI', 'tgl_terima' => '2026-10-25',
                'validasi' => 'tervalidasi', 'publikasi' => 'terbit', 'tgl_periksa' => '2026-10-26', 'tgl_terbit' => '2026-10-27',
                'catatan_pemeriksa' => 'Seluruh butir verifikasi terpenuhi.',
                'alasan_tutup' => null, 'tgl_tutup' => null, 'tutup_oleh' => null,
            ],
            [
                'judul' => 'Fleet Telematics Data Officer', 'perusahaan_idx' => 3, 'jenis' => 'Kontrak', 'jenjang' => 'D-IV',
                'lokasi' => 'DKI Jakarta', 'tenggat' => '2027-01-15 23:59:59', 'prodi' => ['RSTJ', 'TRO'],
                'kebutuhan' => 2, 'pengalaman' => 'Lulusan baru',
                'sertifikasi' => 'Terbiasa dengan sistem pelacakan armada (GPS/Telematics)',
                'metode' => 'tautan_eksternal', 'email' => 'talent@bluebirdgroup.com',
                'sumber' => 'Poster & Surat Mitra Bluebird', 'tgl_terima' => '2026-10-26',
                'validasi' => 'tervalidasi', 'publikasi' => 'terbit', 'tgl_periksa' => '2026-10-27', 'tgl_terbit' => '2026-10-28',
                'catatan_pemeriksa' => 'Validasi lengkap dan siap dibuka.',
                'alasan_tutup' => null, 'tgl_tutup' => null, 'tutup_oleh' => null,
            ],
            // 9: UAT-15 Tervalidasi tetapi masih berstatus publikasi draf (belum diterbitkan)
            [
                'judul' => 'Safety Operations Coordinator', 'perusahaan_idx' => 0, 'jenis' => 'Tetap', 'jenjang' => 'D-IV',
                'lokasi' => 'Cirebon / Tegal', 'tenggat' => '2027-01-20 23:59:59', 'prodi' => ['RSTJ'],
                'kebutuhan' => 2, 'pengalaman' => 'Lulusan baru',
                'sertifikasi' => null,
                'metode' => 'tautan_eksternal', 'email' => 'rekrutmen@kai.id',
                'sumber' => 'Surat Pengantar Mitra No. 132/SDM-KAI/X/2026', 'tgl_terima' => '2026-10-27',
                'validasi' => 'tervalidasi', 'publikasi' => 'draf', 'tgl_periksa' => '2026-10-28', 'tgl_terbit' => null,
                'catatan_pemeriksa' => 'Sudah tervalidasi dan menunggu jadwal penerbitan resmi oleh staf karir.',
                'alasan_tutup' => null, 'tgl_tutup' => null, 'tutup_oleh' => null,
            ],
            // 10: LOW-04 Perusahaan belum diperiksa & lowongan berstatus diajukan (menunggu verifikasi)
            [
                'judul' => 'Fleet Dispatch Analyst', 'perusahaan_idx' => 5, 'jenis' => 'Kontrak', 'jenjang' => 'D-III / D-IV',
                'lokasi' => 'Semarang, Jawa Tengah', 'tenggat' => '2027-01-25 23:59:59', 'prodi' => ['RSTJ', 'TO'],
                'kebutuhan' => 3, 'pengalaman' => 'Lulusan baru',
                'sertifikasi' => null,
                'metode' => 'email', 'email' => 'hr@nusantaralogistik.co.id',
                'sumber' => 'Surel pengajuan mitra baru', 'tgl_terima' => '2026-10-29',
                'validasi' => 'diajukan', 'publikasi' => 'draf', 'tgl_periksa' => null, 'tgl_terbit' => null,
                'catatan_pemeriksa' => null,
                'alasan_tutup' => null, 'tgl_tutup' => null, 'tutup_oleh' => null,
            ],
            // 11: UAT-16 Berakhir otomatis karena melewati batas_pendaftaran sebelum 2026-10-30
            [
                'judul' => 'Junior Road Inspector', 'perusahaan_idx' => 1, 'jenis' => 'Magang', 'jenjang' => 'D-IV',
                'lokasi' => 'DKI Jakarta', 'tenggat' => '2026-10-20 23:59:59', 'prodi' => ['RSTJ'],
                'kebutuhan' => 4, 'pengalaman' => 'Taruna tingkat akhir / lulusan baru',
                'sertifikasi' => null,
                'metode' => 'tautan_eksternal', 'email' => 'karir@transjakarta.co.id',
                'sumber' => 'Portal Resmi Karir Transjakarta', 'tgl_terima' => '2026-10-01',
                'validasi' => 'tervalidasi', 'publikasi' => 'terbit', 'tgl_periksa' => '2026-10-02', 'tgl_terbit' => '2026-10-03',
                'catatan_pemeriksa' => 'Sudah selesai masa pendaftarannya pada 20 Oktober 2026.',
                'alasan_tutup' => null, 'tgl_tutup' => null, 'tutup_oleh' => null,
            ],
            // 12: UAT-16 Ditutup manual dengan alasan sebelum tenggat selesai
            [
                'judul' => 'Chassis Quality Auditor', 'perusahaan_idx' => 4, 'jenis' => 'Tetap', 'jenjang' => 'D-III',
                'lokasi' => 'Purwakarta', 'tenggat' => '2026-12-10 23:59:59', 'prodi' => ['TO'],
                'kebutuhan' => 2, 'pengalaman' => 'Lulusan baru',
                'sertifikasi' => null,
                'metode' => 'tautan_eksternal', 'email' => 'hr.recruitment@hino.co.id',
                'sumber' => 'Surat Mitra Hino', 'tgl_terima' => '2026-10-05',
                'validasi' => 'tervalidasi', 'publikasi' => 'ditutup', 'tgl_periksa' => '2026-10-06', 'tgl_terbit' => '2026-10-07',
                'catatan_pemeriksa' => 'Disetujui untuk program studi Teknologi Otomotif.',
                'alasan_tutup' => 'Kuota pelamar telah terpenuhi', 'tgl_tutup' => '2026-10-25', 'tutup_oleh' => 'PIC Karir & Alumni',
            ],
            // 13: Perlu perbaikan
            [
                'judul' => 'Automotive Diagnostic Technician', 'perusahaan_idx' => 2, 'jenis' => 'Tetap', 'jenjang' => 'D-III / D-IV',
                'lokasi' => 'Jakarta Utara', 'tenggat' => '2027-01-28 23:59:59', 'prodi' => ['TRO', 'TO'],
                'kebutuhan' => 4, 'pengalaman' => 'Lulusan baru',
                'sertifikasi' => null,
                'metode' => 'tautan_eksternal', 'email' => 'recruitment@daihatsu.astra.co.id',
                'sumber' => 'Pesan singkat informasi posisi', 'tgl_terima' => '2026-10-28',
                'validasi' => 'perlu_perbaikan', 'publikasi' => 'draf', 'tgl_periksa' => '2026-10-29', 'tgl_terbit' => null,
                'catatan_pemeriksa' => 'Lampirkan surat resmi perusahaan atau poster rekrutmen agar sumber lowongan dapat diverifikasi.',
                'alasan_tutup' => null, 'tgl_tutup' => null, 'tutup_oleh' => null,
            ],
        ];

        foreach ($vacancies as $vIdx => $vac) {
            $riwayat = [
                [
                    'tanggal' => $vac['tgl_terima'],
                    'pelaku'  => 'PIC Karir & Alumni',
                    'aksi'    => 'Dicatat & Diajukan',
                    'status'  => 'diajukan',
                    'catatan' => 'Pengajuan lowongan beserta rincian kanal lamaran dikirim ke pemeriksa.',
                ],
            ];
            if ($vac['validasi'] === 'tervalidasi') {
                $riwayat[] = [
                    'tanggal' => $vac['tgl_periksa'],
                    'pelaku'  => 'Validator Karir PKTJ',
                    'aksi'    => 'Divalidasi',
                    'status'  => 'tervalidasi',
                    'catatan' => $vac['catatan_pemeriksa'] ?? 'Kelengkapan lowongan dinyatakan valid.',
                ];
            } elseif ($vac['validasi'] === 'perlu_perbaikan') {
                $riwayat[] = [
                    'tanggal' => $vac['tgl_periksa'],
                    'pelaku'  => 'Validator Karir PKTJ',
                    'aksi'    => 'Dikembalikan',
                    'status'  => 'perlu_perbaikan',
                    'catatan' => $vac['catatan_pemeriksa'] ?? 'Membutuhkan kelengkapan dokumen pendukung.',
                ];
            }
            if (!empty($vac['tgl_terbit'])) {
                $riwayat[] = [
                    'tanggal' => $vac['tgl_terbit'],
                    'pelaku'  => 'PIC Karir & Alumni',
                    'aksi'    => 'Diterbitkan',
                    'status'  => 'terbit',
                    'catatan' => 'Lowongan ditayangkan pada direktori karir dan alumni.',
                ];
            }
            if (!empty($vac['tgl_tutup'])) {
                $riwayat[] = [
                    'tanggal' => $vac['tgl_tutup'],
                    'pelaku'  => $vac['tutup_oleh'] ?? 'PIC Karir & Alumni',
                    'aksi'    => 'Ditutup Manual',
                    'status'  => 'ditutup',
                    'catatan' => $vac['alasan_tutup'] ?? 'Penutupan manual oleh pengelola.',
                ];
            }

            $tautan = $vac['metode'] === 'email'
                ? ''
                : 'https://karir.mitra-transportasi.go.id/apply/' . url_title($vac['judul'], '-', true);

            $rowLow = [
                'perusahaan_id'      => $compIds[$vac['perusahaan_idx']],
                'judul_lowongan'     => $vac['judul'],
                'jenis_pekerjaan'    => $vac['jenis'],
                'lokasi'             => $vac['lokasi'],
                'deskripsi'          => 'Tanggung jawab mencakup kepatuhan standar keselamatan kerja armada, audit kelaikan operasional, koordinasi pemeliharaan, dan kepatuhan regulasi transportasi jalan.',
                'persyaratan'        => 'Lulusan ' . $vac['jenjang'] . ' PKTJ Tegal, memahami regulasi keselamatan transportasi darat, sehat jasmani dan rohani.',
                'batas_pendaftaran'  => $vac['tenggat'],
                'tautan_pendaftaran' => $tautan,
                'status_publikasi'   => $vac['publikasi'],
                'status_validasi'    => $vac['validasi'],
                'created_at'         => $now,
                'updated_at'         => $now,
            ];

            if ($hasExtendedLow) {
                $rowLow = array_merge($rowLow, [
                    'jenjang_pendidikan'        => $vac['jenjang'],
                    'tahun_lulus_sasaran'       => '2024 - 2026',
                    'jumlah_kebutuhan'          => $vac['kebutuhan'],
                    'pengalaman_minimum'        => $vac['pengalaman'],
                    'sertifikasi_opsional'      => $vac['sertifikasi'],
                    'metode_lamaran'            => $vac['metode'],
                    'email_lamaran'             => $vac['email'],
                    'sumber_informasi'          => $vac['sumber'],
                    'tanggal_diterima'          => $vac['tgl_terima'],
                    'pic_pengelola'             => 'PIC Karir & Alumni',
                    'catatan_sumber'            => 'Diterima melalui korespondensi resmi mitra industri dan diperiksa silang dengan kontak HRD.',
                    'checklist_verifikasi_json' => $vac['validasi'] === 'tervalidasi' ? $defaultChecklist : null,
                    'catatan_pemeriksa'         => $vac['catatan_pemeriksa'],
                    'diajukan_oleh'             => 'PIC Karir & Alumni',
                    'diperiksa_oleh'            => $vac['tgl_periksa'] ? 'Validator Karir PKTJ' : null,
                    'tanggal_pemeriksaan'       => $vac['tgl_periksa'],
                    'dipublikasi_oleh'          => $vac['tgl_terbit'] ? 'PIC Karir & Alumni' : null,
                    'tanggal_publikasi'         => $vac['tgl_terbit'],
                    'alasan_penutupan'          => $vac['alasan_tutup'],
                    'ditutup_oleh'              => $vac['tutup_oleh'],
                    'tanggal_ditutup'           => $vac['tgl_tutup'],
                    'riwayat_proses_json'       => json_encode($riwayat, JSON_UNESCAPED_UNICODE),
                ]);
            }

            $db->table('lowongan_pekerjaan')->insert($rowLow);
            $lowonganId = $db->insertID();

            foreach ($vac['prodi'] as $kp) {
                if (isset($prodiMap[$kp])) {
                    $db->table('lowongan_sasaran_prodi')->insert([
                        'lowongan_id'      => $lowonganId,
                        'program_studi_id' => $prodiMap[$kp],
                        'created_at'       => $now,
                    ]);
                }
            }

            if ($hasDokTable) {
                $statusDok = $vac['validasi'] === 'tervalidasi' ? 'sesuai' : ($vac['validasi'] === 'perlu_perbaikan' ? 'perlu_perbaikan' : 'belum_diperiksa');
                $compName  = $companies[$vac['perusahaan_idx']]['nama'];
                $compWeb   = $companies[$vac['perusahaan_idx']]['web'];

                $docs = [
                    [
                        'lowongan_id'        => $lowonganId,
                        'kategori_dokumen'   => 'surat_perusahaan',
                        'judul_dokumen'      => 'Surat Permohonan Rekrutmen ' . $compName,
                        'sumber_dokumen'     => 'HRD ' . $compName,
                        'nama_berkas'        => null,
                        'url_sumber'         => null,
                        'tanggal_diterima'   => $vac['tgl_terima'],
                        'status_pemeriksaan' => $statusDok,
                        'is_internal'        => 1,
                        'catatan'            => 'Dokumen pengantar resmi mitra industri (khusus staf internal).',
                        'diunggah_oleh'      => 'PIC Karir & Alumni',
                        'created_at'         => $now,
                        'updated_at'         => $now,
                    ],
                    [
                        'lowongan_id'        => $lowonganId,
                        'kategori_dokumen'   => 'tautan_sumber',
                        'judul_dokumen'      => 'Tautan Sumber Portal Karir Mitra',
                        'sumber_dokumen'     => 'Situs Resmi Perusahaan',
                        'nama_berkas'        => null,
                        'url_sumber'         => $compWeb . '/karir/' . url_title($vac['judul'], '-', true),
                        'tanggal_diterima'   => $vac['tgl_terima'],
                        'status_pemeriksaan' => $statusDok,
                        'is_internal'        => 1,
                        'catatan'            => 'Rujukan pemeriksaan keaslian lowongan pada kanal resmi perusahaan.',
                        'diunggah_oleh'      => 'PIC Karir & Alumni',
                        'created_at'         => $now,
                        'updated_at'         => $now,
                    ],
                ];
                if ($vIdx % 2 === 0 && $vac['validasi'] !== 'perlu_perbaikan') {
                    $docs[] = [
                        'lowongan_id'        => $lowonganId,
                        'kategori_dokumen'   => 'poster_rekrutmen',
                        'judul_dokumen'      => 'Poster Informasi Lowongan ' . $vac['judul'],
                        'sumber_dokumen'     => 'Humas & Rekrutmen Mitra',
                        'nama_berkas'        => null,
                        'url_sumber'         => null,
                        'tanggal_diterima'   => $vac['tgl_terima'],
                        'status_pemeriksaan' => $statusDok,
                        'is_internal'        => 0,
                        'catatan'            => 'Materi publikasi resmi untuk taruna tingkat akhir dan alumni.',
                        'diunggah_oleh'      => 'PIC Karir & Alumni',
                        'created_at'         => $now,
                        'updated_at'         => $now,
                    ];
                    $docs[] = [
                        'lowongan_id'        => $lowonganId,
                        'kategori_dokumen'   => 'bukti_konfirmasi',
                        'judul_dokumen'      => 'Catatan Konfirmasi Kontak HRD',
                        'sumber_dokumen'     => 'Telepon & Surel Resmi PIC',
                        'nama_berkas'        => null,
                        'url_sumber'         => null,
                        'tanggal_diterima'   => $vac['tgl_terima'],
                        'status_pemeriksaan' => $statusDok,
                        'is_internal'        => 1,
                        'catatan'            => 'Konfirmasi langsung dengan PIC perusahaan (tidak dipublikasikan ke alumni/taruna).',
                        'diunggah_oleh'      => 'Validator Karir PKTJ',
                        'created_at'         => $now,
                        'updated_at'         => $now,
                    ];
                }
                $db->table('lowongan_dokumen')->insertBatch($docs);
            }
        }

        // 12. Capaian Rekam Jejak Taruna (86 Tervalidasi, 7 Diajukan, 1 Perlu Perbaikan)
        if ($db->tableExists('taruna_capaian')) {
            $capaianBatch = [
                [
                    'nomor_taruna'      => '2401001',
                    'kategori'          => 'kompetensi_sertifikasi',
                    'nama_capaian'      => 'Sertifikasi Keselamatan Jalan',
                    'tanggal_kegiatan'  => '2026-10-20',
                    'tahun_akademik_id' => $taMap['2026/2027 Ganjil'],
                    'semester'          => 5,
                    'penyelenggara'     => 'Lembaga Sertifikasi Kompetensi',
                    'tingkat_kegiatan'  => 'Nasional',
                    'uraian_capaian'    => 'Mengikuti dan dinyatakan lulus Sertifikasi Keselamatan Jalan.',
                    'hasil_capaian'     => 'Kompeten',
                    'nilai_angka'       => 92.50,
                    'satuan_nilai'      => 'Skor',
                    'nomor_dokumen'     => 'SERT/026/2026',
                    'unit_pemilik'      => 'Ketarunaan',
                    'sumber_dokumen'    => 'Sertifikat',
                    'bukti_berkas'      => 'Sertifikat.pdf',
                    'ukuran_berkas_kb'  => 456,
                    'jumlah_berkas'     => 1,
                    'status_validasi'   => 'tervalidasi',
                    'catatan_validasi'  => null,
                    'diajukan_oleh'     => 'PIC Ketarunaan',
                    'divalidasi_oleh'   => 'Validator Unit',
                    'created_at'        => $now,
                    'updated_at'        => '2026-10-30 09:00:00',
                ],
                [
                    'nomor_taruna'      => '2401001',
                    'kategori'          => 'organisasi',
                    'nama_capaian'      => 'Pengurus Organisasi Taruna',
                    'tanggal_kegiatan'  => '2026-10-12',
                    'tahun_akademik_id' => $taMap['2026/2027 Ganjil'],
                    'semester'          => 5,
                    'penyelenggara'     => 'Himpunan Taruna RSTJ PKTJ',
                    'tingkat_kegiatan'  => 'Kampus / Internal',
                    'uraian_capaian'    => 'Menjabat sebagai Sekretaris pada Himpunan Taruna RSTJ.',
                    'hasil_capaian'     => 'Sekretaris',
                    'nilai_angka'       => null,
                    'satuan_nilai'      => null,
                    'nomor_dokumen'     => 'ST/ORG/019/2026',
                    'unit_pemilik'      => 'Ketarunaan',
                    'sumber_dokumen'    => 'Surat Tugas',
                    'bukti_berkas'      => 'Surat_Tugas.pdf',
                    'ukuran_berkas_kb'  => 312,
                    'jumlah_berkas'     => 1,
                    'status_validasi'   => 'tervalidasi',
                    'catatan_validasi'  => null,
                    'diajukan_oleh'     => 'PIC Ketarunaan',
                    'divalidasi_oleh'   => 'Validator Unit',
                    'created_at'        => $now,
                    'updated_at'        => '2026-10-29 14:00:00',
                ],
                [
                    'nomor_taruna'      => '2401001',
                    'kategori'          => 'pengembangan_diri',
                    'nama_capaian'      => 'Pelatihan Kepemimpinan',
                    'tanggal_kegiatan'  => '2026-10-08',
                    'tahun_akademik_id' => $taMap['2026/2027 Ganjil'],
                    'semester'          => 5,
                    'penyelenggara'     => 'Pusbangkar PKTJ',
                    'tingkat_kegiatan'  => 'Kampus / Internal',
                    'uraian_capaian'    => 'Mengikuti pelatihan kepemimpinan taruna tingkat lanjut.',
                    'hasil_capaian'     => 'Selesai',
                    'nilai_angka'       => 88.00,
                    'satuan_nilai'      => 'Poin',
                    'nomor_dokumen'     => 'SERT/PK/041/2026',
                    'unit_pemilik'      => 'Pusbangkar',
                    'sumber_dokumen'    => 'Sertifikat',
                    'bukti_berkas'      => 'Sertifikat_Kepemimpinan.pdf',
                    'ukuran_berkas_kb'  => 280,
                    'jumlah_berkas'     => 2,
                    'status_validasi'   => 'diajukan',
                    'catatan_validasi'  => null,
                    'diajukan_oleh'     => 'PIC Pusbangkar',
                    'divalidasi_oleh'   => null,
                    'created_at'        => $now,
                    'updated_at'        => '2026-10-28 11:00:00',
                ],
                [
                    'nomor_taruna'      => '2401001',
                    'kategori'          => 'akademik',
                    'nama_capaian'      => 'Ringkasan Seminar Keselamatan Jalan',
                    'tanggal_kegiatan'  => '2026-10-05',
                    'tahun_akademik_id' => $taMap['2026/2027 Ganjil'],
                    'semester'          => 5,
                    'penyelenggara'     => 'Program Studi RSTJ',
                    'tingkat_kegiatan'  => 'Nasional',
                    'uraian_capaian'    => 'Peserta seminar nasional rekayasa keselamatan transportasi jalan.',
                    'hasil_capaian'     => 'Peserta',
                    'nilai_angka'       => null,
                    'satuan_nilai'      => null,
                    'nomor_dokumen'     => 'SERT/SEM/088/2026',
                    'unit_pemilik'      => 'Program Studi',
                    'sumber_dokumen'    => 'Sertifikat',
                    'bukti_berkas'      => 'Sertifikat_Seminar.pdf',
                    'ukuran_berkas_kb'  => 195,
                    'jumlah_berkas'     => 1,
                    'status_validasi'   => 'perlu_perbaikan',
                    'catatan_validasi'  => 'Mohon unggah ulang sertifikat dengan nomor registrasi yang terbaca jelas.',
                    'diajukan_oleh'     => 'PIC Prodi',
                    'divalidasi_oleh'   => 'Validator Unit',
                    'created_at'        => $now,
                    'updated_at'        => '2026-10-27 16:00:00',
                ],
                [
                    'nomor_taruna'      => '2401001',
                    'kategori'          => 'pengembangan_diri',
                    'nama_capaian'      => 'Lokakarya Komunikasi',
                    'tanggal_kegiatan'  => '2026-09-20',
                    'tahun_akademik_id' => $taMap['2026/2027 Ganjil'],
                    'semester'          => 5,
                    'penyelenggara'     => 'PKTJ Tegal',
                    'tingkat_kegiatan'  => 'Kampus / Internal',
                    'uraian_capaian'    => 'Menyelesaikan lokakarya komunikasi efektif dan pelayanan publik.',
                    'hasil_capaian'     => 'Selesai',
                    'nilai_angka'       => null,
                    'satuan_nilai'      => null,
                    'nomor_dokumen'     => 'SERT/LK/011/2026',
                    'unit_pemilik'      => 'Ketarunaan',
                    'sumber_dokumen'    => 'Sertifikat',
                    'bukti_berkas'      => 'Sertifikat_Lokakarya.pdf',
                    'ukuran_berkas_kb'  => 220,
                    'jumlah_berkas'     => 1,
                    'status_validasi'   => 'tervalidasi',
                    'catatan_validasi'  => null,
                    'diajukan_oleh'     => 'PIC Ketarunaan',
                    'divalidasi_oleh'   => 'Validator Unit',
                    'created_at'        => $now,
                    'updated_at'        => '2026-09-25 10:00:00',
                ],
            ];

            // Tambah 83 capaian tervalidasi lainnya agar total capaian tervalidasi = 86
            $kategoriTemplates = [
                ['kat' => 'kompetensi_sertifikasi', 'nama' => 'Sertifikasi Pengujian Kendaraan Bermotor', 'hasil' => 'Kompeten', 'penyelenggara' => 'LSP Transportasi Darat', 'tingkat' => 'Nasional', 'sumber' => 'Sertifikat'],
                ['kat' => 'organisasi',             'nama' => 'Kepengurusan Korps Batalyon Taruna',      'hasil' => 'Pengurus', 'penyelenggara' => 'Pusbangkar PKTJ',        'tingkat' => 'Kampus / Internal', 'sumber' => 'Surat Tugas'],
                ['kat' => 'pengembangan_diri',      'nama' => 'Pelatihan Tanggap Darurat Jalan Raya',    'hasil' => 'Selesai',  'penyelenggara' => 'PKTJ Tegal',            'tingkat' => 'Kampus / Internal', 'sumber' => 'Sertifikat'],
                ['kat' => 'akademik',               'nama' => 'Kuliah Umum Manajemen Lalu Lintas',       'hasil' => 'Peserta',  'penyelenggara' => 'Program Studi',         'tingkat' => 'Nasional', 'sumber' => 'Sertifikat'],
            ];

            for ($c = 1; $c <= 83; $c++) {
                $tpl = $kategoriTemplates[($c - 1) % count($kategoriTemplates)];
                $tarunaIdx = ($c % 60) + 1;
                $capaianBatch[] = [
                    'nomor_taruna'      => $tarunaBatch[$tarunaIdx]['nomor_taruna'],
                    'kategori'          => $tpl['kat'],
                    'nama_capaian'      => $tpl['nama'],
                    'tanggal_kegiatan'  => '2026-10-' . sprintf('%02d', ($c % 25) + 1),
                    'tahun_akademik_id' => $taMap['2026/2027 Ganjil'],
                    'semester'          => 5,
                    'penyelenggara'     => $tpl['penyelenggara'],
                    'tingkat_kegiatan'  => $tpl['tingkat'],
                    'uraian_capaian'    => 'Partisipasi aktif dalam kegiatan ' . strtolower($tpl['nama']) . '.',
                    'hasil_capaian'     => $tpl['hasil'],
                    'nilai_angka'       => null,
                    'satuan_nilai'      => null,
                    'nomor_dokumen'     => 'SERT/CAP/2026/' . sprintf('%03d', $c),
                    'unit_pemilik'      => 'Ketarunaan',
                    'sumber_dokumen'    => $tpl['sumber'],
                    'bukti_berkas'      => 'bukti_capaian_' . $c . '.pdf',
                    'ukuran_berkas_kb'  => 240,
                    'jumlah_berkas'     => 1,
                    'status_validasi'   => 'tervalidasi',
                    'catatan_validasi'  => null,
                    'diajukan_oleh'     => 'PIC Ketarunaan',
                    'divalidasi_oleh'   => 'Validator Unit',
                    'created_at'        => $now,
                    'updated_at'        => '2026-10-' . sprintf('%02d', ($c % 28) + 1) . ' 10:00:00',
                ];
            }

            // Tambah 6 capaian berstatus diajukan agar total menunggu validasi = 7 (1 milik Aditya + 6 lainnya)
            for ($cd = 1; $cd <= 6; $cd++) {
                $capaianBatch[] = [
                    'nomor_taruna'      => $tarunaBatch[$cd + 10]['nomor_taruna'],
                    'kategori'          => 'kompetensi_sertifikasi',
                    'nama_capaian'      => 'Sertifikasi Kompetensi Keselamatan #' . $cd,
                    'tanggal_kegiatan'  => '2026-10-25',
                    'tahun_akademik_id' => $taMap['2026/2027 Ganjil'],
                    'semester'          => 5,
                    'penyelenggara'     => 'LSP Transportasi',
                    'tingkat_kegiatan'  => 'Nasional',
                    'uraian_capaian'    => 'Pengajuan sertifikat uji kompetensi bidang transportasi jalan.',
                    'hasil_capaian'     => 'Kompeten',
                    'nilai_angka'       => 89.00,
                    'satuan_nilai'      => 'Skor',
                    'nomor_dokumen'     => 'SERT/USUL-CAP/2026/' . $cd,
                    'unit_pemilik'      => 'Ketarunaan',
                    'sumber_dokumen'    => 'Sertifikat',
                    'bukti_berkas'      => 'usulan_capaian_' . $cd . '.pdf',
                    'ukuran_berkas_kb'  => 310,
                    'jumlah_berkas'     => 1,
                    'status_validasi'   => 'diajukan',
                    'catatan_validasi'  => null,
                    'diajukan_oleh'     => 'PIC Ketarunaan',
                    'divalidasi_oleh'   => null,
                    'created_at'        => $now,
                    'updated_at'        => '2026-10-29 15:00:00',
                ];
            }

            foreach (array_chunk($capaianBatch, 100) as $chunk) {
                $db->table('taruna_capaian')->insertBatch($chunk);
            }
        }
        if (!empty($alumniRiwayatBatch) && $db->tableExists('alumni_riwayat_perubahan')) {
            foreach (array_chunk($alumniRiwayatBatch, 100) as $chunk) {
                $db->table('alumni_riwayat_perubahan')->insertBatch($chunk);
            }
        }
    }
}
