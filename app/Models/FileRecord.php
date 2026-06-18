<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Department;
use App\Models\FileTransfer;


class FileRecord extends Model
{
    protected $table = 'file_records';

    protected $fillable = [
        'department_id',
        'file_name',
        'file_number',
        'remarks',
        'created_by',
        'current_user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function department()
    {
        return $this->belongsTo(Department::class);
    }

   
    public function currentUser()
    {
        return $this->belongsTo(User::class, 'current_user_id');
    }

    


    public function transfers()
    {
        return $this->hasMany(FileMovement::class, 'file_id');
    }

    public function currentHolder()
    {
        return $this->belongsTo(User::class, 'current_user_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
