<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class FileRecord extends Model
{
    protected $table = 'file_records';

    protected $fillable = [
        'uuid',
        'department_id',
        'file_name',
        'file_number',
        'remarks',
        'attachment_path',
        'attachment_name',
        'attachment_mime',
        'created_by',
        'current_user_id',
        'status',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = Str::uuid()->toString();
            }
        });
        // Invalidate dashboard caches after writes
        static::created(fn()  => \App\Services\DashboardService::clearSuperAdminCache());
        static::deleted(fn()  => \App\Services\DashboardService::clearSuperAdminCache());
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function currentHolder()
    {
        return $this->belongsTo(User::class, 'current_user_id');
    }

    // Alias for views using ->currentUser
    public function currentUser()
    {
        return $this->belongsTo(User::class, 'current_user_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function movements()
    {
        return $this->hasMany(FileMovement::class, 'file_id');
    }
}
