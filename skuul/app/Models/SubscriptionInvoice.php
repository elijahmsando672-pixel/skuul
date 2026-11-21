<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionInvoice extends Model
{
    use HasFactory;
    protected $fillable = ['school_id','subscription_id','invoice_number','amount','currency','status','due_date','paid_at','meta'];
    protected $casts = ['amount'=>'decimal:2','due_date'=>'datetime','paid_at'=>'datetime','meta'=>'array'];

    public function school(): BelongsTo { return $this->belongsTo(School::class); }
    public function subscription(): BelongsTo { return $this->belongsTo(Subscription::class); }
    public function transactions(): HasMany { return $this->hasMany(PaymentTransaction::class); }
}

