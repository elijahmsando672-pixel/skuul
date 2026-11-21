<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\FeeRecord;
use App\Models\FeeStructure;
use App\Models\Student;
use Illuminate\Database\Seeder;

class FeeSeeder extends Seeder
{
    public function run(): void
    {
        $academicYears = AcademicYear::where('is_active', true)->limit(1)->get();

        if ($academicYears->isEmpty()) {
            $academicYears = AcademicYear::orderBy('id')->limit(1)->get();
        }

        $students = Student::orderBy('id')->limit(5)->get();

        if ($academicYears->isEmpty() || $students->isEmpty()) {
            return;
        }

        $definitions = [
            ['name' => 'Tuition Fee', 'amount' => 50000, 'frequency' => 'monthly', 'due_offset' => 10],
            ['name' => 'Library Fee', 'amount' => 5000, 'frequency' => 'annual', 'due_offset' => 15],
            ['name' => 'Activity Fee', 'amount' => 3000, 'frequency' => 'termly', 'due_offset' => 20],
        ];

        $payments = [
            ['status' => 'paid', 'ratio' => 1],
            ['status' => 'partial', 'ratio' => 0.5],
            ['status' => 'pending', 'ratio' => 0],
            ['status' => 'overdue', 'ratio' => 0],
            ['status' => 'partial', 'ratio' => 0.25],
        ];

        foreach ($academicYears as $year) {
            foreach ($definitions as $definition) {
                $dueDate = $year->start_date?->copy()->addDays($definition['due_offset'])->toDateString()
                    ?? $year->end_date?->toDateString()
                    ?? now()->toDateString();

                $feeStructure = FeeStructure::updateOrCreate(
                    [
                        'academic_year_id' => $year->id,
                        'name' => $definition['name'],
                    ],
                    [
                        'amount' => $definition['amount'],
                        'frequency' => $definition['frequency'],
                        'due_date' => $dueDate,
                        'is_active' => true,
                    ]
                );

                foreach ($students as $index => $student) {
                    $payment = $payments[$index % count($payments)];
                    $amountPaid = round((float) $feeStructure->amount * $payment['ratio'], 2);
                    $paidAt = $payment['status'] === 'paid'
                        ? ($year->start_date?->copy()->addDays(5) ?? now())
                        : null;

                    FeeRecord::updateOrCreate(
                        [
                            'student_id' => $student->id,
                            'fee_structure_id' => $feeStructure->id,
                        ],
                        [
                            'amount' => $feeStructure->amount,
                            'amount_paid' => $amountPaid,
                            'due_date' => $dueDate,
                            'status' => $payment['status'],
                            'paid_at' => $paidAt,
                        ]
                    );
                }
            }
        }
    }
}
