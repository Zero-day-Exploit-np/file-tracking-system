<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FileRecord;
use App\Models\Department;
use Illuminate\Support\Facades\Auth;

class FileRecordController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $files = FileRecord::with(['department', 'creator'])->latest()->get();
        return view('files.index', compact('files'));
    }

    public function create()
    {
        $departments = Department::all();
        return view('files.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'file_name' => 'required',
            'department_id' => 'required',
        ]);

        $fileNumber = 'FILE-' . date('Ymd') . '-' . rand(1000, 9999);

        FileRecord::create([
            'file_number' => $fileNumber,
            'file_name' => $request->file_name,
            'department_id' => $request->department_id,
            'created_by' => Auth::id(),
            'current_user_id' => Auth::id(),
            'remarks' => $request->remarks,
        ]);

        return redirect()->route('files.index')
            ->with('success', 'File created successfully');
    }
    public function show($id)
    {
        $file = FileRecord::with(['transfers.fromUser', 'transfers.toUser'])
            ->findOrFail($id);

        return view('files.show', compact('file'));
    }

    /**
     * Show the form for creating a new resource.
     */


    /**
     * Store a newly created resource in storage.
     */



    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
