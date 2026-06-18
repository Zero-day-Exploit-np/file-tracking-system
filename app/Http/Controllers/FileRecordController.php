<?php

namespace App\Http\Controllers;

use App\Models\FileRecord;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class FileRecordController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->role === 'super_admin') {

            $files = FileRecord::latest()->get();
        } else {

            $files = FileRecord::where(
                'department_id',
                $user->department_id
            )->latest()->get();
        }

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
            'department_id' => 'required|exists:departments,id',
            'file_name' => 'required|string|max:255',
            'remarks' => 'nullable|string',
        ]);

        $departmentId = auth()->user()->role === 'super_admin'
            ? $request->department_id
            : auth()->user()->department_id;

        FileRecord::create([
            'created_by' => auth()->id(),
            'department_id' => $departmentId,
            'file_name' => $request->file_name,
            'file_number' => 'FILE-' . strtoupper(Str::random(10)),
            'remarks' => $request->remarks,
        ]);

        return redirect()
            ->route('files.index')
            ->with('success', 'File created successfully');
    }

    public function show($id)
    {
        $file = FileRecord::with([
            'department',
            'creator',
            'currentHolder',
            'transfers.fromUser',
            'transfers.toUser',
            'transfers.fromDepartment',
            'transfers.toDepartment'
        ])->findOrFail($id);

        $user = auth()->user();

        if (
            $user->role !== 'super_admin' &&
            $file->department_id != $user->department_id
        ) {
            abort(403);
        }

        return view('files.show', compact('file'));
    }
}
