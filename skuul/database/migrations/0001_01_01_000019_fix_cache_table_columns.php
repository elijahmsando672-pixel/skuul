<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('cache', 'primary_key') && ! Schema::hasColumn('cache', 'key')) {
            Schema::table('cache', function (Blueprint $table) {
                $table->renameColumn('primary_key', 'key');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('cache', 'key') && ! Schema::hasColumn('cache', 'primary_key')) {
            Schema::table('cache', function (Blueprint $table) {
                $table->renameColumn('key', 'primary_key');
            });
        }
    }
};
