<?php

namespace App\Libraries\Ketarunaan;

/**
 * Definisi resmi 2 bundel soal Tracer Study Tahun 2025 Politeknik Keselamatan Transportasi Jalan (PKTJ):
 * 1. Tracer Study Tahun 2025 untuk Lulusan PKTJ Tahun 2024 dan 2023 (target: alumni)
 * 2. Tracer Study Tahun 2025 untuk Pengguna Lulusan PKTJ (target: mitra)
 *
 * Seluruh redaksi surat pengantar, bagian, pertanyaan, pilihan jawaban, skala penilaian,
 * dan percabangan kondisional mengikuti dokumen PDF resmi PKTJ.
 */
class TracerStudyOfficialTemplates
{
    public const BUNDLE_ALUMNI_2025 = 'BND-ALUMNI-2025';
    public const BUNDLE_MITRA_2025  = 'BND-MITRA-2025';

    /**
     * Daftar 38 Provinsi Indonesia resmi Kepmendagri 2025 dari dataset cahyadsn/wilayah.
     *
     * @return list<string>
     */
    public static function provincesIndonesia(): array
    {
        $dataFile = APPPATH . 'Database/Data/wilayah_level_1_2.php';
        if (is_file($dataFile)) {
            $all  = (array) require $dataFile;
            $list = [];
            foreach ($all as $kode => $nama) {
                if (strlen((string) $kode) === 2) {
                    $list[] = (string) $nama;
                }
            }
            if (!empty($list)) {
                return $list;
            }
        }

        return [
            'Aceh',
            'Sumatera Utara',
            'Sumatera Barat',
            'Riau',
            'Jambi',
            'Sumatera Selatan',
            'Bengkulu',
            'Lampung',
            'Kepulauan Bangka Belitung',
            'Kepulauan Riau',
            'Daerah Khusus Ibukota Jakarta',
            'Jawa Barat',
            'Jawa Tengah',
            'Daerah Istimewa Yogyakarta',
            'Jawa Timur',
            'Banten',
            'Bali',
            'Nusa Tenggara Barat',
            'Nusa Tenggara Timur',
            'Kalimantan Barat',
            'Kalimantan Tengah',
            'Kalimantan Selatan',
            'Kalimantan Timur',
            'Kalimantan Utara',
            'Sulawesi Utara',
            'Sulawesi Tengah',
            'Sulawesi Selatan',
            'Sulawesi Tenggara',
            'Gorontalo',
            'Sulawesi Barat',
            'Maluku',
            'Maluku Utara',
            'Papua',
            'Papua Barat',
            'Papua Selatan',
            'Papua Tengah',
            'Papua Pegunungan',
            'Papua Barat Daya',
        ];
    }

    /**
     * Bundel Resmi 1: Tracer Study Tahun 2025 untuk Lulusan PKTJ (Alumni).
     *
     * @return array{bundle:array<string,mixed>,questions:list<array<string,mixed>>}
     */
    public static function alumni2025Bundle(): array
    {
        $suratPengantar = implode("\n\n", [
            'Yth. Para Alumni Politeknik Keselamatan Transportasi Jalan yang Kami Hormati dan Banggakan,',
            'Dengan hormat,',
            'Tracer study terhadap alumni merupakan salah satu instrumen penting dalam rangka evaluasi keberhasilan penyelenggaraan pendidikan di Politeknik Keselamatan Transportasi Jalan (PKTJ) sejak awal berdiri hingga saat ini. Studi ini bertujuan untuk memperoleh data dan informasi yang komprehensif guna mendukung upaya perbaikan dan pengembangan berkelanjutan dalam menjamin mutu pendidikan tinggi vokasi di bidang keselamatan transportasi jalan.',
            'Melalui pelaksanaan tracer study ini, diharapkan PKTJ dapat mengidentifikasi berbagai kekurangan yang mungkin terdapat dalam pelaksanaan program studi, serta merumuskan landasan yang lebih kuat untuk penyusunan kebijakan dan perencanaan strategis ke depan. Informasi terkait keberhasilan profesional alumni, termasuk aspek karier, status pekerjaan, dan tingkat pendapatan, sangat dibutuhkan sebagai tolok ukur relevansi dan efektivitas kurikulum yang telah diterapkan.',
            'Selain itu, masukan dari para pemangku kepentingan (stakeholder) terkait keterkaitan antara kompetensi lulusan dengan kebutuhan dunia kerja, ruang lingkup tugas, serta posisi profesional di pasar kerja saat ini, menjadi bagian integral dari tracer study ini. Kami juga berharap para alumni dapat memberikan evaluasi terhadap kondisi dan pengalaman belajar selama di PKTJ, serta relevansinya dengan tuntutan kerja yang dihadapi saat ini.',
            'Kami mengucapkan terima kasih yang sebesar-besarnya atas partisipasi dan kontribusi Bapak/Ibu dalam pelaksanaan tracer study ini. Semoga data dan informasi yang diberikan dapat menjadi dasar yang kuat dalam peningkatan kualitas layanan pendidikan di PKTJ demi mencetak lulusan yang profesional, kompeten, dan adaptif terhadap dinamika dunia kerja.',
            "Hormat kami,\nTIM TRACER 2025 Administrasi Ketarunaan dan Alumni, Politeknik Keselamatan Transportasi Jalan",
        ]);

        $bulanOptions = ['0 bulan (Sebelum Kelulusan)'];
        for ($b = 1; $b <= 18; $b++) {
            $bulanOptions[] = $b . ' bulan';
        }
        $bulanOptions[] = '>18 bulan';

        $showIfSudahBekerja = ['kode' => 'TS25_STATUS_PEKERJAAN', 'values' => ['Sudah Bekerja']];
        $showIfBelumBekerja = ['kode' => 'TS25_STATUS_PEKERJAAN', 'values' => ['Belum Bekerja']];
        $showIfTidakBekerja = ['kode' => 'TS25_STATUS_PEKERJAAN', 'values' => ['Tidak Bekerja']];
        $showIfLanjutStudi  = ['kode' => 'TS25_LANJUT_STUDI', 'values' => ['Ya']];

        $questions = [
            // Bagian 1: Informasi Data Pribadi Alumni (Q11 - Q18 PDF 2)
            [
                'kode_pertanyaan'    => 'TS25_TEMPAT_LAHIR',
                'bagian'             => 'Informasi Data Pribadi Alumni',
                'urutan'             => 1,
                'teks_pertanyaan'    => 'Tempat Lahir',
                'keterangan_bantuan' => null,
                'tipe_jawaban'       => 'teks_singkat',
                'opsi_jawaban_json'  => self::encode([
                    'prefill_key'  => 'tempat_lahir',
                    'wilayah_role' => 'kabupaten_kota_tags',
                ]),
                'is_wajib'           => 1,
            ],
            [
                'kode_pertanyaan'    => 'TS25_TANGGAL_LAHIR',
                'bagian'             => 'Informasi Data Pribadi Alumni',
                'urutan'             => 2,
                'teks_pertanyaan'    => 'Tanggal Lahir',
                'keterangan_bantuan' => 'Contoh: 2001-01-07',
                'tipe_jawaban'       => 'tanggal',
                'opsi_jawaban_json'  => self::encode(['prefill_key' => 'tanggal_lahir']),
                'is_wajib'           => 1,
            ],
            [
                'kode_pertanyaan'    => 'TS25_JENIS_KELAMIN',
                'bagian'             => 'Informasi Data Pribadi Alumni',
                'urutan'             => 3,
                'teks_pertanyaan'    => 'Jenis Kelamin',
                'keterangan_bantuan' => null,
                'tipe_jawaban'       => 'pilihan_tunggal',
                'opsi_jawaban_json'  => self::encode([
                    'options'     => ['Laki-laki', 'Perempuan'],
                    'render_as'   => 'dropdown',
                    'prefill_key' => 'jenis_kelamin',
                ]),
                'is_wajib'           => 1,
            ],
            [
                'kode_pertanyaan'    => 'TS25_EMAIL',
                'bagian'             => 'Informasi Data Pribadi Alumni',
                'urutan'             => 4,
                'teks_pertanyaan'    => 'Email aktif',
                'keterangan_bantuan' => null,
                'tipe_jawaban'       => 'teks_singkat',
                'opsi_jawaban_json'  => self::encode([
                    'input_type'  => 'email',
                    'prefill_key' => 'email_alumni',
                ]),
                'is_wajib'           => 1,
            ],
            [
                'kode_pertanyaan'    => 'TS25_NO_HP',
                'bagian'             => 'Informasi Data Pribadi Alumni',
                'urutan'             => 5,
                'teks_pertanyaan'    => 'No. Handphone yang bisa dihubungi',
                'keterangan_bantuan' => null,
                'tipe_jawaban'       => 'teks_singkat',
                'opsi_jawaban_json'  => self::encode([
                    'input_type'  => 'tel',
                    'prefill_key' => 'telepon_alumni',
                ]),
                'is_wajib'           => 1,
            ],
            [
                'kode_pertanyaan'    => 'TS25_KOTA_DOMISILI',
                'bagian'             => 'Informasi Data Pribadi Alumni',
                'urutan'             => 6,
                'teks_pertanyaan'    => 'Kota/Kabupaten domisili saat ini',
                'keterangan_bantuan' => 'Pilih dari daftar Kabupaten/Kota Indonesia (otomatis terhubung dengan Provinsi)',
                'tipe_jawaban'       => 'teks_singkat',
                'opsi_jawaban_json'  => self::encode([
                    'prefill_key'         => 'kota_domisili',
                    'wilayah_role'        => 'kabupaten_kota',
                    'wilayah_parent_kode' => 'TS25_PROVINSI_DOMISILI',
                ]),
                'is_wajib'           => 1,
            ],
            [
                'kode_pertanyaan'    => 'TS25_PROVINSI_DOMISILI',
                'bagian'             => 'Informasi Data Pribadi Alumni',
                'urutan'             => 7,
                'teks_pertanyaan'    => 'Provinsi domisili saat ini',
                'keterangan_bantuan' => null,
                'tipe_jawaban'       => 'pilihan_tunggal',
                'opsi_jawaban_json'  => self::encode([
                    'options'      => self::provincesIndonesia(),
                    'render_as'    => 'dropdown',
                    'wilayah_role' => 'provinsi',
                ]),
                'is_wajib'           => 1,
            ],
            [
                'kode_pertanyaan'    => 'TS25_STATUS_PEKERJAAN',
                'bagian'             => 'Informasi Data Pribadi Alumni',
                'urutan'             => 8,
                'teks_pertanyaan'    => 'Status Pekerjaan',
                'keterangan_bantuan' => 'Pilihan ini menentukan bagian lanjutan yang perlu Anda isi.',
                'tipe_jawaban'       => 'pilihan_tunggal',
                'opsi_jawaban_json'  => self::encode([
                    'options'   => ['Sudah Bekerja', 'Belum Bekerja', 'Tidak Bekerja'],
                    'render_as' => 'radio',
                ]),
                'is_wajib'           => 1,
            ],

            // Bagian 2: Informasi Data Pekerjaan Alumni (Q19 - Q27 PDF 2, khusus Sudah Bekerja)
            [
                'kode_pertanyaan'    => 'TS25_KATEGORI_WT',
                'bagian'             => 'Informasi Data Pekerjaan Alumni',
                'urutan'             => 9,
                'teks_pertanyaan'    => 'Berapa bulan waktu tunggu (WT) untuk mendapatkan pekerjaan pertama ?',
                'keterangan_bantuan' => null,
                'tipe_jawaban'       => 'pilihan_tunggal',
                'opsi_jawaban_json'  => self::encode([
                    'options' => [
                        'WT < 3 bulan',
                        '3 ≤ WT < 6 bulan',
                        '6 ≤ WT < 18 bulan',
                        'WT > 18 bulan',
                        'Sudah Bekerja Sebelum Kelulusan',
                    ],
                    'render_as' => 'radio',
                    'show_if'   => $showIfSudahBekerja,
                ]),
                'is_wajib'           => 0,
            ],
            [
                'kode_pertanyaan'    => 'TS25_SPESIFIK_WT',
                'bagian'             => 'Informasi Data Pekerjaan Alumni',
                'urutan'             => 10,
                'teks_pertanyaan'    => 'Spesifikkan dalam berapa bulan anda mendapatkan pekerjaan ?',
                'keterangan_bantuan' => null,
                'tipe_jawaban'       => 'pilihan_tunggal',
                'opsi_jawaban_json'  => self::encode([
                    'options'   => $bulanOptions,
                    'render_as' => 'dropdown',
                    'show_if'   => $showIfSudahBekerja,
                ]),
                'is_wajib'           => 1,
            ],
            [
                'kode_pertanyaan'    => 'TS25_TEMPAT_BEKERJA',
                'bagian'             => 'Informasi Data Pekerjaan Alumni',
                'urutan'             => 11,
                'teks_pertanyaan'    => 'Tempat Anda bekerja saat ini',
                'keterangan_bantuan' => null,
                'tipe_jawaban'       => 'pilihan_tunggal',
                'opsi_jawaban_json'  => self::encode([
                    'options'     => [
                        'Instansi Pemerintah',
                        'Perusahaan Swasta',
                        'BUMN',
                        'BUMD',
                        'Wirausaha',
                        'Organisasi Non-Profit',
                        'Institusi Multinasional',
                    ],
                    'render_as'   => 'radio',
                    'allow_other' => true,
                    'show_if'     => $showIfSudahBekerja,
                ]),
                'is_wajib'           => 1,
            ],
            [
                'kode_pertanyaan'    => 'TS25_NAMA_INSTANSI',
                'bagian'             => 'Informasi Data Pekerjaan Alumni',
                'urutan'             => 12,
                'teks_pertanyaan'    => 'Nama perusahaan / instansi',
                'keterangan_bantuan' => null,
                'tipe_jawaban'       => 'teks_singkat',
                'opsi_jawaban_json'  => self::encode([
                    'show_if'     => $showIfSudahBekerja,
                    'prefill_key' => 'instansi_saat_ini',
                ]),
                'is_wajib'           => 1,
            ],
            [
                'kode_pertanyaan'    => 'TS25_STATUS_KEPEGAWAIAN',
                'bagian'             => 'Informasi Data Pekerjaan Alumni',
                'urutan'             => 13,
                'teks_pertanyaan'    => 'Status kepegawaian Anda',
                'keterangan_bantuan' => null,
                'tipe_jawaban'       => 'pilihan_tunggal',
                'opsi_jawaban_json'  => self::encode([
                    'options'     => [
                        'CPNS / PNS',
                        'Pegawai Tetap',
                        'Pegawai Kontrak / Honorer',
                        'Pegawai Harian Lepas / Magang',
                    ],
                    'render_as'   => 'radio',
                    'allow_other' => true,
                    'show_if'     => $showIfSudahBekerja,
                ]),
                'is_wajib'           => 1,
            ],
            [
                'kode_pertanyaan'    => 'TS25_LEVEL_INSTANSI',
                'bagian'             => 'Informasi Data Pekerjaan Alumni',
                'urutan'             => 14,
                'teks_pertanyaan'    => 'Level perusahaan / instansi Anda',
                'keterangan_bantuan' => null,
                'tipe_jawaban'       => 'pilihan_tunggal',
                'opsi_jawaban_json'  => self::encode([
                    'options'   => [
                        'Lokal / Wilayah / Berwirausaha tidak berijin : Instansi, perusahaan, atau usaha yang lingkup kerjanya terbatas pada satu daerah, kota, atau provinsi saja, dan/atau usaha mandiri yang belum memiliki izin resmi (misalnya belum memiliki NIB/SIUP).',
                        'Nasional / Berwirausaha berijin : Instansi, perusahaan, atau usaha yang beroperasi secara nasional (di berbagai provinsi/kota di Indonesia), dan/atau usaha mandiri yang sudah memiliki izin resmi seperti NIB/SIUP.',
                        'Multinasional / Internasional : Perusahaan atau instansi yang beroperasi di lebih dari satu negara, baik milik negara maupun swasta, dan memiliki jaringan internasional.',
                    ],
                    'render_as' => 'radio',
                    'show_if'   => $showIfSudahBekerja,
                ]),
                'is_wajib'           => 1,
            ],
            [
                'kode_pertanyaan'    => 'TS25_JABATAN_PERTAMA',
                'bagian'             => 'Informasi Data Pekerjaan Alumni',
                'urutan'             => 15,
                'teks_pertanyaan'    => 'Nama Jabatan pekerjaan pertama',
                'keterangan_bantuan' => null,
                'tipe_jawaban'       => 'teks_singkat',
                'opsi_jawaban_json'  => self::encode([
                    'show_if'     => $showIfSudahBekerja,
                    'prefill_key' => 'pekerjaan_saat_ini',
                ]),
                'is_wajib'           => 1,
            ],
            [
                'kode_pertanyaan'    => 'TS25_KESESUAIAN_KOMPETENSI',
                'bagian'             => 'Informasi Data Pekerjaan Alumni',
                'urutan'             => 16,
                'teks_pertanyaan'    => 'Tingkat kesesuaian kompetensi yang didapat dari PKTJ dengan pekerjaan saat ini',
                'keterangan_bantuan' => null,
                'tipe_jawaban'       => 'pilihan_tunggal',
                'opsi_jawaban_json'  => self::encode([
                    'options'   => ['Rendah', 'Sedang', 'Tinggi'],
                    'render_as' => 'radio',
                    'show_if'   => $showIfSudahBekerja,
                ]),
                'is_wajib'           => 0,
            ],
            [
                'kode_pertanyaan'    => 'TS25_PENGHASILAN',
                'bagian'             => 'Informasi Data Pekerjaan Alumni',
                'urutan'             => 17,
                'teks_pertanyaan'    => 'Penghasilan rata-rata per-bulan',
                'keterangan_bantuan' => null,
                'tipe_jawaban'       => 'pilihan_tunggal',
                'opsi_jawaban_json'  => self::encode([
                    'options'   => [
                        '< 1,5 juta',
                        '1,5 juta s/d 3 juta',
                        '3 juta s/d 5 juta',
                        '> 5 juta',
                    ],
                    'render_as' => 'radio',
                    'show_if'   => $showIfSudahBekerja,
                ]),
                'is_wajib'           => 1,
            ],

            // Bagian 3: Alasan Belum Bekerja (Q28 PDF 2)
            [
                'kode_pertanyaan'    => 'TS25_ALASAN_BELUM_BEKERJA',
                'bagian'             => 'Alasan Belum Bekerja',
                'urutan'             => 18,
                'teks_pertanyaan'    => 'Tuliskan alasan Anda belum bekerja saat ini',
                'keterangan_bantuan' => null,
                'tipe_jawaban'       => 'teks_panjang',
                'opsi_jawaban_json'  => self::encode(['show_if' => $showIfBelumBekerja]),
                'is_wajib'           => 1,
            ],

            // Bagian 4: Alasan Tidak Bekerja (Q29 PDF 2)
            [
                'kode_pertanyaan'    => 'TS25_ALASAN_TIDAK_BEKERJA',
                'bagian'             => 'Alasan Tidak Bekerja',
                'urutan'             => 19,
                'teks_pertanyaan'    => 'Tuliskan alasan Anda tidak bekerja saat ini',
                'keterangan_bantuan' => null,
                'tipe_jawaban'       => 'teks_panjang',
                'opsi_jawaban_json'  => self::encode(['show_if' => $showIfTidakBekerja]),
                'is_wajib'           => 1,
            ],

            // Bagian 5: Saran Untuk Pengembangan Program Studi (Q30 - Q33 PDF 2)
            [
                'kode_pertanyaan'    => 'TS25_SARAN_TO',
                'bagian'             => 'Saran Untuk Pengembangan Program Studi',
                'urutan'             => 20,
                'teks_pertanyaan'    => 'Saran terhadap pengembangan keterampilan yang diperlukan untuk mendukung dalam pekerjaan, khususnya untuk Program Studi TO',
                'keterangan_bantuan' => 'contoh : public speaking, IT, dll',
                'tipe_jawaban'       => 'teks_panjang',
                'opsi_jawaban_json'  => null,
                'is_wajib'           => 0,
            ],
            [
                'kode_pertanyaan'    => 'TS25_SARAN_TRO',
                'bagian'             => 'Saran Untuk Pengembangan Program Studi',
                'urutan'             => 21,
                'teks_pertanyaan'    => 'Saran terhadap pengembangan keterampilan yang diperlukan untuk mendukung dalam pekerjaan, khususnya untuk Program Studi TRO',
                'keterangan_bantuan' => 'contoh : public speaking, IT, dll',
                'tipe_jawaban'       => 'teks_panjang',
                'opsi_jawaban_json'  => null,
                'is_wajib'           => 0,
            ],
            [
                'kode_pertanyaan'    => 'TS25_SARAN_RSTJ',
                'bagian'             => 'Saran Untuk Pengembangan Program Studi',
                'urutan'             => 22,
                'teks_pertanyaan'    => 'Saran terhadap pengembangan keterampilan yang diperlukan untuk mendukung dalam pekerjaan, khususnya untuk Program Studi RSTJ',
                'keterangan_bantuan' => 'contoh : public speaking, IT, dll',
                'tipe_jawaban'       => 'teks_panjang',
                'opsi_jawaban_json'  => null,
                'is_wajib'           => 0,
            ],
            [
                'kode_pertanyaan'    => 'TS25_SARAN_ALUMNI',
                'bagian'             => 'Saran Untuk Pengembangan Program Studi',
                'urutan'             => 23,
                'teks_pertanyaan'    => 'Saran terhadap pengelolaan alumni yang Anda harapkan dari Kampus PKTJ',
                'keterangan_bantuan' => null,
                'tipe_jawaban'       => 'teks_panjang',
                'opsi_jawaban_json'  => null,
                'is_wajib'           => 0,
            ],

            // Bagian 6: Data Pendidikan Lanjutan (Q34 PDF 2)
            [
                'kode_pertanyaan'    => 'TS25_LANJUT_STUDI',
                'bagian'             => 'Data Pendidikan Lanjutan',
                'urutan'             => 24,
                'teks_pertanyaan'    => 'Apakah anda melanjutkan pendidikan ke jenjang yang lebih tinggi setelah lulus dari PKTJ ?',
                'keterangan_bantuan' => null,
                'tipe_jawaban'       => 'pilihan_tunggal',
                'opsi_jawaban_json'  => self::encode([
                    'options'   => ['Ya', 'Tidak'],
                    'render_as' => 'radio',
                ]),
                'is_wajib'           => 0,
            ],

            // Bagian 7: Data Perguruan Tinggi Lanjutan dan Program Studi Lanjutan (Q35 - Q37 PDF 2)
            [
                'kode_pertanyaan'    => 'TS25_JENJANG_LANJUT',
                'bagian'             => 'Data Perguruan Tinggi Lanjutan dan Program Studi Lanjutan',
                'urutan'             => 25,
                'teks_pertanyaan'    => 'Jenjang pendidikan lanjutan',
                'keterangan_bantuan' => 'dapat diisi lebih dari 1 jenjang',
                'tipe_jawaban'       => 'pilihan_ganda',
                'opsi_jawaban_json'  => self::encode([
                    'options'       => ['D4', 'S1', 'S2', 'S3'],
                    'show_if'       => $showIfLanjutStudi,
                    'section_intro' => 'Isilah Nama Universitas dan Nama Program Studi sesuai dengan jenjang pendidikan terakhir Anda yang ditempuh setelah lulus dari PKTJ.',
                ]),
                'is_wajib'           => 1,
            ],
            [
                'kode_pertanyaan'    => 'TS25_NAMA_PT_LANJUT',
                'bagian'             => 'Data Perguruan Tinggi Lanjutan dan Program Studi Lanjutan',
                'urutan'             => 26,
                'teks_pertanyaan'    => 'Nama Universitas / Perguruan Tinggi',
                'keterangan_bantuan' => 'contoh : S1 Univ. Indonesia , S2 Univ Pancasakti',
                'tipe_jawaban'       => 'teks_singkat',
                'opsi_jawaban_json'  => self::encode(['show_if' => $showIfLanjutStudi]),
                'is_wajib'           => 1,
            ],
            [
                'kode_pertanyaan'    => 'TS25_NAMA_PRODI_LANJUT',
                'bagian'             => 'Data Perguruan Tinggi Lanjutan dan Program Studi Lanjutan',
                'urutan'             => 27,
                'teks_pertanyaan'    => 'Nama Program Studi',
                'keterangan_bantuan' => 'contoh : S1 Teknik Mesin , S2 Teknik Sipil',
                'tipe_jawaban'       => 'teks_singkat',
                'opsi_jawaban_json'  => self::encode(['show_if' => $showIfLanjutStudi]),
                'is_wajib'           => 1,
            ],
        ];

        return [
            'bundle' => [
                'kode_bundle'      => self::BUNDLE_ALUMNI_2025,
                'nama_bundle'      => 'Tracer Study Tahun 2025 untuk Lulusan PKTJ',
                'target_responden' => 'alumni',
                'deskripsi'        => $suratPengantar,
                'is_aktif'         => 1,
            ],
            'questions' => $questions,
        ];
    }

    /**
     * Bundel Resmi 2: Tracer Study Tahun 2025 untuk Pengguna Lulusan PKTJ (Mitra / Atasan Langsung).
     *
     * @return array{bundle:array<string,mixed>,questions:list<array<string,mixed>>}
     */
    public static function mitra2025Bundle(): array
    {
        $suratPengantar = implode("\n\n", [
            "Yth. Bapak/Ibu Pimpinan/Manajemen Instansi Pengguna Lulusan Politeknik Keselamatan Transportasi Jalan\ndi Tempat",
            'Dengan hormat,',
            'Dalam rangka meningkatkan kualitas dan relevansi pendidikan vokasi di Politeknik Keselamatan Transportasi Jalan (PKTJ), kami melaksanakan kegiatan tracer study sebagai bagian dari upaya evaluasi terhadap proses pendidikan yang telah dilaksanakan sejak awal berdirinya hingga saat ini.',
            'Kegiatan tracer study ini bertujuan untuk menghimpun informasi secara empiris mengenai profil lulusan, kesesuaian kompetensi dengan dunia kerja, serta masukan dari pihak pengguna lulusan. Hasil yang diperoleh akan menjadi dasar dalam mengidentifikasi kekuatan dan kelemahan pelaksanaan program studi, serta menjadi acuan dalam penyusunan rencana strategis pengembangan institusi ke depan.',
            'Bagi instansi pengguna lulusan, data dan informasi dari tracer study ini diharapkan dapat menjadi referensi dalam menilai kualitas lulusan PKTJ, sekaligus menjadi bahan pertimbangan dalam proses rekrutmen tenaga kerja pada masa mendatang.',
            'Kami mengucapkan terima kasih atas kesediaan dan partisipasi Bapak/Ibu dalam mengisi kuesioner tracer study ini. Semoga kontribusi yang diberikan dapat memberikan dampak positif terhadap peningkatan mutu pendidikan dan lulusan Politeknik Keselamatan Transportasi Jalan.',
            'Atas perhatian dan kerja sama yang baik, kami sampaikan terima kasih.',
            "Hormat kami,\nTIM TRACER STUDY 2025 Administrasi Ketarunaan dan Alumni, Politeknik Keselamatan Transportasi Jalan",
        ]);

        $introIdentitasLulusan = 'Pada bagian ini Bapak/Ibu Pimpinan/Manajemen sebagai Atasan Langsung dari para Lulusan PKTJ kami mohon untuk dapat menuliskan nama tiap Alumni yang akan diberikan penilaian. Apabila terdapat Alumni lebih dari 1 orang, maka pengisian formulir dapat dilakukan sebanyak jumlah Alumni yang ada. Terima kasih.';
        $introPenilaian        = 'Pada bagian ini Bapak/Ibu Pimpinan/Manajemen sebagai Atasan Langsung dari para Lulusan PKTJ kami mohon untuk dapat memberikan penilaian terhadap performance dari para Lulusan Program Studi ini dengan sebenar-benarnya dalam rangka proses peningkatan kualitas dan mutu pendidikan di PKTJ. Terima kasih.';

        $likert4 = self::encode([
            'min_label' => 'Kurang Baik',
            'max_label' => 'Sangat Baik',
            'max_scale' => 4,
        ]);

        $likert4First = self::encode([
            'min_label'     => 'Kurang Baik',
            'max_label'     => 'Sangat Baik',
            'max_scale'     => 4,
            'section_intro' => $introPenilaian,
        ]);

        $questions = [
            // Bagian 1: Identitas Atasan Langsung (Q1 - Q4 PDF 1)
            [
                'kode_pertanyaan'    => 'TSM25_NAMA_ATASAN',
                'bagian'             => 'Identitas Atasan Langsung',
                'urutan'             => 1,
                'teks_pertanyaan'    => 'Nama Anda (Atasan Langsung)',
                'keterangan_bantuan' => null,
                'tipe_jawaban'       => 'teks_singkat',
                'opsi_jawaban_json'  => null,
                'is_wajib'           => 1,
            ],
            [
                'kode_pertanyaan'    => 'TSM25_INSTANSI',
                'bagian'             => 'Identitas Atasan Langsung',
                'urutan'             => 2,
                'teks_pertanyaan'    => 'Nama perusahaan / instansi Anda',
                'keterangan_bantuan' => null,
                'tipe_jawaban'       => 'teks_singkat',
                'opsi_jawaban_json'  => null,
                'is_wajib'           => 1,
            ],
            [
                'kode_pertanyaan'    => 'TSM25_EMAIL',
                'bagian'             => 'Identitas Atasan Langsung',
                'urutan'             => 3,
                'teks_pertanyaan'    => 'Email aktif (atasan Langsung)',
                'keterangan_bantuan' => null,
                'tipe_jawaban'       => 'teks_singkat',
                'opsi_jawaban_json'  => self::encode(['input_type' => 'email']),
                'is_wajib'           => 1,
            ],
            [
                'kode_pertanyaan'    => 'TSM25_NO_HP',
                'bagian'             => 'Identitas Atasan Langsung',
                'urutan'             => 4,
                'teks_pertanyaan'    => 'No. Handphone (atasan langsung)',
                'keterangan_bantuan' => null,
                'tipe_jawaban'       => 'teks_singkat',
                'opsi_jawaban_json'  => self::encode(['input_type' => 'tel']),
                'is_wajib'           => 1,
            ],

            // Bagian 2: Identitas Lulusan PKTJ yang akan Dinilai (Q5 - Q6 PDF 1)
            [
                'kode_pertanyaan'    => 'TSM25_NAMA_ALUMNI',
                'bagian'             => 'Identitas Lulusan PKTJ yang akan Dinilai',
                'urutan'             => 5,
                'teks_pertanyaan'    => 'Nama Alumni PKTJ',
                'keterangan_bantuan' => null,
                'tipe_jawaban'       => 'teks_singkat',
                'opsi_jawaban_json'  => self::encode(['section_intro' => $introIdentitasLulusan]),
                'is_wajib'           => 1,
            ],
            [
                'kode_pertanyaan'    => 'TSM25_PRODI_ALUMNI',
                'bagian'             => 'Identitas Lulusan PKTJ yang akan Dinilai',
                'urutan'             => 6,
                'teks_pertanyaan'    => 'Alumni dari Program Studi',
                'keterangan_bantuan' => 'Pilihan ini menyesuaikan bagian penilaian sesuai Program Studi alumni.',
                'tipe_jawaban'       => 'pilihan_tunggal',
                'opsi_jawaban_json'  => self::encode([
                    'options'   => [
                        'Sarjana Terapan RSTJ',
                        'Sarjana Terapan TRO',
                        'Diploma III TO',
                    ],
                    'render_as' => 'radio',
                ]),
                'is_wajib'           => 1,
            ],

            // Bagian 3: Penilaian terhadap Alumni {PRODI_LENGKAP} (Q7-16 / Q17-26 / Q27-36 PDF 1 — Skala 1 - 4)
            [
                'kode_pertanyaan'    => 'TSM25_ETIKA',
                'bagian'             => 'Penilaian terhadap Alumni {PRODI_LENGKAP}',
                'urutan'             => 7,
                'teks_pertanyaan'    => 'Bagaimana Etika Alumni {PRODI} dalam bekerja ?',
                'keterangan_bantuan' => 'Skala 1 (Kurang Baik) sampai 4 (Sangat Baik).',
                'tipe_jawaban'       => 'skala_likert',
                'opsi_jawaban_json'  => $likert4First,
                'is_wajib'           => 1,
            ],
            [
                'kode_pertanyaan'    => 'TSM25_KEAHLIAN',
                'bagian'             => 'Penilaian terhadap Alumni {PRODI_LENGKAP}',
                'urutan'             => 8,
                'teks_pertanyaan'    => 'Bagaimana Keahlian bidang ilmu (kompetensi utama) Alumni {PRODI} dalam bekerja ?',
                'keterangan_bantuan' => 'Skala 1 (Kurang Baik) sampai 4 (Sangat Baik).',
                'tipe_jawaban'       => 'skala_likert',
                'opsi_jawaban_json'  => $likert4,
                'is_wajib'           => 0,
            ],
            [
                'kode_pertanyaan'    => 'TSM25_BAHASA_ASING',
                'bagian'             => 'Penilaian terhadap Alumni {PRODI_LENGKAP}',
                'urutan'             => 9,
                'teks_pertanyaan'    => 'Bagaimana Kemampuan berbahasa asing Alumni {PRODI} dalam bekerja ?',
                'keterangan_bantuan' => 'Skala 1 (Kurang Baik) sampai 4 (Sangat Baik).',
                'tipe_jawaban'       => 'skala_likert',
                'opsi_jawaban_json'  => $likert4,
                'is_wajib'           => 1,
            ],
            [
                'kode_pertanyaan'    => 'TSM25_TEKNOLOGI_INFO',
                'bagian'             => 'Penilaian terhadap Alumni {PRODI_LENGKAP}',
                'urutan'             => 10,
                'teks_pertanyaan'    => 'Bagaimana Kemampuan penggunaan teknologi informasi Alumni {PRODI} dalam bekerja ?',
                'keterangan_bantuan' => 'Skala 1 (Kurang Baik) sampai 4 (Sangat Baik).',
                'tipe_jawaban'       => 'skala_likert',
                'opsi_jawaban_json'  => $likert4,
                'is_wajib'           => 0,
            ],
            [
                'kode_pertanyaan'    => 'TSM25_KOMUNIKASI',
                'bagian'             => 'Penilaian terhadap Alumni {PRODI_LENGKAP}',
                'urutan'             => 11,
                'teks_pertanyaan'    => 'Bagaimana Kemampuan komunikasi Alumni {PRODI} dalam bekerja ?',
                'keterangan_bantuan' => 'Skala 1 (Kurang Baik) sampai 4 (Sangat Baik).',
                'tipe_jawaban'       => 'skala_likert',
                'opsi_jawaban_json'  => $likert4,
                'is_wajib'           => 1,
            ],
            [
                'kode_pertanyaan'    => 'TSM25_KERJASAMA',
                'bagian'             => 'Penilaian terhadap Alumni {PRODI_LENGKAP}',
                'urutan'             => 12,
                'teks_pertanyaan'    => 'Bagaimana Kemampuan kerjasama Alumni {PRODI} dalam bekerja ?',
                'keterangan_bantuan' => 'Skala 1 (Kurang Baik) sampai 4 (Sangat Baik).',
                'tipe_jawaban'       => 'skala_likert',
                'opsi_jawaban_json'  => $likert4,
                'is_wajib'           => 1,
            ],
            [
                'kode_pertanyaan'    => 'TSM25_PENGEMBANGAN_DIRI',
                'bagian'             => 'Penilaian terhadap Alumni {PRODI_LENGKAP}',
                'urutan'             => 13,
                'teks_pertanyaan'    => 'Bagaimana kemampuan pengembangan diri Alumni {PRODI} ?',
                'keterangan_bantuan' => 'Skala 1 (Kurang Baik) sampai 4 (Sangat Baik).',
                'tipe_jawaban'       => 'skala_likert',
                'opsi_jawaban_json'  => $likert4,
                'is_wajib'           => 1,
            ],
            [
                'kode_pertanyaan'    => 'TSM25_KEDISIPLINAN',
                'bagian'             => 'Penilaian terhadap Alumni {PRODI_LENGKAP}',
                'urutan'             => 14,
                'teks_pertanyaan'    => 'Bagaimana Tingkat Kedisiplinan Alumni {PRODI} dalam bekerja ?',
                'keterangan_bantuan' => 'Skala 1 (Kurang Baik) sampai 4 (Sangat Baik).',
                'tipe_jawaban'       => 'skala_likert',
                'opsi_jawaban_json'  => $likert4,
                'is_wajib'           => 1,
            ],
            [
                'kode_pertanyaan'    => 'TSM25_KREATIFITAS',
                'bagian'             => 'Penilaian terhadap Alumni {PRODI_LENGKAP}',
                'urutan'             => 15,
                'teks_pertanyaan'    => 'Bagaimana Tingkat Kreatifitas Alumni {PRODI} dalam bekerja ?',
                'keterangan_bantuan' => 'Skala 1 (Kurang Baik) sampai 4 (Sangat Baik).',
                'tipe_jawaban'       => 'skala_likert',
                'opsi_jawaban_json'  => $likert4,
                'is_wajib'           => 1,
            ],
            [
                'kode_pertanyaan'    => 'TSM25_SARAN_PRODI',
                'bagian'             => 'Penilaian terhadap Alumni {PRODI_LENGKAP}',
                'urutan'             => 16,
                'teks_pertanyaan'    => 'Saran untuk pengembangan profil Lulusan {PRODI_LENGKAP} terkait kompetensi yang diperlukan dalam bekerja',
                'keterangan_bantuan' => 'contoh : kompetensi terkait ms office, kompetensi terkait gambar teknik, dll',
                'tipe_jawaban'       => 'teks_panjang',
                'opsi_jawaban_json'  => null,
                'is_wajib'           => 0,
            ],
        ];

        return [
            'bundle' => [
                'kode_bundle'      => self::BUNDLE_MITRA_2025,
                'nama_bundle'      => 'Tracer Study Tahun 2025 untuk Pengguna Lulusan PKTJ',
                'target_responden' => 'mitra',
                'deskripsi'        => $suratPengantar,
                'is_aktif'         => 1,
            ],
            'questions' => $questions,
        ];
    }

    /**
     * @param array<string, mixed> $data
     */
    private static function encode(array $data): string
    {
        return (string) json_encode($data, JSON_UNESCAPED_UNICODE);
    }
}
