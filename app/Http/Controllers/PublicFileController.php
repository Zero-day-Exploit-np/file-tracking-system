<?php

namespace App\Http\Controllers;

use App\Models\PublicFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PublicFileController extends Controller
{
    public function index()
    {
        $files = PublicFile::latest()->get();

        return view(
            'admin.public-files.index',
            compact('files')
        );
    }

    public function download($id)
    {
        $file = PublicFile::findOrFail($id);

        if (! $file->attachment_path || ! Storage::disk('public')->exists($file->attachment_path)) {
            return redirect()
                ->route('admin.public-files.index')
                ->with('error', 'Attachment not found or has been removed.');
        }

        return Storage::disk('public')->download($file->attachment_path);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'applicant_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'contact_number' => 'required|string|max:20',
            'subject' => 'required|string|max:255',
            'remarks' => 'nullable|string|max:1000',
            'attachment' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('attachment')) {
            $validated['attachment_path'] = $request->file('attachment')
                ->store('uploads', 'public');
        }

        PublicFile::create($validated);

        return back()->with('success', 'Your file submission has been received successfully.');
    }
}
