<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\FeeRecord;
use App\Models\FeeStructure;
use App\Models\ParentModel;
use App\Models\Student;
use App\Models\User;
use Tests\TestCase;

class FeeTest extends TestCase
{
    private User $admin;
    private AcademicYear $academicYear;
    private User $studentUser;
    private Student $student;
    private User $parentUser;
    private ParentModel $parent;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = $this->createRoleUser('admin', 'admin-fee@example.test');
        $this->academicYear = $this->createAcademicYearFixture();

        $this->parentUser = $this->createRoleUser('parent', 'parent-fee@example.test');
        $this->parent = $this->createParentFixture($this->parentUser);

        $this->studentUser = $this->createRoleUser('student', 'student-fee@example.test');
        $this->student = $this->createStudentFixture($this->studentUser, $this->parent);
    }

    public function test_admin_can_create_fee_structure_and_fee_record(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.fees.store'), [
                'academic_year_id' => $this->academicYear->id,
                'name' => 'Tuition Fee',
                'amount' => 500,
                'frequency' => 'monthly',
                'due_date' => '2026-12-31',
                'is_active' => 1,
            ])
            ->assertRedirect(route('admin.fees.index'));

        $feeStructure = FeeStructure::where('name', 'Tuition Fee')->firstOrFail();

        $this->assertDatabaseHas('fee_structures', [
            'id' => $feeStructure->id,
            'name' => 'Tuition Fee',
            'frequency' => 'monthly',
        ]);

        $this->actingAs($this->admin)
            ->post(route('admin.fees.records.store'), [
                'student_id' => $this->student->id,
                'fee_structure_id' => $feeStructure->id,
                'amount' => 500,
                'amount_paid' => 0,
                'due_date' => '2026-12-31',
                'status' => 'pending',
            ])
            ->assertRedirect(route('admin.fees.records'));

        $this->assertDatabaseHas('fee_records', [
            'student_id' => $this->student->id,
            'fee_structure_id' => $feeStructure->id,
            'amount' => 500,
            'status' => 'pending',
        ]);
    }

    public function test_payment_update_marks_record_paid_when_full_amount_paid(): void
    {
        $feeStructure = $this->createFeeStructure();
        $feeRecord = FeeRecord::create([
            'student_id' => $this->student->id,
            'fee_structure_id' => $feeStructure->id,
            'amount' => 500,
            'amount_paid' => 100,
            'due_date' => '2026-12-31',
            'status' => 'partial',
        ]);

        $this->actingAs($this->admin)
            ->post(route('admin.fees.records.status', $feeRecord), [
                'amount_paid' => 500,
                'status' => 'pending',
            ])
            ->assertRedirect(route('admin.fees.records'));

        $feeRecord->refresh();

        $this->assertSame('paid', $feeRecord->status);
        $this->assertSame(500.0, (float) $feeRecord->amount_paid);
        $this->assertNotNull($feeRecord->paid_at);
    }

    public function test_parent_fees_page_loads(): void
    {
        $feeStructure = $this->createFeeStructure();

        FeeRecord::create([
            'student_id' => $this->student->id,
            'fee_structure_id' => $feeStructure->id,
            'amount' => 500,
            'amount_paid' => 0,
            'due_date' => '2026-12-31',
            'status' => 'pending',
        ]);

        $this->actingAs($this->parentUser)
            ->get(route('parent.fees.index'))
            ->assertOk()
            ->assertViewHas('feeRecords', function ($records) {
                return $records->count() === 1
                    && $records->first()->student_id === $this->student->id;
            })
            ->assertSee('Tuition Fee');
    }

    private function createFeeStructure(): FeeStructure
    {
        return FeeStructure::create([
            'academic_year_id' => $this->academicYear->id,
            'name' => 'Tuition Fee',
            'amount' => 500,
            'frequency' => 'monthly',
            'due_date' => '2026-12-31',
            'is_active' => true,
        ]);
    }
}
