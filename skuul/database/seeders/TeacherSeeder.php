<?php

namespace Database\Seeders;

use App\Models\Teacher;
use Illuminate\Database\Seeder;

class TeacherSeeder extends Seeder
{
    public function run(): void
    {
        if (Teacher::query()->count() === 0) {
            Teacher::factory()->count(10)->create();
        }
    }
}
