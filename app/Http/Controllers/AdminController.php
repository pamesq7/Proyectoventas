<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Cargar la vista del dashboard del administrador
        return view('dashboard.admin');
    }
}
