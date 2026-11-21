<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subscription extends Model
{
    use HasFactory;
    protected $fillable = ['school_id','plan_id','billing_cycle','status','trial_ends_at','starts_at','ends_at','next_billing_at','cancelled_at','grace_ends_at','meta'];
    protected $casts = ['trial_ends_at'=>'datetime','starts_at'=>'datetime','ends_at'=>'datetime','next_billing_at'=>'datetime','cancelled_at'=>'datetime','grace_ends_at'=>'datetime','meta'=>'array'];

    public function school(): BelongsTo { return $this->belongsTo(School::class); }
    public function plan(): BelongsTo { return $this->belongsTo(Plan::class); }
    public function invoices(): HasMany { return $this->hasMany(SubscriptionInvoice::class); }
}

