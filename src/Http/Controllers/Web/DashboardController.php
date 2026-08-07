<?php

namespace Alyani\Subsystem\Http\Controllers\Web;

class DashboardController extends Controller
{
    public function index()
    {
        logger()->info('DASHBOARD SESSION', [
            'id' => request()->session()->getId(),
            'user_id' => auth()->id(),
            'csrf' => request()->session()->token(),
        ]);
        return view('subsystem::admin/dashboard');
    }
}
