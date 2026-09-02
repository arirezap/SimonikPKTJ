<?php

namespace App\Controllers\Kepegawaian;

use App\Controllers\BaseController;
use App\Models\User;
use App\Models\TargetKinerja;
use App\Models\SettingModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Dompdf\Dompdf;
use Dompdf\Options;

/**
 * Class MonitoringTargetController
 * 
 * Mengelola dasbor monitoring status penyusunan, pengiriman, dan persetujuan
 * Target Kinerja Bulanan (RHK) seluruh pegawai institusi untuk Tim Kepegawaian,
 * Admin, dan Pimpinan di lingkungan Evidence Command Center (ECC).
 */
class MonitoringTargetController extends BaseController
{
    /**
     * Halaman Utama Monitoring Target Kinerja Bulanan
     */
    public function index()
    {
        // Role yang diizinkan mengakses modul Kepegawaian (Direktur, Wadir, Kabag, Kepegawaian, Admin)
        if (!hasAnyRole(['kepegawaian', 'admin', 'direktur', 'wadir', 'kabag', 'kabag_aak', 'kabag_kuk'])) {
            return redirect()->to('/dashboard');
        }

        helper(['avatar']);

        $userModel = new User();
        $targetModel = new TargetKinerja();

        $bulanTerpilih = $this->request->getGet('bulan') ?? (string)date('n');
        $tahunTerpilih = $this->request->getGet('tahun') ?? (string)date('Y');
        $unitFilter    = $this->request->getGet('unit') ?? '';
        $roleFilter    = $this->request->getGet('role') ?? '';
        $statusFilter  = $this->request->getGet('status') ?? '';

        $bulanIndo = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $namaBulan = ($bulanTerpilih === 'all') ? 'Sepanjang Tahun' : ($bulanIndo[(int)$bulanTerpilih - 1] ?? '');

        // Ambil daftar unit kerja untuk filter dropdown
        $units = $userModel->select('unit')->distinct()->where('unit !=', null)->where('unit !=', '')->orderBy('unit', 'ASC')->findAll();
        $daftarUnit = array_column($units, 'unit');

        // Ambil data semua pegawai (kecuali admin) dengan selective columns untuk efisiensi memori
        $builder = $userModel->select('id, nama_lengkap, nip, unit, jabatan, role, atasan_id, foto')
                             ->where('role !=', 'admin');
        if (!empty($unitFilter)) {
            $builder = $builder->where('unit', $unitFilter);
        }
        if (!empty($roleFilter)) {
            if ($roleFilter === 'pimpinan') {
                $builder = $builder->whereIn('role', ['direktur', 'wadir']);
            } elseif ($roleFilter === 'manajemen' || $roleFilter === 'struktural') {
                $builder = $builder->whereIn('role', ['kabag', 'kabag_aak', 'kabag_kuk', 'manajemen', 'katim', 'kapus', 'kanit']);
            } elseif ($roleFilter === 'kepegawaian') {
                $builder = $builder->where('role', 'kepegawaian');
            } elseif ($roleFilter === 'tugas_belajar') {
                $builder = $builder->where('role', 'tugas_belajar');
            } elseif ($roleFilter === 'user' || $roleFilter === 'staf') {
                $builder = $builder->where('role', 'user');
            } else {
                $builder = $builder->where('role', $roleFilter);
            }
        }
        $semuaPegawai = $builder->findAll();

        // Urutkan berdasarkan hierarki resmi institusi
        $semuaPegawai = $this->sortPegawaiByHierarchy($semuaPegawai);

        // Mapping atasan langsung untuk lookup cepat
        $allUsers = $userModel->select('id, nama_lengkap, nip, unit, jabatan')->findAll();
        $userMap = [];
        foreach ($allUsers as $u) {
            $userMap[$u['id']] = $u;
        }

        // Single Batch Fetching Target Kinerja Bulanan
        $userIds = array_column($semuaPegawai, 'id');
        $targetsByUser = [];
        if (!empty($userIds)) {
            $tBuilder = $targetModel->whereIn('user_id', $userIds)
                                    ->where('tahun', $tahunTerpilih);
            if ($bulanTerpilih !== 'all') {
                $tBuilder->where('bulan', $bulanTerpilih);
            }
            $allTargets = $tBuilder->orderBy('id', 'ASC')->findAll();
            foreach ($allTargets as $t) {
                $targetsByUser[$t['user_id']][] = $t;
            }
        }

        // Struktur data rekap monitoring target
        $rekapTarget = [];
        $statTotalPegawai = count($semuaPegawai);
        $statSudahMengirim = 0;
        $statDraft = 0;
        $statBelumMengisi = 0;
        $statSudahDisetujui = 0;
        $statMenungguPersetujuan = 0;
        $statTotalRhk = 0;

        foreach ($semuaPegawai as $p) {
            $pId = $p['id'];
            $pTargets = $targetsByUser[$pId] ?? [];
            $totalRhk = count($pTargets);
            $statTotalRhk += $totalRhk;

            $countTerkirim  = 0;
            $countDisetujui = 0;
            $countDraft     = 0;

            foreach ($pTargets as $item) {
                $stAppr = $item['status_approval'] ?? 'menunggu_persetujuan';
                $stKirim = $item['status'] ?? 'draft';

                if ($stAppr === 'disetujui') {
                    $countDisetujui++;
                }
                if ($stKirim === 'terkirim') {
                    $countTerkirim++;
                } else {
                    $countDraft++;
                }
            }

            // Auto-Approve rule untuk akun Direktur
            $isDirektur = ($p['role'] === 'direktur');
            if ($isDirektur && $countTerkirim > 0) {
                $countDisetujui = $countTerkirim;
            }

            // Tentukan status agregat pengiriman & persetujuan
            if ($totalRhk === 0) {
                $statusPengiriman = 'belum_mengisi';
                $statusPersetujuan = 'belum_mengisi';
                $statBelumMengisi++;
            } elseif ($countDisetujui === $totalRhk) {
                $statusPengiriman = 'disetujui';
                $statusPersetujuan = 'disetujui';
                $statSudahMengirim++;
                $statSudahDisetujui++;
            } elseif ($countTerkirim > 0) {
                if ($countDisetujui > 0) {
                    $statusPengiriman = 'sebagian_disetujui';
                    $statusPersetujuan = 'sebagian_disetujui';
                    $statMenungguPersetujuan++;
                } else {
                    $statusPengiriman = 'terkirim';
                    $statusPersetujuan = 'menunggu_persetujuan';
                    $statMenungguPersetujuan++;
                }
                $statSudahMengirim++;
            } else {
                $statusPengiriman = 'draft';
                $statusPersetujuan = 'draft';
                $statDraft++;
            }

            // Lookup atasan langsung
            $atasanInfo = null;
            if (!empty($p['atasan_id']) && isset($userMap[$p['atasan_id']])) {
                $atasanInfo = $userMap[$p['atasan_id']];
            }

            $rowPegawai = [
                'user'               => $p,
                'atasan'             => $atasanInfo,
                'total_rhk'          => $totalRhk,
                'count_terkirim'     => $countTerkirim,
                'count_disetujui'    => $countDisetujui,
                'count_draft'        => $countDraft,
                'status_pengiriman'  => $statusPengiriman,
                'status_persetujuan' => $statusPersetujuan,
                'targets'            => $pTargets
            ];

            // Filter status jika dipilih
            if (!empty($statusFilter)) {
                if ($statusFilter === 'sudah_mengirim' && !in_array($statusPengiriman, ['terkirim', 'disetujui', 'sebagian_disetujui'])) {
                    continue;
                } elseif ($statusFilter === 'draft' && $statusPengiriman !== 'draft') {
                    continue;
                } elseif ($statusFilter === 'belum_mengisi' && $statusPengiriman !== 'belum_mengisi') {
                    continue;
                } elseif ($statusFilter === 'disetujui' && $statusPersetujuan !== 'disetujui') {
                    continue;
                } elseif ($statusFilter === 'menunggu_persetujuan' && !in_array($statusPersetujuan, ['menunggu_persetujuan', 'sebagian_disetujui'])) {
                    continue;
                }
            }

            $rekapTarget[] = $rowPegawai;
        }

        // Pengecekan informasi deadline target
        $settingModel = new SettingModel();
        $isDeadlineActive = $settingModel->getValue('enable_target_deadline', '0') === '1';
        $batasTarget = (int) $settingModel->getValue('batas_input_target', 5);

        $persenKirim = $statTotalPegawai > 0 ? round(($statSudahMengirim / $statTotalPegawai) * 100, 1) : 0;
        $persenSetuju = $statTotalPegawai > 0 ? round(($statSudahDisetujui / $statTotalPegawai) * 100, 1) : 0;

        $data = [
            'title'                     => 'Monitoring Target Kinerja Bulanan',
            'rekap_target'              => $rekapTarget,
            'bulan_terpilih'            => $bulanTerpilih,
            'tahun_terpilih'            => $tahunTerpilih,
            'nama_bulan'                => $namaBulan,
            'bulan_indo'                => $bulanIndo,
            'daftar_unit'               => $daftarUnit,
            'unit_filter'               => $unitFilter,
            'role_filter'               => $roleFilter,
            'status_filter'             => $statusFilter,
            'stat_total_pegawai'        => $statTotalPegawai,
            'stat_sudah_mengirim'       => $statSudahMengirim,
            'stat_draft'                => $statDraft,
            'stat_belum_mengisi'        => $statBelumMengisi,
            'stat_sudah_disetujui'      => $statSudahDisetujui,
            'stat_menunggu_persetujuan' => $statMenungguPersetujuan,
            'stat_total_rhk'            => $statTotalRhk,
            'persen_kirim'              => $persenKirim,
            'persen_setuju'             => $persenSetuju,
            'is_deadline_active'        => $isDeadlineActive,
            'batas_target'              => $batasTarget,
        ];

        return view('kepegawaian/monitoring_target', $data);
    }

    /**
     * AJAX Endpoint: Mendapatkan Rincian Target Kinerja Pegawai untuk Modal Detail
     */
    public function getDetailTarget()
    {
        if (!hasAnyRole(['kepegawaian', 'admin', 'direktur', 'wadir', 'kabag', 'kabag_aak', 'kabag_kuk'])) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'Akses ditolak']);
        }

        $userId = (int)$this->request->getGet('user_id');
        $bulan  = $this->request->getGet('bulan') ?? (string)date('n');
        $tahun  = $this->request->getGet('tahun') ?? (string)date('Y');

        if (!$userId) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Parameter user_id diperlukan']);
        }

        $userModel = new User();
        $targetModel = new TargetKinerja();

        $user = $userModel->find($userId);
        if (!$user) {
            return $this->response->setStatusCode(404)->setJSON(['success' => false, 'message' => 'Pegawai tidak ditemukan']);
        }

        $atasan = null;
        if (!empty($user['atasan_id'])) {
            $atasan = $userModel->select('id, nama_lengkap, nip, jabatan, unit')->find($user['atasan_id']);
        }

        $builder = $targetModel->where('user_id', $userId)
                               ->where('tahun', $tahun);
        if ($bulan !== 'all') {
            $builder->where('bulan', $bulan);
        }
        $targets = $builder->orderBy('id', 'ASC')->findAll();

        $bulanIndo = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $namaBulan = ($bulan === 'all') ? 'Sepanjang Tahun' : ($bulanIndo[(int)$bulan - 1] ?? "Bulan {$bulan}");

        $targetsWithMonth = [];
        foreach ($targets as $t) {
            $tBulan = (int)($t['bulan'] ?? 1);
            $t['nama_bulan'] = $bulanIndo[$tBulan - 1] ?? "Bulan {$tBulan}";
            $targetsWithMonth[] = $t;
        }

        return $this->response->setJSON([
            'success'    => true,
            'user'       => [
                'id'           => $user['id'],
                'nama_lengkap' => $user['nama_lengkap'],
                'nip'          => $user['nip'] ?? '-',
                'jabatan'      => $user['jabatan'] ?? '-',
                'unit'         => $user['unit'] ?? '-',
                'role'         => $user['role'],
                'foto'         => !empty($user['foto']) ? base_url('uploads/foto_profil/' . $user['foto']) : null
            ],
            'atasan'     => $atasan,
            'periode'    => [
                'bulan'      => $bulan,
                'tahun'      => $tahun,
                'nama_bulan' => $namaBulan
            ],
            'targets'    => $targetsWithMonth,
            'total_rhk'  => count($targetsWithMonth)
        ]);
    }

    /**
     * Ekspor Berkas Excel Multi-Sheet Standar Microsoft Excel (BOM UTF-8)
     */
    public function exportExcel()
    {
        if (!hasAnyRole(['kepegawaian', 'admin', 'direktur', 'wadir', 'kabag', 'kabag_aak', 'kabag_kuk'])) {
            return redirect()->to('/dashboard');
        }

        $userModel = new User();
        $targetModel = new TargetKinerja();

        $bulanTerpilih = $this->request->getGet('bulan') ?? (string)date('n');
        $tahunTerpilih = $this->request->getGet('tahun') ?? (string)date('Y');
        $unitFilter    = $this->request->getGet('unit') ?? '';
        $roleFilter    = $this->request->getGet('role') ?? '';
        $statusFilter  = $this->request->getGet('status') ?? '';

        $bulanIndo = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $namaBulan = ($bulanTerpilih === 'all') ? 'Sepanjang Tahun' : ($bulanIndo[(int)$bulanTerpilih - 1] ?? '');

        // Ambil data pegawai dengan selective columns
        $builder = $userModel->select('id, nama_lengkap, nip, unit, jabatan, role, atasan_id, foto')
                             ->where('role !=', 'admin');
        if (!empty($unitFilter)) $builder->where('unit', $unitFilter);
        if (!empty($roleFilter)) {
            if ($roleFilter === 'pimpinan') {
                $builder->whereIn('role', ['direktur', 'wadir']);
            } elseif ($roleFilter === 'manajemen' || $roleFilter === 'struktural') {
                $builder->whereIn('role', ['kabag', 'kabag_aak', 'kabag_kuk', 'manajemen', 'katim', 'kapus', 'kanit']);
            } elseif ($roleFilter === 'kepegawaian') {
                $builder->where('role', 'kepegawaian');
            } elseif ($roleFilter === 'tugas_belajar') {
                $builder->where('role', 'tugas_belajar');
            } elseif ($roleFilter === 'user' || $roleFilter === 'staf') {
                $builder->where('role', 'user');
            } else {
                $builder->where('role', $roleFilter);
            }
        }
        $semuaPegawai = $this->sortPegawaiByHierarchy($builder->findAll());

        $allUsers = $userModel->select('id, nama_lengkap, nip, unit, jabatan')->findAll();
        $userMap = [];
        foreach ($allUsers as $u) {
            $userMap[$u['id']] = $u;
        }

        $userIds = array_column($semuaPegawai, 'id');
        $targetsByUser = [];
        if (!empty($userIds)) {
            $tBuilder = $targetModel->whereIn('user_id', $userIds)->where('tahun', $tahunTerpilih);
            if ($bulanTerpilih !== 'all') {
                $tBuilder->where('bulan', $bulanTerpilih);
            }
            $allTargets = $tBuilder->orderBy('id', 'ASC')->findAll();
            foreach ($allTargets as $t) {
                $targetsByUser[$t['user_id']][] = $t;
            }
        }

        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()
            ->setCreator("Evidence Command Center (ECC) PKTJ")
            ->setLastModifiedBy("Tim Kepegawaian PKTJ")
            ->setTitle("Monitoring Target Kinerja Bulanan {$namaBulan} {$tahunTerpilih}")
            ->setSubject("Monitoring Target Kinerja")
            ->setDescription("Laporan Rekapitulasi Status Target Kinerja Bulanan Pegawai PKTJ Tegal");

        // SHEET 1: REKAP STATUS TARGET PEGAWAI
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setTitle('Rekap Status Target');

        // Header Title
        $sheet1->setCellValue('A1', 'POLITEKNIK KESELAMATAN TRANSPORTASI JALAN');
        $sheet1->setCellValue('A2', 'MONITORING TARGET KINERJA BULANAN PEGAWAI');
        $sheet1->setCellValue('A3', "PERIODE: " . strtoupper($namaBulan) . " " . $tahunTerpilih);
        $sheet1->getStyle('A1:A3')->getFont()->setBold(true);
        $sheet1->getStyle('A1')->getFont()->setSize(14);
        $sheet1->getStyle('A2:A3')->getFont()->setSize(11);

        $headers1 = ['No', 'Nama Pegawai', 'NIP', 'Jabatan', 'Unit Kerja', 'Atasan Langsung', 'Jml Target (RHK)', 'Status Pengiriman', 'Status Persetujuan'];
        $col1 = 'A';
        foreach ($headers1 as $h) {
            $sheet1->setCellValue($col1 . '5', $h);
            $col1++;
        }
        $sheet1->getStyle('A5:I5')->getFont()->setBold(true);
        $sheet1->getStyle('A5:I5')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('0F172A');
        $sheet1->getStyle('A5:I5')->getFont()->getColor()->setARGB('FFFFFF');
        $sheet1->getStyle('A5:I5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);

        $rowNum = 6;
        $no = 1;
        $rincianRows = [];

        foreach ($semuaPegawai as $p) {
            $pId = $p['id'];
            $pTargets = $targetsByUser[$pId] ?? [];
            $totalRhk = count($pTargets);

            $countTerkirim  = 0;
            $countDisetujui = 0;
            $countDraft     = 0;

            foreach ($pTargets as $item) {
                if (($item['status_approval'] ?? '') === 'disetujui') $countDisetujui++;
                if (($item['status'] ?? '') === 'terkirim') $countTerkirim++;
                else $countDraft++;

                $bNum = (int)($item['bulan'] ?? 1);
                $bName = $bulanIndo[$bNum - 1] ?? "Bulan {$bNum}";

                $rincianRows[] = [
                    'nama'        => $p['nama_lengkap'],
                    'nip'         => $p['nip'] ?? '-',
                    'unit'        => $p['unit'] ?? '-',
                    'bulan'       => $bName,
                    'sasaran'     => $item['sasaran_program'] ?? '',
                    'indikator'   => $item['indikator_kinerja'] ?? '',
                    'target'      => (float)($item['target_bulanan'] ?? 0),
                    'satuan'      => $item['satuan'] ?? '',
                    'status_kirim'=> ($item['status'] ?? 'draft') === 'terkirim' ? 'Terkirim' : 'Draf',
                    'status_appr' => ($item['status_approval'] ?? '') === 'disetujui' ? 'Disetujui' : 'Menunggu Persetujuan'
                ];
            }

            $isDirektur = ($p['role'] === 'direktur');
            if ($isDirektur && $countTerkirim > 0) {
                $countDisetujui = $countTerkirim;
            }

            if ($totalRhk === 0) {
                $statusPengiriman = 'belum_mengisi';
                $statusPersetujuan = 'belum_mengisi';
                $stKirimTxt = 'Belum Mengisi';
                $stApprTxt = 'Belum Mengisi';
            } elseif ($countDisetujui === $totalRhk) {
                $statusPengiriman = 'disetujui';
                $statusPersetujuan = 'disetujui';
                $stKirimTxt = 'Disetujui';
                $stApprTxt = 'Disetujui';
            } elseif ($countTerkirim > 0) {
                $statusPengiriman = ($countDisetujui > 0) ? 'sebagian_disetujui' : 'terkirim';
                $statusPersetujuan = ($countDisetujui > 0) ? 'sebagian_disetujui' : 'menunggu_persetujuan';
                $stKirimTxt = 'Terkirim (' . $countTerkirim . '/' . $totalRhk . ')';
                $stApprTxt = $countDisetujui > 0 ? "Sebagian Disetujui ({$countDisetujui}/{$totalRhk})" : 'Menunggu Persetujuan';
            } else {
                $statusPengiriman = 'draft';
                $statusPersetujuan = 'draft';
                $stKirimTxt = 'Draf (Belum Dikirim)';
                $stApprTxt = 'Draf';
            }

            if (!empty($statusFilter)) {
                if ($statusFilter === 'sudah_mengirim' && !in_array($statusPengiriman, ['terkirim', 'disetujui', 'sebagian_disetujui'])) continue;
                if ($statusFilter === 'draft' && $statusPengiriman !== 'draft') continue;
                if ($statusFilter === 'belum_mengisi' && $statusPengiriman !== 'belum_mengisi') continue;
                if ($statusFilter === 'disetujui' && $statusPersetujuan !== 'disetujui') continue;
                if ($statusFilter === 'menunggu_persetujuan' && !in_array($statusPersetujuan, ['menunggu_persetujuan', 'sebagian_disetujui'])) continue;
            }

            $atasanName = (!empty($p['atasan_id']) && isset($userMap[$p['atasan_id']])) ? $userMap[$p['atasan_id']]['nama_lengkap'] : '-';

            $sheet1->setCellValue('A' . $rowNum, $no++);
            $sheet1->setCellValue('B' . $rowNum, $p['nama_lengkap']);
            $sheet1->setCellValueExplicit('C' . $rowNum, (string)($p['nip'] ?? '-'), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet1->setCellValue('D' . $rowNum, $p['jabatan'] ?? '-');
            $sheet1->setCellValue('E' . $rowNum, $p['unit'] ?? '-');
            $sheet1->setCellValue('F' . $rowNum, $atasanName);
            $sheet1->setCellValue('G' . $rowNum, $totalRhk);
            $sheet1->setCellValue('H' . $rowNum, $stKirimTxt);
            $sheet1->setCellValue('I' . $rowNum, $stApprTxt);

            $sheet1->getStyle('A' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet1->getStyle('G' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet1->getStyle('H' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet1->getStyle('I' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $rowNum++;
        }

        $styleBorder1 = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'CBD5E1'],
                ],
            ],
        ];
        $sheet1->getStyle('A5:I' . ($rowNum - 1))->applyFromArray($styleBorder1);

        foreach (range('A', 'I') as $c) {
            $sheet1->getColumnDimension($c)->setAutoSize(true);
        }

        // SHEET 2: RINCIAN TARGET RHK PER PEGAWAI
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Rincian Target RHK');

        $sheet2->setCellValue('A1', 'RINCIAN TARGET KINERJA BULANAN (RHK)');
        $sheet2->setCellValue('A2', "PERIODE: " . strtoupper($namaBulan) . " " . $tahunTerpilih);
        $sheet2->getStyle('A1:A2')->getFont()->setBold(true);

        $isAllMonth = ($bulanTerpilih === 'all');
        if ($isAllMonth) {
            $headers2 = ['No', 'Nama Pegawai', 'NIP', 'Unit Kerja', 'Bulan', 'Rencana Hasil Kerja (RHK)', 'Indikator Kinerja Individu', 'Target Kuantitas', 'Satuan', 'Status Pengiriman', 'Status Persetujuan'];
            $lastCol2 = 'K';
        } else {
            $headers2 = ['No', 'Nama Pegawai', 'NIP', 'Unit Kerja', 'Rencana Hasil Kerja (RHK)', 'Indikator Kinerja Individu', 'Target Kuantitas', 'Satuan', 'Status Pengiriman', 'Status Persetujuan'];
            $lastCol2 = 'J';
        }

        $col2 = 'A';
        foreach ($headers2 as $h2) {
            $sheet2->setCellValue($col2 . '4', $h2);
            $col2++;
        }
        $sheet2->getStyle("A4:{$lastCol2}4")->getFont()->setBold(true);
        $sheet2->getStyle("A4:{$lastCol2}4")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('0F172A');
        $sheet2->getStyle("A4:{$lastCol2}4")->getFont()->getColor()->setARGB('FFFFFF');
        $sheet2->getStyle("A4:{$lastCol2}4")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);

        $rowNum2 = 5;
        $no2 = 1;
        foreach ($rincianRows as $rItem) {
            $sheet2->setCellValue('A' . $rowNum2, $no2++);
            $sheet2->setCellValue('B' . $rowNum2, $rItem['nama']);
            $sheet2->setCellValueExplicit('C' . $rowNum2, (string)$rItem['nip'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet2->setCellValue('D' . $rowNum2, $rItem['unit']);
            
            if ($isAllMonth) {
                $sheet2->setCellValue('E' . $rowNum2, $rItem['bulan']);
                $sheet2->setCellValue('F' . $rowNum2, $rItem['sasaran']);
                $sheet2->setCellValue('G' . $rowNum2, $rItem['indikator']);
                $sheet2->setCellValue('H' . $rowNum2, $rItem['target']);
                $sheet2->setCellValue('I' . $rowNum2, $rItem['satuan']);
                $sheet2->setCellValue('J' . $rowNum2, $rItem['status_kirim']);
                $sheet2->setCellValue('K' . $rowNum2, $rItem['status_appr']);

                $sheet2->getStyle('E' . $rowNum2)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet2->getStyle('H' . $rowNum2)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet2->getStyle('I' . $rowNum2)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet2->getStyle('J' . $rowNum2)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet2->getStyle('K' . $rowNum2)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            } else {
                $sheet2->setCellValue('E' . $rowNum2, $rItem['sasaran']);
                $sheet2->setCellValue('F' . $rowNum2, $rItem['indikator']);
                $sheet2->setCellValue('G' . $rowNum2, $rItem['target']);
                $sheet2->setCellValue('H' . $rowNum2, $rItem['satuan']);
                $sheet2->setCellValue('I' . $rowNum2, $rItem['status_kirim']);
                $sheet2->setCellValue('J' . $rowNum2, $rItem['status_appr']);

                $sheet2->getStyle('G' . $rowNum2)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet2->getStyle('H' . $rowNum2)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet2->getStyle('I' . $rowNum2)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet2->getStyle('J' . $rowNum2)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }

            $sheet2->getStyle('A' . $rowNum2)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $rowNum2++;
        }

        if ($rowNum2 > 5) {
            $sheet2->getStyle("A4:{$lastCol2}" . ($rowNum2 - 1))->applyFromArray($styleBorder1);
        }
        foreach (range('A', $lastCol2) as $c2) {
            $sheet2->getColumnDimension($c2)->setAutoSize(true);
        }

        // Set active sheet ke sheet pertama
        $spreadsheet->setActiveSheetIndex(0);

        $filename = "Monitoring_Target_Kinerja_" . str_replace(' ', '_', $namaBulan) . "_{$tahunTerpilih}.xlsx";

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        // Audit trail export Excel
        if (function_exists('log_audit')) {
            $currentUserId = session()->get('id') ?? session()->get('user_id');
            log_audit(
                'EXPORT_EXCEL_MONITORING_TARGET',
                'target_kinerja_bulanan',
                $currentUserId,
                null,
                [
                    'bulan'          => $bulanTerpilih,
                    'tahun'          => $tahunTerpilih,
                    'unit'           => $unitFilter,
                    'role'           => $roleFilter,
                    'status'         => $statusFilter,
                    'jumlah_pegawai' => $no - 1
                ]
            );
        }

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    /**
     * Ekspor Berkas PDF Landscape Standar Kedinasan Resmi
     */
    public function exportPdf()
    {
        if (!hasAnyRole(['kepegawaian', 'admin', 'direktur', 'wadir', 'kabag', 'kabag_aak', 'kabag_kuk'])) {
            return redirect()->to('/dashboard');
        }

        $userModel = new User();
        $targetModel = new TargetKinerja();

        $bulanTerpilih = $this->request->getGet('bulan') ?? (string)date('n');
        $tahunTerpilih = $this->request->getGet('tahun') ?? (string)date('Y');
        $unitFilter    = $this->request->getGet('unit') ?? '';
        $roleFilter    = $this->request->getGet('role') ?? '';
        $statusFilter  = $this->request->getGet('status') ?? '';

        $bulanIndo = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $namaBulan = ($bulanTerpilih === 'all') ? 'Sepanjang Tahun' : ($bulanIndo[(int)$bulanTerpilih - 1] ?? '');
        $namaBulanTtd = ($bulanTerpilih === 'all') ? ($bulanIndo[(int)date('n') - 1] ?? 'Januari') : $namaBulan;

        $builder = $userModel->select('id, nama_lengkap, nip, unit, jabatan, role, atasan_id, foto')
                             ->where('role !=', 'admin');
        if (!empty($unitFilter)) $builder->where('unit', $unitFilter);
        if (!empty($roleFilter)) {
            if ($roleFilter === 'pimpinan') {
                $builder->whereIn('role', ['direktur', 'wadir']);
            } elseif ($roleFilter === 'manajemen' || $roleFilter === 'struktural') {
                $builder->whereIn('role', ['kabag', 'kabag_aak', 'kabag_kuk', 'manajemen', 'katim', 'kapus', 'kanit']);
            } elseif ($roleFilter === 'kepegawaian') {
                $builder->where('role', 'kepegawaian');
            } elseif ($roleFilter === 'tugas_belajar') {
                $builder->where('role', 'tugas_belajar');
            } elseif ($roleFilter === 'user' || $roleFilter === 'staf') {
                $builder->where('role', 'user');
            } else {
                $builder->where('role', $roleFilter);
            }
        }
        $semuaPegawai = $this->sortPegawaiByHierarchy($builder->findAll());

        $allUsers = $userModel->select('id, nama_lengkap, nip, unit, jabatan')->findAll();
        $userMap = [];
        foreach ($allUsers as $u) {
            $userMap[$u['id']] = $u;
        }

        $userIds = array_column($semuaPegawai, 'id');
        $targetsByUser = [];
        if (!empty($userIds)) {
            $tBuilder = $targetModel->whereIn('user_id', $userIds)->where('tahun', $tahunTerpilih);
            if ($bulanTerpilih !== 'all') {
                $tBuilder->where('bulan', $bulanTerpilih);
            }
            $allTargets = $tBuilder->orderBy('id', 'ASC')->findAll();
            foreach ($allTargets as $t) {
                $targetsByUser[$t['user_id']][] = $t;
            }
        }

        $rekapTarget = [];
        $statTotal = count($semuaPegawai);
        $statKirim = 0;
        $statDraft = 0;
        $statKosong = 0;
        $statSetuju = 0;

        foreach ($semuaPegawai as $p) {
            $pId = $p['id'];
            $pTargets = $targetsByUser[$pId] ?? [];
            $totalRhk = count($pTargets);

            $countTerkirim  = 0;
            $countDisetujui = 0;

            foreach ($pTargets as $item) {
                if (($item['status_approval'] ?? '') === 'disetujui') $countDisetujui++;
                if (($item['status'] ?? '') === 'terkirim') $countTerkirim++;
            }

            $isDirektur = ($p['role'] === 'direktur');
            if ($isDirektur && $countTerkirim > 0) {
                $countDisetujui = $countTerkirim;
            }

            if ($totalRhk === 0) {
                $statusPengiriman = 'belum_mengisi';
                $statusPersetujuan = 'belum_mengisi';
                $statKosong++;
            } elseif ($countDisetujui === $totalRhk) {
                $statusPengiriman = 'disetujui';
                $statusPersetujuan = 'disetujui';
                $statKirim++;
                $statSetuju++;
            } elseif ($countTerkirim > 0) {
                $statusPengiriman = ($countDisetujui > 0) ? 'sebagian_disetujui' : 'terkirim';
                $statusPersetujuan = ($countDisetujui > 0) ? 'sebagian_disetujui' : 'menunggu_persetujuan';
                $statKirim++;
            } else {
                $statusPengiriman = 'draft';
                $statusPersetujuan = 'draft';
                $statDraft++;
            }

            if (!empty($statusFilter)) {
                if ($statusFilter === 'sudah_mengirim' && !in_array($statusPengiriman, ['terkirim', 'disetujui', 'sebagian_disetujui'])) continue;
                if ($statusFilter === 'draft' && $statusPengiriman !== 'draft') continue;
                if ($statusFilter === 'belum_mengisi' && $statusPengiriman !== 'belum_mengisi') continue;
                if ($statusFilter === 'disetujui' && $statusPersetujuan !== 'disetujui') continue;
                if ($statusFilter === 'menunggu_persetujuan' && !in_array($statusPersetujuan, ['menunggu_persetujuan', 'sebagian_disetujui'])) continue;
            }

            $rekapTarget[] = [
                'user'               => $p,
                'atasan'             => (!empty($p['atasan_id']) && isset($userMap[$p['atasan_id']])) ? $userMap[$p['atasan_id']] : null,
                'total_rhk'          => $totalRhk,
                'status_pengiriman'  => $statusPengiriman,
                'status_persetujuan' => $statusPersetujuan,
            ];
        }

        $data = [
            'rekap_target'   => $rekapTarget,
            'nama_bulan'     => $namaBulan,
            'nama_bulan_ttd' => $namaBulanTtd,
            'tahun_terpilih' => $tahunTerpilih,
            'unit_filter'    => $unitFilter,
            'stat_total'     => $statTotal,
            'stat_kirim'     => $statKirim,
            'stat_draft'     => $statDraft,
            'stat_kosong'    => $statKosong,
            'stat_setuju'    => $statSetuju,
        ];

        $html = view('kepegawaian/monitoring_target_pdf', $data);

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'Helvetica');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        // Audit trail export PDF
        if (function_exists('log_audit')) {
            $currentUserId = session()->get('id') ?? session()->get('user_id');
            log_audit(
                'EXPORT_PDF_MONITORING_TARGET',
                'target_kinerja_bulanan',
                $currentUserId,
                null,
                [
                    'bulan'          => $bulanTerpilih,
                    'tahun'          => $tahunTerpilih,
                    'unit'           => $unitFilter,
                    'role'           => $roleFilter,
                    'status'         => $statusFilter,
                    'jumlah_pegawai' => count($rekapTarget)
                ]
            );
        }

        $filename = "Monitoring_Target_Kinerja_" . str_replace(' ', '_', $namaBulan) . "_{$tahunTerpilih}.pdf";
        $dompdf->stream($filename, ['Attachment' => false]);
        exit;
    }

    /**
     * Menghitung bobot hierarki jabatan organisasi (Hierarki Resmi PKTJ)
     * Direktur -> Wadir -> Kabag -> Katim -> Kapus -> Kanit -> Kaprodi -> Pokja -> Dosen -> JFT -> Staf -> Tubel
     */
    private function getHierarchyWeight($u): int
    {
        $role    = strtolower(trim($u['role'] ?? ''));
        $jabatan = strtolower(trim($u['jabatan'] ?? ''));
        $unit    = strtolower(trim($u['unit'] ?? ''));
        $nama    = strtolower(trim($u['nama_lengkap'] ?? ''));

        // Tier 1: Direktur
        if ($role === 'direktur' || $jabatan === 'direktur' || (strpos($jabatan, 'direktur') === 0 && strpos($jabatan, 'wakil') === false) || $unit === 'direktur') {
            return 100;
        }

        // Tier 2: Wakil Direktur 1, 2, 3
        if (strpos($jabatan, 'wakil direktur 1') !== false || strpos($jabatan, 'wadir 1') !== false || strpos($unit, 'wakil direktur 1') !== false) {
            return 200;
        }
        if (strpos($jabatan, 'wakil direktur 2') !== false || strpos($jabatan, 'wadir 2') !== false || strpos($unit, 'wakil direktur 2') !== false) {
            return 300;
        }
        if (strpos($jabatan, 'wakil direktur 3') !== false || strpos($jabatan, 'wadir 3') !== false || strpos($unit, 'wakil direktur 3') !== false) {
            return 400;
        }
        if ($role === 'wadir' || strpos($jabatan, 'wakil direktur') !== false || strpos($jabatan, 'wadir') !== false) {
            return 450;
        }

        // Tier 3: Kepala Bagian (Kabag AAK / KUK) - di atas Katim & di bawah Wadir
        if (in_array($role, ['kabag', 'kabag_aak', 'kabag_kuk']) || strpos($jabatan, 'kepala bagian') !== false || strpos($jabatan, 'kabag') !== false || (strpos($unit, 'bagian ') === 0 && $role === 'manajemen')) {
            return 500;
        }

        // Tier 4: Ketua Tim (Katim) & Koordinator Substansi - di atas Kapus, Kanit, Kaprodi
        if ($role === 'katim' || strpos($jabatan, 'katim') !== false || strpos($jabatan, 'ketua tim') !== false || strpos($jabatan, 'koordinator') !== false || (strpos($unit, 'tim substansi') !== false && $role === 'manajemen') || (strpos($unit, 'satuan') !== false && $role === 'manajemen')) {
            return 600;
        }

        // Tier 5: Kepala Pusat (Kapus P3M / Karakter)
        if ($role === 'kapus' || strpos($jabatan, 'kapus') !== false || strpos($jabatan, 'kepala pusat') !== false || (strpos($unit, 'pusat') !== false && $role === 'manajemen')) {
            return 700;
        }

        // Tier 6: Kepala Unit (Kanit TI / Lab / Usaha / Kesehatan / Perpus / Bahasa / Asrama)
        if ($role === 'kanit' || strpos($jabatan, 'kanit') !== false || strpos($jabatan, 'kepala unit') !== false || (strpos($unit, 'unit ') === 0 && $role === 'manajemen')) {
            return 800;
        }

        // Tier 7: Ketua / Sekretaris Program Studi (Kaprodi / Sekprodi)
        if (strpos($jabatan, 'kaprodi') !== false || strpos($jabatan, 'ketua prodi') !== false || strpos($jabatan, 'sekretaris prodi') !== false || strpos($jabatan, 'sekprodi') !== false || (strpos($unit, 'prodi') !== false && $role === 'manajemen')) {
            return 900;
        }

        // Tier 8: Ketua Pokja (Kapokja Diklat / Humas / Sarpras) & Manajemen
        if ($role === 'kapokja' || strpos($jabatan, 'kapokja') !== false || strpos($jabatan, 'ketua pokja') !== false || (strpos($unit, 'pokja') !== false && $role === 'manajemen') || $role === 'manajemen') {
            return 1000;
        }

        // Tier 9: Tenaga Pendidik / Dosen / Lektor / Instruktur
        if (strpos($jabatan, 'dosen') !== false || strpos($jabatan, 'lektor') !== false || strpos($jabatan, 'instruktur') !== false || strpos($jabatan, 'asisten ahli') !== false || strpos($jabatan, 'guru') !== false) {
            return 1100;
        }

        // Tier 10: Jabatan Fungsional Tertentu (JFT: Pranata Komputer, Arsiparis, Analis, Auditor, Medis, dll.)
        if (strpos($jabatan, 'ahli') !== false || strpos($jabatan, 'pranata') !== false || strpos($jabatan, 'arsiparis') !== false || strpos($jabatan, 'analis') !== false || strpos($jabatan, 'auditor') !== false || strpos($jabatan, 'pustakawan') !== false || strpos($jabatan, 'terampil') !== false || strpos($jabatan, 'penelaah') !== false || strpos($jabatan, 'perekam') !== false) {
            return 1200;
        }

        // Tier 11: Pegawai Tugas Belajar
        if ($role === 'tugas_belajar' || strpos($jabatan, 'tugas belajar') !== false || strpos($unit, 'tugas belajar') !== false) {
            return 1400;
        }

        // Tier 12: Staf Pelaksana & Fungsional Umum
        return 1300;
    }

    /**
     * Mengurutkan daftar pegawai berdasarkan hierarki jabatan resmi
     */
    private function sortPegawaiByHierarchy(array $pegawaiList): array
    {
        usort($pegawaiList, function($a, $b) {
            $wA = $this->getHierarchyWeight($a);
            $wB = $this->getHierarchyWeight($b);
            if ($wA !== $wB) {
                return $wA <=> $wB;
            }
            return strcasecmp($a['nama_lengkap'] ?? '', $b['nama_lengkap'] ?? '');
        });
        return $pegawaiList;
    }
}
