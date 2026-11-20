<x-app-layout>
    <x-slot name="title">Take Attendance</x-slot>

    <div class="max-w-4xl mx-auto">
        <div class="card mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Take Attendance</h2>
            <form id="attendanceForm" class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Classroom</label>
                    <select name="classroom_id" id="classroomSelect" class="input-field" required onchange="loadStudents()">
                        <option value="">Select Classroom</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Subject</label>
                    <select name="subject_id" id="subjectSelect" class="input-field" required>
                        <option value="">Select Subject</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                    <input type="date" name="date" class="input-field" required value="{{ date('Y-m-d') }}">
                </div>
                <div id="studentsContainer" class="hidden">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="table-header">Student ID</th>
                                <th class="table-header">Name</th>
                                <th class="table-header">Status</th>
                                <th class="table-header">Notes</th>
                            </tr>
                        </thead>
                        <tbody id="studentsTableBody">
                            <!-- Rows populated via JavaScript -->
                        </tbody>
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
            if (!classroomId) return;

            fetch('/attendance/get-students?classroom_id=' + classroomId)
                .then(response => response.json())
                .then(students => {
                    const container = document.getElementById('studentsContainer');
                    const tbody = document.getElementById('studentsTableBody');
                    const submitContainer = document.getElementById('submitContainer');

                    container.classList.remove('hidden');
                    submitContainer.classList.remove('hidden');
                    tbody.innerHTML = '';

                    students.forEach(student => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td class="table-cell">{{ auth()->user()->teacher ? '' : '' }}</td>
                            <td class="table-cell">{{ auth()->user()->teacher ? '' : '' }}</td>
                            <td class="table-cell">{{ auth()->user()->teacher ? '' : '' }}</td>
                        `;
                        // Simplified - in production, use proper student data
                        row.innerHTML = `
                            <td class="table-cell font-medium">STU-${student.id}</td>
                            <td class="table-cell">{{ auth()->user()->teacher ? 'Student ' + student.id : 'Student ' + student.id }}</td>
                            <td class="table-cell">
                                <select class="input-field w-full">
                                    <option value="present">Present</option>
                                    <option value="absent">Absent</option>
                                    <option value="late">Late</option>
                                    <option value="excused">Excused</option>
                                </select>
                            </td>
                            <td class="table-cell">
                                <input type="text" class="input-field w-full" placeholder="Notes">
                            </td>
                        `;
                        tbody.appendChild(row);
                    });
                });
        }
    </script>
</x-app-layout>