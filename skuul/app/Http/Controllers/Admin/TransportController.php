<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\TransportAssignment;
use App\Models\TransportRoute;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TransportController extends Controller
{
    use \App\Http\Controllers\Concerns\AuthorizesTenant;

    public function index()
    {
        $routes = TransportRoute::query()->orderBy('name')->paginate(15);

        return view('admin.transport.routes.index', compact('routes'));
    }

    public function create()
    {
        return view('admin.transport.routes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('transport_routes', 'name')],
            'origin' => ['required', 'string', 'max:255'],
            'destination' => ['required', 'string', 'max:255'],
            'distance_km' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'estimated_minutes' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        TransportRoute::create($validated);

        return redirect()->route('admin.transport.routes.index')->with('success', 'Transport route created successfully.');
    }

    public function show(TransportRoute $route)
    {
        $this->authorizeTenant($route);
        return redirect()->route('admin.transport.routes.edit', $route);
    }

    public function edit(TransportRoute $route)
    {
        $this->authorizeTenant($route);
        return view('admin.transport.routes.edit', compact('route'));
    }

    public function update(Request $request, TransportRoute $route)
    {
        $this->authorizeTenant($route);
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('transport_routes', 'name')->ignore($route->id)],
            'origin' => ['required', 'string', 'max:255'],
            'destination' => ['required', 'string', 'max:255'],
            'distance_km' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'estimated_minutes' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $route->update($validated);

        return redirect()->route('admin.transport.routes.index')->with('success', 'Transport route updated successfully.');
    }

    public function destroy(TransportRoute $route)
    {
        $this->authorizeTenant($route);
        $route->delete();

        return redirect()->route('admin.transport.routes.index')->with('success', 'Transport route deleted successfully.');
    }

    public function vehicles()
    {
        $vehicles = Vehicle::query()->orderBy('registration_number')->paginate(15);

        return view('admin.transport.vehicles.index', compact('vehicles'));
    }

    public function vehicleStore(Request $request)
    {
        $validated = $request->validate([
            'registration_number' => ['required', 'string', 'max:255', Rule::unique('vehicles', 'registration_number')],
            'make' => ['required', 'string', 'max:255'],
            'model' => ['required', 'string', 'max:255'],
            'capacity' => ['required', 'integer', 'min:1'],
            'driver_name' => ['required', 'string', 'max:255'],
            'driver_phone' => ['nullable', 'string', 'max:255'],
            'is_active' => ['boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        Vehicle::create($validated);

        return redirect()->route('admin.transport.vehicles')->with('success', 'Vehicle created successfully.');
    }

    public function vehicleUpdate(Request $request, Vehicle $vehicle)
    {
        $validated = $request->validate([
            'registration_number' => ['required', 'string', 'max:255', Rule::unique('vehicles', 'registration_number')->ignore($vehicle->id)],
            'make' => ['required', 'string', 'max:255'],
            'model' => ['required', 'string', 'max:255'],
            'capacity' => ['required', 'integer', 'min:1'],
            'driver_name' => ['required', 'string', 'max:255'],
            'driver_phone' => ['nullable', 'string', 'max:255'],
            'is_active' => ['boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        if (!empty($validated['route_id'])) tap(\App\Models\TransportRoute::findOrFail($validated['route_id']), fn($x)=>$this->authorizeTenant($x));

        $vehicle->update($validated);

        return redirect()->route('admin.transport.vehicles')->with('success', 'Vehicle updated successfully.');
    }

    public function vehicleDestroy(Vehicle $vehicle)
    {
        $vehicle->delete();

        return redirect()->route('admin.transport.vehicles')->with('success', 'Vehicle deleted successfully.');
    }

    public function assignments()
    {
        $assignments = TransportAssignment::query()
            ->with(['assignable.user', 'transportRoute', 'vehicle', 'academicYear'])
            ->latest()
            ->paginate(15);

        $routes = TransportRoute::query()->where('is_active', true)->orderBy('name')->get();
        $vehicles = Vehicle::query()->where('is_active', true)->orderBy('registration_number')->get();
        $students = Student::query()->with('user')->orderBy('id')->get();
        $teachers = Teacher::query()->with('user')->orderBy('id')->get();
        $academicYears = AcademicYear::query()->orderByDesc('id')->get();

        return view('admin.transport.assignments.index', compact(
            'assignments',
            'routes',
            'vehicles',
            'students',
            'teachers',
            'academicYears'
        ));
    }

    public function assignmentStore(Request $request)
    {
        $assignableType = $request->input('assignable_type');
        $assignableTable = $assignableType === 'teacher' ? 'teachers' : 'students';

        $validated = $request->validate([
            'transport_route_id' => ['required', 'integer', Rule::exists('transport_routes', 'id')],
            'vehicle_id' => ['required', 'integer', Rule::exists('vehicles', 'id')],
            'assignable_type' => ['required', Rule::in(['student', 'teacher'])],
            'assignable_id' => ['required', 'integer', Rule::exists($assignableTable, 'id')],
            'pickup_point' => ['nullable', 'string', 'max:255'],
            'academic_year_id' => ['nullable', 'integer', Rule::exists('academic_years', 'id')],
            'is_active' => ['boolean'],
        ]);

        $validated['academic_year_id'] = ! empty($validated['academic_year_id'] ?? null)
            ? $validated['academic_year_id']
            : null;
        $validated['is_active'] = $request->boolean('is_active');

        $duplicateQuery = TransportAssignment::query()
            ->where('assignable_type', $validated['assignable_type'])
            ->where('assignable_id', $validated['assignable_id']);

        if ($validated['academic_year_id'] === null) {
            $duplicateQuery->whereNull('academic_year_id');
        } else {
            $duplicateQuery->where('academic_year_id', $validated['academic_year_id']);
        }

        if ($duplicateQuery->exists()) {
            return back()->withErrors([
                'assignable_id' => 'This person already has an assignment for the selected academic year.',
            ])->withInput();
        }

        TransportAssignment::create($validated);

        return redirect()->route('admin.transport.assignments')->with('success', 'Transport assignment created successfully.');
    }

    public function assignmentDestroy(TransportAssignment $assignment)
    {
        $assignment->delete();

        return redirect()->route('admin.transport.assignments')->with('success', 'Transport assignment removed successfully.');
    }
}




