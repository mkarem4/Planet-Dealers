<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    public function index()
    {
        return view('merchant.dashboard',get_defined_vars());
    }
}
