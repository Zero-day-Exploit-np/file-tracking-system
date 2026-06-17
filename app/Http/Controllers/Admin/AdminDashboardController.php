<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Department;
use App\Models\Designation;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Admin belongs to ONE department
        $departmentId = $user->department_id;

        $totalUsers = User::where('department_id', $departmentId)
            ->where('role', 'user')
            ->count();

        $totalDesignations = Designation::where('department_id', $departmentId)->count();

        return view('admin.dashboard', compact('totalUsers', 'totalDesignations'));
    }
}
