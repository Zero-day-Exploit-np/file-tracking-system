<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Department;
use App\Models\Designation;
use Illuminate\Http\Request;
use App\Models\FileRecord;

class AdminDashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', [
            'users' => User::count(),
            'departments' => Department::count(),
            'designations' => Designation::count(),
            'files' => 0 // TEMP FIX
        ]);
    }
}
