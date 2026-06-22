<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FileRecord;
use App\Models\FileMovement;

class FileTimelineController extends Controller
{
    /**
     * Show file timeline using UUID route binding.
     * Route: GET /admin/files/{uuid}/timeline
     */
    public function show(string $uuid)
    {
        $file = FileRecord::with(['currentUser', 'department'])
            ->where('uuid', $uuid)
            ->firstOrFail();

        $this->authorizeFile($file);

        $timeline = FileMovement::with(['fromUser', 'toUser', 'fromDept', 'toDept'])
            ->where('file_id', $file->id)
            ->orderByDesc('created_at')
            ->get();

        return view('admin.files.show', compact('file', 'timeline'));
    }

    /**
     * Show file details using UUID.
     * Route: GET /admin/files/{uuid}
     */
    public function fileDetails(string $uuid)
    {
        $file = FileRecord::with([
            'currentUser',
            'department',
            'movements.fromUser',
            'movements.toUser',
            'movements.fromDept',
            'movements.toDept',
        ])->where('uuid', $uuid)->firstOrFail();

        $this->authorizeFile($file);

        return view('admin.files.show', compact('file'));
    }

    private function authorizeFile(FileRecord $file): void
    {
        $user = auth()->user();
        if ($user->role !== 'super_admin' &&
            (int) $file->department_id !== (int) $user->department_id) {
            abort(403, 'You do not have access to this file.');
        }
    }
}
