<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FileRecord;
use Illuminate\Http\Request;
use App\Models\FileMovement;
use App\Models\Department;


class AdminFileController extends Controller
{
    public function index(Request $request)
    {
        $query = FileRecord::query();

        // Search
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('file_number', 'like', "%{$search}%")
                    ->orWhere('file_name', 'like', "%{$search}%")
                    ->orWhere('remarks', 'like', "%{$search}%");
            });
        }

        // Status Filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Date From
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        // Date To
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $files = $query->latest()->get();

        return view('admin.files.index', compact('files'));
    }
    public function timeline($id)
    {
        $file = FileRecord::findOrFail($id);

        $user = auth()->user();

        if (
            $user->role !== 'super_admin' &&
            $file->department_id != $user->department_id
        ) {
            abort(403);
        }

        $movements = FileMovement::where('file_id', $id)
            ->latest()
            ->get();

        return view('admin.files.timeline', compact('file', 'movements'));
    }
}
