<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Department;
use App\Models\FileTransfer;

class FileRecord extends Model
{
    protected $fillable = [
        'file_number',
        'file_name',
        'department_id',
        'created_by',
        'current_user_id',
        'remarks',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function currentUser()
    {
        return $this->belongsTo(User::class, 'current_user_id');
    }

    public function transfers()
    {
        return $this->hasMany(FileTransfer::class);
    }
}
