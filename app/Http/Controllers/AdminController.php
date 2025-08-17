<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Dashboard simplificado sin estadísticas específicas
        return view('dashboard');
    }
}
