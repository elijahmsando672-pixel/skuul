<?php

namespace Database\Seeders;

use App\Models\Subject;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    public function run(): void
    {
        if (Subject::query()->count() === 0) {
            Subject::factory()->count(24)->create();
        }
    }
}
