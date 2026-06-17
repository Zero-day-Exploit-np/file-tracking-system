<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;
use App\Models\Designation;


class Department extends Model
{
    protected $fillable = [
        'name',
        'code',
        'is_active',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
    public function designations()
    {
        return $this->hasMany(Designation::class);
    }
}
