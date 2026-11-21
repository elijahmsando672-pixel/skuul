<?php

namespace Database\Seeders;

use App\Models\Classroom;
use Illuminate\Database\Seeder;

class ClassroomSeeder extends Seeder
{
    public function run(): void
    {
        if (Classroom::query()->count() === 0) {
            Classroom::factory()->count(12)->create();
        }
    }
}
