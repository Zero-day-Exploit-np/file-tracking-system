<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Designation extends Model
{
    protected $fillable = [
        'department_id',
        'name',
        'is_active',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
    public function department()
    {
        return $this->belongsTo(Department::class);
    }
    
}