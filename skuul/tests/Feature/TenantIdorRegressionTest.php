<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\TransportRoute;
use App\Models\User;
use App\Support\Tenancy\TenantContext;
use Tests\TestCase;

class TenantIdorRegressionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        TenantContext::clear();
    }

    private function makeSchool(string $slug)
    {
        $school = \App\Models\School::create(['name'=>$slug,'slug'=>$slug,'email'=>$slug.'@test.local','country'=>'Kenya','status'=>'active']);
        $plan = \App\Models\Plan::firstOrCreate(['slug'=>'p-'.$slug], ['name'=>'P','billing_cycle'=>'monthly','price'=>0,'currency'=>'KES','is_active'=>true]);
        \App\Models\Subscription::create(['school_id'=>$school->id,'plan_id'=>$plan->id,'status'=>'active','starts_at'=>now()->subDay(),'ends_at'=>now()->addMonth()]);
        return $school;
    }

    private function makeAdmin($school)
    {
        TenantContext::set($school);
        $u = User::factory()->create(['current_school_id'=>$school->id,'role_type'=>'admin','is_active'=>true]);
        $u->assignRole('admin');
        $school->users()->syncWithoutDetaching([$u->id=>['role'=>'admin','is_active'=>true,'joined_at'=>now()]]);
        TenantContext::clear();
        return $u;
    }

    public function test_admin_cannot_update_other_school_fee_record(): void
    {
        $a=$this->makeSchool('a1'); $b=$this->makeSchool('b1');
        $adminA=$this->makeAdmin($a); $adminB=$this->makeAdmin($b);
        TenantContext::set($b);
        $year=\App\Models\AcademicYear::create(['school_id'=>$b->id,'name'=>'Y','start_date'=>'2026-01-01','end_date'=>'2026-12-31','is_active'=>true]);
        $stuUser=User::factory()->create(['current_school_id'=>$b->id,'role_type'=>'student']); $stuUser->assignRole('student'); $b->users()->syncWithoutDetaching([$stuUser->id=>['role'=>'student','is_active'=>true,'joined_at'=>now()]]);
        $student=Student::create(['user_id'=>$stuUser->id,'school_id'=>$b->id,'student_id'=>'S1','enrollment_date'=>now()->toDateString()]);
        $fs=\App\Models\FeeStructure::create(['school_id'=>$b->id,'academic_year_id'=>$year->id,'name'=>'F','amount'=>100,'frequency'=>'term','is_active'=>true]);
        $fr=\App\Models\FeeRecord::create(['school_id'=>$b->id,'student_id'=>$student->id,'fee_structure_id'=>$fs->id,'amount'=>100,'amount_paid'=>0,'due_date'=>now()->toDateString(),'status'=>'pending']);
        TenantContext::clear();

        $resp=$this->actingAs($adminA)->post(route('admin.fees.records.status',$fr),['amount_paid'=>10,'status'=>'partial']);
        $resp->assertStatus(404);
    }

    public function test_admin_cannot_issue_other_school_book(): void
    {
        $a=$this->makeSchool('a2'); $b=$this->makeSchool('b2');
        $adminA=$this->makeAdmin($a);
        TenantContext::set($b);
        $book=Book::create(['school_id'=>$b->id,'title'=>'B','author'=>'A','category'=>'C','quantity'=>1,'available_quantity'=>1]);
        $stuUser=User::factory()->create(['current_school_id'=>$b->id,'role_type'=>'student']); $stuUser->assignRole('student'); $b->users()->syncWithoutDetaching([$stuUser->id=>['role'=>'student','is_active'=>true,'joined_at'=>now()]]);
        $student=Student::create(['user_id'=>$stuUser->id,'school_id'=>$b->id,'student_id'=>'S2','enrollment_date'=>now()->toDateString()]);
        TenantContext::clear();
        $resp=$this->actingAs($adminA)->post(route('admin.library.issues.store'),['book_id'=>$book->id,'borrower_type'=>'student','borrower_id'=>$student->id,'issued_at'=>now()->toDateString(),'due_date'=>now()->addDay()->toDateString()]);
        $this->assertSame(404,$resp->status(),'loc:'.$resp->headers->get('Location')); $this->assertDatabaseMissing('book_issues',['book_id'=>$book->id,'borrower_id'=>$student->id]);
    }
}


