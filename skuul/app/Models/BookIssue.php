<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\Relation;

class BookIssue extends Model
{
    use HasFactory;

    public const STATUS_ISSUED = 'issued';

    public const STATUS_RETURNED = 'returned';

    protected $fillable = [
        'book_id',
        'borrower_type',
        'borrower_id',
        'issued_at',
        'due_date',
        'returned_at',
        'status',
    ];

    protected $casts = [
        'issued_at' => 'date',
        'due_date' => 'date',
        'returned_at' => 'date',
    ];

    protected static function booted(): void
    {
        Relation::morphMap([
            'student' => Student::class,
            'teacher' => Teacher::class,
        ], true);
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class, 'book_id');
    }

    public function borrower(): MorphTo
    {
        return $this->morphTo();
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'borrower_id');
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'borrower_id');
    }

    protected function isOverdue(): Attribute
    {
        return Attribute::get(fn (): bool => $this->status === self::STATUS_ISSUED
            && $this->returned_at === null
            && $this->due_date !== null
            && $this->due_date->isPast());
    }
}
