<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class TransportAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'transport_route_id',
        'vehicle_id',
        'assignable_type',
        'assignable_id',
        'pickup_point',
        'academic_year_id',
        'is_active',
    ];

    protected $casts = [
        'transport_route_id' => 'integer',
        'vehicle_id' => 'integer',
        'assignable_id' => 'integer',
        'academic_year_id' => 'integer',
        'is_active' => 'boolean',
    ];

    public function transportRoute(): BelongsTo
    {
        return $this->belongsTo(TransportRoute::class, 'transport_route_id');
    }

    public function route(): BelongsTo
    {
        return $this->transportRoute();
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    public function assignable(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
