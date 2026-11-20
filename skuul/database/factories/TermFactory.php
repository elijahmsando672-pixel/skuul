<?php

namespace Database\Factories;

use App\Models\AcademicYear;
use App\Models\Term;
use Illuminate\Database\Eloquent\Factories\Factory;

class TermFactory extends Factory
{
    protected $model = Term::class;

    public function definition(): array
    {
        $academicYear = AcademicYear::factory()->create();
        $startDate = \Illuminate\Support\Carbon::parse($academicYear->start_date);
        $endDate = \Illuminate\Support\Carbon::parse($academicYear->end_date);

        return [
            'academic_year_id' => $academicYear->id,
            'name' => fake()->randomElement(['Term 1', 'Term 2', 'Term 3']),
            'start_date' => $startDate->copy()->addDays(fake()->numberBetween(0, 30)),
            'end_date' => $endDate->copy()->subDays(fake()->numberBetween(0, 30)),
            'is_active' => fake()->boolean(),
        ];
    }
}