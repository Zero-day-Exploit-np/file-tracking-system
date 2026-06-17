<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Designation;

class DesignationController extends Controller
{
    //
    // public function index()
    // {
    //     $designations = Designation::with('department')
    //         ->latest()
    //         ->paginate(10);

    //     return view('designations.index', compact('designations'));
    // }



    public function index()
{
    $designations = Designation::latest()->paginate(10);

    return view('designations.index', compact('designations'));
}
}
