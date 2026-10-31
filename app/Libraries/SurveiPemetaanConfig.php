<?php

namespace App\Libraries;

class SurveiPemetaanConfig
{
    public const LIKERT_AGREEMENT = [
        1 => 'Sangat tidak setuju',
        2 => 'Tidak setuju',
        3 => 'Netral',
        4 => 'Setuju',
        5 => 'Sangat setuju',
    ];

    public const PANGKAT_GOLONGAN_SUGGESTIONS = [
        'Juru Muda (I/a)',
        'Juru Muda Tingkat I (I/b)',
        'Juru (I/c)',
        'Juru Tingkat I (I/d)',
        'Pengatur Muda (II/a)',
        'Pengatur Muda Tingkat I (II/b)',
        'Pengatur (II/c)',
        'Pengatur Tingkat I (II/d)',
        'Penata Muda (III/a)',
        'Penata Muda Tingkat I (III/b)',
        'Penata (III/c)',
        'Penata Tingkat I (III/d)',
        'Pembina (IV/a)',
        'Pembina Tingkat I (IV/b)',
        'Pembina Utama Muda (IV/c)',
        'Pembina Utama Madya (IV/d)',
        'Pembina Utama (IV/e)',
        'PPPK Golongan V',
        'PPPK Golongan VI',
        'PPPK Golongan VII',
        'PPPK Golongan VIII',
        'PPPK Golongan IX',
        'PPPK Golongan X',
        'PPPK Golongan XI',
    ];

    /**
     * Daftar 10 Bagian Survei Pemetaan SDM PKTJ.
     */
    public static function getSections(): array
    {
        return [
            1 => [
                'step'        => 1,
                'code'        => 'BAGIAN_1',
                'roman'       => 'I',
                'short_title' => 'Identitas & Profil',
                'title'       => 'BAGIAN I. IDENTITAS DAN PROFIL PEGAWAI',
                'subtitle'    => 'Membangun data profil dasar pegawai.',
                'questions'   => range(1, 14),
            ],
            2 => [
                'step'        => 2,
                'code'        => 'BAGIAN_2',
                'roman'       => 'II',
                'short_title' => 'Domisili & Mobilitas',
                'title'       => 'BAGIAN II. DOMISILI DAN AKSESIBILITAS TEMPAT KERJA',
                'subtitle'    => 'Mengetahui kondisi mobilitas pegawai dan potensi pengaturan penugasan.',
                'questions'   => range(15, 19),
            ],
            3 => [
                'step'        => 3,
                'code'        => 'BAGIAN_3',
                'roman'       => 'III',
                'short_title' => 'Kompetensi & Keahlian',
                'title'       => 'BAGIAN III. KOMPETENSI DAN KEAHLIAN PEGAWAI',
                'subtitle'    => 'Mengetahui kompetensi utama dan keahlian tambahan yang dapat dimanfaatkan organisasi.',
                'questions'   => range(20, 26),
            ],
            4 => [
                'step'        => 4,
                'code'        => 'BAGIAN_4',
                'roman'       => 'IV',
                'short_title' => 'Kerja Lintas Unit',
                'title'       => 'BAGIAN IV. KEMAMPUAN DAN KESEDIAAN BEKERJA LINTAS UNIT',
                'subtitle'    => 'Mengetahui kesediaan dan kapasitas pegawai membantu unit kerja lain saat dibutuhkan.',
                'questions'   => range(27, 35),
            ],
            5 => [
                'step'        => 5,
                'code'        => 'BAGIAN_5',
                'roman'       => 'V',
                'short_title' => 'Diklat & Pengembangan',
                'title'       => 'BAGIAN V. KESEDIAAN MENGIKUTI PENDIDIKAN DAN PENGEMBANGAN KOMPETENSI',
                'subtitle'    => 'Mengidentifikasi kemauan berkembang dan potensi penguasaan kompetensi baru.',
                'questions'   => range(36, 42),
            ],
            6 => [
                'step'        => 6,
                'code'        => 'BAGIAN_6',
                'roman'       => 'VI',
                'short_title' => 'Motivasi & Perilaku',
                'title'       => 'BAGIAN VI. MOTIVASI, INISIATIF, DAN PERILAKU KERJA',
                'subtitle'    => 'Mengetahui kecenderungan perilaku kerja, pendorong motivasi, dan hambatan produktivitas.',
                'questions'   => range(43, 58),
            ],
            7 => [
                'step'        => 7,
                'code'        => 'BAGIAN_7',
                'roman'       => 'VII',
                'short_title' => 'Pendapatan BLU',
                'title'       => 'BAGIAN VII. KESIAPAN MENDUKUNG PENINGKATAN PENDAPATAN BLU PKTJ',
                'subtitle'    => 'Memetakan kesiapan kontribusi pegawai dalam kegiatan layanan BLU PKTJ.',
                'questions'   => range(59, 67),
            ],
            8 => [
                'step'        => 8,
                'code'        => 'BAGIAN_8',
                'roman'       => 'VIII',
                'short_title' => 'Beban & Kapasitas',
                'title'       => 'BAGIAN VIII. KAPASITAS KERJA DAN KESIAPAN MENERIMA TANGGUNG JAWAB TAMBAHAN',
                'subtitle'    => 'Memahami kondisi beban kerja utama dan kebutuhan dukungan kerja pegawai.',
                'questions'   => range(68, 72),
            ],
            9 => [
                'step'        => 9,
                'code'        => 'BAGIAN_9',
                'roman'       => 'IX',
                'short_title' => 'Kinerja & Penghargaan',
                'title'       => 'BAGIAN IX. PERSEPSI TERHADAP SISTEM KINERJA DAN PENGHARGAAN',
                'subtitle'    => 'Mengetahui pandangan pegawai terhadap sistem penilaian kinerja dan penghargaan.',
                'questions'   => range(73, 77),
            ],
            10 => [
                'step'        => 10,
                'code'        => 'BAGIAN_10',
                'roman'       => 'X',
                'short_title' => 'Evaluasi Diri & Saran',
                'title'       => 'BAGIAN X. EVALUASI DIRI DAN KOMITMEN PENGEMBANGAN',
                'subtitle'    => 'Menyampaikan kekuatan utama, rencana pengembangan diri, serta masukan bagi pimpinan PKTJ.',
                'questions'   => range(78, 83),
            ],
        ];
    }

    /**
     * Daftar lengkap 83 Butir Pertanyaan Survei Pemetaan SDM PKTJ (Sumber Kebenaran Tunggal).
     */
    public static function getQuestions(): array
    {
        static $questions = null;
        if ($questions !== null) {
            return $questions;
        }

        $likertStatements = [
            43 => 'Saya berusaha menyelesaikan pekerjaan dengan hasil terbaik meskipun tidak selalu diawasi oleh atasan.',
            44 => 'Saya memiliki inisiatif untuk mencari solusi ketika menghadapi permasalahan pekerjaan.',
            45 => 'Saya bersedia mempelajari hal baru untuk meningkatkan kemampuan kerja.',
            46 => 'Saya memiliki keinginan untuk memberikan kontribusi lebih besar bagi kemajuan PKTJ.',
            47 => 'Saya bersedia membantu rekan kerja yang mengalami kesulitan dalam menyelesaikan pekerjaan.',
            48 => 'Saya mampu menyesuaikan diri ketika organisasi mengalami perubahan kebijakan atau sistem kerja.',
            49 => 'Saya berusaha menyelesaikan pekerjaan sesuai target dan batas waktu yang ditentukan.',
            50 => 'Saya tertarik terlibat dalam kegiatan inovasi yang dapat meningkatkan kualitas pelayanan PKTJ.',
            51 => 'Saya memahami bahwa keberhasilan organisasi merupakan tanggung jawab seluruh pegawai.',
            52 => 'Saya bersedia menerima tanggung jawab baru apabila organisasi membutuhkan kemampuan saya.',
            53 => 'Saya berusaha mencari cara agar pekerjaan dapat dilaksanakan lebih efektif dan efisien.',
            54 => 'Saya memiliki motivasi untuk menghasilkan pekerjaan yang memberikan manfaat nyata bagi organisasi.',
            55 => 'Saya terbuka terhadap evaluasi dan masukan untuk memperbaiki hasil pekerjaan saya.',
        ];

        $q = [
            // ================= BAGIAN I (1 - 14) =================
            1 => [
                'no'            => 1,
                'step'          => 1,
                'section'       => 'BAGIAN_1',
                'label'         => 'Nama lengkap',
                'type'          => 'text',
                'profile_field' => 'nama_lengkap',
                'max_length'    => 120,
                'placeholder'   => 'Tuliskan nama lengkap beserta gelar',
                'helper'        => 'Dapat disesuaikan untuk melengkapi penulisan gelar.',
            ],
            2 => [
                'no'            => 2,
                'step'          => 1,
                'section'       => 'BAGIAN_1',
                'label'         => 'NIP pegawai',
                'type'          => 'identitas',
                'profile_field' => 'nip',
                'max_length'    => 18,
                'helper'        => 'Terisi otomatis sesuai 18 digit NIP akun pegawai Anda.',
            ],
            3 => [
                'no'      => 3,
                'step'    => 1,
                'section' => 'BAGIAN_1',
                'label'   => 'Jenis kelamin',
                'type'    => 'radio',
                'options' => ['Laki-laki', 'Perempuan'],
            ],
            4 => [
                'no'          => 4,
                'step'        => 1,
                'section'     => 'BAGIAN_1',
                'label'       => 'Usia',
                'type'        => 'number',
                'min'         => 17,
                'max'         => 75,
                'unit_suffix' => 'Tahun',
                'placeholder' => 'Contoh: 32',
            ],
            5 => [
                'no'      => 5,
                'step'    => 1,
                'section' => 'BAGIAN_1',
                'label'   => 'Jenis kepegawaian',
                'type'    => 'radio',
                'options' => ['PNS', 'PPPK'],
            ],
            6 => [
                'no'            => 6,
                'step'          => 1,
                'section'       => 'BAGIAN_1',
                'label'         => 'Pangkat dan golongan',
                'type'          => 'datalist',
                'profile_field' => 'pangkat',
                'max_length'    => 100,
                'suggestions'   => self::PANGKAT_GOLONGAN_SUGGESTIONS,
                'placeholder'   => 'Contoh: Penata Muda (III/a) atau PPPK Golongan IX',
                'helper'        => 'Wajib diisi bagi PNS/PPPK.',
            ],
            7 => [
                'no'            => 7,
                'step'          => 1,
                'section'       => 'BAGIAN_1',
                'label'         => 'Jabatan saat ini',
                'type'          => 'text',
                'profile_field' => 'jabatan',
                'max_length'    => 120,
                'placeholder'   => 'Contoh: Pranata Komputer Ahli Pertama / Pengolah Data dan Informasi',
            ],
            8 => [
                'no'            => 8,
                'step'          => 1,
                'section'       => 'BAGIAN_1',
                'label'         => 'Unit kerja saat ini',
                'type'          => 'unit_select',
                'profile_field' => 'unit',
            ],
            9 => [
                'no'          => 9,
                'step'        => 1,
                'section'     => 'BAGIAN_1',
                'label'       => 'Tanggal mulai bekerja di PKTJ',
                'type'        => 'date',
                'placeholder' => 'Pilih tanggal mulai bekerja di PKTJ',
            ],
            10 => [
                'no'      => 10,
                'step'    => 1,
                'section' => 'BAGIAN_1',
                'label'   => 'Masa kerja di PKTJ',
                'type'    => 'auto_tenure',
                'options' => [
                    'Kurang dari 1 tahun',
                    '1–3 tahun',
                    '4–6 tahun',
                    '7–10 tahun',
                    'Lebih dari 10 tahun',
                ],
                'helper'  => 'Dihitung otomatis dari Tanggal Mulai Bekerja pada Nomor 9.',
            ],
            11 => [
                'no'      => 11,
                'step'    => 1,
                'section' => 'BAGIAN_1',
                'label'   => 'Pendidikan terakhir',
                'type'    => 'radio',
                'options' => ['SMA/SMK', 'D3', 'D4', 'S1', 'S2', 'S3'],
            ],
            12 => [
                'no'          => 12,
                'step'        => 1,
                'section'     => 'BAGIAN_1',
                'label'       => 'Program studi/jurusan pendidikan terakhir',
                'type'        => 'text',
                'max_length'  => 150,
                'placeholder' => 'Contoh: Transportasi / Teknik Sipil / Manajemen',
            ],
            13 => [
                'no'      => 13,
                'step'    => 1,
                'section' => 'BAGIAN_1',
                'label'   => 'Apakah memiliki sertifikat profesi atau sertifikasi kompetensi?',
                'type'    => 'radio',
                'options' => ['Ya', 'Tidak'],
            ],
            14 => [
                'no'          => 14,
                'step'        => 1,
                'section'     => 'BAGIAN_1',
                'label'       => 'Jika ya, sebutkan sertifikasi yang dimiliki',
                'type'        => 'textarea',
                'conditional' => 'q13_yes',
                'placeholder' => 'Tuliskan nama sertifikat profesi atau sertifikasi kompetensi yang dimiliki',
            ],

            // ================= BAGIAN II (15 - 19) =================
            15 => [
                'no'          => 15,
                'step'        => 2,
                'section'     => 'BAGIAN_2',
                'label'       => 'Di kecamatan/kota mana tempat tinggal Anda saat ini?',
                'type'        => 'text',
                'max_length'  => 150,
                'placeholder' => 'Contoh: Margadana, Kota Tegal',
                'helper'      => 'Cukup tuliskan kecamatan dan kota/kabupaten, tanpa alamat lengkap rumah.',
            ],
            16 => [
                'no'      => 16,
                'step'    => 2,
                'section' => 'BAGIAN_2',
                'label'   => 'Berapa perkiraan jarak tempat tinggal ke kantor PKTJ?',
                'type'    => 'radio',
                'options' => [
                    'Kurang dari 5 km',
                    '5–10 km',
                    '11–20 km',
                    '21–30 km',
                    'Lebih dari 30 km',
                ],
            ],
            17 => [
                'no'      => 17,
                'step'    => 2,
                'section' => 'BAGIAN_2',
                'label'   => 'Berapa rata-rata waktu perjalanan dari rumah ke kantor?',
                'type'    => 'radio',
                'options' => [
                    'Kurang dari 15 menit',
                    '15–30 menit',
                    '31–60 menit',
                    '1–2 jam',
                    'Lebih dari 2 jam',
                ],
            ],
            18 => [
                'no'          => 18,
                'step'        => 2,
                'section'     => 'BAGIAN_2',
                'label'       => 'Apa moda transportasi utama yang digunakan?',
                'type'        => 'radio',
                'has_other'   => true,
                'other_value' => 'Lainnya',
                'options'     => [
                    'Kendaraan pribadi',
                    'Transportasi umum',
                    'Kendaraan dinas',
                    'Lainnya',
                ],
            ],
            19 => [
                'no'                 => 19,
                'step'               => 2,
                'section'            => 'BAGIAN_2',
                'label'              => 'Apakah Anda memiliki keterbatasan mobilitas yang memengaruhi pelaksanaan tugas kedinasan tertentu?',
                'type'               => 'radio_optional_note',
                'note_trigger_value' => 'Ya, jelaskan secara opsional',
                'note_placeholder'   => 'Keterangan tambahan (opsional, boleh dikosongkan)',
                'options'            => [
                    'Tidak',
                    'Ya, jelaskan secara opsional',
                ],
            ],

            // ================= BAGIAN III (20 - 26) =================
            20 => [
                'no'          => 20,
                'step'        => 3,
                'section'     => 'BAGIAN_3',
                'label'       => 'Apa bidang kompetensi utama yang Anda kuasai saat ini?',
                'type'        => 'checkbox',
                'has_other'   => true,
                'other_value' => 'Lainnya',
                'helper'      => 'Boleh memilih lebih dari satu jawaban.',
                'options'     => [
                    'Administrasi perkantoran',
                    'Pengelolaan keuangan dan anggaran',
                    'Akuntansi dan pelaporan keuangan',
                    'Pengelolaan kepegawaian dan SDM',
                    'Perencanaan dan evaluasi program',
                    'Pengadaan barang dan jasa',
                    'Teknologi informasi dan komputer',
                    'Pemrograman dan pengembangan aplikasi',
                    'Pengelolaan jaringan dan database',
                    'Pendidikan dan pengajaran',
                    'Penelitian dan pengembangan',
                    'Keselamatan transportasi jalan',
                    'Pengujian kendaraan bermotor',
                    'Pelatihan dan sertifikasi',
                    'Pengelolaan sarana dan prasarana',
                    'Humas dan komunikasi publik',
                    'Pemasaran dan promosi',
                    'Pengelolaan bisnis dan kerja sama',
                    'Pelayanan masyarakat',
                    'Lainnya',
                ],
            ],
            21 => [
                'no'      => 21,
                'step'    => 3,
                'section' => 'BAGIAN_3',
                'label'   => 'Selain kompetensi utama pekerjaan Anda, apakah Anda memiliki keahlian tambahan yang dapat dimanfaatkan untuk mendukung pekerjaan unit lain?',
                'type'    => 'radio',
                'options' => [
                    'Ya, memiliki beberapa keahlian tambahan',
                    'Ya, memiliki satu keahlian tambahan',
                    'Belum memiliki keahlian tambahan',
                    'Belum mengetahui keahlian lain yang dapat dimanfaatkan',
                ],
            ],
            22 => [
                'no'          => 22,
                'step'        => 3,
                'section'     => 'BAGIAN_3',
                'label'       => 'Jika memiliki keahlian tambahan, sebutkan keahlian tersebut.',
                'type'        => 'textarea',
                'conditional' => 'q21_has_skill',
                'helper'      => 'Contoh: desain grafis, fotografi, videografi, public speaking, bahasa Inggris, bahasa asing, pengelolaan media sosial, Microsoft Excel tingkat lanjut, analisis data, pemrograman, digital marketing, penulisan laporan, dsb.',
                'placeholder' => 'Tuliskan keahlian tambahan yang Anda miliki',
            ],
            23 => [
                'no'           => 23,
                'step'         => 3,
                'section'      => 'BAGIAN_3',
                'label'        => 'Seberapa baik tingkat penguasaan Anda terhadap keahlian tambahan tersebut?',
                'type'         => 'scale_custom',
                'conditional'  => 'q21_has_skill',
                'scale_labels' => [
                    1 => 'Mengenal, tetapi belum mampu melaksanakan',
                    2 => 'Memahami dasar-dasarnya',
                    3 => 'Mampu melaksanakan dengan bimbingan',
                    4 => 'Mampu bekerja secara mandiri',
                    5 => 'Sangat menguasai dan mampu membimbing orang lain',
                ],
            ],
            24 => [
                'no'          => 24,
                'step'        => 3,
                'section'     => 'BAGIAN_3',
                'label'       => 'Apakah Anda memiliki pengalaman menggunakan keahlian tambahan tersebut untuk pekerjaan atau menghasilkan suatu produk/jasa?',
                'type'        => 'radio',
                'conditional' => 'q21_has_skill',
                'options'     => [
                    'Belum pernah',
                    'Pernah untuk kebutuhan pribadi',
                    'Pernah membantu pekerjaan kantor',
                    'Pernah mendapatkan penugasan resmi',
                    'Pernah menghasilkan produk atau layanan yang memberikan nilai tambah bagi organisasi',
                ],
            ],
            25 => [
                'no'          => 25,
                'step'        => 3,
                'section'     => 'BAGIAN_3',
                'label'       => 'Apakah Anda memiliki portofolio, sertifikat, atau bukti hasil pekerjaan yang menunjukkan keahlian tersebut?',
                'type'        => 'radio',
                'conditional' => 'q21_has_skill',
                'options'     => [
                    'Ada',
                    'Tidak ada',
                    'Sedang dalam proses pengumpulan',
                ],
            ],
            26 => [
                'no'          => 26,
                'step'        => 3,
                'section'     => 'BAGIAN_3',
                'label'       => 'Keahlian apa yang menurut Anda perlu dikembangkan agar dapat memberikan kontribusi lebih besar kepada PKTJ?',
                'type'        => 'textarea',
                'placeholder' => 'Tuliskan keahlian yang menurut Anda perlu dikembangkan',
            ],

            // ================= BAGIAN IV (27 - 35) =================
            27 => [
                'no'      => 27,
                'step'    => 4,
                'section' => 'BAGIAN_4',
                'label'   => 'Apakah Anda bersedia membantu pekerjaan pada unit kerja lain di luar tugas utama Anda apabila dibutuhkan oleh organisasi?',
                'type'    => 'radio',
                'options' => [
                    'Sangat bersedia',
                    'Bersedia',
                    'Bersedia dengan mempertimbangkan jenis pekerjaan dan beban kerja',
                    'Kurang bersedia',
                    'Tidak bersedia',
                ],
            ],
            28 => [
                'no'          => 28,
                'step'        => 4,
                'section'     => 'BAGIAN_4',
                'label'       => 'Apabila Anda bersedia membantu unit lain, jenis pekerjaan seperti apa yang dapat Anda lakukan?',
                'type'        => 'checkbox',
                'conditional' => 'q27_willing',
                'has_other'   => true,
                'other_value' => 'Lainnya',
                'helper'      => 'Boleh memilih lebih dari satu jawaban.',
                'options'     => [
                    'Pekerjaan administrasi dan pengarsipan',
                    'Pengolahan data dan penyusunan laporan',
                    'Pengelolaan keuangan dan administrasi anggaran',
                    'Pelayanan pendidikan dan pelatihan',
                    'Kegiatan operasional lapangan',
                    'Kegiatan pemasaran dan promosi layanan BLU',
                    'Kegiatan pengelolaan usaha dan kerja sama',
                    'Kegiatan penelitian dan pengembangan',
                    'Pengelolaan teknologi informasi',
                    'Kegiatan pendukung akademik dan kemahasiswaan',
                    'Kegiatan pelayanan umum dan rumah tangga',
                    'Lainnya',
                ],
            ],
            29 => [
                'no'      => 29,
                'step'    => 4,
                'section' => 'BAGIAN_4',
                'label'   => 'Apakah Anda pernah membantu pekerjaan unit lain di luar tupoksi selama bekerja di PKTJ?',
                'type'    => 'radio',
                'options' => [
                    'Sering',
                    'Beberapa kali',
                    'Pernah satu atau dua kali',
                    'Belum pernah',
                ],
            ],
            30 => [
                'no'          => 30,
                'step'        => 4,
                'section'     => 'BAGIAN_4',
                'label'       => 'Jika pernah, pekerjaan apa yang pernah Anda lakukan dan unit mana yang Anda bantu?',
                'type'        => 'textarea',
                'conditional' => 'q29_ever',
                'placeholder' => 'Tuliskan jenis pekerjaan dan unit kerja yang pernah Anda bantu',
            ],
            31 => [
                'no'                  => 31,
                'step'                => 4,
                'section'             => 'BAGIAN_4',
                'label'               => 'Apa pertimbangan utama Anda apabila diminta membantu pekerjaan di luar tupoksi?',
                'type'                => 'checkbox',
                'max_select'          => 3,
                'has_other'           => true,
                'other_value'         => 'Lainnya',
                'exclusive_option'    => 'Tidak tertarik melakukan pekerjaan di luar tupoksi',
                'exclusive_conflicts' => [
                    'Ingin membantu organisasi mencapai target',
                    'Ingin menambah pengalaman dan kompetensi',
                    'Ingin memperoleh tantangan pekerjaan baru',
                    'Ingin mendapatkan tambahan penghasilan atau penghargaan sesuai ketentuan',
                    'Bersedia sepanjang tidak mengganggu tugas utama',
                    'Bersedia sepanjang mendapatkan penugasan yang jelas',
                    'Bersedia jika pekerjaan sesuai dengan keahlian',
                ],
                'helper'              => 'Pilih maksimal 3 jawaban.',
                'options'             => [
                    'Ingin membantu organisasi mencapai target',
                    'Ingin menambah pengalaman dan kompetensi',
                    'Ingin memperoleh tantangan pekerjaan baru',
                    'Ingin mendapatkan tambahan penghasilan atau penghargaan sesuai ketentuan',
                    'Bersedia sepanjang tidak mengganggu tugas utama',
                    'Bersedia sepanjang mendapatkan penugasan yang jelas',
                    'Bersedia jika pekerjaan sesuai dengan keahlian',
                    'Mempertimbangkan kondisi keluarga dan waktu pribadi',
                    'Mempertimbangkan beban pekerjaan saat ini',
                    'Tidak tertarik melakukan pekerjaan di luar tupoksi',
                    'Lainnya',
                ],
            ],
            32 => [
                'no'      => 32,
                'step'    => 4,
                'section' => 'BAGIAN_4',
                'label'   => 'Apabila unit kerja lain membutuhkan bantuan Anda, berapa besar kapasitas waktu yang secara realistis dapat Anda alokasikan untuk membantu pekerjaan tersebut dalam jam kerja normal?',
                'type'    => 'radio',
                'options' => [
                    'Kurang dari 2 jam per minggu',
                    '2–4 jam per minggu',
                    '5–8 jam per minggu',
                    'Lebih dari 8 jam per minggu',
                    'Belum dapat menentukan karena tergantung beban pekerjaan',
                ],
            ],
            33 => [
                'no'      => 33,
                'step'    => 4,
                'section' => 'BAGIAN_4',
                'label'   => 'Apakah Anda bersedia diberikan penugasan tambahan di luar jam kerja normal apabila terdapat kegiatan kedinasan yang mendesak?',
                'type'    => 'radio',
                'options' => [
                    'Sangat bersedia, sesuai ketentuan',
                    'Bersedia dengan pemberitahuan sebelumnya',
                    'Bersedia dengan mempertimbangkan jenis dan durasi pekerjaan',
                    'Hanya dalam kondisi tertentu',
                    'Tidak bersedia',
                ],
            ],
            34 => [
                'no'          => 34,
                'step'        => 4,
                'section'     => 'BAGIAN_4',
                'label'       => 'Apa faktor yang paling memengaruhi kesediaan Anda melaksanakan pekerjaan tambahan di luar jam kerja?',
                'type'        => 'checkbox',
                'has_other'   => true,
                'other_value' => 'Lainnya',
                'helper'      => 'Boleh memilih lebih dari satu jawaban.',
                'options'     => [
                    'Kebutuhan organisasi',
                    'Kejelasan surat tugas dan tanggung jawab',
                    'Kompensasi atau penghargaan sesuai ketentuan',
                    'Ketersediaan waktu pribadi',
                    'Kondisi keluarga',
                    'Kondisi kesehatan atau kemampuan fisik',
                    'Jenis dan tingkat kesulitan pekerjaan',
                    'Durasi dan frekuensi pekerjaan tambahan',
                    'Lainnya',
                ],
            ],
            35 => [
                'no'      => 35,
                'step'    => 4,
                'section' => 'BAGIAN_4',
                'label'   => 'Jika organisasi memberikan kesempatan kepada Anda untuk membantu unit yang kekurangan SDM, apakah Anda bersedia mengikuti mekanisme penugasan lintas unit secara berkala?',
                'type'    => 'radio',
                'options' => [
                    'Ya, sangat bersedia',
                    'Ya, dengan mempertimbangkan kompetensi dan kapasitas',
                    'Bersedia apabila ada kebutuhan mendesak',
                    'Tidak bersedia',
                ],
            ],

            // ================= BAGIAN V (36 - 42) =================
            36 => [
                'no'      => 36,
                'step'    => 5,
                'section' => 'BAGIAN_5',
                'label'   => 'Apakah Anda bersedia mengikuti pendidikan, pelatihan, atau sertifikasi untuk menguasai kompetensi baru di luar bidang pekerjaan Anda saat ini?',
                'type'    => 'radio',
                'options' => [
                    'Sangat bersedia',
                    'Bersedia',
                    'Bersedia apabila kompetensi tersebut dibutuhkan organisasi',
                    'Kurang bersedia',
                    'Tidak bersedia',
                ],
            ],
            37 => [
                'no'          => 37,
                'step'        => 5,
                'section'     => 'BAGIAN_5',
                'label'       => 'Jika diberikan kesempatan mengikuti pelatihan, bidang apa yang paling ingin Anda pelajari?',
                'type'        => 'checkbox',
                'has_other'   => true,
                'other_value' => 'Lainnya',
                'helper'      => 'Boleh memilih lebih dari satu jawaban.',
                'options'     => [
                    'Teknologi informasi dan kecerdasan buatan (AI)',
                    'Pengolahan data dan analisis data',
                    'Administrasi dan manajemen perkantoran',
                    'Keuangan dan akuntansi',
                    'Manajemen bisnis dan kewirausahaan',
                    'Pemasaran dan digital marketing',
                    'Komunikasi dan public speaking',
                    'Bahasa Inggris atau bahasa asing',
                    'Pengelolaan pendidikan dan pelatihan',
                    'Pengujian kendaraan bermotor',
                    'Keselamatan transportasi jalan',
                    'Penelitian dan penulisan ilmiah',
                    'Pengadaan barang dan jasa',
                    'Manajemen proyek',
                    'Kepemimpinan dan manajemen organisasi',
                    'Lainnya',
                ],
            ],
            38 => [
                'no'      => 38,
                'step'    => 5,
                'section' => 'BAGIAN_5',
                'label'   => 'Apabila organisasi memberikan kesempatan pendidikan atau pelatihan, metode pembelajaran apa yang paling sesuai bagi Anda?',
                'type'    => 'radio',
                'options' => [
                    'Pelatihan tatap muka',
                    'Pelatihan daring',
                    'Pelatihan praktik langsung',
                    'Magang atau belajar di unit kerja lain',
                    'Pendampingan oleh pegawai yang lebih berpengalaman',
                    'Kombinasi beberapa metode',
                ],
            ],
            39 => [
                'no'      => 39,
                'step'    => 5,
                'section' => 'BAGIAN_5',
                'label'   => 'Apakah Anda bersedia mengikuti pendidikan atau pelatihan yang dilaksanakan di luar kota atau di luar jam kerja normal apabila diperlukan?',
                'type'    => 'radio',
                'options' => [
                    'Ya, bersedia',
                    'Bersedia dengan pemberitahuan sebelumnya',
                    'Bersedia dengan mempertimbangkan durasi dan kondisi pribadi',
                    'Tidak bersedia',
                ],
            ],
            40 => [
                'no'           => 40,
                'step'         => 5,
                'section'      => 'BAGIAN_5',
                'label'        => 'Menurut Anda, apakah penguasaan kompetensi baru dapat meningkatkan kontribusi Anda terhadap organisasi?',
                'type'         => 'likert_1_5',
                'scale_labels' => self::LIKERT_AGREEMENT,
            ],
            41 => [
                'no'      => 41,
                'step'    => 5,
                'section' => 'BAGIAN_5',
                'label'   => 'Apakah Anda bersedia apabila setelah mengikuti pelatihan diberikan penugasan untuk menerapkan kompetensi baru tersebut pada unit kerja lain?',
                'type'    => 'radio',
                'options' => [
                    'Sangat bersedia',
                    'Bersedia',
                    'Bersedia dengan mempertimbangkan jenis penugasan',
                    'Kurang bersedia',
                    'Tidak bersedia',
                ],
            ],
            42 => [
                'no'          => 42,
                'step'        => 5,
                'section'     => 'BAGIAN_5',
                'label'       => 'Kompetensi apa yang menurut Anda saat ini paling dibutuhkan PKTJ tetapi belum tersedia atau masih terbatas jumlah pegawainya?',
                'type'        => 'textarea',
                'placeholder' => 'Tuliskan kompetensi yang menurut Anda saat ini paling dibutuhkan oleh PKTJ',
            ],
        ];

        // ================= BAGIAN VI (43 - 58) =================
        foreach ($likertStatements as $num => $stmt) {
            $q[$num] = [
                'no'           => $num,
                'step'         => 6,
                'section'      => 'BAGIAN_6',
                'label'        => $stmt,
                'type'         => 'likert_1_5',
                'is_matrix'    => true,
                'scale_labels' => self::LIKERT_AGREEMENT,
            ];
        }

        $q[56] = [
            'no'          => 56,
            'step'        => 6,
            'section'     => 'BAGIAN_6',
            'label'       => 'Menurut Anda, apa yang paling memotivasi Anda untuk memberikan kinerja terbaik?',
            'type'        => 'checkbox',
            'max_select'  => 3,
            'has_other'   => true,
            'other_value' => 'Lainnya',
            'helper'      => 'Pilih maksimal 3 jawaban.',
            'options'     => [
                'Kesempatan mengembangkan karier',
                'Penghargaan atas hasil kerja',
                'Tambahan penghasilan sesuai ketentuan',
                'Lingkungan kerja yang baik',
                'Pengakuan dari pimpinan',
                'Kesempatan mengikuti pendidikan',
                'Tantangan dan variasi pekerjaan',
                'Kesempatan berinovasi',
                'Keinginan memberikan kontribusi kepada organisasi',
                'Tanggung jawab profesional',
                'Lainnya',
            ],
        ];
        $q[57] = [
            'no'                  => 57,
            'step'                => 6,
            'section'             => 'BAGIAN_6',
            'label'               => 'Apa hambatan utama yang selama ini memengaruhi produktivitas atau kinerja Anda?',
            'type'                => 'checkbox',
            'has_other'           => true,
            'other_value'         => 'Lainnya',
            'exclusive_option'    => 'Tidak ada hambatan berarti',
            'exclusive_conflicts' => [
                'Beban pekerjaan terlalu banyak',
                'Jumlah pegawai pada unit kerja terbatas',
                'Pembagian pekerjaan kurang merata',
                'Keterbatasan kompetensi atau keterampilan',
                'Keterbatasan fasilitas dan sarana kerja',
                'Sistem atau prosedur kerja kurang efektif',
                'Kurangnya koordinasi antarunit',
                'Kurangnya motivasi',
                'Kurangnya dukungan pimpinan',
                'Kurangnya penghargaan terhadap hasil kerja',
            ],
            'helper'              => 'Boleh memilih lebih dari satu jawaban.',
            'options'             => [
                'Beban pekerjaan terlalu banyak',
                'Jumlah pegawai pada unit kerja terbatas',
                'Pembagian pekerjaan kurang merata',
                'Keterbatasan kompetensi atau keterampilan',
                'Keterbatasan fasilitas dan sarana kerja',
                'Sistem atau prosedur kerja kurang efektif',
                'Kurangnya koordinasi antarunit',
                'Kurangnya motivasi',
                'Kurangnya dukungan pimpinan',
                'Kurangnya penghargaan terhadap hasil kerja',
                'Tidak ada hambatan berarti',
                'Lainnya',
            ],
        ];
        $q[58] = [
            'no'          => 58,
            'step'        => 6,
            'section'     => 'BAGIAN_6',
            'label'       => 'Menurut Anda, apa yang perlu diperbaiki oleh PKTJ agar seluruh pegawai dapat bekerja lebih produktif?',
            'type'        => 'textarea',
            'placeholder' => 'Tuliskan masukan perbaikan agar seluruh pegawai dapat bekerja lebih produktif',
        ];

        // ================= BAGIAN VII (59 - 67) =================
        $q[59] = [
            'no'      => 59,
            'step'    => 7,
            'section' => 'BAGIAN_7',
            'label'   => 'Apakah Anda mengetahui jenis layanan atau kegiatan PKTJ yang dapat menghasilkan pendapatan BLU?',
            'type'    => 'radio',
            'options' => [
                'Mengetahui dengan baik',
                'Mengetahui sebagian',
                'Pernah mendengar tetapi belum memahami',
                'Belum mengetahui',
            ],
        ];
        $q[60] = [
            'no'      => 60,
            'step'    => 7,
            'section' => 'BAGIAN_7',
            'label'   => 'Apakah Anda tertarik terlibat dalam kegiatan yang dapat meningkatkan pendapatan BLU PKTJ?',
            'type'    => 'radio',
            'options' => [
                'Sangat tertarik',
                'Tertarik',
                'Tertarik apabila sesuai kompetensi',
                'Belum memiliki ketertarikan',
                'Tidak tertarik',
            ],
        ];
        $q[61] = [
            'no'          => 61,
            'step'        => 7,
            'section'     => 'BAGIAN_7',
            'label'       => 'Dalam kegiatan apa Anda bersedia berkontribusi untuk mendukung pendapatan BLU?',
            'type'        => 'checkbox',
            'has_other'   => true,
            'other_value' => 'Lainnya',
            'helper'      => 'Boleh memilih lebih dari satu jawaban.',
            'options'     => [
                'Pelaksanaan pendidikan dan pelatihan',
                'Pelayanan pengujian kendaraan bermotor',
                'Pelaksanaan sertifikasi kompetensi',
                'Pemasaran dan promosi layanan PKTJ',
                'Pengembangan kerja sama dengan instansi pemerintah',
                'Pengembangan kerja sama dengan perusahaan swasta',
                'Pengembangan produk atau layanan baru',
                'Pengelolaan aset dan fasilitas yang berpotensi menghasilkan pendapatan',
                'Pengelolaan administrasi dan keuangan kegiatan BLU',
                'Pengembangan teknologi informasi untuk layanan BLU',
                'Penelitian dan konsultasi di bidang transportasi',
                'Kegiatan kewirausahaan dan pengembangan usaha',
                'Lainnya',
            ],
        ];
        $q[62] = [
            'no'      => 62,
            'step'    => 7,
            'section' => 'BAGIAN_7',
            'label'   => 'Apakah Anda memiliki pengalaman dalam kegiatan bisnis, pemasaran, penjualan jasa, kerja sama, atau pengembangan usaha?',
            'type'    => 'radio',
            'options' => [
                'Memiliki pengalaman profesional',
                'Pernah terlibat dalam kegiatan tersebut',
                'Memiliki pengalaman pribadi atau usaha sampingan',
                'Belum memiliki pengalaman',
            ],
        ];
        $q[63] = [
            'no'      => 63,
            'step'    => 7,
            'section' => 'BAGIAN_7',
            'label'   => 'Apabila diberikan kesempatan, apakah Anda bersedia membantu mencari peluang kerja sama atau calon pengguna jasa layanan PKTJ?',
            'type'    => 'radio',
            'options' => [
                'Sangat bersedia',
                'Bersedia',
                'Bersedia apabila mendapatkan pembekalan',
                'Kurang bersedia',
                'Tidak bersedia',
            ],
        ];
        $q[64] = [
            'no'      => 64,
            'step'    => 7,
            'section' => 'BAGIAN_7',
            'label'   => 'Apakah Anda memiliki jaringan atau relasi profesional dengan instansi pemerintah, BUMN, perusahaan swasta, asosiasi, atau lembaga lain yang berpotensi menjadi mitra PKTJ?',
            'type'    => 'radio',
            'options' => [
                'Ya, memiliki jaringan yang relevan',
                'Ada, tetapi belum pernah dimanfaatkan',
                'Tidak memiliki jaringan yang relevan',
                'Tidak bersedia menjawab',
            ],
        ];
        $q[65] = [
            'no'          => 65,
            'step'        => 7,
            'section'     => 'BAGIAN_7',
            'label'       => 'Menurut Anda, kegiatan atau layanan apa yang memiliki peluang untuk dikembangkan PKTJ guna meningkatkan pendapatan BLU?',
            'type'        => 'textarea',
            'placeholder' => 'Tuliskan ide layanan atau kegiatan yang berpeluang meningkatkan pendapatan BLU',
        ];
        $q[66] = [
            'no'      => 66,
            'step'    => 7,
            'section' => 'BAGIAN_7',
            'label'   => 'Apabila PKTJ memberikan target tertentu untuk mendukung peningkatan pendapatan BLU, apakah Anda bersedia berkontribusi sesuai kompetensi dan penugasan yang diberikan?',
            'type'    => 'radio',
            'options' => [
                'Sangat bersedia',
                'Bersedia',
                'Bersedia apabila diberikan pelatihan dan pendampingan',
                'Bersedia apabila tidak mengganggu pekerjaan utama',
                'Kurang bersedia',
                'Tidak bersedia',
            ],
        ];
        $q[67] = [
            'no'          => 67,
            'step'        => 7,
            'section'     => 'BAGIAN_7',
            'label'       => 'Menurut Anda, bentuk kontribusi apa yang paling realistis dapat Anda berikan untuk membantu peningkatan pendapatan BLU?',
            'type'        => 'textarea',
            'placeholder' => 'Tuliskan bentuk kontribusi nyata yang paling realistis dapat Anda berikan',
        ];

        // ================= BAGIAN VIII (68 - 72) =================
        $q[68] = [
            'no'      => 68,
            'step'    => 8,
            'section' => 'BAGIAN_8',
            'label'   => 'Bagaimana Anda menilai beban pekerjaan Anda saat ini?',
            'type'    => 'radio',
            'options' => [
                'Sangat ringan',
                'Ringan',
                'Sedang',
                'Berat',
                'Sangat berat',
            ],
        ];
        $q[69] = [
            'no'      => 69,
            'step'    => 8,
            'section' => 'BAGIAN_8',
            'label'   => 'Apakah seluruh pekerjaan utama Anda saat ini dapat diselesaikan sesuai waktu dan target yang ditentukan?',
            'type'    => 'radio',
            'options' => [
                'Selalu dapat diselesaikan',
                'Sebagian besar dapat diselesaikan',
                'Kadang-kadang mengalami keterlambatan',
                'Sering mengalami keterlambatan',
                'Tidak dapat diselesaikan karena keterbatasan waktu atau sumber daya',
            ],
        ];
        $q[70] = [
            'no'      => 70,
            'step'    => 8,
            'section' => 'BAGIAN_8',
            'label'   => 'Apakah Anda merasa masih memiliki kapasitas untuk menerima tanggung jawab tambahan?',
            'type'    => 'radio',
            'options' => [
                'Ya, kapasitas masih cukup besar',
                'Ya, tetapi terbatas',
                'Hanya untuk pekerjaan tertentu',
                'Saat ini tidak memiliki kapasitas tambahan',
                'Belum dapat menentukan',
            ],
        ];
        $q[71] = [
            'no'          => 71,
            'step'        => 8,
            'section'     => 'BAGIAN_8',
            'label'       => 'Jika diberikan pekerjaan tambahan, dukungan apa yang Anda perlukan?',
            'type'        => 'checkbox',
            'has_other'   => true,
            'other_value' => 'Lainnya',
            'helper'      => 'Boleh memilih lebih dari satu jawaban.',
            'options'     => [
                'Pembagian tugas yang jelas',
                'Pelatihan atau peningkatan kompetensi',
                'Pendampingan dari pegawai berpengalaman',
                'Fasilitas dan peralatan kerja',
                'Dukungan administrasi',
                'Penyesuaian beban pekerjaan utama',
                'Penugasan resmi dari pimpinan',
                'Penghargaan sesuai ketentuan',
                'Lainnya',
            ],
        ];
        $q[72] = [
            'no'      => 72,
            'step'    => 8,
            'section' => 'BAGIAN_8',
            'label'   => 'Apakah Anda bersedia dievaluasi berdasarkan hasil pekerjaan tambahan yang diberikan kepada Anda?',
            'type'    => 'radio',
            'options' => [
                'Sangat bersedia',
                'Bersedia',
                'Bersedia dengan indikator penilaian yang jelas',
                'Kurang bersedia',
                'Tidak bersedia',
            ],
        ];

        // ================= BAGIAN IX (73 - 77) =================
        $q[73] = [
            'no'        => 73,
            'step'      => 9,
            'section'   => 'BAGIAN_9',
            'label'     => 'Apakah menurut Anda pegawai yang memberikan kontribusi dan hasil kerja lebih besar seharusnya mendapatkan penghargaan lebih besar?',
            'type'      => 'radio_scored',
            'score_map' => [
                'Sangat setuju'       => 5,
                'Setuju'              => 4,
                'Netral'              => 3,
                'Tidak setuju'        => 2,
                'Sangat tidak setuju' => 1,
            ],
            'options'   => ['Sangat setuju', 'Setuju', 'Netral', 'Tidak setuju', 'Sangat tidak setuju'],
        ];
        $q[74] = [
            'no'          => 74,
            'step'        => 9,
            'section'     => 'BAGIAN_9',
            'label'       => 'Menurut Anda, faktor apa yang paling layak menjadi dasar pemberian penghargaan kepada pegawai?',
            'type'        => 'checkbox',
            'max_select'  => 5,
            'has_other'   => true,
            'other_value' => 'Lainnya',
            'helper'      => 'Pilih maksimal 5 jawaban.',
            'options'     => [
                'Pencapaian target pekerjaan',
                'Kualitas hasil pekerjaan',
                'Kedisiplinan',
                'Tanggung jawab',
                'Inisiatif dan inovasi',
                'Kontribusi membantu unit lain',
                'Penguasaan kompetensi tambahan',
                'Peningkatan produktivitas',
                'Kontribusi terhadap peningkatan pendapatan BLU',
                'Efisiensi penggunaan anggaran',
                'Kualitas pelayanan kepada masyarakat',
                'Kerja sama tim',
                'Lainnya',
            ],
        ];
        $q[75] = [
            'no'          => 75,
            'step'        => 9,
            'section'     => 'BAGIAN_9',
            'label'       => 'Bentuk penghargaan apa yang paling Anda harapkan atas kontribusi dan hasil kerja yang baik?',
            'type'        => 'checkbox',
            'has_other'   => true,
            'other_value' => 'Bentuk penghargaan lainnya',
            'helper'      => 'Boleh memilih lebih dari satu jawaban.',
            'options'     => [
                'Insentif finansial sesuai ketentuan',
                'Kesempatan mengikuti pendidikan atau pelatihan',
                'Pengembangan karier',
                'Pengakuan atau apresiasi dari pimpinan',
                'Penghargaan pegawai berprestasi',
                'Kesempatan mendapatkan penugasan strategis',
                'Kesempatan mengikuti kegiatan atau pelatihan di luar instansi',
                'Bentuk penghargaan lainnya',
            ],
        ];
        $q[76] = [
            'no'        => 76,
            'step'      => 9,
            'section'   => 'BAGIAN_9',
            'label'     => 'Apakah Anda setuju apabila penilaian kinerja pegawai mempertimbangkan dua aspek, yaitu pencapaian tugas pokok dan kontribusi tambahan bagi organisasi?',
            'type'      => 'radio_scored',
            'score_map' => [
                'Sangat setuju'       => 5,
                'Setuju'              => 4,
                'Netral'              => 3,
                'Tidak setuju'        => 2,
                'Sangat tidak setuju' => 1,
            ],
            'options'   => ['Sangat setuju', 'Setuju', 'Netral', 'Tidak setuju', 'Sangat tidak setuju'],
        ];
        $q[77] = [
            'no'        => 77,
            'step'      => 9,
            'section'   => 'BAGIAN_9',
            'label'     => 'Apakah Anda bersedia apabila sistem penghargaan pegawai didasarkan pada hasil penilaian kinerja yang terukur, transparan, dan dapat dipertanggungjawabkan?',
            'type'      => 'radio_scored',
            'score_map' => [
                'Sangat bersedia' => 5,
                'Bersedia'        => 4,
                'Netral'          => 3,
                'Kurang bersedia' => 2,
                'Tidak bersedia'  => 1,
            ],
            'options'   => ['Sangat bersedia', 'Bersedia', 'Netral', 'Kurang bersedia', 'Tidak bersedia'],
        ];

        // ================= BAGIAN X (78 - 83) =================
        $q[78] = [
            'no'           => 78,
            'step'         => 10,
            'section'      => 'BAGIAN_10',
            'label'        => 'Menurut Anda, apa tiga kelebihan atau kemampuan utama yang dapat Anda kontribusikan untuk kemajuan PKTJ?',
            'type'         => 'triple_text',
            'helper'       => 'Sebutkan 3 kemampuan, keterampilan, atau potensi utama Anda (isi ketiga kolom di bawah).',
            'placeholders' => [
                'Kelebihan / kemampuan utama 1',
                'Kelebihan / kemampuan utama 2',
                'Kelebihan / kemampuan utama 3',
            ],
        ];
        $q[79] = [
            'no'          => 79,
            'step'        => 10,
            'section'     => 'BAGIAN_10',
            'label'       => 'Apa kelemahan atau keterbatasan yang menurut Anda masih perlu diperbaiki untuk meningkatkan kinerja?',
            'type'        => 'textarea',
            'placeholder' => 'Tuliskan keterbatasan atau area yang masih ingin Anda perbaiki',
        ];
        $q[80] = [
            'no'          => 80,
            'step'        => 10,
            'section'     => 'BAGIAN_10',
            'label'       => 'Jika diberikan kesempatan mengembangkan kemampuan baru, kompetensi apa yang ingin Anda kuasai dalam dua tahun mendatang?',
            'type'        => 'textarea',
            'placeholder' => 'Tuliskan rencana kompetensi yang ingin dikuasai dalam 2 tahun ke depan',
        ];
        $q[81] = [
            'no'      => 81,
            'step'    => 10,
            'section' => 'BAGIAN_10',
            'label'   => 'Apakah Anda memiliki keinginan untuk mendapatkan tanggung jawab yang lebih besar di lingkungan PKTJ?',
            'type'    => 'radio',
            'options' => [
                'Ya, sangat ingin',
                'Ya, apabila diberikan kesempatan',
                'Belum memiliki rencana',
                'Tidak ingin',
            ],
        ];
        $q[82] = [
            'no'           => 82,
            'step'         => 10,
            'section'      => 'BAGIAN_10',
            'label'        => 'Seberapa besar komitmen Anda untuk ikut berkontribusi dalam meningkatkan kinerja dan pendapatan BLU PKTJ?',
            'type'         => 'scale_custom',
            'scale_labels' => [
                1 => 'Sangat rendah',
                2 => 'Rendah',
                3 => 'Sedang',
                4 => 'Tinggi',
                5 => 'Sangat tinggi',
            ],
        ];
        $q[83] = [
            'no'          => 83,
            'step'        => 10,
            'section'     => 'BAGIAN_10',
            'label'       => 'Apa saran atau masukan Anda kepada pimpinan PKTJ terkait pengelolaan SDM, pembagian pekerjaan, peningkatan kinerja, dan sistem penghargaan pegawai?',
            'type'        => 'textarea',
            'placeholder' => 'Tuliskan saran atau masukan Anda kepada pimpinan PKTJ',
        ];

        ksort($q);
        $questions = $q;
        return $questions;
    }

    /**
     * Mengecek apakah suatu pertanyaan wajib/relevan berdasarkan cabang jawaban sebelumnya.
     */
    public static function isQuestionRequired(int $qNo, array $answersMap): bool
    {
        if ($qNo === 14) {
            $q13 = trim((string) ($answersMap[13]['jawaban_teks'] ?? ''));
            return $q13 === 'Ya';
        }

        if (in_array($qNo, [22, 23, 24, 25], true)) {
            $q21 = trim((string) ($answersMap[21]['jawaban_teks'] ?? ''));
            return in_array($q21, [
                'Ya, memiliki beberapa keahlian tambahan',
                'Ya, memiliki satu keahlian tambahan',
            ], true);
        }

        if ($qNo === 28) {
            $q27 = trim((string) ($answersMap[27]['jawaban_teks'] ?? ''));
            if ($q27 === '' || $q27 === 'Tidak bersedia') {
                return false;
            }
            return true;
        }

        if ($qNo === 30) {
            $q29 = trim((string) ($answersMap[29]['jawaban_teks'] ?? ''));
            if ($q29 === '' || $q29 === 'Belum pernah') {
                return false;
            }
            return true;
        }

        return true;
    }

    /**
     * Daftar pertanyaan kondisional yang wajib dibersihkan (orphan cleanup) jika induknya tidak terpenuhi.
     */
    public static function getOrphanQuestionNumbers(array $answersMap): array
    {
        $orphans = [];

        $q13 = trim((string) ($answersMap[13]['jawaban_teks'] ?? ''));
        if ($q13 === 'Tidak') {
            $orphans[] = 14;
        }

        $q21 = trim((string) ($answersMap[21]['jawaban_teks'] ?? ''));
        if (in_array($q21, ['Belum memiliki keahlian tambahan', 'Belum mengetahui keahlian lain yang dapat dimanfaatkan'], true)) {
            $orphans[] = 22;
            $orphans[] = 23;
            $orphans[] = 24;
            $orphans[] = 25;
        }

        $q27 = trim((string) ($answersMap[27]['jawaban_teks'] ?? ''));
        if ($q27 === 'Tidak bersedia') {
            $orphans[] = 28;
        }

        $q29 = trim((string) ($answersMap[29]['jawaban_teks'] ?? ''));
        if ($q29 === 'Belum pernah') {
            $orphans[] = 30;
        }

        return $orphans;
    }

    /**
     * Mengecek apakah suatu butir pertanyaan telah terisi lengkap & sah.
     */
    public static function isQuestionAnswered(int $qNo, array $answersMap): bool
    {
        if (!isset($answersMap[$qNo])) {
            return false;
        }

        $questions = self::getQuestions();
        $meta      = $questions[$qNo] ?? null;
        if (!$meta) {
            return false;
        }

        $row  = $answersMap[$qNo];
        $type = $meta['type'];

        if ($type === 'checkbox') {
            $arr = $row['pilihan_array'] ?? [];
            if (empty($arr) && !empty($row['jawaban_pilihan'])) {
                $dec = json_decode((string) $row['jawaban_pilihan'], true);
                if (is_array($dec)) {
                    $arr = $dec;
                }
            }
            if (empty($arr)) {
                return false;
            }
            if (!empty($meta['has_other']) && in_array($meta['other_value'], $arr, true)) {
                return trim((string) ($row['jawaban_lainnya'] ?? '')) !== '';
            }
            return true;
        }

        if ($type === 'triple_text') {
            $arr = $row['pilihan_array'] ?? [];
            if (empty($arr) && !empty($row['jawaban_pilihan'])) {
                $dec = json_decode((string) $row['jawaban_pilihan'], true);
                if (is_array($dec)) {
                    $arr = $dec;
                }
            }
            $nonEmpty = array_filter(array_map(static fn($v) => trim((string) $v), $arr), static fn($v) => $v !== '');
            return count($nonEmpty) >= 3;
        }

        if (in_array($type, ['likert_1_5', 'scale_custom', 'number'], true)) {
            $rawAngka = $row['jawaban_angka'] ?? null;
            return $rawAngka !== null && $rawAngka !== '';
        }

        $textVal = trim((string) ($row['jawaban_teks'] ?? ''));
        if ($textVal === '') {
            return false;
        }

        if (!empty($meta['has_other']) && $textVal === ($meta['other_value'] ?? 'Lainnya')) {
            return trim((string) ($row['jawaban_lainnya'] ?? '')) !== '';
        }

        return true;
    }

    /**
     * Menghitung progres dinamis (total_answered, total_applicable, progress_percent, status tiap bagian).
     */
    public static function calculateProgress(array $answersMap): array
    {
        $sections        = self::getSections();
        $totalApplicable = 0;
        $totalAnswered   = 0;
        $sectionComplete = [];
        $missingByStep   = [];

        foreach ($sections as $step => $sec) {
            $secApplicable = 0;
            $secAnswered   = 0;
            $missing       = [];

            foreach ($sec['questions'] as $qNo) {
                if (!self::isQuestionRequired($qNo, $answersMap)) {
                    continue;
                }
                $secApplicable++;
                $totalApplicable++;

                if (self::isQuestionAnswered($qNo, $answersMap)) {
                    $secAnswered++;
                    $totalAnswered++;
                } else {
                    $missing[] = $qNo;
                }
            }

            $sectionComplete[$step] = ($secApplicable > 0 && $secAnswered === $secApplicable);
            $missingByStep[$step]   = $missing;
        }

        $percent = $totalApplicable > 0
            ? min(100.0, round(($totalAnswered / $totalApplicable) * 100, 2))
            : 0.0;

        return [
            'total_answered'   => $totalAnswered,
            'total_applicable' => $totalApplicable,
            'progress_percent' => $percent,
            'section_complete' => $sectionComplete,
            'missing_by_step'  => $missingByStep,
        ];
    }

    /**
     * Menghitung masa kerja otomatis dari tanggal mulai bekerja di PKTJ (YYYY-MM-DD).
     */
    public static function computeTenureFromDate(string $ymd): ?array
    {
        $ymd = trim($ymd);
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $ymd)) {
            return null;
        }

        try {
            $start = new \DateTimeImmutable($ymd);
            $today = new \DateTimeImmutable('today');
            if ($start > $today) {
                return null;
            }

            $diff         = $start->diff($today);
            $years        = (int) $diff->y;
            $months       = (int) $diff->m;
            $decimalYears = round($years + ($months / 12), 2);

            if ($decimalYears < 1) {
                $bucket = 'Kurang dari 1 tahun';
            } elseif ($years <= 3) {
                $bucket = '1–3 tahun';
            } elseif ($years <= 6) {
                $bucket = '4–6 tahun';
            } elseif ($years <= 10) {
                $bucket = '7–10 tahun';
            } else {
                $bucket = 'Lebih dari 10 tahun';
            }

            $parts = [];
            if ($years > 0) {
                $parts[] = $years . ' Tahun';
            }
            if ($months > 0 || empty($parts)) {
                $parts[] = $months . ' Bulan';
            }
            $preciseText = implode(' ', $parts);

            return [
                'years'         => $years,
                'months'        => $months,
                'decimal_years' => $decimalYears,
                'bucket'        => $bucket,
                'display'       => $preciseText . ' (' . $bucket . ')',
            ];
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Memberikan saran awal editable Jenis Kelamin (No. 3), Usia (No. 4), dan Jenis Kepegawaian (No. 5) dari NIP 18 digit standar ASN.
     */
    public static function suggestFromNip18(string $nip): array
    {
        $clean = preg_replace('/\D+/', '', $nip);
        if (strlen($clean) !== 18) {
            return [
                'gender'             => null,
                'age'                => null,
                'status_kepegawaian' => null,
            ];
        }

        $birthYear  = (int) substr($clean, 0, 4);
        $birthMonth = (int) substr($clean, 4, 2);
        $birthDay   = (int) substr($clean, 6, 2);
        $tmtCode    = substr($clean, 12, 2);
        $genderCode = substr($clean, 14, 1);

        $gender = null;
        if ($genderCode === '1') {
            $gender = 'Laki-laki';
        } elseif ($genderCode === '2') {
            $gender = 'Perempuan';
        }

        $age = null;
        if (checkdate($birthMonth, $birthDay, $birthYear) && $birthYear >= 1950 && $birthYear <= ((int) date('Y') - 17)) {
            try {
                $dob  = new \DateTimeImmutable(sprintf('%04d-%02d-%02d', $birthYear, $birthMonth, $birthDay));
                $diff = $dob->diff(new \DateTimeImmutable('today'));
                if ($diff->y >= 17 && $diff->y <= 75) {
                    $age = (int) $diff->y;
                }
            } catch (\Throwable $e) {
                $age = null;
            }
        }

        // Deteksi status kepegawaian standar BKN dari 18 digit NIP:
        // Digit 13-14 = '21' menandakan pengangkatan PPPK; '01'-'12' menandakan bulan pengangkatan CPNS/PNS.
        $suggestedStatus = null;
        if ($tmtCode === '21') {
            $suggestedStatus = 'PPPK';
        } elseif ((int) $tmtCode >= 1 && (int) $tmtCode <= 12) {
            $suggestedStatus = 'PNS';
        }

        return [
            'gender'             => $gender,
            'age'                => $age,
            'status_kepegawaian' => $suggestedStatus,
        ];
    }

    /**
     * Memberikan pesan umpan balik ramah dan spesifik saat suatu butir pertanyaan belum lengkap.
     */
    public static function describeMissingQuestion(int $qNo, array $answersMap): string
    {
        $questions = self::getQuestions();
        $meta      = $questions[$qNo] ?? null;
        if (!$meta) {
            return 'Mohon lengkapi Pertanyaan Nomor ' . $qNo . '.';
        }

        if ($qNo === 78) {
            $row = $answersMap[78] ?? null;
            $arr = $row['pilihan_array'] ?? [];
            if (empty($arr) && !empty($row['jawaban_pilihan'])) {
                $dec = json_decode((string) $row['jawaban_pilihan'], true);
                if (is_array($dec)) {
                    $arr = $dec;
                }
            }
            $filledCount = count(array_filter(array_map('trim', (array) $arr)));
            if ($filledCount < 3) {
                return 'Mohon lengkapi ketiga kolom kelebihan atau kemampuan utama pada Pertanyaan Nomor 78.';
            }
        }

        if (!empty($meta['has_other'])) {
            $otherVal = $meta['other_value'] ?? 'Lainnya';
            $row      = $answersMap[$qNo] ?? null;
            $selected = false;
            if ($meta['type'] === 'checkbox') {
                $arr = $row['pilihan_array'] ?? [];
                if (empty($arr) && !empty($row['jawaban_pilihan'])) {
                    $dec = json_decode((string) $row['jawaban_pilihan'], true);
                    if (is_array($dec)) {
                        $arr = $dec;
                    }
                }
                $selected = in_array($otherVal, $arr, true);
            } else {
                $selected = trim((string) ($row['jawaban_teks'] ?? '')) === $otherVal;
            }

            if ($selected && trim((string) ($row['jawaban_lainnya'] ?? '')) === '') {
                return 'Tuliskan keterangan lainnya pada Pertanyaan Nomor ' . $qNo . '.';
            }
        }

        return 'Mohon lengkapi Pertanyaan Nomor ' . $qNo . ' sebelum melanjutkan.';
    }

    /**
     * Format tampilan jawaban untuk ringkasan, modal detail responden, dan ekspor Excel.
     */
    public static function formatAnswerDisplay(int $qNo, ?array $row): string
    {
        if (!$row) {
            return '-';
        }

        $questions = self::getQuestions();
        $meta      = $questions[$qNo] ?? null;
        if (!$meta) {
            return trim((string) ($row['jawaban_teks'] ?? '-')) ?: '-';
        }

        $type = $meta['type'];

        if ($type === 'checkbox') {
            $arr = $row['pilihan_array'] ?? [];
            if (empty($arr) && !empty($row['jawaban_pilihan'])) {
                $dec = json_decode((string) $row['jawaban_pilihan'], true);
                if (is_array($dec)) {
                    $arr = $dec;
                }
            }
            if (empty($arr)) {
                return '-';
            }
            $otherVal  = $meta['other_value'] ?? 'Lainnya';
            $otherNote = trim((string) ($row['jawaban_lainnya'] ?? ''));
            $formatted = [];
            foreach ($arr as $opt) {
                if ($opt === $otherVal && $otherNote !== '') {
                    $formatted[] = $opt . ': ' . $otherNote;
                } else {
                    $formatted[] = $opt;
                }
            }
            return implode('; ', $formatted);
        }

        if ($type === 'triple_text') {
            $arr = $row['pilihan_array'] ?? [];
            if (empty($arr) && !empty($row['jawaban_pilihan'])) {
                $dec = json_decode((string) $row['jawaban_pilihan'], true);
                if (is_array($dec)) {
                    $arr = $dec;
                }
            }
            if (!empty($arr)) {
                $lines = [];
                foreach (array_values($arr) as $i => $v) {
                    $v = trim((string) $v);
                    if ($v !== '') {
                        $lines[] = ($i + 1) . '. ' . $v;
                    }
                }
                if (!empty($lines)) {
                    return implode(' | ', $lines);
                }
            }
            return trim((string) ($row['jawaban_teks'] ?? '-')) ?: '-';
        }

        if (in_array($type, ['likert_1_5', 'scale_custom'], true)) {
            $rawAngka = $row['jawaban_angka'] ?? null;
            $score    = ($rawAngka !== null && $rawAngka !== '') ? (int) round((float) $rawAngka) : 0;
            if ($score < 1) {
                return '-';
            }
            $lbl = $meta['scale_labels'][$score] ?? ($row['jawaban_teks'] ?? '');
            return $score . ' — ' . $lbl;
        }

        if ($type === 'number') {
            $rawAngka = $row['jawaban_angka'] ?? null;
            if ($rawAngka === null || $rawAngka === '') {
                return '-';
            }
            $val = (int) round((float) $rawAngka);
            return $val . (!empty($meta['unit_suffix']) ? ' ' . $meta['unit_suffix'] : '');
        }

        $txt       = trim((string) ($row['jawaban_teks'] ?? ''));
        $otherNote = trim((string) ($row['jawaban_lainnya'] ?? ''));
        if ($txt === '') {
            return '-';
        }

        if ((!empty($meta['has_other']) && $txt === ($meta['other_value'] ?? 'Lainnya') && $otherNote !== '')
            || ($type === 'radio_optional_note' && $otherNote !== '')
        ) {
            return $txt . ' (' . $otherNote . ')';
        }

        return $txt;
    }
}
