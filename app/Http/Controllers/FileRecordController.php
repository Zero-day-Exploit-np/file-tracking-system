<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Department;
use App\Models\FileMovement;
use App\Models\FileRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class FileRecordController extends Controller
{
    public function index(Request $request)
    {
        $user  = Auth::user();
        $query = FileRecord::with(['department', 'creator', 'currentHolder']);

        if ($user->role === 'user') {
            // Show files the user: created, currently holds, or was involved in via transfer history
            $involvedFileIds = \App\Models\FileTransfer::where(fn($q) => $q
                ->where('sender_id',   $user->id)
                ->orWhere('receiver_id', $user->id))
                ->pluck('file_id')
                ->unique()
                ->values();

            $query->where(fn($q) => $q
                ->where('created_by',       $user->id)
                ->orWhere('current_user_id', $user->id)
                ->orWhereIn('id',            $involvedFileIds));
        } elseif ($user->role === 'admin') {
            $query->where('department_id', $user->department_id);
        }
        // super_admin sees all — no additional scope

        if ($request->filled('search')) {
            $s = $request->string('search')->trim()->value();
            $query->where(fn($q) => $q
                ->where('file_name',    'like', "%{$s}%")
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
        // Only role:user can create files — enforced at route + policy level
        // If an admin/super_admin somehow hits this, abort 403
        if (Auth::user()->role !== 'user') {
            abort(403, 'Only users can create files.');
        }
        $this->authorize('create', FileRecord::class);
        $departments = Department::orderBy('name')->get();
        return view('files.create', compact('departments'));
    }

    public function store(Request $request)
    {
        // Only role:user can store files
        if (Auth::user()->role !== 'user') {
            abort(403, 'Only users can create files.');
        }
        $this->authorize('create', FileRecord::class);

        $request->validate([
            'file_name' => 'required|string|max:255',
            'remarks'   => 'nullable|string|max:1000',
        ]);

        // User always uses their own department
        $deptId = Auth::user()->department_id;

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
     * Show file details — policy check via FileRecordPolicy::view().
     */
    public function show(FileRecord $file)
    {
        $this->authorize('view', $file);

        $file->load([
            'department',
            'creator',
            'currentHolder',
            'movements.fromUser',
            'movements.toUser',
            'movements.fromDept',
            'movements.toDept',
        ]);

        return view('files.show', compact('file'));
    }

    /**
     * Download a file attachment — policy check via FileRecordPolicy::download().
     * Logs every download to audit log.
     */
    public function download(FileRecord $file)
    {
        $this->authorize('download', $file);

        try {
            // File records in this system are tracked documents without physical attachments.
            // This action logs the access.
            AuditLog::create([
                'user_id'        => Auth::id(),
                'action'         => 'file_accessed',
                'auditable_type' => FileRecord::class,
                'auditable_id'   => $file->id,
                'description'    => 'File record accessed: ' . $file->file_name,
                'metadata'       => [
                    'file_number' => $file->file_number,
                    'ip'          => request()->ip(),
                ],
            ]);

            return redirect()->route('files.show', $file->uuid)
                ->with('info', 'File record viewed and access logged.');
        } catch (\Throwable $e) {
            Log::error('File access error', [
                'file_id' => $file->id,
                'user_id' => Auth::id(),
                'error'   => $e->getMessage(),
            ]);
            abort(500, 'File access error. Please try again.');
        }
    }
}
