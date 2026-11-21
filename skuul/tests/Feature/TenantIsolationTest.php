<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\FeeRecord;
use App\Models\FeeStructure;
use App\Models\ParentModel;
use App\Models\Plan;
use App\Models\School;
use App\Models\Student;
use App\Models\Subscription;
use App\Models\Teacher;
use App\Models\User;
use App\Support\Tenancy\TenantContext;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class TenantIsolationTest extends TestCase
{
    private School $green;
    private School $sunrise;
    private User $greenAdmin;
    private User $sunriseAdmin;
    private User $greenTeacherUser;
    private User $sunriseTeacherUser;
    private User $greenParentUser;
    private User $sunriseParentUser;
    private Student $greenStudent;
    private Student $sunriseStudent;
    private FeeRecord $greenFee;
    private FeeRecord $sunriseFee;

    protected function setUp(): void
    {
        parent::setUp();
        TenantContext::clear();

        $this->green = School::create(['name'=>'Green Valley School','slug'=>'green-valley','email'=>'green@test.local','country'=>'Kenya','status'=>'active']);
        $this->sunrise = School::create(['name'=>'Sunrise Academy','slug'=>'sunrise-academy','email'=>'sunrise@test.local','country'=>'Kenya','status'=>'active']);
        $this->seedSubscription($this->green);
        $this->seedSubscription($this->sunrise);

        $this->greenAdmin = $this->createSchoolUser($this->green, 'admin', 'green-admin@test.local');
        $this->sunriseAdmin = $this->createSchoolUser($this->sunrise, 'admin', 'sunrise-admin@test.local');
        $this->greenTeacherUser = $this->createSchoolUser($this->green, 'teacher', 'green-teacher@test.local');
        $this->sunriseTeacherUser = $this->createSchoolUser($this->sunrise, 'teacher', 'sunrise-teacher@test.local');
        $this->greenParentUser = $this->createSchoolUser($this->green, 'parent', 'green-parent@test.local');
        $this->sunriseParentUser = $this->createSchoolUser($this->sunrise, 'parent', 'sunrise-parent@test.local');

        $this->greenStudent = $this->seedStudent($this->green, $this->greenParentUser, 'GV-STU-1', 'green-student@test.local');
        $this->sunriseStudent = $this->seedStudent($this->sunrise, $this->sunriseParentUser, 'SR-STU-1', 'sunrise-student@test.local');

        $this->greenFee = $this->seedFee($this->green, $this->greenStudent, 'Green Fee', 1000);
        $this->sunriseFee = $this->seedFee($this->sunrise, $this->sunriseStudent, 'Sunrise Fee', 2000);
    }

    private function createSchoolUser(School $school, string $role, string $email): User
    {
        TenantContext::set($school);
        $user = User::create([
            'current_school_id' => $school->id,
            'name' => ucfirst($role).' '.$school->slug,
            'email' => $email,
            'password' => Hash::make('password'),
            'role_type' => $role,
            'is_active' => true,
            'is_platform_admin' => false,
        ]);
        $user->assignRole($role);
        $school->users()->syncWithoutDetaching([$user->id => ['role'=>$role,'is_active'=>true,'joined_at'=>now()]]);
        if ($role === 'teacher') {
            Teacher::create(['user_id'=>$user->id,'school_id'=>$school->id,'employee_id'=>'EMP-'.$user->id,'department'=>'General','hire_date'=>now()->toDateString()]);
        }
        if ($role === 'parent') {
            ParentModel::create(['user_id'=>$user->id,'school_id'=>$school->id,'parent_id_code'=>'PAR-'.$user->id,'relationship'=>'father']);
        }
        TenantContext::clear();
        return $user;
    }

    private function seedStudent(School $school, User $parentUser, string $studentId, string $email): Student
    {
        TenantContext::set($school);
        $parent = ParentModel::where('user_id', $parentUser->id)->firstOrFail();
        $studentUser = User::create([
            'current_school_id'=>$school->id,'name'=>'Student '.$studentId,'email'=>$email,'password'=>Hash::make('password'),'role_type'=>'student','is_active'=>true,'is_platform_admin'=>false,
        ]);
        $studentUser->assignRole('student');
        $school->users()->syncWithoutDetaching([$studentUser->id => ['role'=>'student','is_active'=>true,'joined_at'=>now()]]);
        $student = Student::create([
            'user_id'=>$studentUser->id,'school_id'=>$school->id,'student_id'=>$studentId,'grade_level'=>'Grade 1','enrollment_date'=>now()->toDateString(),'parent_id'=>$parent->id,
        ]);
        TenantContext::clear();
        return $student;
    }

    private function seedSubscription(School $school): void
    {
        $plan = Plan::firstOrCreate(['slug'=>'test-plan-'.$school->id], ['name'=>'Test','billing_cycle'=>'monthly','price'=>0,'currency'=>'KES','is_active'=>true]);
        Subscription::create(['school_id'=>$school->id,'plan_id'=>$plan->id,'status'=>'active','starts_at'=>now()->subDay(),'ends_at'=>now()->addMonth()]);
    }

    private function seedFee(School $school, Student $student, string $name, int $amount): FeeRecord
    {
        TenantContext::set($school);
        $year = AcademicYear::create(['school_id'=>$school->id,'name'=>'2026-'.$school->id,'start_date'=>'2026-01-01','end_date'=>'2026-12-31','is_active'=>true]);
        $structure = FeeStructure::create(['school_id'=>$school->id,'academic_year_id'=>$year->id,'name'=>$name,'amount'=>$amount,'frequency'=>'term','grade_level'=>'Grade 1','is_active'=>true]);
        $fee = FeeRecord::create(['school_id'=>$school->id,'student_id'=>$student->id,'fee_structure_id'=>$structure->id,'amount'=>$amount,'amount_paid'=>0,'due_date'=>now()->addMonth()->toDateString(),'status'=>'pending']);
        TenantContext::clear();
        return $fee;
    }

    public function test_green_valley_admin_can_access_green_valley_student(): void
    {
        $resp = $this->actingAs($this->greenAdmin)->get(route('admin.students.show', $this->greenStudent));
        $this->assertSame(200, $resp->status(), 'redirect:'.$resp->headers->get('Location'));
    }

    public function test_sunrise_admin_can_access_sunrise_student(): void
    {
        $this->actingAs($this->sunriseAdmin)
            ->get(route('admin.students.show', $this->sunriseStudent))
            ->assertOk();
    }

    public function test_green_valley_cannot_retrieve_sunrise_student_record(): void
    {
        $resp = $this->actingAs($this->greenAdmin)->get(route('admin.students.show', $this->sunriseStudent));
        $this->assertContains($resp->status(), [403,404]);
    }

    public function test_sunrise_cannot_retrieve_green_valley_financial_record(): void
    {
        TenantContext::set($this->sunrise);
        $resp = $this->actingAs($this->sunriseAdmin)->getJson('/api/fee-records/'.$this->greenFee->id);
        $this->assertContains($resp->status(), [403,404]);
    }

    public function test_teacher_can_only_access_authorized_school_records(): void
    {
        TenantContext::set($this->sunrise);
        $resp = $this->actingAs($this->greenTeacherUser)->getJson('/api/students/'.$this->sunriseStudent->id);
        $this->assertContains($resp->status(), [403,404]);
    }

    public function test_parent_can_only_access_their_linked_children(): void
    {
        TenantContext::set($this->green);
        $this->assertTrue($this->greenParentUser->parent->students()->whereKey($this->greenStudent->id)->exists());
        $this->assertFalse($this->greenParentUser->parent->students()->whereKey($this->sunriseStudent->id)->exists());
        TenantContext::set($this->sunrise);
        $resp = $this->actingAs($this->greenParentUser)->getJson('/api/students/'.$this->sunriseStudent->id);
        $this->assertContains($resp->status(), [403,404]);
    }

    public function test_api_endpoints_enforce_tenant_restrictions(): void
    {
        $token = $this->greenAdmin->createToken('test')->plainTextToken;
        $resp = $this->withHeader('Authorization', 'Bearer '.$token)->getJson('/api/students/'.$this->sunriseStudent->id);
        $this->assertContains($resp->status(), [403,404]);
    }
}
