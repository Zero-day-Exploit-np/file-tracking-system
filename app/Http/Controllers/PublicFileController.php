<?php

namespace App\Http\Controllers;

use App\Models\PublicFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PublicFileController extends Controller
{
    public function index()
    {
        $files = PublicFile::latest()->paginate(20);
        return view('admin.public-files.index', compact('files'));
    }

    public function download($id)
    {
        $file = PublicFile::findOrFail($id);

        if (!$file->attachment_path || !Storage::disk('public')->exists($file->attachment_path)) {
            return redirect()
                ->route('admin.public-files.index')
                ->with('error', 'Attachment not found or has been removed.');
        }

        return Storage::disk('public')->download($file->attachment_path);
    }

    public function store(Request $request)
    {
        $request->validate([
            'applicant_name' => 'required|string|max:255',
            'email'          => 'required|email:rfc|max:255',
            'contact_number' => 'required|string|max:20',
            'subject'        => 'required|string|max:255',
            'remarks'        => 'nullable|string|max:1000',
            // MIME + extension + 10 MB limit — blocks php/exe/js/sh/bat/cmd
            'attachment'     => [
                'required',
                'file',
                'mimes:pdf,doc,docx,jpg,jpeg,png',
                'max:10240',
                function ($attribute, $value, $fail) {
                    $blocked = ['php', 'exe', 'js', 'sh', 'bat', 'cmd', 'phtml', 'phar', 'asp', 'aspx', 'htaccess'];
                    $ext = strtolower($value->getClientOriginalExtension());
                    if (in_array($ext, $blocked, true)) {
                        $fail("File type .{$ext} is not allowed.");
                    }
                },
            ],
        ]);

        if ($request->hasFile('attachment')) {
            $file      = $request->file('attachment');
            $ext       = strtolower($file->getClientOriginalExtension());
            // Random UUID filename — never use user-supplied name
            $filename  = Str::uuid() . '.' . $ext;
            $path      = $file->storeAs('uploads', $filename, 'public');

            PublicFile::create([
                'applicant_name'  => $request->applicant_name,
                'email'           => $request->email,
                'contact_number'  => $request->contact_number,
                'subject'         => $request->subject,
                'remarks'         => $request->remarks,
                'attachment_path' => $path,
            ]);
        }

        return back()->with('success', 'Your file submission has been received successfully.');
    }
}
