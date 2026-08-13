<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AuditLog;

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
}
