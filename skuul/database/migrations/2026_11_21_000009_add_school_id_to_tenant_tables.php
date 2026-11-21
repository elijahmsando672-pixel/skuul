<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = [
            'students','teachers','parents','academic_years','terms','classrooms','subjects','enrollments','attendance','grades',
            'announcements','timetables','fee_structures','fee_records','exams','exam_results','books','book_issues',
            'transport_routes','vehicles','transport_assignments'
        ];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'school_id')) {
                    $table->foreignId('school_id')->nullable()->after('id')->constrained('schools')->nullOnDelete()->index();
                }
            });
        }
    }

    public function down(): void
    {
        $tables = [
            'transport_assignments','vehicles','transport_routes','book_issues','books','exam_results','exams','fee_records','fee_structures','timetables','announcements','grades','attendance','enrollments','subjects','classrooms','terms','academic_years','parents','teachers','students'
        ];
        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (Schema::hasColumn($tableName, 'school_id')) {
                    $table->dropConstrainedForeignId('school_id');
                }
            });
        }
    }
};
