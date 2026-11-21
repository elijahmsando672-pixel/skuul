<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('book_issues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained('books')->cascadeOnDelete();
            $table->string('borrower_type');
            $table->unsignedBigInteger('borrower_id');
            $table->date('issued_at');
            $table->date('due_date');
            $table->date('returned_at')->nullable();
            $table->string('status')->default('issued');
            $table->timestamps();

            $table->index(['borrower_type', 'borrower_id']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('book_issues');
    }
};
