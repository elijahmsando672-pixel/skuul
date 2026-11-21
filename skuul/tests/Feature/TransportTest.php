<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Student;
use App\Models\TransportAssignment;
use App\Models\TransportRoute;
use App\Models\User;
use App\Models\Vehicle;
use Tests\TestCase;

class TransportTest extends TestCase
{
    private User $admin;
    private AcademicYear $academicYear;
    private User $studentUser;
    private Student $student;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = $this->createRoleUser('admin', 'admin-transport@example.test');
        $this->academicYear = $this->createAcademicYearFixture();

        $this->studentUser = $this->createRoleUser('student', 'student-transport@example.test');
        $this->student = $this->createStudentFixture($this->studentUser);
    }

    public function test_admin_can_create_route_vehicle_and_assignment(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.transport.routes.store'), [
                'name' => 'North Route',
                'origin' => 'North Gate',
                'destination' => 'School',
                'distance_km' => 12.5,
                'estimated_minutes' => 30,
                'is_active' => 1,
            ])
            ->assertRedirect(route('admin.transport.routes.index'));

        $route = TransportRoute::where('name', 'North Route')->firstOrFail();

        $this->actingAs($this->admin)
            ->post(route('admin.transport.vehicles.store'), [
                'registration_number' => 'ABC-123',
                'make' => 'Toyota',
                'model' => 'Coaster',
                'capacity' => 30,
                'driver_name' => 'Jane Doe',
                'is_active' => 1,
            ])
            ->assertRedirect(route('admin.transport.vehicles'));

        $vehicle = Vehicle::where('registration_number', 'ABC-123')->firstOrFail();

        $this->actingAs($this->admin)
            ->post(route('admin.transport.assignments.store'), [
                'transport_route_id' => $route->id,
                'vehicle_id' => $vehicle->id,
                'assignable_type' => 'student',
                'assignable_id' => $this->student->id,
                'pickup_point' => 'Main Gate',
                'academic_year_id' => $this->academicYear->id,
                'is_active' => 1,
            ])
            ->assertRedirect(route('admin.transport.assignments'));

        $this->assertDatabaseHas('transport_assignments', [
            'transport_route_id' => $route->id,
            'vehicle_id' => $vehicle->id,
            'assignable_type' => 'student',
            'assignable_id' => $this->student->id,
            'is_active' => 1,
        ]);
    }

    public function test_student_transport_page_loads(): void
    {
        $route = TransportRoute::create([
            'name' => 'West Route',
            'origin' => 'West Gate',
            'destination' => 'School',
            'is_active' => true,
        ]);

        $vehicle = Vehicle::create([
            'registration_number' => 'XYZ-789',
            'make' => 'Isuzu',
            'model' => 'Bus',
            'capacity' => 40,
            'driver_name' => 'John Smith',
            'is_active' => true,
        ]);

        TransportAssignment::create([
            'transport_route_id' => $route->id,
            'vehicle_id' => $vehicle->id,
            'assignable_type' => 'student',
            'assignable_id' => $this->student->id,
            'pickup_point' => 'West Gate',
            'academic_year_id' => $this->academicYear->id,
            'is_active' => true,
        ]);

        $this->actingAs($this->studentUser)
            ->get(route('student.transport.index'))
            ->assertOk()
            ->assertSee('West Route')
            ->assertSee('XYZ-789');
    }
}
