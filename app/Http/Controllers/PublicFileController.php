<?php

namespace App\Http\Controllers;

use App\Models\PublicFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class PublicFileController extends Controller
{
    public function index()
    {
        $files = PublicFile::latest()->paginate(20);
        return view('admin.public-files.index', compact('files'));
    }

    /**
     * Generate a 15-minute signed download URL (replaces direct /storage access).
     */
    public function download(string $uuid)
    {
        $file = \App\Models\PublicFile::where('uuid', $uuid)->firstOrFail();

        if (!$file->attachment_path || !Storage::disk('private')->exists($file->attachment_path)) {
            return redirect()->route('admin.public-files.index')
                ->with('error', 'Attachment not found or has been removed.');
        }

        // Log the download
        \App\Models\AuditLog::create([
            'user_id'        => auth()->id(),
            'action'         => 'file_downloaded',
            'auditable_type' => \App\Models\PublicFile::class,
            'auditable_id'   => $file->id,
            'description'    => "Downloaded: {$file->subject}",
            'metadata'       => ['ip' => request()->ip()],
        ]);

        return Storage::disk('private')->download($file->attachment_path);
    }

    /**
     * Store uploaded file in private storage with UUID filename.
     * Files are NOT accessible via /storage URL.
     */
    public function store(Request $request)
    {
        $request->validate([
            'applicant_name' => 'required|string|max:255',
            'email'          => 'required|email:rfc|max:255',
            'contact_number' => 'required|string|max:20',
            'subject'        => 'required|string|max:255',
            'remarks'        => 'nullable|string|max:1000',
            'attachment'     => [
                'required', 'file',
                'mimes:pdf,doc,docx,jpg,jpeg,png',
                'max:10240',
                function ($attribute, $value, $fail) {
                    $blocked = ['php', 'exe', 'js', 'sh', 'bat', 'cmd', 'phtml', 'phar', 'asp', 'aspx'];
                    $ext     = strtolower($value->getClientOriginalExtension());
                    if (in_array($ext, $blocked, true)) {
                        $fail("File type .{$ext} is not permitted.");
                    }
                },
            ],
        ]);

        $file     = $request->file('attachment');
        $ext      = strtolower($file->getClientOriginalExtension());
        $filename = Str::uuid() . '.' . $ext;

        // Store in PRIVATE disk (storage/app/private/uploads)
        $path = $file->storeAs('uploads', $filename, 'private');

        PublicFile::create([
            'applicant_name'  => $request->applicant_name,
            'email'           => $request->email,
            'contact_number'  => $request->contact_number,
            'subject'         => $request->subject,
            'remarks'         => $request->remarks,
            'attachment_path' => $path,
        ]);

        return back()->with('success', 'Your submission has been received successfully.');
    }
}
