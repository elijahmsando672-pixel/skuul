<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentTransaction extends Model
{
    use HasFactory;
    protected $fillable = ['school_id','subscription_id','subscription_invoice_id','provider','reference','merchant_request_id','checkout_request_id','phone_number','amount','currency','status','payload','paid_at'];
    protected $casts = ['amount'=>'decimal:2','payload'=>'array','paid_at'=>'datetime'];

    public function school(): BelongsTo { return $this->belongsTo(School::class); }
    public function subscription(): BelongsTo { return $this->belongsTo(Subscription::class); }
    public function invoice(): BelongsTo { return $this->belongsTo(SubscriptionInvoice::class, 'subscription_invoice_id'); }
}

