<x-app-layout>
    <x-slot name="title">Edit Timetable Entry</x-slot>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-slate-100">Edit Timetable Entry</h1>
        <p class="text-gray-600 dark:text-slate-400">Update class schedule details</p>
    </div>

    <form method="POST" action="{{ route('admin.timetables.update', $timetable) }}" class="card max-w-4xl space-y-5">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
            <div>
                <label for="academic_year_id" class="form-label">Academic Year</label>
                <select id="academic_year_id" name="academic_year_id" required class="select-field">
                    @foreach($academicYears as $year)
                        <option value="{{ $year->id }}" @selected(old('academic_year_id', $timetable->academic_year_id) == $year->id)>{{ $year->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="classroom_id" class="form-label">Classroom</label>
                <select id="classroom_id" name="classroom_id" required class="select-field">
                    @foreach($classrooms as $classroom)
                        <option value="{{ $classroom->id }}" @selected(old('classroom_id', $timetable->classroom_id) == $classroom->id)>{{ $classroom->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="subject_id" class="form-label">Subject</label>
                <select id="subject_id" name="subject_id" required class="select-field">
                    @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}" @selected(old('subject_id', $timetable->subject_id) == $subject->id)>{{ $subject->name }} ({{ $subject->classroom->name ?? 'N/A' }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="teacher_id" class="form-label">Teacher</label>
                <select id="teacher_id" name="teacher_id" required class="select-field">
                    @foreach($teachers as $teacher)
                        <option value="{{ $teacher->id }}" @selected(old('teacher_id', $timetable->teacher_id) == $teacher->id)>{{ $teacher->user->name ?? 'Teacher #'.$teacher->id }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="day_of_week" class="form-label">Day</label>
                <select id="day_of_week" name="day_of_week" required class="select-field">
                    @foreach($days as $day)
                        <option value="{{ $day }}" @selected(old('day_of_week', $timetable->day_of_week) === $day)>{{ $day }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="room" class="form-label">Room</label>
                <input id="room" type="text" name="room" value="{{ old('room', $timetable->room) }}" maxlength="100" class="input-field">
            </div>
            <div>
                <label for="start_time" class="form-label">Start Time</label>
                <input id="start_time" type="time" name="start_time" value="{{ old('start_time', $timetable->start_time) }}" required class="input-field">
            </div>
            <div>
                <label for="end_time" class="form-label">End Time</label>
                <input id="end_time" type="time" name="end_time" value="{{ old('end_time', $timetable->end_time) }}" required class="input-field">
            </div>
        </div>

        <div class="flex flex-wrap gap-3">
            <button type="submit" class="btn-primary">Update Entry</button>
            <a href="{{ route('admin.timetables.index') }}" class="btn-secondary">Cancel</a>
        </div>
    </form>
</x-app-layout>
