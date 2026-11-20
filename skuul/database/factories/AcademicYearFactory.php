<?php

namespace Database\Factories;

use App\Models\AcademicYear;
use Illuminate\Database\Eloquent\Factories\Factory;

class AcademicYearFactory extends Factory
{
    protected $model = AcademicYear::class;

    public function definition(): array
    {
        $startYear = fake()->randomElement([2022, 2023, 2024]);
        $startDate = \Illuminate\Support\Carbon::parse("{$startYear}-08-01");
        $endDate = \Illuminate\Support\Carbon::parse("{$startYear + 1}-05-31");

        return [
            'name' => "{$startYear}-" . ($startYear + 1),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'is_active' => $startYear === date('Y'),
        ];
    }
}