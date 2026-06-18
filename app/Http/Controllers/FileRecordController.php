<?php

namespace App\Http\Controllers;

use App\Models\FileRecord;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Models\FileMovement;

class FileRecordController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $query = FileRecord::query();

        // Department restriction
        if ($user->role !== 'super_admin') {
            $query->where('department_id', $user->department_id);
        }

        // Search file name / number
        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('file_name', 'like', "%{$search}%")
                    ->orWhere('file_number', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Date range
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $files = $query->latest()->get();

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

       $file= FileRecord::create([
            'created_by' => auth()->id(),
            'department_id' => $departmentId,
            'file_name' => $request->file_name,
            'file_number' => 'FILE-' . strtoupper(Str::random(10)),
            'remarks' => $request->remarks,
        ]);
        FileMovement::create([
            'file_id' => $file->id,
            'from_user' => auth()->id(),
            'to_user' => auth()->id(),
            'from_department' => $departmentId,
            'to_department' => $departmentId,
            'action' => 'created',
            'remarks' => 'File created'
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
            'movements.fromUser',
            'movements.toUser',
            'movements.fromDept',
            'movements.toDept'
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

