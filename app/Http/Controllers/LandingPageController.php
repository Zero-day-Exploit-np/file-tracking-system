<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\FileRecord;
use App\Models\FileTransfer;
use App\Models\User;

class LandingPageController extends Controller
{
    public function index()
    {
        $stats = [
            'departments' => Department::count(),
            'users' => User::count(),
            'files' => FileRecord::count(),
            'transfers' => FileTransfer::count(),
        ];

        return view('welcome', compact('stats'));
    }
}
