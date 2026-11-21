<?php

namespace Database\Seeders;

use App\Models\Announcement;
use Illuminate\Database\Seeder;

class AnnouncementSeeder extends Seeder
{
    public function run(): void
    {
        if (Announcement::query()->count() === 0) {
            Announcement::factory()->count(10)->create();
        }
    }
}
