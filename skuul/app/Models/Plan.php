<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = ['name','slug','description','monthly_price','annual_price','currency','student_limit','staff_limit','storage_limit_mb','features','enabled_modules','trial_days','is_custom','is_active'];
    protected $casts = ['monthly_price'=>'decimal:2','annual_price'=>'decimal:2','features'=>'array','enabled_modules'=>'array','is_custom'=>'boolean','is_active'=>'boolean'];

    public function subscriptions(): HasMany { return $this->hasMany(Subscription::class); }
}

