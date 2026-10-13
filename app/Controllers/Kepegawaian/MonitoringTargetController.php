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

        $bulanTerpilih = $this->request->getGet('bulan') ?? (string)date('n');
        $tahunTerpilih = $this->request->getGet('tahun') ?? (string)date('Y');
        $unitFilter    = $this->request->getGet('unit') ?? '';
        $roleFilter    = $this->request->getGet('role') ?? '';
        $statusFilter  = $this->request->getGet('status') ?? '';

        $dataset = $this->prepareMonitoringDataset($bulanTerpilih, $tahunTerpilih, $unitFilter, $roleFilter, $statusFilter);

        // Ambil daftar unit kerja untuk filter dropdown
        $userModel = new User();
        $units = $userModel->select('unit')->distinct()->where('unit !=', null)->where('unit !=', '')->orderBy('unit', 'ASC')->findAll();
        $daftarUnit = array_column($units, 'unit');

        // Pengecekan informasi deadline target
        $settingModel = new SettingModel();
        $isDeadlineActive = $settingModel->getValue('enable_target_deadline', '0') === '1';
        $batasTarget = (int) $settingModel->getValue('batas_input_target', 5);

        $data = array_merge($dataset, [
            'title'              => 'Monitoring Target Kinerja Bulanan',
            'daftar_unit'        => $daftarUnit,
            'is_deadline_active' => $isDeadlineActive,
            'batas_target'       => $batasTarget,
        ]);

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

        // Penyelarasan Atasan Langsung & Penugasan Berdasarkan Periode
        $atasan = null;
        $bulanCek = ($bulan !== 'all') ? (int)$bulan : (int)date('n');
        $penugasan = $userModel->getPenugasanPeriode($userId, $bulanCek, (int)$tahun);
        if ($penugasan) {
            $user['jabatan'] = $penugasan['jabatan'] ?: $user['jabatan'];
            $user['unit']    = $penugasan['unit'] ?: $user['unit'];
            if (!empty($penugasan['atasan_id'])) {
                $atasan = $userModel->select('id, nama_lengkap, nip, jabatan, unit')->find($penugasan['atasan_id']);
            }
        }
        if (!$atasan && !empty($user['atasan_id'])) {
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
                'foto'         => (!empty($user['foto']) && file_exists(FCPATH . 'assets/uploads/profile/' . $user['foto'])) ? base_url('assets/uploads/profile/' . $user['foto']) : null
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

        // Proteksi Throttling: Batasi frekuensi unduhan dokumen berat (Maks 5x / menit)
        if (!$this->checkExportRateLimit('EXPORT_EXCEL_MONITORING_TARGET')) {
            $this->session->setFlashdata('error', 'Pengunduhan dokumen dibatasi. Silakan tunggu beberapa saat sebelum mengunduh kembali.');
            $referer = $this->request->getServer('HTTP_REFERER');
            return !empty($referer) ? redirect()->back() : redirect()->to(site_url('kepegawaian/target-kinerja'));
        }

        $bulanTerpilih = $this->request->getGet('bulan') ?? (string)date('n');
        $tahunTerpilih = $this->request->getGet('tahun') ?? (string)date('Y');
        $unitFilter    = $this->request->getGet('unit') ?? '';
        $roleFilter    = $this->request->getGet('role') ?? '';
        $statusFilter  = $this->request->getGet('status') ?? '';

        $dataset = $this->prepareMonitoringDataset($bulanTerpilih, $tahunTerpilih, $unitFilter, $roleFilter, $statusFilter);

        $namaBulan   = $dataset['nama_bulan'];
        $rekapTarget = $dataset['rekap_target'];
        $rincianRows = $dataset['rincian_rows'];

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

        foreach ($rekapTarget as $row) {
            $p = $row['user'];
            $totalRhk = $row['total_rhk'];
            $statusPengiriman = $row['status_pengiriman'];
            $statusPersetujuan = $row['status_persetujuan'];

            if ($statusPengiriman === 'belum_mengisi') {
                $stKirimTxt = 'Belum Mengisi';
            } elseif ($statusPengiriman === 'disetujui') {
                $stKirimTxt = 'Disetujui';
            } elseif ($statusPengiriman === 'sebagian_disetujui' || $statusPengiriman === 'terkirim') {
                $stKirimTxt = 'Terkirim (' . $row['count_terkirim'] . '/' . $totalRhk . ')';
            } else {
                $stKirimTxt = 'Draf (Belum Dikirim)';
            }

            if ($statusPersetujuan === 'belum_mengisi') {
                $stApprTxt = 'Belum Mengisi';
            } elseif ($statusPersetujuan === 'disetujui') {
                $stApprTxt = 'Disetujui';
            } elseif ($statusPersetujuan === 'sebagian_disetujui') {
                $stApprTxt = "Sebagian Disetujui ({$row['count_disetujui']}/{$totalRhk})";
            } elseif ($statusPersetujuan === 'menunggu_persetujuan') {
                $stApprTxt = 'Menunggu Persetujuan';
            } else {
                $stApprTxt = 'Draf';
            }

            $atasanName = !empty($row['atasan']['nama_lengkap']) ? $row['atasan']['nama_lengkap'] : ($p['role'] === 'direktur' ? 'Auto-Approve (Direktur)' : '-');

            $sheet1->setCellValue('A' . $rowNum, $no++);
            $sheet1->setCellValue('B' . $rowNum, $p['nama_lengkap']);
            $sheet1->setCellValueExplicit('C' . $rowNum, (string)($p['nip'] ?? '-'), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet1->setCellValue('D' . $rowNum, $p['jabatan'] ?? '-');
            $sheet1->setCellValue('E' . $rowNum, $p['unit'] ?? '-');
            $sheet1->setCellValue('F' . $rowNum, $atasanName);
            $sheet1->setCellValue('G' . $rowNum, $totalRhk);
            $sheet1->setCellValue('H' . $rowNum, $stKirimTxt);
            $sheet1->setCellValue('I' . $rowNum, $stApprTxt);

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

        // Bulk Styling Sheet 1 (Cepat & Ringan)
        if ($rowNum > 5) {
            $sheet1->getStyle('A5:A' . ($rowNum - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet1->getStyle('G5:I' . ($rowNum - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet1->getStyle('A5:I' . ($rowNum - 1))->applyFromArray($styleBorder1);
        }

        $s1Widths = ['A' => 6, 'B' => 28, 'C' => 22, 'D' => 24, 'E' => 22, 'F' => 26, 'G' => 14, 'H' => 18, 'I' => 20];
        foreach ($s1Widths as $c => $w) {
            $sheet1->getColumnDimension($c)->setWidth($w);
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
            } else {
                $sheet2->setCellValue('E' . $rowNum2, $rItem['sasaran']);
                $sheet2->setCellValue('F' . $rowNum2, $rItem['indikator']);
                $sheet2->setCellValue('G' . $rowNum2, $rItem['target']);
                $sheet2->setCellValue('H' . $rowNum2, $rItem['satuan']);
                $sheet2->setCellValue('I' . $rowNum2, $rItem['status_kirim']);
                $sheet2->setCellValue('J' . $rowNum2, $rItem['status_appr']);
            }

            $rowNum2++;
        }

        // Bulk Styling Sheet 2
        if ($rowNum2 > 5) {
            $sheet2->getStyle('A5:A' . ($rowNum2 - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            if ($isAllMonth) {
                $sheet2->getStyle('E5:E' . ($rowNum2 - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet2->getStyle('H5:K' . ($rowNum2 - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            } else {
                $sheet2->getStyle('G5:J' . ($rowNum2 - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }
            $sheet2->getStyle("A4:{$lastCol2}" . ($rowNum2 - 1))->applyFromArray($styleBorder1);
        }

        $s2Widths = [
            'A' => 6, 'B' => 28, 'C' => 22, 'D' => 22,
            'E' => 14, 'F' => 30, 'G' => 30, 'H' => 14,
            'I' => 14, 'J' => 18, 'K' => 18
        ];
        foreach (range('A', $lastCol2) as $c2) {
            $sheet2->getColumnDimension($c2)->setWidth($s2Widths[$c2] ?? 16);
        }

        // Set active sheet ke sheet pertama
        $spreadsheet->setActiveSheetIndex(0);

        $filename = "Monitoring_Target_Kinerja_" . str_replace(' ', '_', $namaBulan) . "_{$tahunTerpilih}.xlsx";

        // Bersihkan output buffer sebelum streaming berkas biner Excel
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Access-Control-Expose-Headers: Content-Disposition');
        header('Cache-Control: max-age=0');
        header('Pragma: public');

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
                    'jumlah_pegawai' => count($rekapTarget)
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

        // Proteksi Throttling: Batasi frekuensi unduhan dokumen berat (Maks 5x / menit)
        if (!$this->checkExportRateLimit('EXPORT_PDF_MONITORING_TARGET')) {
            $this->session->setFlashdata('error', 'Pengunduhan dokumen dibatasi. Silakan tunggu beberapa saat sebelum mengunduh kembali.');
            $referer = $this->request->getServer('HTTP_REFERER');
            return !empty($referer) ? redirect()->back() : redirect()->to(site_url('kepegawaian/target-kinerja'));
        }

        $bulanTerpilih = $this->request->getGet('bulan') ?? (string)date('n');
        $tahunTerpilih = $this->request->getGet('tahun') ?? (string)date('Y');
        $unitFilter    = $this->request->getGet('unit') ?? '';
        $roleFilter    = $this->request->getGet('role') ?? '';
        $statusFilter  = $this->request->getGet('status') ?? '';

        $dataset = $this->prepareMonitoringDataset($bulanTerpilih, $tahunTerpilih, $unitFilter, $roleFilter, $statusFilter);

        $data = [
            'rekap_target'   => $dataset['rekap_target'],
            'nama_bulan'     => $dataset['nama_bulan'],
            'nama_bulan_ttd' => $dataset['nama_bulan_ttd'],
            'tahun_terpilih' => $dataset['tahun_terpilih'],
            'unit_filter'    => $dataset['unit_filter'],
            'stat_total'     => $dataset['stat_total_pegawai'],
            'stat_kirim'     => $dataset['stat_sudah_mengirim'],
            'stat_draft'     => $dataset['stat_draft'],
            'stat_kosong'    => $dataset['stat_belum_mengisi'],
            'stat_setuju'    => $dataset['stat_sudah_disetujui'],
            'stat_menunggu'  => $dataset['stat_menunggu_persetujuan'],
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
                    'jumlah_pegawai' => count($dataset['rekap_target'])
                ]
            );
        }

        // Bersihkan output buffer sebelum streaming PDF
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        header('Access-Control-Expose-Headers: Content-Disposition');
        $filename = "Monitoring_Target_Kinerja_" . str_replace(' ', '_', $dataset['nama_bulan']) . "_{$tahunTerpilih}.pdf";
        $dompdf->stream($filename, ['Attachment' => false]);
        exit;
    }

    /**
     * Mempersiapkan dataset rekapitulasi monitoring target kinerja institusi
     * yang digunakan bersama oleh method index(), exportExcel(), dan exportPdf().
     */
    private function prepareMonitoringDataset(string $bulanTerpilih, string $tahunTerpilih, string $unitFilter = '', string $roleFilter = '', string $statusFilter = ''): array
    {
        $userModel = new User();
        $targetModel = new TargetKinerja();

        $bulanIndo = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $namaBulan = ($bulanTerpilih === 'all') ? 'Sepanjang Tahun' : ($bulanIndo[(int)$bulanTerpilih - 1] ?? '');
        $namaBulanTtd = ($bulanTerpilih === 'all') ? ($bulanIndo[(int)date('n') - 1] ?? 'Januari') : $namaBulan;

        // Ambil data semua pegawai (kecuali admin) dengan selective columns untuk efisiensi memori
        $builder = $userModel->select('id, nama_lengkap, nip, unit, jabatan, role, status_pegawai, tmt_selesai, atasan_id, foto');

        // MT-01: Kecualikan role admin (baik primary role maupun pivot user_roles)
        $db = \Config\Database::connect();
        $adminPivotIds = array_column(
            $db->table('user_roles')->select('user_id')->where('role_name', 'admin')->get()->getResultArray(),
            'user_id'
        );
        $builder->where('role !=', 'admin');
        if (!empty($adminPivotIds)) {
            $builder->whereNotIn('id', $adminPivotIds);
        }

        // MT-03: Filter periode mencakup status aktif serta mutasi_keluar, pensiun, dan nonaktif dengan TMT selesai
        if ($bulanTerpilih !== 'all' && !empty($bulanTerpilih)) {
            $periodMonth = sprintf('%04d-%02d-01', (int)$tahunTerpilih, (int)$bulanTerpilih);
            $builder->groupStart()
                    ->where('status_pegawai', 'aktif')
                    ->orGroupStart()
                        ->whereIn('status_pegawai', ['mutasi_keluar', 'pensiun', 'nonaktif'])
                        ->where('tmt_selesai IS NOT NULL')
                        ->where('tmt_selesai >=', $periodMonth)
                    ->groupEnd()
                    ->groupEnd();
        } else {
            $builder->groupStart()
                    ->where('status_pegawai', 'aktif')
                    ->orGroupStart()
                        ->whereIn('status_pegawai', ['mutasi_keluar', 'pensiun', 'nonaktif'])
                        ->where('tmt_selesai IS NOT NULL')
                        ->where('YEAR(tmt_selesai) >=', (int)$tahunTerpilih)
                    ->groupEnd()
                    ->groupEnd();
        }

        if (!empty($unitFilter)) {
            $builder->where('unit', $unitFilter);
        }

        // MT-01: Filter Kategori Jabatan dengan dukungan Multi-Role Pivot (user_roles)
        if (!empty($roleFilter)) {
            if ($roleFilter === 'pimpinan') {
                $targetRoles = ['direktur', 'wadir'];
            } elseif ($roleFilter === 'manajemen' || $roleFilter === 'struktural') {
                $targetRoles = ['kabag', 'kabag_aak', 'kabag_kuk', 'manajemen', 'katim', 'kapus', 'kanit', 'kapokja'];
            } elseif ($roleFilter === 'kepegawaian') {
                $targetRoles = ['kepegawaian'];
            } elseif ($roleFilter === 'tugas_belajar') {
                $targetRoles = ['tugas_belajar'];
            } elseif ($roleFilter === 'user' || $roleFilter === 'staf') {
                $targetRoles = ['user'];
            } else {
                $targetRoles = [$roleFilter];
            }

            $matchedPivotIds = array_column(
                $db->table('user_roles')->select('user_id')->whereIn('role_name', $targetRoles)->get()->getResultArray(),
                'user_id'
            );

            $builder->groupStart()
                    ->whereIn('role', $targetRoles);
            if (!empty($matchedPivotIds)) {
                $builder->orWhereIn('id', $matchedPivotIds);
            }
            $builder->groupEnd();
        }

        $semuaPegawai = $builder->findAll();

        // Urutkan berdasarkan hierarki resmi institusi
        $semuaPegawai = $this->sortPegawaiByHierarchy($semuaPegawai);

        // MT-04: Lookup Atasan Langsung Terfokus (Selective In-Query)
        $atasanIds = array_unique(array_filter(array_column($semuaPegawai, 'atasan_id')));
        $userMap = [];
        if (!empty($atasanIds)) {
            $atasanList = $userModel->select('id, nama_lengkap, nip, unit, jabatan')
                                    ->whereIn('id', $atasanIds)
                                    ->findAll();
            foreach ($atasanList as $u) {
                $userMap[$u['id']] = $u;
            }
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

        // Rekonsiliasi data dan penghitungan agregat KPI
        $rekapTarget = [];
        $rincianRows = [];
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

            // Lookup atasan langsung & penugasan dinamis periode
            $atasanInfo = null;
            $bulanCek = ($bulanTerpilih !== 'all' && !empty($bulanTerpilih)) ? (int)$bulanTerpilih : (int)date('n');
            $penugasanBulan = $userModel->getPenugasanPeriode($pId, $bulanCek, (int)$tahunTerpilih);
            if ($penugasanBulan) {
                $p['jabatan'] = $penugasanBulan['jabatan'] ?: $p['jabatan'];
                $p['unit']    = $penugasanBulan['unit'] ?: $p['unit'];
                if (!empty($penugasanBulan['atasan_id'])) {
                    $atasanIdPeriode = (int)$penugasanBulan['atasan_id'];
                    if (isset($userMap[$atasanIdPeriode])) {
                        $atasanInfo = $userMap[$atasanIdPeriode];
                    } elseif (!empty($penugasanBulan['nama_atasan'])) {
                        $atasanInfo = [
                            'id'           => $atasanIdPeriode,
                            'nama_lengkap' => $penugasanBulan['nama_atasan'],
                            'nip'          => $penugasanBulan['nip_atasan'] ?? '',
                            'jabatan'      => $penugasanBulan['jabatan_atasan'] ?? '',
                            'unit'         => $penugasanBulan['unit'] ?? ''
                        ];
                    } else {
                        $atasanInfo = $userModel->select('id, nama_lengkap, nip, unit, jabatan')->find($atasanIdPeriode);
                    }
                }
            }
            if (!$atasanInfo && !empty($p['atasan_id']) && isset($userMap[$p['atasan_id']])) {
                $atasanInfo = $userMap[$p['atasan_id']];
            }

            $rekapTarget[] = [
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

            // Rincian target untuk Sheet 2 Excel (hanya untuk pegawai yang lolos filter)
            foreach ($pTargets as $item) {
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
        }

        $persenKirim = $statTotalPegawai > 0 ? round(($statSudahMengirim / $statTotalPegawai) * 100, 1) : 0;
        $persenSetuju = $statTotalPegawai > 0 ? round(($statSudahDisetujui / $statTotalPegawai) * 100, 1) : 0;

        return [
            'semua_pegawai'             => $semuaPegawai,
            'rekap_target'              => $rekapTarget,
            'rincian_rows'              => $rincianRows,
            'targets_by_user'           => $targetsByUser,
            'user_map'                  => $userMap,
            'bulan_terpilih'            => $bulanTerpilih,
            'tahun_terpilih'            => $tahunTerpilih,
            'nama_bulan'                => $namaBulan,
            'nama_bulan_ttd'            => $namaBulanTtd,
            'bulan_indo'                => $bulanIndo,
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
        ];
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
