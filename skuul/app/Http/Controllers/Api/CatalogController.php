<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Announcement;
use App\Models\Classroom;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CatalogController extends Controller
{
    private const MAP = [
        'students' => Student::class,
        'teachers' => Teacher::class,
        'classrooms' => Classroom::class,
        'subjects' => Subject::class,
        'academic-years' => AcademicYear::class,
        'announcements' => Announcement::class,
    ];

    private const RELATIONS = [
        'students' => ['user', 'parent'],
        'teachers' => ['user'],
        'classrooms' => ['academicYear', 'teacher'],
        'subjects' => ['classroom', 'teacher'],
        'academic-years' => [],
        'announcements' => ['user'],
    ];

    private const RULES = [
        'students' => [
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'student_id' => ['required', 'string', 'max:255'],
            'grade_level' => ['nullable', 'string', 'max:255'],
            'enrollment_date' => ['nullable', 'date'],
            'parent_id' => ['nullable', 'integer', 'exists:parents,id'],
            'emergency_contact' => ['nullable', 'string', 'max:255'],
            'medical_info' => ['nullable', 'string'],
        ],
        'teachers' => [
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'employee_id' => ['required', 'string', 'max:255'],
            'department' => ['nullable', 'string', 'max:255'],
            'hire_date' => ['nullable', 'date'],
            'qualification' => ['nullable', 'string', 'max:255'],
            'specialization' => ['nullable', 'string', 'max:255'],
        ],
        'classrooms' => [
            'name' => ['required', 'string', 'max:255'],
            'grade_level' => ['required', 'string', 'max:255'],
            'section' => ['nullable', 'string', 'max:50'],
            'academic_year_id' => ['required', 'integer', 'exists:academic_years,id'],
            'teacher_id' => ['nullable', 'integer', 'exists:teachers,id'],
            'room_number' => ['nullable', 'string', 'max:50'],
            'capacity' => ['nullable', 'integer', 'min:1'],
        ],
        'subjects' => [
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'classroom_id' => ['required', 'integer', 'exists:classrooms,id'],
            'teacher_id' => ['required', 'integer', 'exists:teachers,id'],
            'credits' => ['nullable', 'integer', 'min:1'],
            'is_compulsory' => ['nullable', 'boolean'],
        ],
        'academic-years' => [
            'name' => ['required', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'is_active' => ['nullable', 'boolean'],
        ],
        'announcements' => [
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'target_audience' => ['nullable', 'string', 'in:all,students,teachers,parents,admin'],
            'priority' => ['nullable', 'string', 'in:low,normal,high,urgent'],
            'starts_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date', 'after:starts_at'],
            'is_published' => ['nullable', 'boolean'],
        ],
    ];

    private const UNIQUE_FIELDS = [
        'students' => ['student_id'],
        'teachers' => ['employee_id'],
        'subjects' => ['code'],
    ];

    public function index(Request $request, string $resource): JsonResponse
    {
        $this->ensureAdmin($request);
        $modelClass = $this->modelClass($resource);
        $query = $modelClass::query();

        if (self::RELATIONS[$resource] !== []) {
            $query->with(self::RELATIONS[$resource]);
        }

        return response()->json($query->paginate($this->perPage($request))->withQueryString());
    }

    public function show(Request $request, string $resource, $id): JsonResponse
    {
        $this->ensureAdmin($request);
        $modelClass = $this->modelClass($resource);
        $model = $modelClass::findOrFail($id);

        if (self::RELATIONS[$resource] !== []) {
            $model->load(self::RELATIONS[$resource]);
        }

        return response()->json($model);
    }

    public function store(Request $request, string $resource): JsonResponse
    {
        $this->ensureAdmin($request);
        $modelClass = $this->modelClass($resource);
        $model = new $modelClass;
        $validated = $request->validate($this->rules($resource, $model, true));

        if ($resource === 'announcements' && empty($validated['user_id'])) {
            $validated['user_id'] = $request->user()->id;
        }

        $model->fill($validated)->save();

        if (self::RELATIONS[$resource] !== []) {
            $model->load(self::RELATIONS[$resource]);
        }

        return response()->json($model, 201);
    }

    public function update(Request $request, string $resource, $id): JsonResponse
    {
        $this->ensureAdmin($request);
        $modelClass = $this->modelClass($resource);
        $model = $modelClass::findOrFail($id);
        $validated = $request->validate($this->rules($resource, $model, false));

        $model->fill($validated)->save();

        if (self::RELATIONS[$resource] !== []) {
            $model->load(self::RELATIONS[$resource]);
        }

        return response()->json($model);
    }

    public function destroy(Request $request, string $resource, $id): JsonResponse
    {
        $this->ensureAdmin($request);
        $modelClass = $this->modelClass($resource);
        $modelClass::findOrFail($id)->delete();

        return response()->json(['message' => 'Resource deleted successfully.']);
    }

    private function modelClass(string $resource): string
    {
        if (! isset(self::MAP[$resource])) {
            abort(404, 'Resource not found.');
        }

        return self::MAP[$resource];
    }

    private function ensureAdmin(Request $request): void
    {
        if (! $request->user()?->hasRole('admin')) {
            abort(403, 'Admin access required.');
        }
    }

    private function rules(string $resource, Model $model, bool $creating): array
    {
        $rules = self::RULES[$resource] ?? [];

        foreach (self::UNIQUE_FIELDS[$resource] ?? [] as $field) {
            $unique = Rule::unique($model->getTable(), $field);

            if (! $creating) {
                $unique->ignore($model->getKey());
            }

            $rules[$field][] = $unique;
        }

        if (! $creating) {
            foreach ($rules as $field => $constraints) {
                $rules[$field] = array_merge(['sometimes'], $constraints);
            }
        }

        return $rules;
    }

    private function perPage(Request $request): int
    {
        return max(1, min(100, (int) $request->query('per_page', 15)));
    }
}
