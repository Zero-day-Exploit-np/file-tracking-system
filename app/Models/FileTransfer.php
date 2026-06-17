<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\FileRecord;
use App\Models\User;

class FileTransfer extends Model
{
    protected $fillable = [
        'file_record_id',
        'from_user_id',
        'to_user_id',
        'remarks',
    ];

    public function file()
    {
        return $this->belongsTo(FileRecord::class, 'file_record_id');
    }

    public function fromUser()
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function toUser()
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }
}
