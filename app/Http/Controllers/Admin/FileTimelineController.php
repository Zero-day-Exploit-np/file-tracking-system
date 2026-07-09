<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FileRecord;
use App\Models\FileMovement;

/**
 * Shows file details + horizontal linked-list timeline.
 * Both /admin/files/{uuid} and /admin/files/{uuid}/timeline share the same view.
 * Super Admin sees all movements; Department Admin sees only their dept movements.
 */
class FileTimelineController extends Controller
{
    /** GET /admin/files/{uuid}/timeline */
    public function show(string $uuid)
    {
        $file = $this->loadFile($uuid);
        $this->authorizeFile($file);

        $timeline = FileMovement::with(['fromUser', 'toUser', 'fromDept', 'toDept'])
            ->where('file_id', $file->id)
            ->orderBy('created_at')
            ->get();

        return view('admin.files.show', array_merge(
            compact('file', 'timeline'),
            $this->viewerContext()
        ));
    }

    /** GET /admin/files/{uuid} */
    public function fileDetails(string $uuid)
    {
        $file = $this->loadFile($uuid);
        $this->authorizeFile($file);

        return view('admin.files.show', array_merge(
            compact('file'),
            $this->viewerContext()
        ));
    }

    // ── helpers ───────────────────────────────────────────────────

    private function loadFile(string $uuid): FileRecord
    {
        return FileRecord::with([
            'department',
            'creator',
            'currentHolder',
            'currentUser',
            'movements.fromUser',
            'movements.toUser',
            'movements.fromDept',
            'movements.toDept',
        ])->where('uuid', $uuid)->firstOrFail();
    }

    private function authorizeFile(FileRecord $file): void
    {
        $user = auth()->user();
        if ($user->role !== 'super_admin' &&
            (int) $file->department_id !== (int) $user->department_id) {
            abort(403, 'You do not have access to this file.');
        }
    }

    /** Returns viewer context for dept-scoped timeline rendering. */
    private function viewerContext(): array
    {
        $user = auth()->user();
        return [
            'isSuperAdmin'  => $user->role === 'super_admin',
            'viewerDeptId'  => $user->department_id,
        ];
    }
}
