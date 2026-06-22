<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\PublicFile;
use App\Services\DashboardService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class SecureFileDownloadController extends Controller
{
    /**
     * Generate a temporary signed download URL for a public file.
     * The signed URL expires in 15 minutes.
     */
    public function generateLink(int $id)
    {
        $file = PublicFile::findOrFail($id);
        $user = Auth::user();

        if (!in_array($user->role, ['admin', 'super_admin'], true)) {
            abort(403);
        }

        if (!$file->attachment_path || !Storage::disk('private')->exists($file->attachment_path)) {
            return back()->with('error', 'File not found in storage.');
        }

        // Create a 15-minute signed URL
        $signedUrl = URL::temporarySignedRoute(
            'admin.files.secure-download',
            now()->addMinutes(15),
            ['id' => $id]
        );

        return redirect($signedUrl);
    }

    /**
     * Serve the file after validating the signed URL.
     */
    public function download(int $id)
    {
        $file = PublicFile::findOrFail($id);
        $user = Auth::user();

        if (!Storage::disk('private')->exists($file->attachment_path)) {
            abort(404, 'File not found.');
        }

        // Log the download
        AuditLog::create([
            'user_id'        => $user->id,
            'action'         => 'file_downloaded',
            'auditable_type' => PublicFile::class,
            'auditable_id'   => $file->id,
            'description'    => "File downloaded: {$file->subject}",
            'metadata'       => [
                'file_id'    => $file->id,
                'subject'    => $file->subject,
                'ip'         => request()->ip(),
                'user_agent' => request()->userAgent(),
            ],
        ]);

        return Storage::disk('private')->download($file->attachment_path, $file->subject . '.pdf');
    }
}
