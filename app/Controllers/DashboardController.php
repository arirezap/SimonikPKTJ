<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class DashboardController extends BaseController
{
    public function index()
    {
        $role = session()->get('role');
        $adminRoles = ['admin', 'manajemen', 'kabag_aak', 'kabag_kuk'];
        
        if (in_array($role, $adminRoles)) {
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
