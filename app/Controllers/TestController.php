<?php

namespace App\Controllers;

use App\Models\User;

class TestController extends BaseController
{
    public function index()
    {
        $userModel = new User();
        $res78 = $userModel->getAllBawahan(78, 'manajemen');
        $res50 = $userModel->getAllBawahan(50, 'direktur');
        
        echo "<pre>";
        echo "Bawahan 78:\n";
        print_r($res78);
        echo "Bawahan 50:\n";
        print_r($res50);
        echo "</pre>";
    }
}
