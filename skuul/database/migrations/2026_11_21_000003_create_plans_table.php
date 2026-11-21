<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('monthly_price', 12, 2)->default(0);
            $table->decimal('annual_price', 12, 2)->default(0);
            $table->string('currency', 10)->default('KES');
            $table->unsignedInteger('student_limit')->nullable();
            $table->unsignedInteger('staff_limit')->nullable();
            $table->unsignedInteger('storage_limit_mb')->nullable();
            $table->json('features')->nullable();
            $table->json('enabled_modules')->nullable();
            $table->unsignedInteger('trial_days')->default(14);
            $table->boolean('is_custom')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
