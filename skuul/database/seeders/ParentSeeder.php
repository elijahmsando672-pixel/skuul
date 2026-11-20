<?php

namespace Database\Seeders;

use App\Models\ParentModel;
use Illuminate\Database\Seeder;

class ParentSeeder extends Seeder
{
    public function run(): void
    {
        ParentModel::factory()->count(15)->create();
    }
}