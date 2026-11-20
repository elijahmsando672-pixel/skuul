<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Grade extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'subject_id',
        'classroom_id',
        'academic_year_id',
        'term_id',
        'assessment_type',
        'score',
        'max_score',
        'weight',
        'date_recorded',
        'remarks',
    ];

    protected $casts = [
        'score' => 'decimal:2',
        'max_score' => 'decimal:2',
        'weight' => 'decimal:2',
        'date_recorded' => 'date',
    ];

    public const ASSESSMENT_QUIZ = 'quiz';
    public const ASSESSMENT_ASSIGNMENT = 'assignment';
    public const ASSESSMENT_MIDTERM = 'midterm';
    public const ASSESSMENT_FINAL = 'final';
    public const ASSESSMENT_PROJECT = 'project';

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class, 'classroom_id');
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    public function term(): BelongsTo
    {
        return $this->belongsTo(Term::class, 'term_id');
    }

    public function percentage(): float
    {
        if ($this->max_score <= 0) {
            return 0.0;
        }

        return round(($this->score / $this->max_score) * 100, 2);
    }
}