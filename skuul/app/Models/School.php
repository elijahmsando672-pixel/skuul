<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class School extends Model
{
    use HasFactory;

    protected $fillable = [
        'name','slug','registration_number','school_type','country','county','address','contact_email','phone','logo_path','primary_color','secondary_color','status','approved_at','suspended_at','suspension_reason','settings'
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'suspended_at' => 'datetime',
        'settings' => 'array',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withPivot(['role','is_owner','is_active','joined_at'])->withTimestamps();
    }

    public function subscriptions(): HasMany { return $this->hasMany(Subscription::class); }
    public function currentSubscription(): HasOne { return $this->hasOne(Subscription::class)->latestOfMany(); }
    public function invoices(): HasMany { return $this->hasMany(SubscriptionInvoice::class); }
    public function paymentTransactions(): HasMany { return $this->hasMany(PaymentTransaction::class); }
}

