<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * 
     * show admin dashboard
     * 
     * @return \Illuminate\Http\View view
     * 
     */
    public function index(){
        return view('dashboard', ['title' => 'Dashboard']);
    }
}
