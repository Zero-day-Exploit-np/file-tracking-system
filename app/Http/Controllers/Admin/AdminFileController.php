<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FileRecord;
use Illuminate\Http\Request;
use App\Models\FileMovement;


class AdminFileController extends Controller
{
    public function index(Request $request)
    {
        $query = FileRecord::query();

        $user = auth()->user();

        if ($user->role !== 'super_admin') {
            $query->where('department_id', $user->department_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('file_number', 'like', "%$search%")
                    ->orWhere('file_name', 'like', "%$search%")
                    ->orWhere('remarks', 'like', "%$search%");
            });
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
