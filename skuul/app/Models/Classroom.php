<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Classroom extends Model
{
    use HasFactory;
    use BelongsToSchool;

    protected $fillable = [
        'school_id',
        'name',
        'grade_level',
        'section',
        'academic_year_id',
        'teacher_id',
        'room_number',
        'capacity',
    ];

    protected $casts = [
        'capacity' => 'integer',
    ];

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class, 'classroom_id');
    }

    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class, 'classroom_id');
    }

    public function attendance(): HasMany
    {
        return $this->hasMany(Attendance::class, 'classroom_id');
    }

    public function scopeActive($query)
    {
        return $query->whereHas('academicYear', function ($q) {
            $q->where('is_active', true);
        });
    }
}
