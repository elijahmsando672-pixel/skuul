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

    private const REQUIRED_ON_CREATE = [
        'students' => ['student_id'],
        'teachers' => ['employee_id'],
        'classrooms' => ['name', 'grade_level'],
        'subjects' => ['name', 'code'],
        'academic-years' => ['name', 'start_date', 'end_date'],
        'announcements' => ['title', 'content'],
    ];

    public function index(Request $request, string $resource): JsonResponse
    {
        $modelClass = $this->modelClass($resource);
        $query = $modelClass::query();

        if (self::RELATIONS[$resource] !== []) {
            $query->with(self::RELATIONS[$resource]);
        }

        return response()->json($query->paginate($this->perPage($request))->withQueryString());
    }

    public function show(Request $request, string $resource, $id): JsonResponse
    {
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
        $rules = [];
        $casts = $model->getCasts();

        foreach ($model->getFillable() as $field) {
            if (str_ends_with($field, '_id')) {
                $rules[$field] = $creating ? 'nullable|integer' : 'sometimes|nullable|integer';
                continue;
            }

            $rule = $this->typeRule($casts[$field] ?? 'string');

            if ($creating && in_array($field, self::REQUIRED_ON_CREATE[$resource] ?? [], true)) {
                $rules[$field] = 'required|'.$rule;
                continue;
            }

            $rules[$field] = $creating ? 'nullable|'.$rule : 'sometimes|nullable|'.$rule;
        }

        return $rules;
    }

    private function typeRule(string $cast): string
    {
        if (in_array($cast, ['int', 'integer'], true)) {
            return 'integer';
        }

        if (in_array($cast, ['bool', 'boolean'], true)) {
            return 'boolean';
        }

        if (in_array($cast, ['date', 'datetime', 'immutable_date', 'immutable_datetime'], true)) {
            return 'date';
        }

        if (str_starts_with($cast, 'decimal')) {
            return 'numeric';
        }

        if (str_starts_with($cast, 'array') || str_starts_with($cast, 'json')) {
            return 'array';
        }

        return 'string|max:1000';
    }

    private function perPage(Request $request): int
    {
        return max(1, min(100, (int) $request->query('per_page', 15)));
    }
}
