<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('transport_assignments')) {
            Schema::create('transport_assignments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('transport_route_id')->constrained('transport_routes')->onDelete('cascade');
                $table->foreignId('vehicle_id')->constrained('vehicles')->onDelete('cascade');
                $table->string('assignable_type');
                $table->unsignedBigInteger('assignable_id');
                $table->string('pickup_point')->nullable();
                $table->foreignId('academic_year_id')->nullable()->constrained('academic_years')->onDelete('set null');
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->unique(['assignable_type', 'assignable_id', 'academic_year_id'], 'transport_assignable_unique');
            });

            return;
        }

        if (! Schema::hasIndex('transport_assignments', 'transport_assignable_unique')) {
            Schema::table('transport_assignments', function (Blueprint $table) {
                $table->unique(['assignable_type', 'assignable_id', 'academic_year_id'], 'transport_assignable_unique');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('transport_assignments');
    }
};
