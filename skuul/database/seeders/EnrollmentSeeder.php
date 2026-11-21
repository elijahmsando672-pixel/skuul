<?php

namespace Database\Seeders;

use App\Models\Enrollment;
use Illuminate\Database\Seeder;

class EnrollmentSeeder extends Seeder
{
    public function run(): void
    {
        if (Enrollment::query()->count() === 0) {
            Enrollment::factory()->count(40)->create();
        }
    }
}
