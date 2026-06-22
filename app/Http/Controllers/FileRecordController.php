<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\FileMovement;
use App\Models\FileRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class FileRecordController extends Controller
{
    public function index(Request $request)
    {
        $user  = Auth::user();
        $query = FileRecord::with(['department', 'creator', 'currentHolder']);

        if ($user->role !== 'super_admin') {
            $query->where('department_id', $user->department_id);
        }

        if ($request->filled('search')) {
            $s = $request->string('search')->trim()->value();
            $query->where(fn($q) => $q
                ->where('file_name',   'like', "%{$s}%")
                ->orWhere('file_number', 'like', "%{$s}%"));
        }

        if ($request->filled('status')) {
            $allowed = ['active', 'pending_transfer', 'archived', 'draft'];
            if (in_array($request->status, $allowed, true)) {
                $query->where('status', $request->status);
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

        $deptId = Auth::user()->role === 'super_admin'
            ? (int) $request->department_id
            : Auth::user()->department_id;

        $file = FileRecord::create([
            'created_by'      => Auth::id(),
            'current_user_id' => Auth::id(),
            'department_id'   => $deptId,
            'file_name'       => $request->string('file_name')->trim()->value(),
            'file_number'     => 'FILE-' . strtoupper(Str::random(10)),
            'remarks'         => $request->string('remarks')->trim()->value() ?: null,
            'status'          => 'active',
        ]);

        FileMovement::create([
            'file_id'         => $file->id,
            'from_user'       => Auth::id(),
            'to_user'         => Auth::id(),
            'from_department' => $deptId,
            'to_department'   => $deptId,
            'action'          => 'created',
            'remarks'         => 'File created',
        ]);

        $this->recordAudit('file_created', $file, [
            'file_number' => $file->file_number,
            'department'  => $deptId,
            'ip'          => $request->ip(),
        ], 'File created by ' . Auth::user()->name);

        return redirect()->route('files.index')->with('success', 'File created successfully.');
    }

    /**
     * Route model binding resolves by UUID (FileRecord::getRouteKeyName = 'uuid').
     */
    public function show(FileRecord $file)
    {
        $user = Auth::user();
        if ($user->role !== 'super_admin' &&
            (int) $file->department_id !== (int) $user->department_id) {
            abort(403, 'You do not have access to this file.');
        }

        $file->load([
            'department', 'creator', 'currentHolder',
            'movements.fromUser', 'movements.toUser',
            'movements.fromDept', 'movements.toDept',
        ]);

        return view('files.show', compact('file'));
    }
}
