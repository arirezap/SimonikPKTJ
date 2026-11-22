<?php

namespace App\Models\Ketarunaan;

use CodeIgniter\Model;

class PenerimaBeasiswaModel extends Model
{
    protected $table            = 'taruna_penerima_beasiswa';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'program_beasiswa_id', 'nomor_taruna', 'tahun_akademik_id', 'sumber_klaim',
        'tanggal_pengajuan', 'dicatat_oleh', 'pemeringkatan_id', 'ip_semester',
        'peringkat_kelas', 'kelas_snapshot', 'prestasi_id', 'kategori_prestasi',
        'nama_prestasi', 'tingkat_prestasi', 'peringkat_prestasi', 'tanggal_prestasi',
        'penyelenggara_prestasi', 'sertifikat_berkas', 'dokumentasi_berkas',
        'alamat_domisili', 'provinsi', 'kabupaten_kota', 'kecamatan', 'desa_kelurahan',
        'kode_wilayah', 'tanggal_acuan_domisili', 'acuan_wilayah_3tp', 'versi_acuan_3tp',
        'status_pemeriksaan_3tp', 'dokumen_domisili', 'jenis_dokumen_domisili',
        'dokumen_pendukung_3tp', 'jenis_dokumen_pendukung_3tp', 'pekerjaan_orang_tua',
        'penghasilan_bulanan', 'jumlah_tanggungan', 'uraian_pengajuan',
        'dokumen_ekonomi_json', 'status_pengajuan', 'hasil_verifikasi',
        'checklist_verifikasi_json', 'catatan_pemeriksa', 'diperiksa_oleh',
        'tanggal_pemeriksaan', 'status_penetapan', 'tanggal_penetapan',
        'nomor_keputusan', 'pejabat_penetap', 'dokumen_keputusan', 'catatan_penetapan',
        'status_validasi', 'diajukan_oleh', 'divalidasi_oleh', 'tanggal_validasi',
        'catatan_validasi', 'riwayat_proses_json', 'created_at', 'updated_at',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}

