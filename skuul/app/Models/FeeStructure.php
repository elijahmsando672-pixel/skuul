<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FeeStructure extends Model
{
    protected $fillable = [
        'academic_year_id',
        'name',
        'amount',
        'frequency',
        'due_date',
        'is_active',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'frequency' => 'string',
        'due_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    public function feeRecords(): HasMany
    {
        return $this->hasMany(FeeRecord::class, 'fee_structure_id');
    }
}
