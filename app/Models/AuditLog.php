<?php

namespace App\Models;

use CodeIgniter\Model;

class AuditLog extends Model
{
    protected $table            = 'audit_logs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'action',
        'entity',
        'entity_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'created_at'
    ];

    protected $useTimestamps = false; // We set created_at manually via the helper to exact time
    protected $dateFormat    = 'datetime';

    /**
     * Get logs joined with user details with flexible filtering
     */
    public function getLogsWithUser($limit = null, $offset = 0, $filters = [])
    {
        $builder = $this->buildFilterQuery($filters);
        $builder->orderBy('audit_logs.created_at', 'DESC');

        if ($limit !== null) {
            return $builder->findAll($limit, $offset);
        }

        return $builder->findAll();
    }

    /**
     * Count total rows matching filters for pagination
     */
    public function countFilteredLogs($filters = [])
    {
        $builder = $this->buildFilterQuery($filters);
        return $builder->countAllResults();
    }

    /**
     * Helper to build filter query
     */
    private function buildFilterQuery($filters = [])
    {
        $builder = $this->select('audit_logs.*, users.nama_lengkap, users.nip')
                        ->join('users', 'users.id = audit_logs.user_id', 'left');

        if (!empty($filters['search'])) {
            $search = trim($filters['search']);
            $builder->groupStart()
                    ->like('users.nama_lengkap', $search)
                    ->orLike('users.nip', $search)
                    ->orLike('audit_logs.ip_address', $search)
                    ->orLike('audit_logs.entity_id', $search)
                    ->groupEnd();
        }

        if (!empty($filters['action'])) {
            $builder->where('audit_logs.action', $filters['action']);
        }
        
        if (!empty($filters['entity'])) {
            $builder->where('audit_logs.entity', $filters['entity']);
        }

        if (!empty($filters['user_id'])) {
            $builder->where('audit_logs.user_id', $filters['user_id']);
        }

        if (!empty($filters['date_start'])) {
            $builder->where('audit_logs.created_at >=', $filters['date_start'] . ' 00:00:00');
        }

        if (!empty($filters['date_end'])) {
            $builder->where('audit_logs.created_at <=', $filters['date_end'] . ' 23:59:59');
        }

        return $builder;
    }
}
