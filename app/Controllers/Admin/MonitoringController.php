<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\User as UserModel;
use App\Models\RencanaKinerja as RencanaKinerjaModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Dompdf\Dompdf;
use Dompdf\Options;

class MonitoringController extends BaseController
{
    public function index()
    {
        $userModel = new UserModel();
        $rencanaModel = new RencanaKinerjaModel();

        $data = [
            'page_title' => 'Monitoring Kinerja',
            'unit_pokja' => $userModel->where('role', 'user')->findAll(),
            'daftar_tahun' => $rencanaModel->select('tahun_anggaran')->distinct()->orderBy('tahun_anggaran', 'DESC')->findAll(),
        ];

        return view('admin/monitoring_index', $data);
    }

    public function detail($userId, $tahun)
    {
        $userModel = new UserModel();
        $rencanaModel = new RencanaKinerjaModel();

        $user = $userModel->find($userId);
        if (!$user || $user['role'] !== 'user') {
            return redirect()->to('/admin/monitoring')->with('error', 'Tim/Unit/Pokja tidak ditemukan.');
        }

        $data = [
            'page_title'      => 'Detail Kinerja: ' . $user['nama_lengkap'],
            'user'            => $user,
            'tahun_terpilih'  => $tahun,
            'rencana_kinerja' => $rencanaModel->where('user_id', $userId)
                                              ->where('tahun_anggaran', $tahun)
                                              ->findAll(),
        ];

        return view('admin/monitoring_detail', $data);
    }

    /**
     * Mengekspor data ke Excel.
     */
    public function exportExcel($userId, $tahun)
    {
        $userModel = new UserModel();
        $rencanaModel = new RencanaKinerjaModel();
        $user = $userModel->find($userId);

        $rencana_kinerja = $rencanaModel->where('user_id', $userId)
                                      ->where('tahun_anggaran', $tahun)
                                      ->findAll();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header
        $sheet->setCellValue('A1', 'Laporan Kinerja Tim/Unit/Pokja: ' . $user['nama_lengkap']);
        $sheet->setCellValue('A2', 'Tahun Anggaran: ' . $tahun);
        $sheet->mergeCells('A1:E1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

        // Judul Kolom
        $sheet->setCellValue('A4', 'No');
        $sheet->setCellValue('B4', 'Indikator Kinerja');
        $sheet->setCellValue('C4', 'Target Tahunan');
        $sheet->setCellValue('D4', 'Total Realisasi');
        $sheet->setCellValue('E4', 'Capaian (%)');
        $sheet->getStyle('A4:E4')->getFont()->setBold(true);

        $rowNumber = 5;
        $no = 1;
        foreach ($rencana_kinerja as $rencana) {
            $realisasi_bulanan = json_decode($rencana['realisasi_bulanan'], true) ?? [];
            $total_realisasi = array_sum(array_map('floatval', $realisasi_bulanan));
            $target_utama = (float)$rencana['target_utama'];
            $persentase_capaian = ($target_utama > 0) ? ($total_realisasi / $target_utama) * 100 : 0;

            $sheet->setCellValue('A' . $rowNumber, $no++);
            $sheet->setCellValue('B' . $rowNumber, $rencana['indikator_kinerja']);
            $sheet->setCellValue('C' . $rowNumber, $target_utama . ' ' . $rencana['satuan']);
            $sheet->setCellValue('D' . $rowNumber, $total_realisasi);
            $sheet->setCellValue('E' . $rowNumber, round($persentase_capaian, 2) . '%');
            $rowNumber++;
        }

        // Atur lebar kolom
        foreach (range('B', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = 'Laporan_Kinerja_' . $user['username'] . '_' . $tahun . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . urlencode($fileName) . '"');
        $writer->save('php://output');
        exit();
    }

    /**
     * Mengekspor data ke PDF.
     */
    public function exportPdf($userId, $tahun)
    {
        $userModel = new UserModel();
        $rencanaModel = new RencanaKinerjaModel();
        
        $user = $userModel->find($userId);
        $rencana_kinerja = $rencanaModel->where('user_id', $userId)
                                      ->where('tahun_anggaran', $tahun)
                                      ->findAll();

        $data = [
            'user' => $user,
            'tahun_terpilih' => $tahun,
            'rencana_kinerja' => $rencana_kinerja
        ];

        // Muat view khusus untuk PDF
        $html = view('admin/monitoring_pdf', $data);

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape'); // Atur orientasi kertas
        $dompdf->render();
        
        $fileName = 'Laporan_Kinerja_' . $user['username'] . '_' . $tahun . '.pdf';
        $dompdf->stream($fileName, ['Attachment' => true]); // true untuk download, false untuk preview
    }
}
