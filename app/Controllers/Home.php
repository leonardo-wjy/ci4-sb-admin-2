<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Home extends BaseController
{
    public function __construct()
    {

    }

    public function index()
    {
        return view('home');
    }
}
