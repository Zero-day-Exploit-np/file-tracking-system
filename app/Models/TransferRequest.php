<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransferRequest extends Model
{
    protected $fillable = [
        'file_id',
        'requested_by',
        'from_department',
        'to_department',
        'target_user',
        'status',
    ];

    public function file()
    {
        return $this->belongsTo(FileRecord::class, 'file_id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'target_user');
    }

    public function fromDept()
    {
        return $this->belongsTo(Department::class, 'from_department');
    }

    public function toDept()
    {
        return $this->belongsTo(Department::class, 'to_department');
    }
}
