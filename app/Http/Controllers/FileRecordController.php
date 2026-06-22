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
        $user  = Auth::user();
        $query = FileRecord::with(['department', 'creator', 'currentHolder']);

        // Department isolation — non-super-admin sees only their dept
        if ($user->role !== 'super_admin') {
            $query->where('department_id', $user->department_id);
        }

        // Search — uses Eloquent binding (safe from SQL injection)
        if ($request->filled('search')) {
            $search = $request->string('search')->trim()->value();
            $query->where(function ($q) use ($search) {
                $q->where('file_name',   'like', "%{$search}%")
                  ->orWhere('file_number', 'like', "%{$search}%");
            });
        }

        // Whitelist-validated status filter
        if ($request->filled('status')) {
            $allowed = ['active', 'pending_transfer', 'archived', 'draft'];
            $status  = $request->input('status');
            if (in_array($status, $allowed, true)) {
                $query->where('status', $status);
            }
        }

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->date('from_date'));
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->date('to_date'));
        }

        $files = $query->latest()->paginate(20)->withQueryString();

        return view('files.index', compact('files'));
    }

    public function create()
    {
        $departments = Department::orderBy('name')->get();
        return view('files.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'file_name'     => 'required|string|max:255',
            'remarks'       => 'nullable|string|max:1000',
        ]);

        $departmentId = Auth::user()->role === 'super_admin'
            ? (int) $request->department_id
            : Auth::user()->department_id;

        $file = FileRecord::create([
            'created_by'      => Auth::id(),
            'current_user_id' => Auth::id(),
            'department_id'   => $departmentId,
            'file_name'       => $request->string('file_name')->trim()->value(),
            'file_number'     => 'FILE-' . strtoupper(Str::random(10)),
            'remarks'         => $request->string('remarks')->trim()->value() ?: null,
            'status'          => 'active',
        ]);

        FileMovement::create([
            'file_id'         => $file->id,
            'from_user'       => Auth::id(),
            'to_user'         => Auth::id(),
            'from_department' => $departmentId,
            'to_department'   => $departmentId,
            'action'          => 'created',
            'remarks'         => 'File created',
        ]);

        $this->recordAudit('file_created', $file, [
            'file_number' => $file->file_number,
            'file_name'   => $file->file_name,
            'department'  => $departmentId,
            'created_by'  => Auth::id(),
            'ip'          => $request->ip(),
        ], 'File created by ' . Auth::user()->name);

        return redirect()->route('files.index')->with('success', 'File created successfully.');
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
            'movements.toDept',
        ])->findOrFail($id);

        $user = Auth::user();

        // Authorization: non-super-admin can only see files from their dept
        if ($user->role !== 'super_admin' && (int) $file->department_id !== (int) $user->department_id) {
            abort(403, 'You do not have access to this file.');
        }

        return view('files.show', compact('file'));
    }
}
