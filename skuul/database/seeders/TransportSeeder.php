<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\TransportAssignment;
use App\Models\TransportRoute;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;

class TransportSeeder extends Seeder
{
    public function run(): void
    {
        $routeData = [
            [
                'name' => 'North Campus Loop',
                'origin' => 'North Gate',
                'destination' => 'Main Campus',
                'distance_km' => 12.50,
                'estimated_minutes' => 35,
                'is_active' => true,
            ],
            [
                'name' => 'City Center Shuttle',
                'origin' => 'City Center',
                'destination' => 'Main Campus',
                'distance_km' => 8.75,
                'estimated_minutes' => 25,
                'is_active' => true,
            ],
        ];

        $vehicleData = [
            [
                'registration_number' => 'TRN-001',
                'make' => 'Toyota',
                'model' => 'HiAce',
                'capacity' => 14,
                'driver_name' => 'Daniel Okafor',
                'driver_phone' => '08011110001',
                'is_active' => true,
            ],
            [
                'registration_number' => 'TRN-002',
                'make' => 'Nissan',
                'model' => 'Urvan',
                'capacity' => 18,
                'driver_name' => 'Grace Mensah',
                'driver_phone' => '08011110002',
                'is_active' => true,
            ],
        ];

        $routes = collect();
        foreach ($routeData as $data) {
            $routes->push(TransportRoute::updateOrCreate(
                ['name' => $data['name']],
                $data
            ));
        }

        $vehicles = collect();
        foreach ($vehicleData as $data) {
            $vehicles->push(Vehicle::updateOrCreate(
                ['registration_number' => $data['registration_number']],
                $data
            ));
        }

        $academicYear = AcademicYear::query()->where('is_active', true)->first()
            ?? AcademicYear::query()->orderBy('id')->first();

        if (! $academicYear) {
            return;
        }

        $students = Student::query()->orderBy('id')->take(5)->get();
        $teachers = Teacher::query()->orderBy('id')->take(3)->get();

        foreach ($students as $index => $student) {
            TransportAssignment::updateOrCreate(
                [
                    'assignable_type' => 'student',
                    'assignable_id' => $student->id,
                    'academic_year_id' => $academicYear->id,
                ],
                [
                    'transport_route_id' => $routes[$index % $routes->count()]->id,
                    'vehicle_id' => $vehicles[$index % $vehicles->count()]->id,
                    'pickup_point' => 'Main Gate',
                    'is_active' => true,
                ]
            );
        }

        foreach ($teachers as $index => $teacher) {
            TransportAssignment::updateOrCreate(
                [
                    'assignable_type' => 'teacher',
                    'assignable_id' => $teacher->id,
                    'academic_year_id' => $academicYear->id,
                ],
                [
                    'transport_route_id' => $routes[($index + 1) % $routes->count()]->id,
                    'vehicle_id' => $vehicles[($index + 1) % $vehicles->count()]->id,
                    'pickup_point' => 'Staff Quarters',
                    'is_active' => true,
                ]
            );
        }
    }
}
