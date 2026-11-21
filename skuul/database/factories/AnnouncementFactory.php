<?php

namespace Database\Factories;

use App\Models\Announcement;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AnnouncementFactory extends Factory
{
    protected $model = Announcement::class;

    public function definition(): array
    {
        $announcements = [
            ['School Reopens on Monday', 'School will reopen on Monday at 8:00 AM. All students and staff should report on time.'],
            ['Parent-Teacher Meeting', 'A parent-teacher meeting will be held this Friday at 2:00 PM in the school hall.'],
            ['Fee Payment Reminder', 'Please clear any outstanding fee balances before the end of the month to avoid inconvenience.'],
            ['Examination Timetable Released', 'The examination timetable has been released. Students should check the exams page for details.'],
            ['Library Books Return Notice', 'All borrowed library books should be returned by Friday. Late returns may attract a penalty.'],
            ['Sports Day Announcement', 'Sports Day will take place next Wednesday. Students should wear appropriate sports attire.'],
            ['Transport Schedule Update', 'The morning transport schedule has been updated. Please check your assigned route for new pickup times.'],
            ['Staff Meeting', 'All teachers are requested to attend a staff meeting tomorrow at 4:00 PM in the staff room.'],
        ];

        $item = fake()->randomElement($announcements);

        return [
            'title' => $item[0],
            'content' => $item[1],
            'user_id' => User::factory()->admin(),
            'target_audience' => fake()->randomElement(['all', 'students', 'teachers', 'parents', 'admin']),
            'priority' => fake()->randomElement(['low', 'normal', 'high', 'urgent']),
            'starts_at' => now()->subDay(),
            'expires_at' => now()->addMonth(),
            'is_published' => true,
        ];
    }
}
