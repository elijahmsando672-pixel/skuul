<?php
namespace App\Providers;
use App\Models\Student; use App\Models\Teacher; use App\Models\FeeRecord; use App\Models\FeeStructure; use App\Models\Exam; use App\Models\ExamResult; use App\Models\Book; use App\Models\BookIssue; use App\Models\TransportRoute; use App\Models\Vehicle; use App\Models\TransportAssignment; use App\Models\Attendance; use App\Models\Grade; use App\Models\Classroom; use App\Models\Subject; use App\Models\AcademicYear; use App\Models\Announcement; use App\Models\Enrollment; use App\Models\ParentModel; use App\Models\Timetable;
use App\Policies\TenantPolicy;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}
    public function boot(): void
    {
        Relation::morphMap(['student' => Student::class, 'teacher' => Teacher::class]);
        foreach ([Student::class,Teacher::class,FeeRecord::class,FeeStructure::class,Exam::class,ExamResult::class,Book::class,BookIssue::class,TransportRoute::class,Vehicle::class,TransportAssignment::class,Attendance::class,Grade::class,Classroom::class,Subject::class,AcademicYear::class,Announcement::class,Enrollment::class,ParentModel::class,Timetable::class] as $model) {
            Gate::policy($model, TenantPolicy::class);
        }
    }
}
