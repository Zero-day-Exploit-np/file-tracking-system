<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\FileRecord;
use App\Models\User;

class FileTransfer extends Model
{
    protected $fillable = [
        'file_id',
        'sender_id',
        'receiver_id',
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

    public function fromDepartment()
    {
        return $this->belongsTo(Department::class, 'from_department_id');
    }

    public function toDepartment()
    {
        return $this->belongsTo(Department::class, 'to_department_id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }


}
