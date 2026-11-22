<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class EnhanceTarunaBeasiswaTables extends Migration
{
    public function up()
    {
        // 1. Perluas taruna_program_beasiswa
        $programCols = [
            'tahun_akademik_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'tahun',
            ],
            'nominal' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
                'default'  => 0,
                'after'    => 'kuota',
            ],
            'persyaratan' => [
                'type'  => 'TEXT',
                'null'  => true,
                'after' => 'nominal',
            ],
            'penanggung_jawab' => [
                'type'       => 'VARCHAR',
                'constraint' => 120,
                'default'    => 'Unit Ketarunaan',
                'after'      => 'persyaratan',
            ],
            'dasar_penilaian' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'ip_semester',
                'after'      => 'penanggung_jawab',
            ],
            'periode_nilai_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'dasar_penilaian',
            ],
            'basis_kelas' => [
                'type'       => 'VARCHAR',
                'constraint' => 80,
                'default'    => 'kelas_periode_penilaian',
                'after'      => 'periode_nilai_id',
            ],
            'jumlah_peringkat' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 5,
                'after'      => 'basis_kelas',
            ],
            'aturan_seri' => [
                'type'       => 'ENUM',
                'constraint' => ['belum_ditetapkan', 'ipk', 'prestasi', 'disiplin', 'sks_tertinggi'],
                'default'    => 'belum_ditetapkan',
                'after'      => 'jumlah_peringkat',
            ],
        ];

        $progAdd = [];
        foreach ($programCols as $colName => $def) {
            if (!$this->db->fieldExists($colName, 'taruna_program_beasiswa')) {
                $progAdd[$colName] = $def;
            }
        }
        if (!empty($progAdd)) {
            $this->forge->addColumn('taruna_program_beasiswa', $progAdd);
        }

        $idxProg = $this->db->query("SHOW INDEX FROM taruna_program_beasiswa WHERE Key_name = 'idx_prog_jenis_status'")->getResultArray();
        if (empty($idxProg)) {
            $this->db->query("ALTER TABLE taruna_program_beasiswa ADD INDEX idx_prog_jenis_status (jenis_beasiswa, status_program)");
        }

        // 2. Perluas taruna_penerima_beasiswa
        $penerimaCols = [
            'tahun_akademik_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'nomor_taruna',
            ],
            'sumber_klaim' => [
                'type'       => 'ENUM',
                'constraint' => ['taruna', 'pic', 'pemeringkatan'],
                'default'    => 'pic',
                'after'      => 'tahun_akademik_id',
            ],
            'tanggal_pengajuan' => [
                'type'  => 'DATE',
                'null'  => true,
                'after' => 'sumber_klaim',
            ],
            'dicatat_oleh' => [
                'type'       => 'VARCHAR',
                'constraint' => 120,
                'default'    => 'PIC Ketarunaan',
                'after'      => 'tanggal_pengajuan',
            ],
            'pemeringkatan_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'dicatat_oleh',
            ],
            'ip_semester' => [
                'type'       => 'DECIMAL',
                'constraint' => '4,2',
                'null'       => true,
                'after'      => 'pemeringkatan_id',
            ],
            'peringkat_kelas' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'after'      => 'ip_semester',
            ],
            'kelas_snapshot' => [
                'type'       => 'VARCHAR',
                'constraint' => 80,
                'null'       => true,
                'after'      => 'peringkat_kelas',
            ],
            'prestasi_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'kelas_snapshot',
            ],
            'kategori_prestasi' => [
                'type'       => 'VARCHAR',
                'constraint' => 80,
                'null'       => true,
                'after'      => 'prestasi_id',
            ],
            'nama_prestasi' => [
                'type'       => 'VARCHAR',
                'constraint' => 200,
                'null'       => true,
                'after'      => 'kategori_prestasi',
            ],
            'tingkat_prestasi' => [
                'type'       => 'VARCHAR',
                'constraint' => 60,
                'null'       => true,
                'after'      => 'nama_prestasi',
            ],
            'peringkat_prestasi' => [
                'type'       => 'VARCHAR',
                'constraint' => 60,
                'null'       => true,
                'after'      => 'tingkat_prestasi',
            ],
            'tanggal_prestasi' => [
                'type'  => 'DATE',
                'null'  => true,
                'after' => 'peringkat_prestasi',
            ],
            'penyelenggara_prestasi' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
                'after'      => 'tanggal_prestasi',
            ],
            'sertifikat_berkas' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'penyelenggara_prestasi',
            ],
            'dokumentasi_berkas' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'sertifikat_berkas',
            ],
            'alamat_domisili' => [
                'type'  => 'TEXT',
                'null'  => true,
                'after' => 'dokumentasi_berkas',
            ],
            'provinsi' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'after'      => 'alamat_domisili',
            ],
            'kabupaten_kota' => [
                'type'       => 'VARCHAR',
                'constraint' => 120,
                'null'       => true,
                'after'      => 'provinsi',
            ],
            'kecamatan' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'after'      => 'kabupaten_kota',
            ],
            'desa_kelurahan' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'after'      => 'kecamatan',
            ],
            'kode_wilayah' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'null'       => true,
                'after'      => 'desa_kelurahan',
            ],
            'tanggal_acuan_domisili' => [
                'type'  => 'DATE',
                'null'  => true,
                'after' => 'kode_wilayah',
            ],
            'acuan_wilayah_3tp' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
                'after'      => 'tanggal_acuan_domisili',
            ],
            'versi_acuan_3tp' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'acuan_wilayah_3tp',
            ],
            'status_pemeriksaan_3tp' => [
                'type'       => 'ENUM',
                'constraint' => ['menunggu_verifikasi', 'memenuhi', 'bukan_3tp', 'acuan_belum_ada'],
                'null'       => true,
                'after'      => 'versi_acuan_3tp',
            ],
            'dokumen_domisili' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'status_pemeriksaan_3tp',
            ],
            'jenis_dokumen_domisili' => [
                'type'       => 'VARCHAR',
                'constraint' => 80,
                'null'       => true,
                'after'      => 'dokumen_domisili',
            ],
            'dokumen_pendukung_3tp' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'jenis_dokumen_domisili',
            ],
            'jenis_dokumen_pendukung_3tp' => [
                'type'       => 'VARCHAR',
                'constraint' => 80,
                'null'       => true,
                'after'      => 'dokumen_pendukung_3tp',
            ],
            'pekerjaan_orang_tua' => [
                'type'       => 'VARCHAR',
                'constraint' => 120,
                'null'       => true,
                'after'      => 'jenis_dokumen_pendukung_3tp',
            ],
            'penghasilan_bulanan' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
                'null'     => true,
                'after'    => 'pekerjaan_orang_tua',
            ],
            'jumlah_tanggungan' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'after'      => 'penghasilan_bulanan',
            ],
            'uraian_pengajuan' => [
                'type'  => 'TEXT',
                'null'  => true,
                'after' => 'jumlah_tanggungan',
            ],
            'dokumen_ekonomi_json' => [
                'type'  => 'LONGTEXT',
                'null'  => true,
                'after' => 'uraian_pengajuan',
            ],
            'hasil_verifikasi' => [
                'type'       => 'ENUM',
                'constraint' => ['belum_diperiksa', 'memenuhi', 'perlu_perbaikan', 'tidak_memenuhi'],
                'default'    => 'belum_diperiksa',
                'after'      => 'status_pengajuan',
            ],
            'checklist_verifikasi_json' => [
                'type'  => 'TEXT',
                'null'  => true,
                'after' => 'hasil_verifikasi',
            ],
            'catatan_pemeriksa' => [
                'type'  => 'TEXT',
                'null'  => true,
                'after' => 'checklist_verifikasi_json',
            ],
            'diperiksa_oleh' => [
                'type'       => 'VARCHAR',
                'constraint' => 120,
                'null'       => true,
                'after'      => 'catatan_pemeriksa',
            ],
            'tanggal_pemeriksaan' => [
                'type'  => 'DATE',
                'null'  => true,
                'after' => 'diperiksa_oleh',
            ],
            'pejabat_penetap' => [
                'type'       => 'VARCHAR',
                'constraint' => 120,
                'null'       => true,
                'after'      => 'nomor_keputusan',
            ],
            'dokumen_keputusan' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'pejabat_penetap',
            ],
            'catatan_penetapan' => [
                'type'  => 'TEXT',
                'null'  => true,
                'after' => 'dokumen_keputusan',
            ],
            'diajukan_oleh' => [
                'type'       => 'VARCHAR',
                'constraint' => 120,
                'null'       => true,
                'after'      => 'status_validasi',
            ],
            'divalidasi_oleh' => [
                'type'       => 'VARCHAR',
                'constraint' => 120,
                'null'       => true,
                'after'      => 'diajukan_oleh',
            ],
            'tanggal_validasi' => [
                'type'  => 'DATE',
                'null'  => true,
                'after' => 'divalidasi_oleh',
            ],
            'catatan_validasi' => [
                'type'  => 'TEXT',
                'null'  => true,
                'after' => 'tanggal_validasi',
            ],
            'riwayat_proses_json' => [
                'type'  => 'LONGTEXT',
                'null'  => true,
                'after' => 'catatan_validasi',
            ],
        ];

        $penerimaAdd = [];
        foreach ($penerimaCols as $colName => $def) {
            if (!$this->db->fieldExists($colName, 'taruna_penerima_beasiswa')) {
                $penerimaAdd[$colName] = $def;
            }
        }
        if (!empty($penerimaAdd)) {
            $this->forge->addColumn('taruna_penerima_beasiswa', $penerimaAdd);
        }

        // Pastikan baris lama status_pengajuan = 'diterima' otomatis punya hasil_verifikasi = 'memenuhi'
        $this->db->query("UPDATE taruna_penerima_beasiswa SET hasil_verifikasi = 'memenuhi' WHERE status_pengajuan IN ('diverifikasi', 'diterima') AND hasil_verifikasi = 'belum_diperiksa'");

        $pbIndexes = [
            'idx_pb_validasi'   => 'status_validasi',
            'idx_pb_penetapan'  => 'status_penetapan',
            'idx_pb_verifikasi' => 'hasil_verifikasi',
        ];
        foreach ($pbIndexes as $idxName => $colName) {
            $exists = $this->db->query("SHOW INDEX FROM taruna_penerima_beasiswa WHERE Key_name = ?", [$idxName])->getResultArray();
            if (empty($exists)) {
                $this->db->query("ALTER TABLE taruna_penerima_beasiswa ADD INDEX {$idxName} ({$colName})");
            }
        }

        // 3. Tabel taruna_nilai_ip (IP Semester terpisah dari IPK)
        if (!$this->db->tableExists('taruna_nilai_ip')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'nomor_taruna' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 30,
                ],
                'tahun_akademik_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                ],
                'program_studi_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                ],
                'kelas_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                ],
                'tingkat' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'default'    => 1,
                ],
                'semester' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'default'    => 1,
                ],
                'ip_semester' => [
                    'type'       => 'DECIMAL',
                    'constraint' => '4,2',
                ],
                'sks_lulus' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'default'    => 20,
                ],
                'sumber_data' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'default'    => 'Sistem Akademik / Impor',
                ],
                'versi' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'default'    => 1,
                ],
                'status_validasi' => [
                    'type'       => 'ENUM',
                    'constraint' => ['draf', 'diajukan', 'perlu_perbaikan', 'tervalidasi'],
                    'default'    => 'tervalidasi',
                ],
                'diimpor_oleh' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 120,
                    'null'       => true,
                ],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
                'updated_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addUniqueKey(['nomor_taruna', 'tahun_akademik_id', 'versi'], 'uniq_nilai_ip_periode');
            $this->forge->addKey(['tahun_akademik_id', 'kelas_id', 'status_validasi'], false, false, 'idx_nilai_ip_kelas');
            $this->forge->createTable('taruna_nilai_ip', true);
        }

        // 4. Tabel taruna_beasiswa_pemeringkatan
        if (!$this->db->tableExists('taruna_beasiswa_pemeringkatan')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'program_beasiswa_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                ],
                'tahun_akademik_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                ],
                'program_studi_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                ],
                'kelas_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                ],
                'kode_versi' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 40,
                ],
                'versi' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'default'    => 1,
                ],
                'populasi_kelas' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'default'    => 0,
                ],
                'jumlah_kandidat' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'default'    => 0,
                ],
                'sumber_nilai' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'default'    => 'Nilai semester tervalidasi',
                ],
                'parameter_aturan_json' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'ada_seri_kelima' => [
                    'type'       => 'TINYINT',
                    'constraint' => 1,
                    'default'    => 0,
                ],
                'aturan_seri' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 40,
                    'default'    => 'belum_ditetapkan',
                ],
                'status_finalisasi' => [
                    'type'       => 'ENUM',
                    'constraint' => ['draf', 'ditahan_seri', 'diajukan', 'tervalidasi', 'dikembalikan'],
                    'default'    => 'draf',
                ],
                'alasan_ditahan' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => true,
                ],
                'diproses_oleh' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 120,
                    'null'       => true,
                ],
                'tanggal_hitung' => [
                    'type' => 'DATE',
                    'null' => true,
                ],
                'catatan_validasi' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'divalidasi_oleh' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 120,
                    'null'       => true,
                ],
                'tanggal_validasi' => [
                    'type' => 'DATE',
                    'null' => true,
                ],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
                'updated_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addKey(['program_beasiswa_id', 'tahun_akademik_id', 'kelas_id'], false, false, 'idx_pemeringkatan_kelas');
            $this->forge->createTable('taruna_beasiswa_pemeringkatan', true);
        }

        // 5. Tabel taruna_beasiswa_hasil_peringkat
        if (!$this->db->tableExists('taruna_beasiswa_hasil_peringkat')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'pemeringkatan_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                ],
                'nomor_taruna' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 30,
                ],
                'nama_snapshot' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 150,
                ],
                'kelas_snapshot' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 80,
                ],
                'ip_semester' => [
                    'type'       => 'DECIMAL',
                    'constraint' => '4,2',
                ],
                'peringkat' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                ],
                'status_kandidat' => [
                    'type'       => 'ENUM',
                    'constraint' => ['kandidat', 'seri_kelima', 'di_luar_5_besar'],
                    'default'    => 'di_luar_5_besar',
                ],
                'is_seri' => [
                    'type'       => 'TINYINT',
                    'constraint' => 1,
                    'default'    => 0,
                ],
                'catatan' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => true,
                ],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addUniqueKey(['pemeringkatan_id', 'nomor_taruna'], 'uniq_hasil_peringkat');
            $this->forge->createTable('taruna_beasiswa_hasil_peringkat', true);
        }

        // 6. Tabel ref_wilayah_3tp
        if (!$this->db->tableExists('ref_wilayah_3tp')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'kode_wilayah' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 30,
                ],
                'provinsi' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                ],
                'kabupaten_kota' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 120,
                ],
                'kategori_3tp' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 60,
                    'default'    => 'Daerah Tertinggal',
                ],
                'dasar_acuan' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 150,
                ],
                'versi_acuan' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                ],
                'tahun_berlaku' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                ],
                'is_aktif' => [
                    'type'       => 'TINYINT',
                    'constraint' => 1,
                    'default'    => 1,
                ],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addKey('kode_wilayah', false, false, 'idx_3tp_kode');
            $this->forge->addKey(['provinsi', 'kabupaten_kota'], false, false, 'idx_3tp_kab');
            $this->forge->createTable('ref_wilayah_3tp', true);
        }
    }

    public function down()
    {
        foreach (['ref_wilayah_3tp', 'taruna_beasiswa_hasil_peringkat', 'taruna_beasiswa_pemeringkatan', 'taruna_nilai_ip'] as $tbl) {
            if ($this->db->tableExists($tbl)) {
                $this->forge->dropTable($tbl, true);
            }
        }

        foreach (['idx_pb_validasi', 'idx_pb_penetapan', 'idx_pb_verifikasi'] as $idx) {
            $exists = $this->db->query("SHOW INDEX FROM taruna_penerima_beasiswa WHERE Key_name = ?", [$idx])->getResultArray();
            if (!empty($exists)) {
                $this->db->query("ALTER TABLE taruna_penerima_beasiswa DROP INDEX {$idx}");
            }
        }

        $penerimaCols = [
            'tahun_akademik_id', 'sumber_klaim', 'tanggal_pengajuan', 'dicatat_oleh',
            'pemeringkatan_id', 'ip_semester', 'peringkat_kelas', 'kelas_snapshot',
            'prestasi_id', 'kategori_prestasi', 'nama_prestasi', 'tingkat_prestasi',
            'peringkat_prestasi', 'tanggal_prestasi', 'penyelenggara_prestasi',
            'sertifikat_berkas', 'dokumentasi_berkas',
            'alamat_domisili', 'provinsi', 'kabupaten_kota', 'kecamatan', 'desa_kelurahan',
            'kode_wilayah', 'tanggal_acuan_domisili', 'acuan_wilayah_3tp', 'versi_acuan_3tp',
            'status_pemeriksaan_3tp', 'dokumen_domisili', 'jenis_dokumen_domisili',
            'dokumen_pendukung_3tp', 'jenis_dokumen_pendukung_3tp',
            'pekerjaan_orang_tua', 'penghasilan_bulanan', 'jumlah_tanggungan',
            'uraian_pengajuan', 'dokumen_ekonomi_json',
            'hasil_verifikasi', 'checklist_verifikasi_json', 'catatan_pemeriksa',
            'diperiksa_oleh', 'tanggal_pemeriksaan', 'pejabat_penetap', 'dokumen_keputusan',
            'catatan_penetapan', 'diajukan_oleh', 'divalidasi_oleh', 'tanggal_validasi',
            'catatan_validasi', 'riwayat_proses_json',
        ];
        foreach ($penerimaCols as $col) {
            if ($this->db->fieldExists($col, 'taruna_penerima_beasiswa')) {
                $this->forge->dropColumn('taruna_penerima_beasiswa', $col);
            }
        }

        $idxProg = $this->db->query("SHOW INDEX FROM taruna_program_beasiswa WHERE Key_name = 'idx_prog_jenis_status'")->getResultArray();
        if (!empty($idxProg)) {
            $this->db->query("ALTER TABLE taruna_program_beasiswa DROP INDEX idx_prog_jenis_status");
        }

        $progCols = [
            'tahun_akademik_id', 'nominal', 'persyaratan', 'penanggung_jawab',
            'dasar_penilaian', 'periode_nilai_id', 'basis_kelas',
            'jumlah_peringkat', 'aturan_seri',
        ];
        foreach ($progCols as $col) {
            if ($this->db->fieldExists($col, 'taruna_program_beasiswa')) {
                $this->forge->dropColumn('taruna_program_beasiswa', $col);
            }
        }
    }
}
