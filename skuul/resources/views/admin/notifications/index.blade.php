<x-app-layout>
    <x-slot name="title">Notifications</x-slot>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-slate-100">Notifications</h1>
        <p class="text-gray-600 dark:text-slate-400">Send email notifications through the configured mail server</p>
    </div>

    <form method="POST" action="{{ route('admin.notifications.send') }}" class="card max-w-3xl space-y-5" id="notification-form">
        @csrf
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
            <div>
                <label for="type" class="form-label">Notification Type</label>
                <select id="type" name="type" required class="select-field">
                    <option value="enrollment" @selected(old('type') === 'enrollment')>Enrollment Confirmation</option>
                    <option value="fee" @selected(old('type') === 'fee')>Fee Payment Reminder</option>
                    <option value="exam" @selected(old('type') === 'exam')>Exam Result Published</option>
                    <option value="library" @selected(old('type') === 'library')>Library Book Due</option>
                </select>
            </div>
            <div>
                <label for="user_id" class="form-label">Recipient</label>
                <select id="user_id" name="user_id" required class="select-field">
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" @selected(old('user_id') == $user->id)>{{ $user->name }} ({{ $user->email }})</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="notification-detail" data-type="enrollment">
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
                <div>
                    <label for="student_id" class="form-label">Student</label>
                    <select id="student_id" name="student_id" class="select-field">
                        <option value="">Select student</option>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}" @selected(old('student_id') == $student->id)>{{ $student->user->name ?? $student->student_id }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="classroom_id" class="form-label">Classroom</label>
                    <select id="classroom_id" name="classroom_id" class="select-field">
                        <option value="">Select classroom</option>
                        @foreach($classrooms as $classroom)
                            <option value="{{ $classroom->id }}" @selected(old('classroom_id') == $classroom->id)>{{ $classroom->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="academic_year_id" class="form-label">Academic Year</label>
                    <select id="academic_year_id" name="academic_year_id" class="select-field">
                        <option value="">Select year</option>
                        @foreach($academicYears as $year)
                            <option value="{{ $year->id }}" @selected(old('academic_year_id') == $year->id)>{{ $year->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="notification-detail" data-type="fee">
            <label for="fee_record_id" class="form-label">Fee Record</label>
            <select id="fee_record_id" name="fee_record_id" class="select-field">
                <option value="">Select fee record</option>
                @foreach($feeRecords as $record)
                    <option value="{{ $record->id }}" @selected(old('fee_record_id') == $record->id)>{{ $record->student->user->name ?? 'Student #'.$record->student_id }} - {{ $record->feeStructure->name ?? 'Fee' }}</option>
                @endforeach
            </select>
        </div>

        <div class="notification-detail" data-type="exam">
            <label for="exam_result_id" class="form-label">Exam Result</label>
            <select id="exam_result_id" name="exam_result_id" class="select-field">
                <option value="">Select exam result</option>
                @foreach($examResults as $result)
                    <option value="{{ $result->id }}" @selected(old('exam_result_id') == $result->id)>{{ $result->exam->name ?? 'Exam' }} - {{ $result->student->user->name ?? 'Student #'.$result->student_id }}</option>
                @endforeach
            </select>
        </div>

        <div class="notification-detail" data-type="library">
            <label for="book_issue_id" class="form-label">Book Issue</label>
            <select id="book_issue_id" name="book_issue_id" class="select-field">
                <option value="">Select book issue</option>
                @foreach($bookIssues as $issue)
                    <option value="{{ $issue->id }}" @selected(old('book_issue_id') == $issue->id)>{{ $issue->book->title ?? 'Book #'.$issue->book_id }} - {{ $issue->due_date?->format('M d, Y') ?? 'N/A' }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn-primary">Send Notification</button>
    </form>

    <script>
        const form = document.getElementById('notification-form');
        const type = document.getElementById('type');

        function updateDetails() {
            form.querySelectorAll('.notification-detail').forEach((section) => {
                const active = section.dataset.type === type.value;
                section.classList.toggle('hidden', !active);
                section.querySelectorAll('select').forEach((field) => {
                    field.disabled = !active;
                    field.required = active;
                });
            });
        }

        type.addEventListener('change', updateDetails);
        updateDetails();
    </script>
</x-app-layout>
