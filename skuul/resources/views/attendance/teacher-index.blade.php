<x-app-layout>
    <x-slot name="title">Take Attendance</x-slot>

    <div class="max-w-4xl mx-auto">
        <div class="card mb-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-slate-100 mb-4">Take Attendance</h2>
            <form id="attendanceForm" method="POST" action="{{ route('teacher.attendance.store') }}" class="space-y-6">
                @csrf
                @php
                    if (! isset($classrooms)) {
                        $classrooms = $subjects->pluck('classroom')->filter()->unique('id')->values();
                    }
                @endphp
                <div>
                    <label for="classroomSelect" class="form-label">Classroom</label>
                    <select name="classroom_id" id="classroomSelect" class="input-field" required onchange="loadStudents()">
                        <option value="">Select Classroom</option>
                        @foreach($classrooms as $classroom)
                            <option value="{{ $classroom->id }}">{{ $classroom->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="subjectSelect" class="form-label">Subject</label>
                    <select name="subject_id" id="subjectSelect" class="input-field" required>
                        <option value="">Select Subject</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}">{{ $subject->name }} ({{ $subject->classroom?->name ?? 'N/A' }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="attendanceDate" class="form-label">Date</label>
                    <input id="attendanceDate" type="date" name="date" class="input-field" required value="{{ date('Y-m-d') }}">
                </div>
                <div id="studentsContainer" class="hidden">
                    <table class="min-w-full divide-y divide-gray-100 dark:divide-slate-800">
                        <thead class="bg-gray-50 dark:bg-slate-800/60">
                            <tr>
                                <th class="table-header">Student ID</th>
                                <th class="table-header">Name</th>
                                <th class="table-header">Status</th>
                                <th class="table-header">Notes</th>
                            </tr>
                        </thead>
                        <tbody id="studentsTableBody" class="bg-white dark:bg-slate-900 divide-y divide-gray-100 dark:divide-slate-800"></tbody>
                    </table>
                </div>
                <div id="submitContainer" class="hidden">
                    <button type="submit" class="btn-primary">Save Attendance</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function loadStudents() {
            const classroomId = document.getElementById('classroomSelect').value;
            const tbody = document.getElementById('studentsTableBody');

            tbody.replaceChildren();

            if (!classroomId) {
                document.getElementById('studentsContainer').classList.add('hidden');
                document.getElementById('submitContainer').classList.add('hidden');
                return;
            }

            const url = '{{ route('teacher.attendance.get-students') }}' + '?classroom_id=' + encodeURIComponent(classroomId);

            fetch(url)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Unable to load students');
                    }
                    return response.json();
                })
                .then(students => {
                    students.forEach((student, index) => {
                        const row = document.createElement('tr');

                        const idCell = document.createElement('td');
                        idCell.className = 'table-cell font-medium';
                        const studentIdInput = document.createElement('input');
                        studentIdInput.type = 'hidden';
                        studentIdInput.name = 'records[' + index + '][student_id]';
                        studentIdInput.value = student.id;
                        idCell.appendChild(studentIdInput);
                        idCell.appendChild(document.createTextNode(student.student_id ?? ('STU-' + student.id)));
                        row.appendChild(idCell);

                        const nameCell = document.createElement('td');
                        nameCell.className = 'table-cell';
                        nameCell.textContent = student.user ? student.user.name : ('Student #' + student.id);
                        row.appendChild(nameCell);

                        const statusCell = document.createElement('td');
                        statusCell.className = 'table-cell';
                        const statusSelect = document.createElement('select');
                        statusSelect.name = 'records[' + index + '][status]';
                        statusSelect.className = 'input-field w-full';
                        ['present', 'absent', 'late', 'excused'].forEach(status => {
                            const option = document.createElement('option');
                            option.value = status;
                            option.textContent = status.charAt(0).toUpperCase() + status.slice(1);
                            statusSelect.appendChild(option);
                        });
                        statusCell.appendChild(statusSelect);
                        row.appendChild(statusCell);

                        const notesCell = document.createElement('td');
                        notesCell.className = 'table-cell';
                        const notesInput = document.createElement('input');
                        notesInput.type = 'text';
                        notesInput.name = 'records[' + index + '][notes]';
                        notesInput.className = 'input-field w-full';
                        notesInput.placeholder = 'Notes';
                        notesCell.appendChild(notesInput);
                        row.appendChild(notesCell);

                        tbody.appendChild(row);
                    });

                    document.getElementById('studentsContainer').classList.remove('hidden');
                    document.getElementById('submitContainer').classList.remove('hidden');
                })
                .catch(() => {
                    document.getElementById('studentsContainer').classList.add('hidden');
                    document.getElementById('submitContainer').classList.add('hidden');
                });
        }
    </script>
</x-app-layout>
