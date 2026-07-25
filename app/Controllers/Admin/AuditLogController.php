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
        
        $action = $this->request->getGet('action');
        $entity = $this->request->getGet('entity');
        $user_id = $this->request->getGet('user_id');

        $filters = [];
        if (!empty($action)) $filters['action'] = $action;
        if (!empty($entity)) $filters['entity'] = $entity;
        if (!empty($user_id)) $filters['user_id'] = $user_id;

        // Pagination
        $perPage = 50;
        $page = $this->request->getVar('page') ?? 1;
        $offset = ($page - 1) * $perPage;

        $logs = $auditModel->getLogsWithUser($perPage, $offset, $filters);
        $totalRows = count($auditModel->getLogsWithUser(null, 0, $filters));

        // Untuk Dropdown Filter Entity & Action Unique
        $db = \Config\Database::connect();
        $uniqueActions = $db->table('audit_logs')->select('action')->distinct()->orderBy('action', 'ASC')->get()->getResultArray();
        $uniqueEntities = $db->table('audit_logs')->select('entity')->distinct()->orderBy('entity', 'ASC')->get()->getResultArray();
        
        // Pager custom CI4 manual calculation
        $pager = \Config\Services::pager();

        $data = [
            'title' => 'Log Keamanan Aktivitas',
            'logs' => $logs,
            'pager' => $pager->makeLinks($page, $perPage, $totalRows, 'default_full'),
            'filter_action' => $action,
            'filter_entity' => $entity,
            'unique_actions' => array_column($uniqueActions, 'action'),
            'unique_entities' => array_column($uniqueEntities, 'entity')
        ];

        return view('admin/audit_logs/index', $data);
    }
}
