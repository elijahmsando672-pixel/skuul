<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use Illuminate\Database\Seeder;

class AcademicYearSeeder extends Seeder
{
    public function run(): void
    {
        $startYear = (int) date('Y');
        $name = "{$startYear}-".($startYear + 1);

        AcademicYear::query()->update(['is_active' => false]);

        AcademicYear::updateOrCreate(
            ['name' => $name],
            [
                'start_date' => "{$startYear}-08-01",
                'end_date' => ($startYear + 1).'-05-31',
                'is_active' => true,
            ]
        );
    }
}
