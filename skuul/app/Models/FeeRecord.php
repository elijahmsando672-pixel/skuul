<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeeRecord extends Model
{
    use BelongsToSchool;

    protected $fillable = [
        'school_id',
        'student_id',
        'fee_structure_id',
        'amount',
        'amount_paid',
        'due_date',
        'status',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'due_date' => 'date',
        'status' => 'string',
        'paid_at' => 'datetime',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function feeStructure(): BelongsTo
    {
        return $this->belongsTo(FeeStructure::class, 'fee_structure_id');
    }

    protected function balance(): Attribute
    {
        return Attribute::get(fn (): float => round(max(0, (float) $this->amount - (float) $this->amount_paid), 2));
    }

    protected function isPaid(): Attribute
    {
        return Attribute::get(fn (): bool => (float) $this->amount_paid >= (float) $this->amount);
    }
}

