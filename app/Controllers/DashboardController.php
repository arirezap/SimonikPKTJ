<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class DashboardController extends BaseController
{
    public function index()
    {
        $role = session()->get('role');
        $adminRoles = ['admin', 'direktur', 'wadir', 'manajemen', 'kabag', 'kabag_aak', 'kabag_kuk'];
        
        if (hasAnyRole($adminRoles)) {
            $adminDashboard = new \App\Controllers\Admin\Dashboard();
            $adminDashboard->initController($this->request, $this->response, $this->logger);
            return $adminDashboard->index();
        } else {
            $userDashboard = new \App\Controllers\User\Dashboard();
            $userDashboard->initController($this->request, $this->response, $this->logger);
            return $userDashboard->index();
        }
    }
}
