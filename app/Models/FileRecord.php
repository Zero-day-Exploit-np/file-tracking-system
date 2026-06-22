<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FileRecord extends Model
{
    protected $table = 'file_records';

    protected $fillable = [
        'department_id',
        'file_name',
        'file_number',
        'remarks',
        'created_by',
        'current_user_id',
        'status',
    ];

    /** User who created the file */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /** Current holder of the file */
    public function currentHolder()
    {
        return $this->belongsTo(User::class, 'current_user_id');
    }

    // Alias kept for views that use ->currentUser
    public function currentUser()
    {
        return $this->belongsTo(User::class, 'current_user_id');
    }

    /** Department this file belongs to */
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    /** Movement / timeline history */
    public function movements()
    {
        return $this->hasMany(FileMovement::class, 'file_id');
    }
}
