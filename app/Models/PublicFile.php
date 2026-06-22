<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class PublicFile extends Model
{
    protected $table = 'public_files';

    protected $fillable = [
        'applicant_name',
        'email',
        'contact_number',
        'subject',
        'remarks',
        'attachment_path',
    ];

    // No longer exposes a direct public URL
    // Use getSignedUrl() for secure, time-limited access

    /**
     * Check if the file physically exists in private storage.
     */
    public function getAttachmentExistsAttribute(): bool
    {
        return $this->attachment_path
            ? Storage::disk('private')->exists($this->attachment_path)
            : false;
    }

    /**
     * Return a 15-minute signed download URL via the secure route.
     * Replaces the old direct /storage URL.
     */
    public function getSignedDownloadUrl(): string
    {
        return URL::temporarySignedRoute(
            'admin.public-files.download',
            now()->addMinutes(15),
            ['id' => $this->id]
        );
    }
}
