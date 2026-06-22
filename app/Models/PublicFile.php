<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
