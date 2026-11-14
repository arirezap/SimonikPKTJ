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

        // 4. Kelas Referensi
        $db->table('ref_kelas')->truncate();
        $kelasBatch = [];
        foreach (['A', 'B', 'C'] as $namaKls) {
            $kelasBatch[] = [
                'program_studi_id' => $prodiMap['RSTJ'],
                'angkatan_id'      => $angkatanMap[2024],
                'tingkat'          => 3,
                'nama_kelas'       => $namaKls,
                'is_aktif'         => 1,
                'created_at'       => $now,
            ];
        }
        $db->table('ref_kelas')->insertBatch($kelasBatch);
        $kelasMap = [];
        foreach ($db->table('ref_kelas')->get()->getResultArray() as $k) {
            $kelasMap[$k['nama_kelas']] = (int) $k['id'];
        }
        $kelasAId = $kelasMap['A'] ?? null;

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
        $db->table('taruna_penerima_beasiswa')->truncate();
        $db->table('taruna_program_beasiswa')->truncate();
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

                    $pendidikanBatch[] = [
                        'nomor_taruna'      => $nomorTaruna,
                        'program_studi_id'  => $prodiId,
                        'angkatan_id'       => $angkatanId,
                        'kelas_id'          => $kelasAId,
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
                            'kelas_id'          => $kelasAId,
                            'tingkat'           => $tingkatTaruna,
                            'semester'          => $semesterTaruna,
                            'tahun_akademik_id' => $tahunAkademikId,
                            'mulai_berlaku'     => '2026-09-01',
                            'akhir_berlaku'     => null,
                            'created_at'        => $now,
                        ];
                    }

                    if ($statusCode === 'lulus') {
                        $alumniBatch[] = [
                            'nomor_taruna'          => $nomorTaruna,
                            'tanggal_lulus'         => '2026-08-20',
                            'nomor_keputusan_lulus' => 'SK.LULUS/PKTJ/2026/' . $counter,
                            'tahun_lulus'           => 2026,
                            'pekerjaan_saat_ini'    => 'Penguji Kendaraan Bermotor',
                            'instansi_saat_ini'     => 'BPTD Kelas II Jawa Tengah',
                            'kontak_terakhir'       => '0812' . sprintf('%08d', 10000000 + $counter),
                            'created_at'            => $now,
                            'updated_at'            => $now,
                        ];
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

        // 10. Program Beasiswa (3 Program Aktif, 45 Penerima Ditetapkan)
        $programs = [
            ['nama_program' => 'Beasiswa Apresiasi IP Tertinggi 2026', 'jenis' => 'apresiasi', 'pemberi' => 'PKTJ Tegal', 'kuota' => 20],
            ['nama_program' => 'Beasiswa Prestasi Keselamatan Transportasi', 'jenis' => 'prestasi', 'pemberi' => 'Badan Pengembangan SDM Perhubungan', 'kuota' => 15],
            ['nama_program' => 'Bantuan Pendidikan Daerah 3TP', 'jenis' => 'daerah_3tp', 'pemberi' => 'Kementerian Perhubungan RI', 'kuota' => 15],
        ];

        $progIds = [];
        foreach ($programs as $prog) {
            $db->table('taruna_program_beasiswa')->insert([
                'nama_program'    => $prog['nama_program'],
                'jenis_beasiswa'  => $prog['jenis'],
                'pemberi'         => $prog['pemberi'],
                'tahun'           => 2026,
                'periode_mulai'   => '2026-09-01',
                'periode_selesai' => '2026-12-31',
                'kuota'           => $prog['kuota'],
                'status_program'  => 'berjalan',
                'created_at'      => $now,
                'updated_at'      => $now,
            ]);
            $progIds[] = $db->insertID();
        }

        $bCounter = 50;
        foreach ($progIds as $progIdx => $pId) {
            for ($b = 0; $b < 15; $b++) {
                $isAdityaBeasiswa = ($progIdx === 1 && $b === 0);
                $targetTaruna = $isAdityaBeasiswa ? '2401001' : $tarunaBatch[$bCounter]['nomor_taruna'];
                $db->table('taruna_penerima_beasiswa')->insert([
                    'program_beasiswa_id' => $pId,
                    'nomor_taruna'        => $targetTaruna,
                    'status_pengajuan'    => 'diterima',
                    'status_penetapan'    => 'ditetapkan',
                    'tanggal_penetapan'   => $isAdityaBeasiswa ? '2025-10-01' : '2026-09-15',
                    'nomor_keputusan'     => $isAdityaBeasiswa ? 'Keputusan_Beasiswa.pdf' : ('SK.BEASISWA/2026/' . $pId . '/' . sprintf('%02d', $b + 1)),
                    'status_validasi'     => 'tervalidasi',
                    'created_at'          => $now,
                    'updated_at'          => $now,
                ]);
                $bCounter++;
            }
        }

        // Tambah 4 usulan kandidat beasiswa (diajukan) & 2 perlu_perbaikan (status_penetapan = kandidat, tidak masuk 45 ditetapkan)
        for ($kb = 1; $kb <= 6; $kb++) {
            $db->table('taruna_penerima_beasiswa')->insert([
                'program_beasiswa_id' => $progIds[0],
                'nomor_taruna'        => $tarunaBatch[$bCounter + $kb]['nomor_taruna'],
                'status_pengajuan'    => 'diajukan',
                'status_penetapan'    => 'kandidat',
                'tanggal_penetapan'   => null,
                'nomor_keputusan'     => null,
                'status_validasi'     => ($kb <= 4) ? 'diajukan' : 'perlu_perbaikan',
                'created_at'          => $now,
                'updated_at'          => $now,
            ]);
        }

        // 11. Perusahaan Mitra & Lowongan Aktif (8 Lowongan Aktif, 2 Segera Berakhir)
        $companies = [
            ['nama' => 'PT Kereta Api Indonesia (Persero)', 'sektor' => 'Transportasi Perkeretaapian', 'web' => 'https://kai.id'],
            ['nama' => 'PT Transportasi Jakarta (Transjakarta)', 'sektor' => 'Transportasi Perkotaan', 'web' => 'https://transjakarta.co.id'],
            ['nama' => 'Astra Daihatsu Motor', 'sektor' => 'Manufaktur Otomotif', 'web' => 'https://daihatsu.co.id'],
            ['nama' => 'PT Bluebird Tbk', 'sektor' => 'Layanan Taksi & Armada', 'web' => 'https://bluebirdgroup.com'],
            ['nama' => 'PT Hino Motors Manufacturing', 'sektor' => 'Kendaraan Niaga', 'web' => 'https://hino.co.id'],
        ];

        $compIds = [];
        foreach ($companies as $comp) {
            $db->table('perusahaan_mitra')->insert([
                'nama_perusahaan' => $comp['nama'],
                'sektor'          => $comp['sektor'],
                'alamat'          => 'Kawasan Industri Jakarta & Sekitarnya',
                'website'         => $comp['web'],
                'kontak_person'   => 'Divisi HRD & Rekrutmen',
                'is_verified'     => 1,
                'created_at'      => $now,
                'updated_at'      => $now,
            ]);
            $compIds[] = $db->insertID();
        }

        $vacancies = [
            ['judul' => 'Transport Safety Officer',        'perusahaan_idx' => 0, 'tenggat' => '2026-11-04 23:59:00', 'prodi' => ['RSTJ']],
            ['judul' => 'Fleet Maintenance Supervisor',   'perusahaan_idx' => 1, 'tenggat' => '2026-11-05 23:59:00', 'prodi' => ['TRO', 'TO']],
            ['judul' => 'Traffic Management Analyst',     'perusahaan_idx' => 1, 'tenggat' => '2026-11-30 23:59:00', 'prodi' => ['RSTJ']],
            ['judul' => 'Automotive Quality Inspector',   'perusahaan_idx' => 2, 'tenggat' => '2026-12-15 23:59:00', 'prodi' => ['TRO', 'TO']],
            ['judul' => 'Vehicle Roadworthiness Engineer','perusahaan_idx' => 4, 'tenggat' => '2026-12-20 23:59:00', 'prodi' => ['TRO']],
            ['judul' => 'Safety & Compliance Specialist', 'perusahaan_idx' => 3, 'tenggat' => '2026-12-31 23:59:00', 'prodi' => ['RSTJ']],
            ['judul' => 'Maintenance Operations Planner', 'perusahaan_idx' => 0, 'tenggat' => '2027-01-10 23:59:00', 'prodi' => ['TRO', 'TO']],
            ['judul' => 'Fleet Telematics Data Officer',  'perusahaan_idx' => 3, 'tenggat' => '2027-01-15 23:59:00', 'prodi' => ['RSTJ', 'TRO']],
        ];

        foreach ($vacancies as $vac) {
            $db->table('lowongan_pekerjaan')->insert([
                'perusahaan_id'      => $compIds[$vac['perusahaan_idx']],
                'judul_lowongan'     => $vac['judul'],
                'jenis_pekerjaan'    => 'Tetap',
                'lokasi'             => 'DKI Jakarta / Jawa Barat',
                'deskripsi'          => 'Tanggung jawab mencakup kepatuhan standar keselamatan kerja armada, audit kelaikan operasional, dan kepatuhan regulasi jalan.',
                'persyaratan'        => 'Lulusan D-III / D-IV PKTJ, memahami regulasi keselamatan transportasi, sehat jasmani.',
                'batas_pendaftaran'  => $vac['tenggat'],
                'tautan_pendaftaran' => 'https://karir.mitra-transportasi.go.id/apply/' . url_title($vac['judul'], '-', true),
                'status_publikasi'   => 'terbit',
                'status_validasi'    => 'tervalidasi',
                'created_at'         => $now,
                'updated_at'         => $now,
            ]);
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
    }
}
