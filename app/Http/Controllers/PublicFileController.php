<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
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

    /**
     * Serve a file via the signed URL.
     * Handles files on both 'private' disk and legacy 'public' disk.
     */
    public function download(string $uuid)
    {
        $file = PublicFile::where('uuid', $uuid)->firstOrFail();

        if (!$file->attachment_path) {
            return redirect()->route('admin.public-files.index')
                ->with('error', 'No attachment on record for this submission.');
        }

        // Determine which disk the file is on
        $disk     = null;
        $diskName = null;

        if (Storage::disk('private')->exists($file->attachment_path)) {
            $disk     = Storage::disk('private');
            $diskName = 'private';
        } elseif (Storage::disk('public')->exists($file->attachment_path)) {
            $disk     = Storage::disk('public');
            $diskName = 'public';
        }

        if (!$disk) {
            \Illuminate\Support\Facades\Log::warning('PublicFile download: file not found on any disk', [
                'uuid'            => $uuid,
                'attachment_path' => $file->attachment_path,
                'user_id'         => auth()->id(),
            ]);
            abort(404, 'Attachment file not found. It may have been removed.');
        }

        // Audit the download
        AuditLog::create([
            'user_id'        => auth()->id(),
            'action'         => 'file_downloaded',
            'auditable_type' => PublicFile::class,
            'auditable_id'   => $file->id,
            'description'    => 'Downloaded: ' . $file->subject,
            'metadata'       => [
                'ip'        => request()->ip(),
                'disk'      => $diskName,
                'filename'  => basename($file->attachment_path),
            ],
        ]);

        // Generate a clean download filename for the user
        $ext          = pathinfo($file->attachment_path, PATHINFO_EXTENSION);
        $downloadName = Str::slug($file->subject) . '.' . $ext;

        return $disk->download($file->attachment_path, $downloadName);
    }

    /**
     * Store public submission — private disk, UUID filename.
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

        $uploadedFile = $request->file('attachment');
        $ext          = strtolower($uploadedFile->getClientOriginalExtension());
        $filename     = Str::uuid() . '.' . $ext;
        $path         = $uploadedFile->storeAs('uploads', $filename, 'private');

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
