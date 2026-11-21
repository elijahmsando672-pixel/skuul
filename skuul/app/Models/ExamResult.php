<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamResult extends Model
{
    use HasFactory;
    use BelongsToSchool;

    protected $fillable = [
        'school_id',
        'exam_id',
        'student_id',
        'marks_obtained',
        'grade',
        'remarks',
    ];

    protected $casts = [
        'marks_obtained' => 'decimal:2',
    ];

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class, 'exam_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    protected function percentage(): Attribute
    {
        return Attribute::get(function (): float {
            $total = (float) ($this->exam?->total_marks ?? 0);

            if ($total <= 0) {
                return 0.0;
            }

            return round(((float) $this->marks_obtained / $total) * 100, 2);
        });
    }
}

