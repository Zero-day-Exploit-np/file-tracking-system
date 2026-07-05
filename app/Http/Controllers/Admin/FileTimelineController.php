<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FileRecord;
use App\Models\FileMovement;

/**
 * Shows file details + linked-list timeline for admin/super_admin.
 * Both /admin/files/{uuid} and /admin/files/{uuid}/timeline
 * use the same view (admin.files.show) with the same data.
 */
class FileTimelineController extends Controller
{
    /**
     * GET /admin/files/{uuid}/timeline
     * Same view as fileDetails — both show full info + journey.
     */
    public function show(string $uuid)
    {
        $file = $this->loadFile($uuid);
        $this->authorizeFile($file);

        // Chronological order for the linked-list display
        $timeline = FileMovement::with(['fromUser', 'toUser', 'fromDept', 'toDept'])
            ->where('file_id', $file->id)
            ->orderBy('created_at')
            ->get();

        return view('admin.files.show', compact('file', 'timeline'));
    }

    /**
     * GET /admin/files/{uuid}
     * File detail page — identical layout, movements loaded via relationship.
     */
    public function fileDetails(string $uuid)
    {
        $file = $this->loadFile($uuid);
        $this->authorizeFile($file);

        // No separate $timeline — view falls back to $file->movements
        return view('admin.files.show', compact('file'));
    }

    // ── helpers ──────────────────────────────────────────────

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
}
