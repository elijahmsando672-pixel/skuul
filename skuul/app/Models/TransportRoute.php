<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TransportRoute extends Model
{
    use HasFactory;
    use BelongsToSchool;

    protected $fillable = [
        'school_id',
        'name',
        'origin',
        'destination',
        'distance_km',
        'estimated_minutes',
        'is_active',
    ];

    protected $casts = [
        'distance_km' => 'decimal:2',
        'estimated_minutes' => 'integer',
        'is_active' => 'boolean',
    ];

    public function assignments(): HasMany
    {
        return $this->hasMany(TransportAssignment::class, 'transport_route_id');
    }

    public function vehicles(): BelongsToMany
    {
        return $this->belongsToMany(Vehicle::class, 'transport_assignments', 'transport_route_id', 'vehicle_id')
            ->withPivot(['assignable_type', 'assignable_id', 'pickup_point', 'academic_year_id', 'is_active'])
            ->withTimestamps();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}

