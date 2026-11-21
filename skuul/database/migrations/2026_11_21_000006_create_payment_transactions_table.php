<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subscription_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('subscription_invoice_id')->nullable()->constrained()->nullOnDelete();
            $table->string('provider')->default('mpesa');
            $table->string('reference')->nullable()->index();
            $table->string('merchant_request_id')->nullable()->index();
            $table->string('checkout_request_id')->nullable()->index();
            $table->string('phone_number')->nullable();
            $table->decimal('amount', 12, 2)->default(0);
            $table->string('currency', 10)->default('KES');
            $table->string('status')->default('pending'); // pending, success, failed
            $table->json('payload')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};
