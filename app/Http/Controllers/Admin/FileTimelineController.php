<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FileRecord;
use App\Models\FileMovement;

class FileTimelineController extends Controller
{
    public function show($id)
    {
        $file = FileRecord::with(['creator', 'currentHolder'])->findOrFail($id);

        $timeline = FileMovement::with([
            'fromUser',
            'toUser',
            'fromDept',
            'toDept'
        ])
            ->where('file_id', $id)
            ->orderBy('created_at', 'asc')
            ->get();

        return view('admin.files.timeline', compact('file', 'timeline'));
    }
}