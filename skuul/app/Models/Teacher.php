<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Teacher extends Model
{
    use HasFactory;
    use BelongsToSchool;

    protected $fillable = [
        'school_id',
        'user_id',
        'employee_id',
        'department',
        'hire_date',
        'qualification',
        'specialization',
    ];

    protected $casts = [
        'hire_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class, 'teacher_id');
    }

    public function attendance(): HasMany
    {
        return $this->hasMany(Attendance::class, 'teacher_id');
    }
}
