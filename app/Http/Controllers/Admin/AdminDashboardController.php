<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Department;
use App\Models\Designation;
use Illuminate\Http\Request;
use App\Models\FileRecord;
use App\Models\FileTransfer;


class AdminDashboardController extends Controller
{
    public function index()

    {


    
        return view('admin.dashboard', [
            'users' => User::count(),
            'departments' => Department::count(),
            'designations' => Designation::count(),
            'files' => FileRecord::count(),

            // 🔥 IMPORTANT: load relations
            'recentTransfers' => FileTransfer::with(['sender.designation', 'receiver.designation', 'file'])
                ->latest()
                ->take(5)
                ->get(),



                

            'recentUsers' => User::with(['designation', 'department'])
                ->latest()
                ->take(5)
                ->get(),
        ]);
    }
}
