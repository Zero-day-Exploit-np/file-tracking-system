<?php

namespace App\Http\Controllers;

use App\Models\FileRecord;
use Illuminate\Http\Request;

class PublicFileSearchController extends Controller
{
    /**
     * Show the public file search page.
     */
    public function index()
    {
        return view('public.file-search');
    }

    /**
     * Search for a file by file number and return only safe public fields.
     * Never exposes internal remarks, user info, transfer history, or holder.
     */
    public function search(Request $request)
    {
        $request->validate([
            'file_number' => 'required|string|max:100',
        ]);

        $fileNumber = trim($request->string('file_number')->value());

        $file = FileRecord::where('file_number', $fileNumber)
            ->with('department')
            ->first();

        if (!$file) {
            return back()
                ->withInput()
                ->with('search_error', 'No file found with this File Number.');
        }

        // Only expose safe public fields — no internal data
        $result = [
            'file_number'  => $file->file_number,
            'file_name'    => $file->file_name,
            'department'   => $file->department->name ?? 'N/A',
            'status'       => ucwords(str_replace('_', ' ', $file->status)),
            'created_date' => $file->created_at->format('d M Y'),
        ];

        return view('public.file-search', compact('result'))->with('searched', true);
    }
}
