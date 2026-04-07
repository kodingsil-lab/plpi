<?php

namespace App\Controllers\Public;

use App\Controllers\BaseController;

class HomeController extends BaseController
{
    public function index()
    {
        return view('public/home', [
            'title' => 'PLPI',
            'subtitle' => 'Sistem Informasi Pengelolaan LoA, Invoice, dan Layanan Jurnal',
        ]);
    }
}
