<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

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

    protected $appends = ['attachment_url', 'attachment_exists'];

    public function getAttachmentUrlAttribute(): ?string
    {
        if (! $this->attachment_path) {
            return null;
        }

        return Storage::disk('public')->url($this->attachment_path);
    }

    public function getAttachmentExistsAttribute(): bool
    {
        return $this->attachment_path
            ? Storage::disk('public')->exists($this->attachment_path)
            : false;
    }
}
