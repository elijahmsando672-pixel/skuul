<?php

namespace Tests;

use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\ParentModel;
use App\Models\Plan;
use App\Models\School;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Subscription;
use App\Models\Teacher;
use App\Models\User;
use App\Support\Tenancy\TenantContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    protected School $testSchool;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();

        foreach (['admin', 'teacher', 'student', 'parent'] as $role) {
            Role::findOrCreate($role, config('auth.defaults.guard'));
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        TenantContext::clear();
        $this->testSchool = School::create(['name'=>'Test School','slug'=>'test-school','email'=>'test@school.local','country'=>'Kenya','status'=>'active']);
        $plan = Plan::firstOrCreate(['slug'=>'test-plan'], ['name'=>'Test','billing_cycle'=>'monthly','price'=>0,'currency'=>'KES','is_active'=>true]);
        Subscription::create(['school_id'=>$this->testSchool->id,'plan_id'=>$plan->id,'status'=>'active','starts_at'=>now()->subDay(),'ends_at'=>now()->addMonth()]);
        TenantContext::set($this->testSchool);
    }

    public function createApplication()
    {
        $base = realpath(__DIR__.'/..').DIRECTORY_SEPARATOR.'storage'.DIRECTORY_SEPARATOR.'testing';
        $cachePath = $base.DIRECTORY_SEPARATOR.'cache';
        foreach ([$base, $cachePath, $cachePath.DIRECTORY_SEPARATOR.'views', $cachePath.DIRECTORY_SEPARATOR.'data', $base.DIRECTORY_SEPARATOR.'framework'.DIRECTORY_SEPARATOR.'sessions', $base.DIRECTORY_SEPARATOR.'framework'.DIRECTORY_SEPARATOR.'views', $base.DIRECTORY_SEPARATOR.'logs'] as $dir) {
            if (! is_dir($dir)) {
                @mkdir($dir, 0777, true);
            }
        }
        $manifest = $cachePath.DIRECTORY_SEPARATOR.'packages.php';
        $services = $cachePath.DIRECTORY_SEPARATOR.'services.php';
        foreach (['APP_SERVICES_CACHE'=>$services,'APP_PACKAGES_CACHE'=>$manifest,'APP_CONFIG_CACHE'=>$cachePath.'/config.php','APP_ROUTES_CACHE'=>$cachePath.'/routes.php','APP_EVENTS_CACHE'=>$cachePath.'/events.php'] as $k=>$v) {
            $_ENV[$k] = $v; $_SERVER[$k] = $v; putenv($k.'='.$v);
        }
        $_ENV['VIEW_COMPILED_PATH'] = $cachePath.DIRECTORY_SEPARATOR.'views';
        $_SERVER['VIEW_COMPILED_PATH'] = $cachePath.DIRECTORY_SEPARATOR.'views';

        $app = require __DIR__.'/../bootstrap/app.php';
        $app->useStoragePath($base);
        $app->instance(\Illuminate\Foundation\PackageManifest::class, new class($app->basePath(), $manifest, $app) extends \Illuminate\Foundation\PackageManifest {
            public function __construct($basePath, $manifestPath, $app) { parent::__construct(new \Illuminate\Filesystem\Filesystem, $basePath, $manifestPath); $this->manifestPath = $manifestPath; }
        });

        $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
        return $app;
    }

    protected function createRoleUser(string $role, string $email): User
    {
        $user = User::create([
            'current_school_id' => $this->testSchool->id,
            'name' => ucfirst($role).' User',
            'email' => $email,
            'password' => Hash::make('password'),
            'role_type' => $role,
            'is_active' => true,
            'is_platform_admin' => false,
        ]);

        $user->assignRole($role);
        $this->testSchool->users()->syncWithoutDetaching([$user->id => ['role'=>$role,'is_active'=>true,'joined_at'=>now()]]);

        return $user;
    }

    protected function createAcademicYearFixture(): AcademicYear
    {
        return AcademicYear::create([
            'school_id' => $this->testSchool->id,
            'name' => '2026-2027',
            'start_date' => '2026-01-01',
            'end_date' => '2026-12-31',
            'is_active' => true,
        ]);
    }

    protected function createTeacherFixture(User $user): Teacher
    {
        return Teacher::create([
            'school_id' => $this->testSchool->id,
            'user_id' => $user->id,
            'employee_id' => 'TCH-FIXTURE-'.$user->id,
            'department' => 'Mathematics',
            'hire_date' => '2026-01-01',
        ]);
    }

    protected function createClassroomFixture(AcademicYear $academicYear, Teacher $teacher): Classroom
    {
        return Classroom::create([
            'school_id' => $this->testSchool->id,
            'name' => 'Grade 10 A',
            'grade_level' => 'Grade 10',
            'section' => 'A',
            'academic_year_id' => $academicYear->id,
            'teacher_id' => $teacher->id,
            'room_number' => '101',
            'capacity' => 30,
        ]);
    }

    protected function createSubjectFixture(Classroom $classroom, Teacher $teacher): Subject
    {
        return Subject::create([
            'school_id' => $this->testSchool->id,
            'name' => 'Mathematics',
            'code' => 'MATH-FIXTURE-'.$classroom->id,
            'classroom_id' => $classroom->id,
            'teacher_id' => $teacher->id,
            'credits' => 3,
            'is_compulsory' => true,
        ]);
    }

    protected function createParentFixture(User $user): ParentModel
    {
        return ParentModel::create([
            'school_id' => $this->testSchool->id,
            'user_id' => $user->id,
            'parent_id_code' => 'PAR-FIXTURE-'.$user->id,
            'relationship' => 'father',
        ]);
    }

    protected function createStudentFixture(User $user, ?ParentModel $parent = null): Student
    {
        return Student::create([
            'school_id' => $this->testSchool->id,
            'user_id' => $user->id,
            'student_id' => 'STU-FIXTURE-'.$user->id,
            'grade_level' => 'Grade 10',
            'enrollment_date' => '2026-01-01',
            'parent_id' => $parent?->id,
        ]);
    }
}
