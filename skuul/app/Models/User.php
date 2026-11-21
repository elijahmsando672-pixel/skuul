<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasRoles, Notifiable;

    protected $fillable = [
        'current_school_id',
        'name',
        'email',
        'password',
        'phone',
        'address',
        'date_of_birth',
        'gender',
        'profile_photo',
        'role_type',
        'is_active',
        'is_platform_admin',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'date_of_birth' => 'date',
        'is_active' => 'boolean',
        'is_platform_admin' => 'boolean',
    ];

    public function student(): HasOne
    {
        return $this->hasOne(Student::class, 'user_id');
    }

    public function teacher(): HasOne
    {
        return $this->hasOne(Teacher::class, 'user_id');
    }

    public function parent(): HasOne
    {
        return $this->hasOne(ParentModel::class, 'user_id');
    }

    public function schools()
    {
        return $this->belongsToMany(School::class)->withPivot(['role','is_owner','is_active','joined_at'])->withTimestamps();
    }

    public function currentSchool()
    {
        return $this->belongsTo(School::class, 'current_school_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}