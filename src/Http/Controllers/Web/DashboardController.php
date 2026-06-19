<?php

namespace Alyani\Subsystem\Http\Controllers\Web;

class DashboardController extends Controller
{
    public function index()
    {
        return view('subsystem::admin/dashboard');
    }
}
