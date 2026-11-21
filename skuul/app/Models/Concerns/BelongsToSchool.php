<?php

namespace App\Models\Concerns;

use App\Support\Tenancy\TenantContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait BelongsToSchool
{
    protected static function bootBelongsToSchool(): void
    {
        static::addGlobalScope('school', function (Builder $builder) {
            $schoolId = TenantContext::schoolId();
            $model = $builder->getModel();
            if ($schoolId && $model->getTable() !== 'schools' && in_array('school_id', $model->getFillable(), true)) {
                $builder->where($model->getTable().'.school_id', $schoolId);
            }
        });

        static::creating(function (Model $model) {
            $schoolId = TenantContext::schoolId();
            if ($schoolId && empty($model->school_id) && in_array('school_id', $model->getFillable(), true)) {
                $model->school_id = $schoolId;
            }
        });
    }

    public function school()
    {
        return $this->belongsTo(\App\Models\School::class);
    }

    public function scopeForSchool(Builder $query, $schoolId): Builder
    {
        return $query->withoutGlobalScope('school')->where($query->getModel()->getTable().'.school_id', $schoolId);
    }
}

