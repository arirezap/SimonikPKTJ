<?php

namespace App\Controllers;

use App\Models\User;

class TestController extends BaseController
{
    public function index()
    {
        $userModel = new User();
        $res78 = $userModel->getAllStaf(78, 'manajemen');
        $res50 = $userModel->getAllStaf(50, 'direktur');
        
        echo "<pre>";
        echo "Staf 78:\n";
        print_r($res78);
        echo "Staf 50:\n";
        print_r($res50);
        echo "</pre>";
    }
}
