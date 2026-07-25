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
     * Get logs joined with user details
     */
    public function getLogsWithUser($limit = null, $offset = 0, $filters = [])
    {
        $builder = $this->select('audit_logs.*, users.nama_lengkap, users.nip')
                        ->join('users', 'users.id = audit_logs.user_id', 'left')
                        ->orderBy('audit_logs.created_at', 'DESC');

        if (!empty($filters['action'])) {
            $builder->where('audit_logs.action', $filters['action']);
        }
        
        if (!empty($filters['entity'])) {
            $builder->where('audit_logs.entity', $filters['entity']);
        }

        if (!empty($filters['user_id'])) {
            $builder->where('audit_logs.user_id', $filters['user_id']);
        }

        if ($limit !== null) {
            return $builder->findAll($limit, $offset);
        }

        return $builder->findAll();
    }
}
