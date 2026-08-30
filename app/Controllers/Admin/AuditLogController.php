<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AuditLog;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class AuditLogController extends BaseController
{
    public function index()
    {
        // Pastikan hanya admin yang bisa mengakses
        if (!hasRole('admin')) {
            return redirect()->to('dashboard')->with('error', 'Akses ditolak.');
        }

        $auditModel = new AuditLog();
        
        $action    = $this->request->getGet('action');
        $entity    = $this->request->getGet('entity');
        $search    = $this->request->getGet('search');
        $dateStart = $this->request->getGet('date_start');
        $dateEnd   = $this->request->getGet('date_end');

        $filters = [];
        if (!empty($action))    $filters['action']    = $action;
        if (!empty($entity))    $filters['entity']    = $entity;
        if (!empty($search))    $filters['search']    = $search;
        if (!empty($dateStart)) $filters['date_start'] = $dateStart;
        if (!empty($dateEnd))   $filters['date_end']   = $dateEnd;

        // Pagination
        $perPage = (int)($this->request->getGet('per_page') ?? 50);
        if (!in_array($perPage, [10, 25, 50, 100])) {
            $perPage = 50;
        }

        $page = (int)($this->request->getVar('page') ?? 1);
        if ($page < 1) $page = 1;
        $offset = ($page - 1) * $perPage;

        $logs = $auditModel->getLogsWithUser($perPage, $offset, $filters);
        $totalRows = $auditModel->countFilteredLogs($filters);

        // Dropdown Filter Entity & Action Unique
        $db = \Config\Database::connect();
        $uniqueActions = $db->table('audit_logs')->select('action')->distinct()->orderBy('action', 'ASC')->get()->getResultArray();
        $uniqueEntities = $db->table('audit_logs')->select('entity')->distinct()->orderBy('entity', 'ASC')->get()->getResultArray();
        
        $pager = \Config\Services::pager();
        $queryParams = array_filter([
            'action'     => $action,
            'entity'     => $entity,
            'search'     => $search,
            'date_start' => $dateStart,
            'date_end'   => $dateEnd,
            'per_page'   => $perPage
        ]);
        if (!empty($queryParams)) {
            $pager->setPath('admin/audit-logs?' . http_build_query($queryParams));
        }

        $data = [
            'title'             => 'Log Keamanan Aktivitas',
            'logs'              => $logs,
            'total_rows'        => $totalRows,
            'per_page'          => $perPage,
            'pager'             => $pager->makeLinks($page, $perPage, $totalRows, 'bootstrap'),
            'filter_action'     => $action,
            'filter_entity'     => $entity,
            'filter_search'     => $search,
            'filter_date_start' => $dateStart,
            'filter_date_end'   => $dateEnd,
            'unique_actions'    => array_column($uniqueActions, 'action'),
            'unique_entities'   => array_column($uniqueEntities, 'entity')
        ];

        return view('admin/audit_logs/index', $data);
    }

    /**
     * Export data audit logs ke format Excel (.xlsx) dengan PhpSpreadsheet
     */
    public function exportExcel()
    {
        if (!hasRole('admin')) {
            return redirect()->to('dashboard')->with('error', 'Akses ditolak.');
        }

        $auditModel = new AuditLog();
        
        $action    = $this->request->getGet('action');
        $entity    = $this->request->getGet('entity');
        $search    = $this->request->getGet('search');
        $dateStart = $this->request->getGet('date_start');
        $dateEnd   = $this->request->getGet('date_end');

        $filters = [];
        if (!empty($action))    $filters['action']    = $action;
        if (!empty($entity))    $filters['entity']    = $entity;
        if (!empty($search))    $filters['search']    = $search;
        if (!empty($dateStart)) $filters['date_start'] = $dateStart;
        if (!empty($dateEnd))   $filters['date_end']   = $dateEnd;

        // Ambil data audit logs sesuai filter (maksimal 5000 baris)
        $logs = $auditModel->getLogsWithUser(5000, 0, $filters);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Audit Logs ECC');

        // Header Title
        $sheet->setCellValue('A1', 'EVIDENCE COMMAND CENTER (ECC) - POLITEKNIK KESELAMATAN TRANSPORTASI JALAN');
        $sheet->setCellValue('A2', 'LOG AUDIT KEAMANAN & AKTIVITAS SISTEM');
        $sheet->setCellValue('A3', 'Diekspor pada: ' . date('d/m/Y H:i:s') . ' WIB | Total Baris: ' . count($logs));

        $sheet->mergeCells('A1:J1');
        $sheet->mergeCells('A2:J2');
        $sheet->mergeCells('A3:J3');

        $sheet->getStyle('A1')->getFont()->setSize(13)->setBold(true)->getColor()->setRGB('1E3A8A');
        $sheet->getStyle('A2')->getFont()->setSize(11)->setBold(true)->getColor()->setRGB('0F172A');
        $sheet->getStyle('A3')->getFont()->setSize(9)->setItalic(true)->getColor()->setRGB('64748B');

        $headers = [
            'No', 'Waktu (WIB)', 'Nama Pengguna', 'NIP', 'Aksi',
            'Modul / Entitas', 'ID Entitas', 'Data Lama (Sebelum)', 'Data Baru (Sesudah)', 'IP Address'
        ];

        $headerRow = 5;
        $colIdx = 1;
        foreach ($headers as $h) {
            $colLetter = Coordinate::stringFromColumnIndex($colIdx);
            $sheet->setCellValue("{$colLetter}{$headerRow}", $h);
            $colIdx++;
        }

        $lastCol = Coordinate::stringFromColumnIndex(count($headers));
        $sheet->getStyle("A{$headerRow}:{$lastCol}{$headerRow}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('1E3A8A');
        $sheet->getStyle("A{$headerRow}:{$lastCol}{$headerRow}")->getFont()->setSize(10)->setBold(true)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle("A{$headerRow}:{$lastCol}{$headerRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension($headerRow)->setRowHeight(24);

        $rowNum = 6;
        $no = 1;
        foreach ($logs as $log) {
            $sheet->setCellValue("A{$rowNum}", $no++);
            $sheet->setCellValue("B{$rowNum}", date('d/m/Y H:i:s', strtotime($log['created_at'])));
            $sheet->setCellValue("C{$rowNum}", $log['nama_lengkap'] ?? 'System / Anonymous');
            $sheet->setCellValueExplicit("D{$rowNum}", (string)($log['nip'] ?? '-'), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue("E{$rowNum}", strtoupper($log['action']));
            $sheet->setCellValue("F{$rowNum}", $log['entity']);
            $sheet->setCellValue("G{$rowNum}", (string)($log['entity_id'] ?? '-'));
            $sheet->setCellValue("H{$rowNum}", $log['old_values'] ?: '-');
            $sheet->setCellValue("I{$rowNum}", $log['new_values'] ?: '-');
            $sheet->setCellValue("J{$rowNum}", $log['ip_address'] ?? '127.0.0.1');

            $sheet->getStyle("A{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("B{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("D{$rowNum}:G{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("J{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $sheet->getStyle("A{$rowNum}:{$lastCol}{$rowNum}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('E2E8F0');
            $rowNum++;
        }

        $sheet->setAutoFilter("A{$headerRow}:{$lastCol}" . ($rowNum - 1));
        $sheet->freezePane('C6');

        $widths = [
            'A' => 6, 'B' => 20, 'C' => 25, 'D' => 20, 'E' => 18,
            'F' => 22, 'G' => 14, 'H' => 32, 'I' => 32, 'J' => 16
        ];
        foreach ($widths as $colLetter => $w) {
            $sheet->getColumnDimension($colLetter)->setWidth($w);
        }

        $fileName = 'Audit_Logs_ECC_' . date('Ymd_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Access-Control-Expose-Headers: Content-Disposition');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
