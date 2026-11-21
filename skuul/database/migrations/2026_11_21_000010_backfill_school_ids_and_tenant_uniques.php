<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        $defaultId = DB::table('schools')->where('slug', 'default-school')->value('id');
        if (! $defaultId) {
            $defaultId = DB::table('schools')->insertGetId([
                'name' => 'Default School',
                'slug' => 'default-school',
                'email' => 'admin@skuul.local',
                'phone' => null,
                'address' => null,
                'county' => null,
                'country' => 'Kenya',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $tenantTables = [
            'students','teachers','parents','academic_years','terms','classrooms','subjects','enrollments','attendance','grades',
            'announcements','timetables','fee_structures','fee_records','exams','exam_results','books','book_issues',
            'transport_routes','vehicles','transport_assignments'
        ];
        foreach ($tenantTables as $table) {
            if (Schema::hasColumn($table, 'school_id')) {
                DB::table($table)->whereNull('school_id')->update(['school_id' => $defaultId]);
            }
        }

        // Backfill user-school memberships from existing role profiles.
        $users = DB::table('users')->select('id','role_type','current_school_id')->get();
        foreach ($users as $user) {
            $schoolId = $user->current_school_id ?: $defaultId;
            DB::table('users')->where('id', $user->id)->update(['current_school_id' => $schoolId]);
            $exists = DB::table('school_user')->where('school_id', $schoolId)->where('user_id', $user->id)->exists();
            if (! $exists) {
                DB::table('school_user')->insert([
                    'school_id' => $schoolId,
                    'user_id' => $user->id,
                    'role' => $user->role_type,
                    'is_owner' => $user->role_type === 'admin',
                    'is_active' => true,
                    'joined_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Tenant-safe unique/index adjustments. Guard for sqlite/mysql differences.
        $this->dropUniqueIfExists('users', 'users_email_unique');
        $this->dropUniqueIfExists('students', 'students_student_id_unique');
        $this->dropUniqueIfExists('teachers', 'teachers_employee_id_unique');
        $this->dropUniqueIfExists('parents', 'parents_parent_id_code_unique');
        $this->dropUniqueIfExists('subjects', 'subjects_code_unique');
        $this->dropUniqueIfExists('books', 'books_isbn_unique');
        $this->dropUniqueIfExists('transport_routes', 'transport_routes_name_unique');
        $this->dropUniqueIfExists('vehicles', 'vehicles_registration_number_unique');

        $this->dropUniqueIfExists('enrollments', 'enrollments_student_id_academic_year_id_unique');
        $this->dropUniqueIfExists('attendance', 'attendance_student_id_date_classroom_id_unique');
        $this->dropUniqueIfExists('timetables', 'timetables_classroom_id_day_of_week_start_time_unique');
        $this->dropUniqueIfExists('fee_records', 'fee_records_student_id_fee_structure_id_unique');
        $this->dropUniqueIfExists('exam_results', 'exam_results_exam_id_student_id_unique');
        $this->dropUniqueIfExists('transport_assignments', 'transport_assignable_unique');

        Schema::table('users', fn (Blueprint $t) => $t->unique(['email', 'current_school_id'], 'users_email_school_unique'));
        Schema::table('students', fn (Blueprint $t) => $t->unique(['school_id', 'student_id'], 'students_school_student_id_unique'));
        Schema::table('teachers', fn (Blueprint $t) => $t->unique(['school_id', 'employee_id'], 'teachers_school_employee_id_unique'));
        Schema::table('parents', fn (Blueprint $t) => $t->unique(['school_id', 'parent_id_code'], 'parents_school_parent_id_code_unique'));
        Schema::table('subjects', fn (Blueprint $t) => $t->unique(['school_id', 'code'], 'subjects_school_code_unique'));
        Schema::table('books', fn (Blueprint $t) => $t->unique(['school_id', 'isbn'], 'books_school_isbn_unique'));
        Schema::table('transport_routes', fn (Blueprint $t) => $t->unique(['school_id', 'name'], 'transport_routes_school_name_unique'));
        Schema::table('vehicles', fn (Blueprint $t) => $t->unique(['school_id', 'registration_number'], 'vehicles_school_registration_unique'));

        Schema::table('enrollments', fn (Blueprint $t) => $t->unique(['school_id','student_id','academic_year_id'], 'enrollments_school_student_year_unique'));
        Schema::table('attendance', fn (Blueprint $t) => $t->unique(['school_id','student_id','date','classroom_id'], 'attendance_school_student_date_classroom_unique'));
        Schema::table('timetables', fn (Blueprint $t) => $t->unique(['school_id','classroom_id','day_of_week','start_time'], 'timetables_school_class_day_start_unique'));
        Schema::table('fee_records', fn (Blueprint $t) => $t->unique(['school_id','student_id','fee_structure_id'], 'fee_records_school_student_structure_unique'));
        Schema::table('exam_results', fn (Blueprint $t) => $t->unique(['school_id','exam_id','student_id'], 'exam_results_school_exam_student_unique'));
        Schema::table('transport_assignments', fn (Blueprint $t) => $t->unique(['school_id','assignable_type','assignable_id','academic_year_id'], 'transport_assignments_school_assignable_year_unique'));
    }

    public function down(): void
    {
        // Non-destructive down: keep data and tenant columns.
    }

    private function dropUniqueIfExists(string $table, string $index): void
    {
        try {
            Schema::table($table, function (Blueprint $t) use ($index) {
                $t->dropUnique($index);
            });
        } catch (\Throwable $e) {
            // ignore missing index/db differences
        }
    }
};
