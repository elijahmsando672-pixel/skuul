<?php

namespace Database\Seeders;

use App\Models\Student;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        if (Student::query()->count() === 0) {
            Student::factory()->count(20)->create();
        }
    }
}
