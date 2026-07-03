<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\FileMovement;
use App\Models\FileRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileRecordController extends Controller
{
    public function index(Request $request)
    {
        $user  = Auth::user();
        $query = FileRecord::with(['department', 'creator', 'currentHolder']);

        if ($user->role === 'user') {
            // Show files the user created, currently holds, or was involved in via transfer
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
        // super_admin sees all

        if ($request->filled('search')) {
            $s = $request->string('search')->trim()->value();
            $query->where(fn($q) => $q
                ->where('file_name',    'like', "%{$s}%")
                ->orWhere('file_number', 'like', "%{$s}%"));
        }

        if ($request->filled('status')) {
            $allowed = ['active', 'archived', 'draft'];
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
        if (Auth::user()->role !== 'user') {
            abort(403, 'Only users can create files.');
        }
        $this->authorize('create', FileRecord::class);
        return view('files.create');
    }

    public function store(Request $request)
    {
        if (Auth::user()->role !== 'user') {
            abort(403, 'Only users can create files.');
        }
        $this->authorize('create', FileRecord::class);

        $request->validate([
            'file_name'  => 'required|string|max:255',
            'remarks'    => 'nullable|string|max:1000',
            'attachment' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png|max:10240',
        ]);

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

        if ($request->hasFile('attachment')) {
            $uploaded   = $request->file('attachment');
            $storedName = Str::uuid()->toString() . '.' . $uploaded->extension();
            $path       = $uploaded->storeAs('files/' . $file->uuid, $storedName, 'private');

            $file->update([
                'attachment_path' => $path,
                'attachment_name' => $uploaded->getClientOriginalName(),
                'attachment_mime' => $uploaded->getClientMimeType(),
            ]);
        }

        FileMovement::create([
            'file_id'         => $file->id,
            'from_user'       => Auth::id(),
            'to_user'         => Auth::id(),
            'from_department' => $deptId,
            'to_department'   => $deptId,
            'action'          => 'created',
            'remarks'         => 'File created by ' . Auth::user()->name,
        ]);

        return redirect()->route('files.index')->with('success', 'File created successfully.');
    }

    public function edit(FileRecord $file)
    {
        $this->authorize('update', $file);
        return view('files.edit', compact('file'));
    }

    public function update(Request $request, FileRecord $file)
    {
        $this->authorize('update', $file);

        $request->validate([
            'file_name'  => 'required|string|max:255',
            'remarks'    => 'nullable|string|max:1000',
            'attachment' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png|max:10240',
        ]);

        $file->update([
            'file_name' => $request->string('file_name')->trim()->value(),
            'remarks'   => $request->string('remarks')->trim()->value() ?: null,
        ]);

        if ($request->hasFile('attachment')) {
            if ($file->attachment_path && Storage::disk('private')->exists($file->attachment_path)) {
                Storage::disk('private')->delete($file->attachment_path);
            }

            $uploaded   = $request->file('attachment');
            $storedName = Str::uuid()->toString() . '.' . $uploaded->extension();
            $path       = $uploaded->storeAs('files/' . $file->uuid, $storedName, 'private');

            $file->update([
                'attachment_path' => $path,
                'attachment_name' => $uploaded->getClientOriginalName(),
                'attachment_mime' => $uploaded->getClientMimeType(),
            ]);
        }

        return redirect()->route('files.show', $file->uuid)->with('success', 'File updated successfully.');
    }

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

    public function download(FileRecord $file)
    {
        $this->authorize('download', $file);

        if (!$file->attachment_path || !Storage::disk('private')->exists($file->attachment_path)) {
            return redirect()->route('files.show', $file->uuid)
                ->with('error', 'Attachment not found.');
        }

        return Storage::disk('private')->download(
            $file->attachment_path,
            $file->attachment_name ?: $file->file_name
        );
    }
}
