<?php

namespace App\Services\Payments;

use App\Models\PaymentTransaction;
use App\Models\Subscription;
use App\Models\SubscriptionInvoice;
use Illuminate\Support\Str;

class MpesaDarajaService implements PaymentGateway
{
    public function initiateStkPush(Subscription $subscription, string $phone, float $amount): PaymentTransaction
    {
        $invoice = SubscriptionInvoice::create([
            'school_id'=>$subscription->school_id,
            'subscription_id'=>$subscription->id,
            'invoice_number'=>'INV-'.now()->format('YmdHis').'-'.Str::upper(Str::random(5)),
            'amount'=>$amount,
            'currency'=>'KES',
            'status'=>'pending',
            'due_date'=>now(),
        ]);

        return PaymentTransaction::create([
            'school_id'=>$subscription->school_id,
            'subscription_id'=>$subscription->id,
            'subscription_invoice_id'=>$invoice->id,
            'provider'=>'mpesa',
            'reference'=>'MPESA-'.Str::upper(Str::random(10)),
            'phone_number'=>$phone,
            'amount'=>$amount,
            'currency'=>'KES',
            'status'=>config('services.mpesa.consumer_key') ? 'pending' : 'pending',
            'payload'=>['mode'=>config('services.mpesa.consumer_key') ? 'sandbox-ready' : 'not-configured'],
        ]);
    }

    public function handleCallback(array $payload): void
    {
        $checkout = data_get($payload, 'Body.stkCallback.CheckoutRequestID');
        $code = data_get($payload, 'Body.stkCallback.ResultCode');
        if (!$checkout) return;
        $tx = PaymentTransaction::where('checkout_request_id', $checkout)->orWhere('reference', $checkout)->first();
        if (!$tx || $tx->status === 'success') return;

        $tx->payload = $payload;
        if ((int)$code === 0) {
            $tx->status = 'success';
            $tx->paid_at = now();
            $tx->save();
            $tx->invoice?->update(['status'=>'paid','paid_at'=>now()]);
            $tx->subscription?->update(['status'=>'active','starts_at'=>now()]);
        } else {
            $tx->status = 'failed';
            $tx->save();
            $tx->invoice?->update(['status'=>'failed']);
        }
    }
}
